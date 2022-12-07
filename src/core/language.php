<?php
namespace Core;

/**
 *	Language Class
 *	Copyright (c) 2017-2018 Avatec.pl
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

class Language
{

    // Lista języków strony
    public static $available = [];

    // Wybrany jezyk dla strony
    public static $selected = 'de';

    // Wybrany język strony w PA
    public static $selected_admin = 'de';

    // Tablica z tłumaczeniami
    public static $lang = array();

/**
 * Zwraca listę utworzonych języków dla danego serwisu
 * @return array
 */
    public static function getList()
    {
        if( empty( self::$available )) {
            return [];
        }

        $select = [];

        foreach( self::$available as $code=>$name ) {
            $select[] = [
                'id' => $code,
                'name' => $name
            ];
        }
        
        return $select;
    }

/**
 *	Inicjalizacja języków
    */
    public static function init( $defaultLanguage = 'pl', $allowBrowserLanguage = false )
    {
        global $route;
        $route->language = $defaultLanguage;
        self::$selected = $defaultLanguage;

        if( !empty( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) && $allowBrowserLanguage == true) {
            $browser_language = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
            $browser_language = explode("-", $browser_language);
            $browser_language = strtolower($browser_language[0]);
        }

        // Strona internetowa
        if ($route->isBackend == false) {
            if (isset($_COOKIE['frontendLanguage'])) {
                self::$selected = $_COOKIE['frontendLanguage'];
            }

            self::change($route->language);

            if (!empty($_SESSION['frontendLanguage']['code'])) {
                self::$selected = $_SESSION['frontendLanguage']['code'];
            } else {
                if (!empty(self::$selected)) {
                    self::change(self::$selected);
                } else {
                    self::change($browser_language);
                }
            }
        }

        // Panel administracyjny
        if ($route->isBackend == true) {
            if (isset($_COOKIE['backendLanguage'])) {
                self::$selected_admin = $_COOKIE['backendLanguage'];
                //$_SESSION['backendLanguage']['code'] = self::$selected_admin;
            }

            if (empty(self::$selected_admin)) {
                self::change( $defaultLanguage );
            }
        }
    }

    public static function get_selected()
    {
        global $route;

        if ($route->isBackend == false) {
            return self::$selected;
        } else {
            return self::$selected_admin;
        }
    }

    public static function variety($number, $variant)
    {
        if ($number == 0) {
            return $variant[2];
        }

        if ($number == 1) {
            return $variant[0];
        }

        if ($number > 1 && $number <=4) {
            return $variant[1];
        }

        if ($number > 4) {
            return $variant[2];
        }
    }

    public static function change($code)
    {
        global $route;

        // Strona internetowa
        if ($route->isBackend == false) {
            self::$selected = $code;
            setcookie('frontendLanguage', self::$selected, time() + 3600, '/');
        }

        // Panel administracyjny
        if ($route->isBackend == true) {
            self::$selected_admin = $code;
            setcookie('backendLanguage', self::$selected_admin, time() + 3600, '/');
        }

        self::update();
    }

    public static function get($module, $translate, $replace = array())
    {
        global $app_url;

        if (strpos($module, '/') == true) {
            $e = explode("/", $module);
            $module = $e['0'];
            $file = $e['1'];
            $module_arr = $e['0'] . '_' . $e['1'];
        } else {
            $module_arr = $module;
        }

        if (isset(self::$lang[$module_arr][$translate])) {
            $text = self::$lang[$module_arr][$translate];
            $text = str_replace("[app_url]", $app_url, $text);
            $text = str_replace("[app_url_without_http]", str_replace("http://", "", $app_url), $text);
            if (!empty($replace)) {
                $text = preg_replace_callback('/([##]+)/', function ($matches) use (&$replace) {
                    return array_shift($replace);
                }, $text);
            }
            return self::replace($text, $replace);
        } else {
            $r = Db::row("value", "system_translates", "WHERE module='" . $module . "' AND code='" . self::get_selected() . "' AND slug='" . $translate . "'");
            if (!empty($r['value'])) {

                return self::replace(stripslashes($r['value']), $replace);
            } else {
                foreach (self::$available as $code=>$name) {
                    if (Db::check("system_translates", "module='" . $module . "' AND code='" . $code . "' AND slug='" . $translate . "'") == false) {
                        $r = Db::insert("system_translates", "null,
						'" . $module . "',
						'" . $code . "',
						'" . addslashes($translate) . "',
						'" . addslashes($translate) . "'");

                        if ($r == false) {
                            die(Db::error());
                        }
                    }
                }

                return self::replace($translate, $replace);
            }

            //trigger_error('Translation for <b>' . $module . '</b> => <u>' . $translate . '</u> <b>not found</b><br/>' , E_USER_NOTICE );
            return $translate;
        }
    }

    public static function set($module, $value)
    {
        self::$lang[$module] = $value;
    }

    public static function load($module, $include = false)
    {
        global $app_path;

        if (strpos($module, '/') == true) {
            $e = explode("/", $module);
            $module = $e['0'];
            $file = $e['1'];
            $module_arr = $e['0'].'_' . $e['1'];
        } else {
            $module_arr = $module;
        }

        if ($include == true) {
            $lang_file = $app_path . 'include/languages/' . $module . '_' . self::$selected . '.php';
        } else {
            $lang_file = $app_path . 'modules/' .$module . '/languages/' . (!empty($file) ? $file . '/' : '') . self::$selected . '.php';
        }

        if (file_exists($lang_file) == true) {
            include($lang_file);
            if (isset($Lang)) {
                self::$lang[$module_arr] = $Lang;
                self::update();
            }
        } else {
            Kernel::log("error.log", "Can't find language file in module <b>".$lang_file."</b>");
            //trigger_error('Can\'t find language file in module <b>'.$lang_file.'</b>' , E_USER_NOTICE );
        }
    }

    public static function update()
    {
        global $route;

        // Strona internetowa
        if (isset($route->isBackend) && $route->isBackend == false) {
            $_SESSION['frontendLanguage'] = array(
                'code' => self::$selected,
                'translate' => self::$lang
            );
        }

        // Panel administracyjny
        if (isset($route->isBackend) && $route->isBackend == true) {
            $_SESSION['backendLanguage'] = array(
                'code' => self::$selected_admin,
                'translate' => self::$lang
            );
        }
    }

    public static function detect()
    {
        return substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    }

    protected static function replace( $string, $replacements = null )
    {
        if( empty( $replacements )) {
            return $string;
        }

        if( !empty( $replacements )) {
            foreach( $replacements as $search=>$replace_value) {
                $string = str_replace( $search , $replace_value , $string );
            }

            return $string;
        }
    }
}
