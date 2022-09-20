<?php
namespace Core\Frontend;

class Breadcrumb
{
    protected static $data;

    public static function build()
    {
        $breadcrumb = self::get();
        if(!empty( $breadcrumb )) {
            $html[] = '<ul class="breadcrumb">';
            foreach( $breadcrumb as $i ) {
                $html[] = '<li class="item">' . $i['name'] . '</a>';
            }
            $html[] = '</ul>';

            return implode("" , $html);
        }
    }

    public static function get()
    {
        return self::$data;
    }

    public static function add($name, $url = '#', $main = false)
    {
        self::$data[] = [
            'name' => $name,
            'url' => $url,
            'main' => $main
        ];

        return new self;
    }
}
