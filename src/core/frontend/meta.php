<?php

namespace Core\Frontend;

use \Exception;

class Meta
{
    protected static $title;
    protected static $description;
    protected static $index = false;
    protected static $follow = false;

    /**
     *  Ustawienie meta danych
     *  @param string $title
     *  @param string $description
     *  @param string $index
     *  @param string $follow
     */

    public static function set($title, $description, $index = false, $follow = false)
    {
        self::setTitle($title)
            ::setDescription($description)
            ::setIndex($index)
            ::setFollow($follow);
    }

    /**
     *  Pobranie meta danych
     *  @return array
     */

    public static function get()
    {
        if( empty( self::$title ) && empty( self::$description ) && empty( self::$index ) && empty( self::$follow )) {
            return [
                'title' => '',
                'description' => '',
                'index' => 1,
                'follow' => 1
            ];
        }

        return [
            'title' => self::$title,
            'description' => self::$description,
            'index' => self::$index,
            'follow' => self::$follow
        ];
    }

    /**
     *  Ustawienie nazwy serwisu
     *  @param string $title
     *  @return object
     */

    public static function setTitle($title)
    {
        self::$title = $title;
        return new self;
    }

    /**
     *  Ustawienie opisu serwisu
     *  @param string $description
     *  @return object
     */

    public static function setDescription($description)
    {
        self::$description = $description;
        return new self;
    }

    /**
     *  Ustawienie meta index
     *  @param boolean $index
     *  @return object
     */

    public static function setIndex($index)
    {
        $index = boolval( $index );
        if (is_bool($index) == true) {
            self::$index = $index;
            return new self;
        }

        throw new Exception('Core\Frontend\Meta::setFollow - parametr musi być typu boolean');
    }

    /**
     *  Ustawienie meta follow
     *  @param boolean $follow
     *  @return object
     */

    public static function setFollow($follow)
    {
        $follow = boolval( $follow );
        if (is_bool($follow) == true) {
            self::$follow = $follow;
            return new self;
        }

        throw new Exception('Core\Frontend\Meta::setFollow - parametr musi być typu boolean');
    }
}
