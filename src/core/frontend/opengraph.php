<?php

namespace Core\Frontend;

class Opengraph
{
    protected static $title;
    protected static $description;
    protected static $image;
    protected static $imageType;
    protected static $type;

    /**
     *  Pobranie meta danych
     *  @return array
     */

    public static function get()
    {
        return [
            'title' => self::$title,
            'description' => self::$description,
            'image' => self::$image,
            'imageType' => (!empty( self::$imageType ) ? self::$imageType : ''),
            'type' => (!empty( self::$type ) ? self::$type : 'website')
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
     *  Ustawienie zdjęcia og
     *  @param string $image
     *  @return object
     */

    public static function setImage( $image )
    {
        self::$image = $image;
        return new self;
    }

    /**
     *  Ustawienie mime dla zdjęcia og
     *  @param string $path
     *  @return object
     */

    public static function setImageType( $path )
    {
        if( file_exists( $path ) && !is_dir( $path )) {
            self::$imageType = mime_content_type( $path );
        }
        return new self;
    }

    public static function setType( $type )
    {
        self::$type = $type;
        return new self;
    }
}
