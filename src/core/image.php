<?php namespace Core;

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
    public static function cropImage( int $nw, int $nh, string $source, string $dest, string $stype = "auto" )
	{
		if (extension_loaded('imagick')) {
			self::cropImageUsingImagick($nw, $nh, $source, $stype, $dest);
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
     */

	public static function cropImageUsingImagick( int $nw, int $nh, string $source, string $stype, string $dest )
	{
		$im = new \imagick( $source );
		if($nh > 0) {
			$im->cropThumbnailImage( $nw, $nh );
		} else {
			$im->scaleImage($nw, 0);
		}
		$im->writeImage( $dest );
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
	public static function cropImageUsingGD( int $nw, int $nh, string $source, string $stype, string $dest )
	{
		ini_set('memory_limit', '256M' );

	    $size = getimagesize($source);
	    $w = $size[0];
	    $h = $size[1];

		if($nh==0 || $nh=='AUTO' || $nh =='auto') {
			$prop = $w/$h;
			$nh = $nw * $prop;
		}

        if($stype=="auto") {
        	$stype = mime_content_type($source);
        }

	    switch($stype)
		{
			case 'image/gif': $stype="gif"; $simg = imagecreatefromgif($source); break;
			case 'image/jpg': $stype="jpg"; $simg = imagecreatefromjpeg($source); break;
			case 'image/jpeg': $stype="jpeg"; $simg = imagecreatefromjpeg($source); break;
			case 'image/png': $stype="png"; $simg = imagecreatefrompng($source); break;
			default:
				$simg = imagecreatefromjpeg($source);
			break;
	    }

		imagealphablending($simg, false);
        imagesavealpha($simg, true);
		$dimg = imagecreatetruecolor($nw, $nh);
		$backgroundColor = imagecolorallocate($dimg, 255, 255, 255);
		imagefill($dimg, 0, 0, $backgroundColor);
	    $wm = $w/$nw;
	    $hm = $h/$nh;
	    $h_height = $nh/2;
	    $w_height = $nw/2;

	    if($w> $h) {
	      $adjusted_width = $w / $hm + ($w/$nw);//+($w/6);
	      $half_width = $adjusted_width / 2.2;
	      $int_width = $half_width - $w_height;
	      imagecopyresampled($dimg,$simg,-$int_width,0,0,0,$adjusted_width,$nh,$w,$h);
	    } elseif(($w <$h) || ($w == $h)) {
	      $adjusted_height = $h / $wm;
	      $half_height = $adjusted_height / 3.5;
	      $int_height = $half_height - $h_height;
	      imagecopyresampled($dimg,$simg,0,-$int_height,0,0,$nw,$adjusted_height,$w,$h);
	    } else { imagecopyresampled($dimg,$simg,0,0,0,0,$nw,$nh,$w,$h); }
	    imagejpeg($dimg,$dest,80);
	}

	public static function flop_image( $filename, $mirror_filename, $path, $path_mirror = null )
	{
		if(is_null($path_mirror)) {
			$path_mirror = $path;
		}

		if(class_exists( '\Gmagick' )) {
			$image = new \Gmagick();
			$image->readImage( $path . $filename );
			$image->flopImage();
			$image->writeImage( $path_mirror . $mirror_filename );
			$image->destroy();

			return true;
		}

		$im = imagecreatefromjpeg( $path . $filename );
		imageflip( $im, IMG_FLIP_HORIZONTAL );
		imagejpeg( $im, $path_mirror . $mirror_filename, 80 );

		return true;
	}

	public static function png2jpg( $src_file, $dest_path, $quality = 85 )
	{
		$pi = pathinfo( $src_file );
		$dest_file = $pi['filename'] . '.jpg';

		$image = imagecreatefrompng( $src_file );
		$bg = imagecreatetruecolor(imagesx($image), imagesy($image));
		imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
		imagealphablending($bg, TRUE);
		imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
		imagedestroy($image);
		imagejpeg($bg, $dest_path . $dest_file, $quality);
		imagedestroy($bg);
	}

	public static function resize_width( $after_width = 1920, $file = '', $output = '' )
	{
	    list( $width, $height, $type, $attr) = getimagesize( $file );
		if( $width > $after_width ) {
			if (extension_loaded('imagick')) {
				$reduced_width = ($width - $after_width);
		        $reduced_radio = round(($reduced_width / $width) * 100, 2);
		        $reduced_height = round(($height / 100) * $reduced_radio, 2);
		        $after_height = $height - $reduced_height;

				$thumb = new \Imagick();
				$thumb->readImage($file);
				$thumb->resizeImage($after_width,$after_height,\Imagick::FILTER_LANCZOS,1);
				$thumb->writeImage($output);
				$thumb->clear();
				$thumb->destroy();
			} else {
				$img = imagecreatefromjpeg( $file );
		        $reduced_width = ($width - $after_width);
		        $reduced_radio = round(($reduced_width / $width) * 100, 2);
		        $reduced_height = round(($height / 100) * $reduced_radio, 2);
		        $after_height = $height - $reduced_height;
		        $resized = imagescale( $img, $after_width, $after_height);
				imageantialias($img, true);
		        imagejpeg($resized,  $output, 100 );
			}
		} else {
            copy( $file , $output );
        }
	}

	public static function resize_height( $after_height = 1920, $file = '', $output = '' )
	{
	    list( $width, $height, $type, $attr) = getimagesize( $file );
		if( $height > $after_height ) {
			if (extension_loaded('imagick')) {
				$reduced_height = ($height - $after_height);
		        $reduced_radio = round(($reduced_height / $height) * 100, 2);
		        $reduced_width = round(($width / 100) * $reduced_radio, 2);
		        $after_width = $width - $reduced_width;

				$thumb = new \Imagick();
				$thumb->readImage($file);
				$thumb->resizeImage($after_width,$after_height,\Imagick::FILTER_LANCZOS,1);
				$thumb->writeImage($output);
				$thumb->clear();
				$thumb->destroy();
			} else {
                if(in_array(mime_content_type( $file ), ['image/jpg','image/jpeg'])) {
    				$img = imagecreatefromjpeg( $file );
    				$reduced_height = ($height - $after_height);
    		        $reduced_radio = round(($reduced_height / $height) * 100, 2);
    		        $reduced_width = round(($width / 100) * $reduced_radio, 2);
    		        $after_width = $width - $reduced_width;
    		        $resized = imagescale( $img, $after_width, $after_height);
    				imageantialias($img, true);
    		        imagejpeg($resized,  $output, 100 );
                }

                if(mime_content_type( $file ) == 'image/png' ) {
                    $img = imagecreatefrompng ( $file );
                    $reduced_height = ($height - $after_height);
    		        $reduced_radio = round(($reduced_height / $height) * 100, 2);
    		        $reduced_width = round(($width / 100) * $reduced_radio, 2);
    		        $after_width = $width - $reduced_width;

                    $resized = imagecreatetruecolor ( $after_width, $after_height ); // new wigth and height
                    imagealphablending($resized , false);
                    imagesavealpha($resized , true);
                    imagecopyresampled($resized, $img, 0, 0, 0, 0, $after_width, $after_height, imagesx ( $img ), imagesy ( $img ) );

                    imagealphablending($img , false);
                    imagesavealpha($img , true);
                    imagepng ( $img, $output );
                }
			}
		} else {
            copy( $file , $output );
        }
	}

	public static function watermark( $stamp, $source, $destination, $quality = 100, $margin_left = 0, $margin_top = 0 )
	{
        if (extension_loaded('imagick')) {
            $image = new \Imagick();
            $image->readImage( $source );

			$image_width = $image->getImageWidth();
			$image_height = $image->getImageHeight();

            $watermark = new \Imagick();
            $watermark->readImage( $stamp );

			$watermark_width = $watermark->getImageWidth();
			$watermark_weight = $watermark->getImageHeight();

			//prawy dolny róg $margin_left = 0, $margin_top = 10
            $x = ($image_width - $watermark_width - $margin_left);
			$y = ($image_height - $watermark_weight - $margin_top);

            $image->compositeImage($watermark, \Imagick::COMPOSITE_OVER, $x, $y);
            $image->writeImage( $destination );
        } else {
    		$size = getimagesize( $source );

            $src_width = $size[0];
            $src_height = $size[1];

    		$stamp = imagecreatefrompng( $stamp );
    		$im = imagecreatefromjpeg( $source );

    		$sx = imagesx( $stamp );
    		$sy = imagesy( $stamp );

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
    public function detectFileType( string $file ): string
    {
        $fp = fopen( $file , 'r' );
        fseek( $fp, 1 );
        $result = fread( $fp , 16 );
        fclose($fp);

        foreach( $fileTypePatterns as $i ) {
            if($i['b64'] === base64_encode( $result )) {
                return $i['type'];
            }
        }

        return 'unknown / ' . $result;
    }
}
