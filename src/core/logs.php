<?php namespace Core;

class Logs
{
    public static function create( string $filename, string $text ): void
    {
        global $app_path;
        $now = date('Y-m-d');

        $log_dir = $app_path . 'logs' . DIRECTORY_SEPARATOR . $now;
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0777, true);
        }

        $log_file = $log_dir . DIRECTORY_SEPARATOR . $filename;
        $log_message = sprintf("[%s] %s\r\n", date('Y-m-d H:i:s'), $text);
        file_put_contents($log_file, $log_message, FILE_APPEND);
    }
}
