<?php

namespace Core\Frontend;

class Opengraph
{
    protected $title;
    protected $description;
    protected $image;
    protected $imageType;
    protected $type;

    /**
     *  Pobranie meta danych
     *  @return array
     */

    public function get()
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->image,
            'imageType' => (!empty( $this->imageType ) ? $this->imageType : ''),
            'type' => (!empty( $this->type ) ? $this->type : 'website')
        ];
    }

    /**
     *  Ustawienie nazwy serwisu
     *  @param string $title
     *  @return object
     */

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     *  Ustawienie opisu serwisu
     *  @param string $description
     *  @return object
     */

    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     *  Ustawienie zdjęcia og
     *  @param string $image
     *  @return object
     */

    public function setImage( $image )
    {
        $this->image = $image;
        $this->imageType = mime_content_type( $this->image );
        return $this;
    }

    public function setType( $type )
    {
        $this->type = $type;
        return $this;
    }
}
