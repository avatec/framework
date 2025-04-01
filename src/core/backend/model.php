<?php namespace Core\Backend;

use Core\Language;
use Core\Request;

class Model
{
    public static $Error = [];
    public static $app_path, $app_url;
    public static $UploadPath, $UploadUrl, $BlankImage;
    protected static $_config;
    public $post, $get, $files, $any, $input, $server;
    protected $config, $route;

    public function __construct()
    {
        global $config, $route, $app_path, $app_url;

        $request = new Request();

        $this->input = (!empty( $request->input ) ? $request->input : null);
        $this->post = (!empty( $request->post ) ? $request->post : null);
        $this->get = (!empty( $request->get ) ? $request->get : null);
        $this->files = (!empty( $request->files ) ? $request->files : null);
        $this->any = (!empty( $request->any ) ? $request->any : null);
        $this->server = $request->server;

        $this->config = $config;
        self::$_config = $config;
        $this->route = $route;

        self::$app_path = $app_path;
        self::$app_url = $app_url;
    }

    public static function getErrors(): array
    {
        return !empty( self::$Error ) ? self::$Error : [];
    }

    public static function setError( string $message )
    {
        self::$Error[] = $message;
    }

    public static function hasErrors(): bool
    {
        return !empty( self::$Error ) ? true : false;
    }

    public static function getCurrentLanguage(): string
    {
        return Language::get_selected();
    }

    public static function getAvailableLanguages(): array
    {
        return Language::$available;
    }
}
