<?php

namespace Core;

use Imagick;
use RuntimeException;
use InvalidArgumentException;

class Image
{
	/**
	 * [cropImage description]
	 * @param  int    $nw
	 * @param  int    $nh
	 * @param  string $source
	 * @param  string $dest
	 * @param  string $stype
	 * @return [type]
	 */
	public static function cropImage(int $nw, int $nh, string $source, string $dest, string $stype = "auto")
	{
		if (extension_loaded('imagick')) {
			self::cropAndScaleImageUsingImagick($nw, $nh, $source, $stype, $dest);
		} else {
			self::cropImageUsingGD($nw, $nh, $source, $stype, $dest);
		}
	}

	/**
	 * [cropImageUsingImagick description]
	 * @param  int    $nw
	 * @param  int    $nh
	 * @param  string $source
	 * @param  string $stype
	 * @param  string $dest
	 * @return [type]
	 * 
	 * @deprecated 8.1
	 */

	public static function cropImageUsingImagick(int $nw, int $nh, string $source, string $stype, string $dest)
	{
		return self::cropAndScaleImageUsingImagick( $nw, $nh, $source, $stype, $desc );
	}

	public static function cropAndScaleImageUsingImagick(int $newWidth, int $newHeight, string $sourceFile, string $sourceType, string $destFile, bool $scaleProportionally = true)
	{
		// Validate input parameters
		if ($newWidth <= 0) {
			throw new InvalidArgumentException("Invalid new width: {$newWidth}");
		}
		if ($newHeight < 0) {
			throw new InvalidArgumentException("Invalid new height: {$newHeight}");
		}
		if (!file_exists($sourceFile)) {
			throw new InvalidArgumentException("Source file not found: {$sourceFile}");
		}

		// Open the source image file with Imagick
		$image = new Imagick($sourceFile);

		// Crop and/or scale the image
		if ($newHeight > 0) {
			$image->cropThumbnailImage($newWidth, $newHeight);
		} else if ($scaleProportionally) {
			$image->scaleImage($newWidth, 0);
		} else {
			$image->scaleImage($newWidth, $newWidth);
		}

		// Write the modified image to the destination file
		if (!$image->writeImage($destFile)) {
			throw new RuntimeException("Failed to write image to destination file: {$destFile}");
		}
	}

	public static function resizeAndCropImage(int $newWidth, int $newHeight, string $source, string $destination, string $imageType = 'image/jpeg'): bool
	{
		$sourceImage = imagecreatefromstring(file_get_contents($source));
		if (!$sourceImage) {
			return false;
		}
		
		$sourceWidth = imagesx($sourceImage);
		$sourceHeight = imagesy($sourceImage);
		
		// Calculate the aspect ratios of the source and destination images
		$sourceRatio = $sourceWidth / $sourceHeight;
		$destRatio = $newWidth / $newHeight;

		if ($destRatio > $sourceRatio) {
			// Destination image is wider than source image
			$width = $sourceWidth;
			$height = round($sourceWidth / $destRatio);
			$xOffset = 0;
			$yOffset = round(($sourceHeight - $height) / 2);
		} else {
			// Destination image is taller than source image
			$width = round($sourceHeight * $destRatio);
			$height = $sourceHeight;
			$xOffset = round(($sourceWidth - $width) / 2);
			$yOffset = 0;
		}
		
		$destImage = imagecreatetruecolor($newWidth, $newHeight);
		$backgroundColor = imagecolorallocate($destImage, 255, 255, 255);
		imagefill($destImage, 0, 0, $backgroundColor);

		imagecopyresampled(
			$destImage,
			$sourceImage,
			0,
			0,
			$xOffset,
			$yOffset,
			$newWidth,
			$newHeight,
			$width,
			$height
		);
		
		$success = false;
		switch ($imageType) {
			case 'image/jpeg':
			case 'image/jpg':
				$success = imagejpeg($destImage, $destination, 80);
				break;
			case 'image/png':
				$success = imagepng($destImage, $destination, 8);
				break;
			case 'image/gif':
				$success = imagegif($destImage, $destination);
				break;
		}
		
		imagedestroy($sourceImage);
		imagedestroy($destImage);
		
		return $success;
	}

