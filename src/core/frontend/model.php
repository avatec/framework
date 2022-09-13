<?php namespace Core\Frontend;

class Model
{
    public static $Error = [];
    public $post, $get, $files, $any;
    public $config;

    public function __construct()
    {
        global $config, $request;

        $this->input = (!empty( $request->input ) ? $request->input : null);
        $this->post = (!empty( $request->post ) ? $request->post : null);
        $this->get = (!empty( $request->get ) ? $request->get : null);
        $this->files = (!empty( $request->files ) ? $request->files : null);
        $this->any = (!empty( $request->any ) ? $request->any : null);
        $this->server = $request->server;

        $this->config = $config;
    }
}
