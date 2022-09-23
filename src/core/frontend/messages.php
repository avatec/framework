<?php namespace Core\Frontend;

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

    public static function getErrors()
    {
        if(!empty( self::$data['error'] )) {
            return self::$data['error'];
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