	/**
	 * [cropImageUsingGD description]
	 * @param  int    $nw
	 * @param  int    $nh
	 * @param  string $source
	 * @param  string $stype
	 * @param  string $dest
	 * @return [type]
	 */
	public static function cropImageUsingGD(int $nw, int $nh, string $source, string $stype, string $dest)
	{
		ini_set('memory_limit', '256M');

		$size = getimagesize($source);
		$w = $size[0];
		$h = $size[1];

		if ($nh == 0 || $nh == 'AUTO' || $nh == 'auto') {
			$prop = $w / $h;
			$nh = $nw * $prop;
		}

		if ($stype == "auto") {
			$stype = mime_content_type($source);
		}

		switch ($stype) {
			case 'image/gif':
				$stype = "gif";
				$simg = imagecreatefromgif($source);
				break;
			case 'image/jpg':
				$stype = "jpg";
				$simg = imagecreatefromjpeg($source);
				break;
			case 'image/jpeg':
				$stype = "jpeg";
				$simg = imagecreatefromjpeg($source);
				break;
			case 'image/png':
				$stype = "png";
				$simg = imagecreatefrompng($source);
				break;
			default:
				$simg = imagecreatefromjpeg($source);
				break;
		}

		imagealphablending($simg, false);
		imagesavealpha($simg, true);
		$dimg = imagecreatetruecolor($nw, $nh);
		$backgroundColor = imagecolorallocate($dimg, 255, 255, 255);
		imagefill($dimg, 0, 0, $backgroundColor);
		$wm = $w / $nw;
		$hm = $h / $nh;
		$h_height = $nh / 2;
		$w_height = $nw / 2;

		if ($w > $h) {
			$adjusted_width = $w / $hm + ($w / $nw); //+($w/6);
			$half_width = $adjusted_width / 2.2;
			$int_width = $half_width - $w_height;
			imagecopyresampled($dimg, $simg, -$int_width, 0, 0, 0, $adjusted_width, $nh, $w, $h);
		} elseif (($w < $h) || ($w == $h)) {
			$adjusted_height = $h / $wm;
			$half_height = $adjusted_height / 3.5;
			$int_height = $half_height - $h_height;
			imagecopyresampled($dimg, $simg, 0, -$int_height, 0, 0, $nw, $adjusted_height, $w, $h);
		} else {
			imagecopyresampled($dimg, $simg, 0, 0, 0, 0, $nw, $nh, $w, $h);
		}
		imagejpeg($dimg, $dest, 80);
	}

	public static function flop_image($filename, $mirror_filename, $path, $path_mirror = null)
	{
		if (is_null($path_mirror)) {
			$path_mirror = $path;
		}

		if (class_exists('\Gmagick')) {
			$image = new \Gmagick();
			$image->readImage($path . $filename);
			$image->flopImage();
			$image->writeImage($path_mirror . $mirror_filename);
			$image->destroy();

			return true;
		}

		$im = imagecreatefromjpeg($path . $filename);
		imageflip($im, IMG_FLIP_HORIZONTAL);
		imagejpeg($im, $path_mirror . $mirror_filename, 80);

		return true;
	}

	public static function png2jpg($src_file, $dest_path, $quality = 85)
	{
		$pi = pathinfo($src_file);
		$dest_file = $pi['filename'] . '.jpg';

		$image = imagecreatefrompng($src_file);
		$bg = imagecreatetruecolor(imagesx($image), imagesy($image));
		imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
		imagealphablending($bg, TRUE);
		imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
		imagedestroy($image);
		imagejpeg($bg, $dest_path . $dest_file, $quality);
		imagedestroy($bg);
	}

	public static function resize_width($after_width = 1920, $file = '', $output = '')
	{
		list($width, $height, $type, $attr) = getimagesize($file);
		if ($width > $after_width) {
			if (extension_loaded('imagick')) {
				$reduced_width = ($width - $after_width);
				$reduced_radio = round(($reduced_width / $width) * 100, 2);
				$reduced_height = round(($height / 100) * $reduced_radio, 2);
				$after_height = $height - $reduced_height;

				$thumb = new Imagick();
				$thumb->readImage($file);
				$thumb->resizeImage($after_width, $after_height, Imagick::FILTER_LANCZOS, 1);
				$thumb->writeImage($output);
				$thumb->clear();
				$thumb->destroy();
			} else {
				$img = imagecreatefromjpeg($file);
				$reduced_width = ($width - $after_width);
				$reduced_radio = round(($reduced_width / $width) * 100, 2);
				$reduced_height = round(($height / 100) * $reduced_radio, 2);
				$after_height = $height - $reduced_height;
				$resized = imagescale($img, $after_width, $after_height);
				imageantialias($img, true);
				imagejpeg($resized,  $output, 100);
			}
		} else {
			copy($file, $output);
		}
	}

