<?php

namespace Core;

class Views
{
    public static $template;
    public static $schema;
    public static $module;

    protected static $frontend = true;
    protected static $backend = false;

    public static function backend()
    {
        self::$backend = true;
        self::$frontend = false;
    }

    public static function frontend()
    {
        self::$backend = false;
        self::$frontend = true;
    }

    public static function get_schema()
    {
        if (empty(self::$schema)) {
            return false;
        }

        global $app_path;

        if (self::$frontend == true) {
            $vp = $app_path . 'modules/' . self::$module . '/frontend/views/' . self::$schema . '.smarty';
            if (file_exists($vp) && !is_dir($vp)) {
                return 'modules/' . self::$module . '/frontend/views/' . self::$schema . '.smarty';
            }

            return 'templates/website/schema/' . self::$schema . '.smarty';
        }

        if (self::$backend == true) {
            $vp = $app_path . 'modules/' . self::$module . '/backend/views/' . self::$schema . '.smarty';
            if (file_exists($vp) && !is_dir($vp)) {
                return 'modules/' . self::$module . '/backend/views/' . self::$template . '.smarty';
            }

            return 'templates/admin/schema/' . self::$schema . '.smarty';
        }
    }

    public static function get_template()
    {
        if (empty(self::$template)) {
            return false;
        }

        if (self::$frontend == true) {
            return 'modules/' . self::$module . '/frontend/views/' . self::$template . '.smarty';
        }

        if (self::$backend == true) {
            return 'modules/' . self::$module . '/backend/views/' . self::$template . '.smarty';
        }
    }

    public static function set($template)
    {
        self::$template = $template;

        return new self();
    }

    public static function schema($file)
    {
        self::$schema = $file;

        return new self();
    }

    public static function module($name)
    {
        self::$module = $name;

        return new self();
    }
}
