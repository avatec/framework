<?php namespace Core;

class Form
{
    public static $post;
    public static $get;

    public function __contruct()
    {
        global $request;

        self::$post = $request->post;
        self::$get  = $request->get;

    }

    protected static function is_selected()
    {

    }

    public static function bt4_checkbox( $o )
    {
        print_r($o);
    }
}