	public static function resize_height($after_height = 1920, $file = '', $output = '')
	{
		list($width, $height, $type, $attr) = getimagesize($file);
		if ($height > $after_height) {
			if (extension_loaded('imagick')) {
				$reduced_height = ($height - $after_height);
				$reduced_radio = round(($reduced_height / $height) * 100, 2);
				$reduced_width = round(($width / 100) * $reduced_radio, 2);
				$after_width = $width - $reduced_width;

				$thumb = new \Imagick();
				$thumb->readImage($file);
				$thumb->resizeImage($after_width, $after_height, \Imagick::FILTER_LANCZOS, 1);
				$thumb->writeImage($output);
				$thumb->clear();
				$thumb->destroy();
			} else {
				if (in_array(mime_content_type($file), ['image/jpg', 'image/jpeg'])) {
					$img = imagecreatefromjpeg($file);
					$reduced_height = ($height - $after_height);
					$reduced_radio = round(($reduced_height / $height) * 100, 2);
					$reduced_width = round(($width / 100) * $reduced_radio, 2);
					$after_width = $width - $reduced_width;
					$resized = imagescale($img, $after_width, $after_height);
					imageantialias($img, true);
					imagejpeg($resized,  $output, 100);
				}

				if (mime_content_type($file) == 'image/png') {
					$img = imagecreatefrompng($file);
					$reduced_height = ($height - $after_height);
					$reduced_radio = round(($reduced_height / $height) * 100, 2);
					$reduced_width = round(($width / 100) * $reduced_radio, 2);
					$after_width = $width - $reduced_width;

					$resized = imagecreatetruecolor($after_width, $after_height); // new wigth and height
					imagealphablending($resized, false);
					imagesavealpha($resized, true);
					imagecopyresampled($resized, $img, 0, 0, 0, 0, $after_width, $after_height, imagesx($img), imagesy($img));

					imagealphablending($img, false);
					imagesavealpha($img, true);
					imagepng($img, $output);
				}
			}
		} else {
			copy($file, $output);
		}
	}

	public static function watermark($stamp, $source, $destination, $quality = 100, $margin_left = 0, $margin_top = 0)
	{
		if (extension_loaded('imagick')) {
			$image = new \Imagick();
			$image->readImage($source);

			$image_width = $image->getImageWidth();
			$image_height = $image->getImageHeight();

			$watermark = new \Imagick();
			$watermark->readImage($stamp);

			$watermark_width = $watermark->getImageWidth();
			$watermark_weight = $watermark->getImageHeight();

			//prawy dolny róg $margin_left = 0, $margin_top = 10
			$x = ($image_width - $watermark_width - $margin_left);
			$y = ($image_height - $watermark_weight - $margin_top);

			$image->compositeImage($watermark, \Imagick::COMPOSITE_OVER, $x, $y);
			$image->writeImage($destination);
		} else {
			$size = getimagesize($source);

			$src_width = $size[0];
			$src_height = $size[1];

			$stamp = imagecreatefrompng($stamp);
			$im = imagecreatefromjpeg($source);

			$sx = imagesx($stamp);
			$sy = imagesy($stamp);

			imagecopy(
				$im,
				$stamp,
				$margin_left,
				$margin_top,
				0,
				0,
				$sx,
				$sy
			);

			imagejpeg($im, $destination, $quality);
			@imagedestroy($im);
		}
	}

	protected $fileTypePatterns = [
		['type' => 'heic', 'b64' => 'AAAoZnR5cGhlaWMAAAAAbQ=='],
		['type' => 'jpg',  'b64' => '2P/gABBKRklGAAEBAABIAA=='],
		['type' => 'jpg',  'b64' => '2P/gABBKRklGAAEBAAABAA=='],
		['type' => 'png',  'b64' => 'UE5HDQoaCgAAAA1JSERSAA=='],
		['type' => 'webp', 'b64' => 'SUZG4igAAFdFQlBWUDgg1g==']
	];

	/**
	 * Detekcja formatu pliku
	 * @param  string $file
	 * @return string
	 */
	public static function detectFileType(string $file): string
	{
		$fp = fopen($file, 'r');
		fseek($fp, 1);
		$result = fread($fp, 16);
		fclose($fp);

		foreach (self::$fileTypePatterns as $i) {
			if ($i['b64'] === base64_encode($result)) {
				return $i['type'];
			}
		}

		return 'unknown / ' . $result;
	}

	public static function getDominantColor($img, $default = '#eee')
	{
		if (@exif_imagetype($img)) { 
			$type = getimagesize($img)[2]; 
			if ($type === 1) { 
				$image = imagecreatefromgif($img);
				if (imagecolorsforindex($image, imagecolorstotal($image) - 1)['alpha'] == 127) return 'fff';
			} else if ($type === 2) { // JPG
				$image = imagecreatefromjpeg($img);
			} else if ($type === 3) { // PNG
				$image = imagecreatefrompng($img);
				if ((imagecolorat($image, 0, 0) >> 24) & 0x7F === 127) return 'fff';
			} else {
				return $default;
			}
		} else {
			return $default;
		}
		$newImg = imagecreatetruecolor(1, 1); 
		imagecopyresampled($newImg, $image, 0, 0, 0, 0, 1, 1, imagesx($image), imagesy($image));
		return dechex(imagecolorat($newImg, 0, 0)); 
	}
}
