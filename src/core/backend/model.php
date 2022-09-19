<?php namespace Core\Backend;

use Core\Language;
class Model
{
    public static $Error = [];
    private static $table = '', $table_i18 = '';
    private $post, $get, $files, $any, $input;
    private $config, $route;

    const APP_PATH, APP_URL;

    public function __construct()
    {
        global $config, $route, $request, $app_path, $app_url;

        $this->input = (!empty( $request->input ) ? $request->input : null);
        $this->post = (!empty( $request->post ) ? $request->post : null);
        $this->get = (!empty( $request->get ) ? $request->get : null);
        $this->files = (!empty( $request->files ) ? $request->files : null);
        $this->any = (!empty( $request->any ) ? $request->any : null);
        $this->server = $request->server;

        $this->config = $config;
        $this->route = $route;

        self::APP_PATH = $app_path;
        self::APP_URL = $app_url;
    }

    public static function getTable(): string
    {
        return self::$table;
    }

    public static function getTablei18(): string
    {
        return self::$table_i18;
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
