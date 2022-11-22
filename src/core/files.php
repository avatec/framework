<?php

namespace Core;

use Core\Image;
use Core\Logs;
use Core\Language;
use \Verot\Upload\Upload as Upload;

class Files
{
	public static $Error = [];

	public static function get_format(string $file): string
	{
		return pathinfo($file, PATHINFO_EXTENSION);
	}

	/**
	 *	Zwraca dla mime kod dla Fantastic Awesome Icons
	 *	@param string $mime\
	 *	@return string
	 */

	public static function get_icon(string $mime): string
	{
		$array = [
			"image/png" => "fa-file-image",
			"image/jpg" => "fa-file-image",
			"image/jpeg" => "fa-file-image",
			"image/webp" => "fa-file-image",
			"video/quicktime" => "fa-file-video",
			"video/mp4" => "fa-file-video",
			"video/mpeg" => "fa-file-video",
			"application/pdf" => 'fa-file-pdf',
			"application/zip" => 'fa-file-archive',
			"application/msword" => 'fa-file-word',
			"application/vnd.openxmlformats-officedocument.wordprocessingml.document" => 'fa-file-word',
			"application/vnd.oasis.opendocument.text" => 'fa-file-word',
			"application/vnd.oasis.opendocument.spreadsheet" => 'fa-file-excel',
			"application/vnd.ms-office" => 'fa-file-excel',
			"application/octet-stream" => 'fa-file-excel',
			"application/vnd.ms-excel" => 'fa-file-excel',
			"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" => 'fa-file-excel'
		];

		return ($array[$mime] ? $array[$mime] : 'fa-file');
	}

	/**
	 *	Sprawdzanie czy wybrany plik istnieje i czy nie jest katalogiem
	 *	@param string $file
	 *	@return boolean
	 */

	public static function file_exists($file)
	{
		if (!is_dir($file) && file_exists($file) == true) {
			return true;
		}

		return false;
	}

	/**
	 *	Sprawdzanie czy wybrany katalog istnieje
	 *	@param string $dir
	 *	@return boolean
	 */

	public static function dir_exists($dir)
	{
		if (file_exists($dir) == true && is_dir($dir) == true) {
			return true;
		}
		return false;
	}

	/**
	 *	Tworzenie katalogu
	 *	@param string $dir
	 *  @param int chmod
	 */

	public static function create_dir($dir, $chmod = 0777)
	{
		if (self::dir_exists($dir) == false) {
			mkdir($dir, $chmod, true);
		}
	}

	/**
	 *  Kasowanie pliku
	 *  @param string $file
	 */
	public static function delete($file)
	{
		if (file_exists($file) == true && !is_dir($file)) {
			unlink($file);
		}
	}

	/**
	 *  Uploadowanie plików
	 *  @param array $file
	 *  @param int $o[convert][quality] default 80
	 *  @param int $o[thumbs][width] default 768
	 *  @param int $o[thumbs][height] default auto
	 *  @param boolean $o[thumbs][resize] default true
	 *  @return mixed
	 */

