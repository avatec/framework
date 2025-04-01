<?php
namespace Core;
/**
 *	Language Admin Class
 *	Copyright (c) 2017 Avatec.pl
 *	All Rights Reserved
 *	--------------------------------------------------------------------------------------------------------
 *	@author			Grzegorz Miskiewicz <biuro@avatec.pl>
 *	@version		2.0
 *	@copyright		Avatec.pl
 *	@package		Avatec Framework
 *	@description	Klasa obsługuje wielojęzykowość systemu. Możliwość wywoływania z poziomu SMARTY oraz PHP
 *	--------------------------------------------------------------------------------------------------------
 * 	Ten plik jest integralną częścią frameworka avatec i nie może być kopiowany i wykorzystywany w innym
 *	oprogramowaniu bez pisemnej zgody autora
 */

class LanguageBackend {

	public static $available;
	public static $selected = 'pl';
	public static $lang = array();

	public static function init($defaultLanguage = 'pl'): void
    {
		if(!empty($_SESSION['backend']['translations']['code'])) {
			self::$selected = $_SESSION['backend']['translations']['code'];
		} else {
			self::change( $defaultLanguage );
		}
	}

	public static function change( string $code ): void
    {
		self::$selected = $code;
		self::update();
	}

	public static function update(): void
    {
		$_SESSION['backend']['translations'] = array(
			'code' => self::$selected,
			'translate' => self::$lang
		);
	}

	public static function get( $module, $translate, $replace = null )
	{
		if(strpos($module, '/')) {
			$e = explode("/" , $module);
			$module = $e['0'];
			$file = $e['1'];
			$module_arr = $e['0'] . '_' . $e['1'];
		} else {
			$module_arr = $module;
		}


        if (isset(self::$lang[$module_arr][$translate])) {
            $text = self::$lang[$module_arr][$translate];
            if (!is_array($text)) {
                $text = str_replace("[app_url]", APP_URL, $text);
                $text = str_replace("[app_url_without_http]", str_replace("http://", "", APP_URL), $text);
            }
            if (!empty($replace)) {
                $text = preg_replace_callback('/([##]+)/', function ($matches) use (&$replace) {
                    return array_shift($replace);
                }, $text);
            }
            return $text;
        }

        return $translate;
	}

	public static function set($module, $value)
	{
		self::$lang[$module] = $value;
	}

	public static function load( $module, $include = false )
	{
		if( strpos( $module , '/' ) == true ) {
			$e = explode("/" , $module);
			$module = $e['0'];
			$file = $e['1'];
			$module_arr = $e['0'].'_' . $e['1'];
		} else {
			$module_arr = $module;
		}


		if($include) {
			$lang_file = APP_PATH . 'include/languages/admin/' . $module . '_' . self::$selected . '.php';
		} else {
			$lang_file = APP_PATH . 'modules/' .$module . '/languages/admin/' . (!empty($file) ? $file . '/' : '') . self::$selected . '.php';
		}

		if(!file_exists($lang_file)) {
			$lang_file = APP_PATH . 'modules/' .$module . '/backend/languages/' . (!empty($file) ? $file . '/' : '') . self::$selected . '.php';
		}

		if(file_exists($lang_file)) {
			include( $lang_file );
			if(isset($AdminLang)) {
				self::$lang[$module_arr] = $AdminLang;
				self::update();
			}
		}
	}

	public static function detect()
	{
		return substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
	}
}
