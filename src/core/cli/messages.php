<?php namespace Core\Cli;

class Messages
{
    protected static $data = [];
    protected static $allowed_states = ['success','error','warning','info'];

    public static function get($state = null)
    {
        if( !is_null( $state )) {
            $state = strtolower($state);
        }

        if (!empty(self::$data[$state])) {
            return self::$data[$state];
        } else {
            return self::$data;
        }
    }

    protected static function set($state, $text)
    {
        self::$data[$state][] = $text;
    }

    public static function success($text, $array = null)
    {
        if (!empty($array)) {
            $text = '<b>' . $text . '</b><br/>' . implode('<br/>', $array);
        }

        self::set('success', $text);
    }

    public static function error($text, $array = null)
    {
        if (!empty($array)) {
            $text = '<b>' . $text . '</b><br/>' . implode('<br/>', $array);
        }

        self::set('error', $text);
    }

    public static function warning($text, $array = null)
    {
        if (!empty($array)) {
            $text = '<b>' . $text . '</b><br/>' . implode('<br/>', $array);
        }

        self::set('warning', $text);
    }

    public static function info($text, $array = null)
    {
        if (!empty($array)) {
            $text = $text . '<br/>' . implode('<br/>', $array);
        }

        self::set('info', $text);
    }

    public static function clear()
    {
        if (!empty(self::$data)) {
            self::$data = [];
        }
    }
}
