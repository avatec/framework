<?php namespace Core\Backend;

use Core\Language;
class Model
{
    public static $Error = [];
    private static $table = '';
    private $post, $get, $files, $any, $input;
    private $config, $route;

    public function __construct()
    {
        global $config, $route, $request;

        $this->input = (!empty( $request->input ) ? $request->input : null);
        $this->post = (!empty( $request->post ) ? $request->post : null);
        $this->get = (!empty( $request->get ) ? $request->get : null);
        $this->files = (!empty( $request->files ) ? $request->files : null);
        $this->any = (!empty( $request->any ) ? $request->any : null);
        $this->server = $request->server;

        $this->config = $config;
        $this->route = $route;
    }

    public static function getTable(): string
    {
        return self::$table;
    }

    public static function getErrors(): array
    {
        return !empty( self::$Error ) ? self::$Error : [];
    }

    public static function hasErrors(): bool
    {
        return !empty( self::$Error ) ? true : false;
    }

    public static function getCurrentLanguage(): string
    {
        return Language::get_selected();
    }
}