	public static function upload($file, $o)
	{
		if ((!empty($file) && is_array($file))) {
			if ($file['error'] == 1) {
				self::$Error[] = "uploadowany plik przekracza dyrektywe upload_max_filesize w php.ini";
			}
			if ($file['error'] == 2) {
				self::$Error[] = "uploadowany plik przekracza dyrektywe MAX_FILE_SIZE w formularzu HTML";
			}
			if ($file['error'] == 3) {
				self::$Error[] = "uploadowany plik nie został poprawnie wgrany - błąd numer 3";
			}
			if ($file['error'] == 4) {
				self::$Error[] = "brak pliku";
			}
			if ($file['error'] == 6) {
				self::$Error[] = "brak dostępu do katalogu tymczasowego na serwerze - błąd numer 6";
			}
			if ($file['error'] == 7) {
				self::$Error[] = "nie udało się zapisać na dysku - błąd numer 7";
			}
			if ($file['error'] == 8) {
				self::$Error[] = "uploadowanie przerwane przez rozszerzenie - błąd numer 8 (UPLOAD_ERR_EXTENSION)";
			}

			$handle = new Upload($file);
			if ($handle->uploaded) {
				if (!empty($o['allowed_mime'])) {
					$handle->allowed = $o['allowed_mime'];
				}

				if (!empty($o['filename'])) {
					$handle->file_new_name_body	= $o['filename'];
				}

				if (!empty($o['overwrite'])) {
					$handle->file_overwrite		= $o['overwrite'];
				} else {
					$handle->file_auto_rename	= true;
				}

				Logs::create('core.files.log', 'Src mime: ' . $handle->file_src_mime);

				if (in_array($handle->file_src_mime, ['image/png', 'image/jpg', 'image/jpeg', 'image/gif', 'image/*']) == true) {
					if (!empty($o['convert']) && ($handle->image_src_type != 'jpg' || $handle->image_src_type != 'jpeg')) {
						$handle->image_convert = 'jpeg';
						$handle->jpeg_quality = (!empty($o['convert']['quality']) ? $o['convert']['quality'] : 80);
					}

					if (!empty($o['resize'])) {
						$handle->image_resize = true;

						if (!empty($o['resize']['width']) && empty($o['resize']['height']) && $handle->image_src_x) {
							if ($handle->image_src_x > $o['resize']['width']) {
								$handle->image_x = $o['resize']['width'];
							} else {
								$handle->image_x = $handle->image_src_x;
							}

							$handle->image_ratio_y = true;
						}

						if (empty($o['resize']['width']) && !empty($o['resize']['height'])) {
							$handle->image_y = $o['resize']['height'];
							$handle->image_ratio_x = true;
						}

						if (!empty($o['resize']['width']) && !empty($o['resize']['height'])) {
							$handle->image_x = $o['resize']['width'];
							$handle->image_y = $o['resize']['height'];
						}
					}
				}

				Files::create_dir($o['upload_dir']);
				$handle->Process($o['upload_dir']);

				if ($handle->processed) {

					if (!empty($o['thumbs'])) {
						Files::create_dir($o['upload_dir'] . $o['thumbs']['folder']);

						if (!empty($o['thumbs']['crop'])) {
							Image::cropImage(
								(!empty($o['thumbs']['width']) ? $o['thumbs']['width'] : null),
								(!empty($o['thumbs']['height']) ? $o['thumbs']['height'] : null),
								$o['upload_dir'] . "/" . $handle->file_dst_name,
								$o['upload_dir'] . $o['thumbs']['folder'] . "/" . $handle->file_dst_name
							);
						}

						if (!empty($o['thumbs']['resize'])) {
							if (!empty($o['thumbs']['width']) && empty($o['thumbs']['height'])) {
								Image::resize_width(
									$o['thumbs']['width'],
									$o['upload_dir'] . "/" . $handle->file_dst_name,
									$o['upload_dir'] . $o['thumbs']['folder'] . "/" . $handle->file_dst_name
								);
							}

							if (empty($o['thumbs']['width']) && !empty($o['thumbs']['height'])) {
								Image::resize_height(
									$o['thumbs']['height'],
									$o['upload_dir'] . "/" . $handle->file_dst_name,
									$o['upload_dir'] . $o['thumbs']['folder'] . "/" . $handle->file_dst_name
								);
							}

							if (!empty($o['thumbs']['width']) && !empty($o['thumbs']['height'])) {
								if ($handle->image_src_x <= $handle->image_src_y) {
									Image::resize_width(
										$o['thumbs']['width'],
										$o['upload_dir'] . "/" . $handle->file_dst_name,
										$o['upload_dir'] . $o['thumbs']['folder'] . "/" . $handle->file_dst_name
									);
								} else {
									Image::resize_height(
										$o['thumbs']['height'],
										$o['upload_dir'] . "/" . $handle->file_dst_name,
										$o['upload_dir'] . $o['thumbs']['folder'] . "/" . $handle->file_dst_name
									);
								}
							}
						}
					}
					if (!empty($o['watermark'])) {
						Files::create_dir($o['upload_dir'] . $o['watermark']['folder']);
						Image::watermark(
							$o['watermark']['stamp'],
							$o['watermark']['source'] . "/" . $handle->file_dst_name,
							$o['watermark']['destination'] . "/" . $handle->file_dst_name,
							(!empty($o['watermark']['quality']) ? $o['watermark']['quality'] : 100),
							(!empty($o['watermark']['margin_left']) ? $o['watermark']['margin_left'] : 0),
							(!empty($o['watermark']['margin_top']) ? $o['watermark']['margin_top'] : 10),
						);
						if (!empty($o['watermark']['thumbs']['resize'])) {
							Files::create_dir($o['upload_dir'] . $o['watermark']['folder'] . "/" .  $o['watermark']['thumbs']['folder']);
							Image::watermark(
								$o['watermark']['stamp'],
								$o['upload_dir'] . $o['thumbs']['folder'] . "/" . $handle->file_dst_name,
								$o['upload_dir'] . $o['watermark']['folder'] . "/" . $o['watermark']['thumbs']['folder'] . "/" . $handle->file_dst_name,
								(!empty($o['watermark']['quality']) ? $o['watermark']['quality'] : 100),
								(!empty($o['watermark']['margin_left']) ? $o['watermark']['margin_left'] : 0),
								(!empty($o['watermark']['margin_top']) ? $o['watermark']['margin_top'] : 10),
							);
						}
					}

					return ['success' => true, 'filename' => $handle->file_dst_name];
				}

				self::$Error[] = "plik nie został załadowany na serwer.";
				self::$Error[] = $handle->error;
			} else {
				self::$Error[] = "wystąpił nieoczekiwany błąd podczas uploadu";
			}
		}

		if (!empty(self::$Error)) {
			return ['error' => ['message' => Language::get('cms', 'Wystąpił błąd podczas uploadu:') . implode(self::$Error)]];
		}
	}

	private static $uploadErrorMessages = [
		1 => 'uploadowany plik przekracza dyrektywe upload_max_filesize w php.ini',
		2 => 'uploadowany plik przekracza dyrektywe MAX_FILE_SIZE',
		3 => 'uploadowany plik nie został poprawnie wgrany - błąd numer 3',
		4 => 'brak pliku',
		6 => 'brak dostępu do katalogu tymczasowego na serwerze - błąd numer 6',
		7 => 'nie udało się zapisać na dysku - błąd numer 7',
		8 => 'uploadowanie przerwane przez rozszerzenie - błąd numer 8 (UPLOAD_ERR_EXTENSION)'
	];

	public static function getErrorMessage(int $errno): string
	{
		return (!empty(self::$uploadErrorMessages[$errno]) ? self::$uploadErrorMessages[$errno] : false);
	}
}
