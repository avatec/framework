<?php

namespace Core;

class Request
{
    public $get = array();
    public $post = array();
    public $cookie = array();
    public $files = array();
    public $server = array();
    public $input = array();

    public function __construct()
    {
        $_GET = $this->clean($_GET);
        $_POST = $this->clean($_POST);
        $_REQUEST = $this->clean($_REQUEST);
        $_COOKIE = $this->clean($_COOKIE);
        $_FILES = $this->clean($_FILES);
        $_SERVER = $this->clean($_SERVER);

        $this->get = $_GET;
        $this->post = $_POST;
        $this->any = $_REQUEST;
        $this->cookie = $_COOKIE;
        $this->files = $_FILES;
        $this->server = $_SERVER;

        $this->input = file_get_contents('php://input');
        $input_src = $this->input;
        $this->input = json_decode( $this->input, true );
        if( json_last_error() ) {
            $this->input = json_encode([
                'error' => true,
                'msg' => json_last_error_msg(),
                'response' => $input_src
            ]);
        }
    }

    public function clean( $data )
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                unset($data[$key]);

                $data[$this->clean($key)] = $this->clean($value);
            }
        } else {
            $data = htmlspecialchars($data, ENT_COMPAT, 'UTF-8');
        }

        return $data;
    }

    public static function redirect( string $url, int $code = 301 )
    {
        if (!is_null( $code )) {
            header("HTTP/1.1 {$code} See Other");
        }

        header("Location: " . $url);
        exit;
    }
}
