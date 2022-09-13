<?php

namespace Core\Frontend;

class Meta
{
    protected $title;
    protected $description;
    protected $index;
    protected $follow;

    /**
     *  Ustawienie meta danych
     *  @param string $title
     *  @param string $description
     *  @param string $index
     *  @param string $follow
     */

    public function set($title, $description, $index = false, $follow = false)
    {
        $this->setTitle($title)->setDescription($description)->setIndex($index)->setFollow($follow);
    }

    /**
     *  Pobranie meta danych
     *  @return array
     */

    public function get()
    {
        if( empty( $this->title ) && empty( $this->description ) && empty( $this->index ) && empty( $this->follow )) {
            return [
                'title' => '',
                'description' => '',
                'index' => 1,
                'follow' => 1
            ];
        }

        return [
            'title' => $this->title,
            'description' => $this->description,
            'index' => $this->index,
            'follow' => $this->follow
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
     *  Ustawienie meta index
     *  @param boolean $index
     *  @return object
     */

    public function setIndex($index)
    {
        $index = boolval( $index );
        if (is_bool($index) == true) {
            $this->index = $index;
            return $this;
        }

        die('Core\Frontend\Meta::setFollow - parametr musi być typu boolean');
    }

    /**
     *  Ustawienie meta follow
     *  @param boolean $follow
     *  @return object
     */

    public function setFollow($follow)
    {
        $follow = boolval( $follow );
        if (is_bool($follow) == true) {
            $this->follow = $follow;
            return $this;
        }

        die('Core\Frontend\Meta::setFollow - parametr musi być typu boolean');
    }
}
