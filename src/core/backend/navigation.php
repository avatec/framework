<?php

namespace Core\Backend;

use Modules\Admins\Backend\Admins;

/**
 *  Obsługa menu w panelu administracyjnym
 *  Copyright 2019 Grzegorz Miśkiewicz
 *  @package Avatec Framework
 */

class Navigation
{
    public static $menu;
    public static $config_menu;

    /**
     *  Tworzenie menu poziomu zerowego
     *  @param $lp (int) - liczba porządkowa
     *  @param $module (string) - nazwa modułu, bez polskich znaków i znaków specjalnych
     *  @param $name (string) - wyświetlana nazwa
     *  @param $path (string) - ścieżka bez przedrostka admin
     *  @param $icon (string) - kod ikony fantastic awesome icons np. fa-gears
     */

    public static function menu($lp, $module, $name, $path = null, $icon = null)
    {
        self::$menu[ $module ] = [
            'priority' => (int) $lp,
            'name' => $name,
            'path' => $path,
            'icon' => (!empty($icon) ? $icon : null)
        ];
    }

/**
 *  Tworzenie menu podporządkowanego poziomu pierwszego
 *  @param $module (string) - nazwa modułu zgodna z menu zerowego poziomu, bez polskich znaków i znaków specjalnych
 *  @param $name (string) - wyświetlana nazwa
 *  @param $path (string) - ścieżka bez przedrostka admin
 */

    public static function submenu($module, $name, $path, $lp = 1)
    {
        if(!empty( self::$menu[$module]['submenu'] )) {
            foreach( self::$menu[$module]['submenu'] as $submenu ) {
                if( $submenu['path'] == $path ) {
                    return false;
                }
            }
        }
        
        self::$menu[ $module ]['submenu'][] = [
            'priority' => $lp,
            'name' => $name,
            'path' => $path
        ];
    }

/**
 *  Tworzenie menu konfiguracji w górnej części serwisu
 *  @param $module (string) - nazwa modułu zgodna z menu zerowego poziomu, bez polskich znaków i znaków specjalnych
 *  @param $name (string) - wyświetlana nazwa
 *  @param $path (string) - ścieżka bez przedrostka admin
 */

    public static function configmenu($name, $path, $priority = 1)
    {
        self::$config_menu[] = [
            'priority' => (int) $priority,
            'name' => $name,
            'path' => $path
        ];
    }

/**
 *  Generuje linię oddzielającą
 *  @param $lp (int) - liczba porządkowa
 */

    public static function line($lp)
    {
        self::$menu['line_' . $lp] = [
            'priority' => (int) $lp,
            'line' => true
        ];
    }

/**
 *  Generuje nagłówek z linia oddzielającą
 *  @param $lp (int) - liczba porządkowa
 *  @param $name (string) - wyświetlana nazwa
 */

    public static function label($lp, $name)
    {
        self::$menu[ 'label_' . $lp ] = [
            'priority' => (int) $lp,
            'name' => $name,
            'label' => true
        ];
    }

    private static function sort()
    {
        if(!empty( self::$menu )) {
            foreach (self::$menu as $k=>$i) {
                if(!empty( self::$menu[$k]['submenu'] )) {
                    usort( self::$menu[$k]['submenu'], ['\Core\Backend\Navigation', 'sortByPriority']);
                }
            

                usort(self::$menu, function ($item1, $item2) {
                    if(!empty( $item1['priority'] ) && !empty( $item2['priority'] )) {
                        return $item1['priority'] <=> $item2['priority'];
                    }
                });
            }
        }
    }

    private static function sortByPriority($a, $b)
    {
        return $a['priority'] <=> $a['priority'];
    }

/**
 *  Zwraca wygenerowany kod HTML menu
 *  @return string
 */
    public static function get()
    {
        if (empty(self::$menu)) {
            return '';
        }
    
        foreach (self::$menu as $k=>$i) {       
            self::$menu[$k]['access'] = $k;
        }

        self::sort();

        global $app_admin_url;

        if (!empty(Admins::$auth) && Admins::$auth['access']) {
            $user_access = explode(";", Admins::$auth['access']);
        } else {
            $user_access[] = "";
        }

        //nadanie wszystkim użytkownikom uprawnień do pulpitu
        array_push($user_access, "system");
        
        $html[] = '<ul class="main">';
        foreach (self::$menu as $k=>$i) {
            if(empty($i['name'])) {
                continue;
            }
           
            if ((in_array($i['access'], $user_access) == true) || ($user_access[0] == '')) {
                if (!empty($i['label'])) {
                    $html[] = '<h3 class="menu-separator">' . $i['name'] . '</h3>';
                } elseif (!empty($i['line'])) {
                    $html[] = '<div class="menu-separator"></div>';
                } else {
                    $html[] = '<li rel="' . $k . '">';
                    $html[] = '<a class="' . (!empty($i['submenu']) ? 'has_sub' : '') . '" href="' . (!empty($i['path']) ? $app_admin_url . $i['path'] : '#') . '">';
                    $html[] = '<i class="fa ' . (!empty($i['icon']) ? $i['icon'] : 'fa-file') . '"></i> <span>' . $i['name'] . '</span></a>';
                    if (!empty($i['submenu'])) {
                        $html[] = '<ul>';
                        foreach ($i['submenu'] as $si) {
                            $html[] = '<li rel="' . $k . '"><a href="' . $app_admin_url . $si['path'] . '"><span>'.$si['name'].'</span></a></li>';
                        }
                        $html[] = '</ul>';
                    }
                    $html[] = '</li>';
                }
            }
        }
        $html[] = '</ul>';

        return implode($html);
    }

    public static function getConfigMenu()
    {
        if (empty(self::$config_menu)) {
            return;
        }

        foreach (self::$config_menu as $k=>$i) {
            self::$config_menu[$k]['access'] = $k;
        }

        $config_menu = self::$config_menu;

        usort(self::$config_menu, function ($a, $b) {
            return $a['priority']-$b['priority'];
        });

        global $app_admin_url;

        if (!empty(Admins::$auth) && Admins::$auth['access']) {
            $user_access = explode(";", Admins::$auth['access']);
        } else {
            $user_access[] = "";
        }

        //nadanie wszystkim użytkownikom uprawnień do pulpitu
        array_push($user_access, "system");

        foreach (self::$config_menu as $k=>$i) {
            if ((in_array($i['access'], $user_access) == true) or ($user_access[0] == '')) {
                $html[] = '<li><a href="' . (!empty($i['path']) ? $app_admin_url . $i['path'] : '#') . '">' . $i['name'] . '</a></li>';
            }
        }

        self::$config_menu = $config_menu;

        if (!empty($html)) {
            return implode($html);
        }
    }
}
