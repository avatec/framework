<?php namespace Core;

class Assets
{
    protected static $js_files = [];
    protected static $css_files = [];

    protected static $frontend = true;
    protected static $backend = false;

    public static function frontend()
    {
        self::$frontend = true;
        self::$backend = false;
    }

    public static function backend()
    {
        self::$frontend = false;
        self::$backend = true;
    }

    public static function get()
    {
        return [
            'css' => self::$css_files,
            'js' => self::$js_files
        ];
    }

    private static function has_js( $file )
    {
        if(!empty( self::$js_files )) {
            return in_array( $file, self::$js_files );
        }
        return false;
    }

    public static function js( $file, $module = null )
    {
        if( stripos( $file, 'http' ) !== false) {
            $path = $file;
        } else {
            if( is_null( $module )) {
                $path = '/templates/' . (self::$frontend == true ? 'website' : 'admin') . '/js/';
            } else {
                $path = '/modules/' . $module . '/' . (self::$frontend == true ? 'frontend' : 'backend') .'/js/';
            }

            $file = $path . $file;
        }

        if( self::has_js( $file ) == false ) {
            self::$js_files[] = $file;
        }
    }

    private static function has_css( $file )
    {
        if(!empty( self::$css_files )) {
            return in_array( $file, self::$css_files );
        }
        return false;
    }

    public static function css( $file, $module = null )
    {
        if( is_null( $module )) {
            $path = '/templates/' . (self::$frontend == true ? 'website' : 'admin') . '/css/';
        } else {
            $path = '/modules/' . $module . '/' . (self::$frontend == true ? 'frontend' : 'backend') .'/css/';
        }

        $file = $path . $file;

        if( self::has_css( $file ) == false ) {
            self::$css_files[] = $file;
        }
    }
}
