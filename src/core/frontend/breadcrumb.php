<?php
namespace Core\Frontend;

class Breadcrumb
{
    protected static $data;

    public static function build()
    {
        $breadcrumb = self::get();
        if(!empty( $breadcrumb )) {
            $html[] = '<ol class="breadcrumb">';
            foreach( $breadcrumb as $i ) {
                if (!empty($i['main'])){
                    $html[] = '<li class="breadcrumb-item">' . $i['name'] . '</li>';
                }else{
                    $html[] = '<li class="breadcrumb-item active"><a href="' . $i['url']. '">' . $i['name'] . '</a></li>';
                }
            }
            $html[] = '</ol>';

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
