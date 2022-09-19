<?php namespace Core\Backend;
use Core\Request;
use Core\Language;
class Model
{
    public static $Error = [];
    private static $table = '', $table_i18 = '';
    public $post, $get, $files, $any, $input;
    private $config, $route;

    public static $app_path, $app_url;
    public static $UploadPath, $UploadUrl;

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
        $this->route = $route;

        self::$app_path = $app_path;
        self::$app_url = $app_url;
    }

    public static function setTable( string $table )
    {
        self::$table = $table;

        return new self;
    }

    public static function getTable(): string
    {
        return self::$table;
    }

    public static function setTablei18( string $table )
    {
        self::$table_i18 = $table;

        return new self;
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
