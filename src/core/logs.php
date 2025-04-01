<?php namespace Core;

class Logs
{
    public static function create( $filename, $text )
    {
        global $app_path;

        $now = date('Y-m-d');

		if( is_dir( $app_path . 'logs/') == false ) {
			@mkdir( $app_path . 'logs/');
		}

        if (is_dir($app_path . "logs/" . $now . "/") == false) {
            @mkdir($app_path . "logs/" . $now . "/");
        }

        file_put_contents($app_path . "logs/" . $now . '/' . $filename, "[".date('Y-m-d H:i:s')."] " . $text . "\r\n", FILE_APPEND);
    }
}
