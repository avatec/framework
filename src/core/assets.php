<?php namespace Core;

class Assets
{
    protected static $js_files = [];
    protected static $css_files = [];

    protected static $frontend = true;
    protected static $backend = false;
    protected static $assets = false;

    private static function getPath( $type, $module = null ): string
    {
        if (self::$assets) {
            return '/assets/';
        }

        if( is_null( $module )) {
            return "/templates/" . (self::$frontend ? "website" : "admin") . "/$type/";
        }

        $modulePath = '/modules/' . $module . '/' . (self::$frontend ? 'frontend' : 'backend');

        $result = "/$modulePath/$type/";
        $result = str_replace("//" , "/" , $result);

        return $result;
    }

    public static function assets()
    {
        self::$assets = true;
        return new static();
    }

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

    private static function hasJS($file)
    {
        return in_array($file, self::$js_files);
    }

    public static function js($file, $module = null)
    {
        if (filter_var($file, FILTER_VALIDATE_URL) !== false) {
            self::$js_files[] = $file;
        } elseif (!self::hasJS($file)) {
            $path = self::getPath('js', $module);
            self::$js_files[] = $path . $file;
        }

        return new static();
    }

    private static function hasCss($file)
    {
        return in_array($file, self::$css_files);
    }

    public static function css($file, $module = null)
    {
        if (filter_var($file, FILTER_VALIDATE_URL) !== false) {
            self::$css_files[] = $file;
        } elseif (self::hasCss($file) === false) {
            $path = self::getPath('css', $module);
            self::$css_files[] = $path . $file;
        }

        return new static();
    }

    public static function setExternalJs( $name, $link )
	{
		self::$js_files[$name] = $link;
        return new static();
	}

    public static function setExternalCss( $name, $link )
	{
		self::$css_files[$name] = $link;
        return new static();
	}
}
