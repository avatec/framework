<?php namespace Core;

class Components
{
    protected static $_instance;
    protected static $data;
    protected static $data_sub;

    public function __construct()
    {
        self::$_instance = new self;
    }

    public static function getForSelect()
    {
        $select = [];

        $select[] = [
            'id' => -1,
            'name' => 'Brak komponentu',
            'subname' => ''
        ];

        if( !empty( self::$data )) {
            array_multisort( array_column( self::$data , 'position' ), SORT_DESC, self::$data );

            foreach( self::$data as $i ) {
                $select[] = [
                    'id' => $i['action'],
                    'name' => $i['title'],
                    'subname' => (!empty( $i['description'] ) ? $i['description'] : '')
                ];
            }

            return $select;
        }
    }

    public static function register( $title , $action = null, $position = null )
    {
        if(!empty( $title ) && is_array( $title )) {
            $o = $title;
            if(self::has( $o['action'] ) == true) {
                return;
            }

            self::$data[] = [
                'position' => self::lastPosition( $position ),
                'title' => $o['title'],
                'description' => (!empty( $o['description'] ) ? $o['description'] : ''),
                'action' => $o['action']
            ];

            return;
        }

        if(self::has( $action ) == true) {
            return;
        }

        self::$data[] = [
            'position' => self::lastPosition( $position ),
            'title' => $title,
            'action' => $action
        ];
    }

    protected static function has( $action )
    {
        if( !empty( self::$data )) {
            foreach( self::$data as $i ) {
                if( $i['action'] == $action ) {
                    //throw new \Exception("Core\Components::has( $action ) is duplicate", 1);

                    return true;
                }
            }
        }

        return false;
    }

    protected static function lastPosition( $position = null )
    {
        if( !empty( self::$data ) && isset( $position ) && in_array( $position, array_column( self::$data, 'position' )) == true) {
            throw new \Error("Core\Components::lastPosition( $position ) is duplicate", 1);
        }

        if( !empty( self::$data ) && is_array( self::$data )) {
            array_multisort( array_column( self::$data , 'position' ), SORT_DESC, self::$data );
            return ++self::$data[0]['position'];
        }

        return 1;
    }

    public static function getSubForSelect()
    {
        $select = [];
        if( !empty( self::$data_sub )) {
            array_multisort( array_column( self::$data_sub , 'position' ), SORT_DESC, self::$data_sub );

            foreach( self::$data_sub as $i ) {
                $select[] = [
                    'id' => $i['action'],
                    'name' => $i['title'],
                    'subname' => (!empty( $i['description'] ) ? $i['description'] : '')
                ];
            }

            return $select;
        }
    }

    public static function registerSub( $title , $action = null, $position = null )
    {
        if(!empty( $title ) && is_array( $title )) {
            $o = $title;
            if(self::hasSub( $o['action'] ) == true) {
                return;
            }

            self::$data_sub[] = [
                'position' => self::lastSubPosition( $position ),
                'title' => $o['title'],
                'description' => (!empty( $o['description'] ) ? $o['description'] : ''),
                'action' => $o['action']
            ];

            return;
        }

        if(self::hasSub( $action ) == true) {
            return;
        }

        self::$data_sub[] = [
            'position' => self::lastSubPosition( $position ),
            'title' => $title,
            'action' => $action
        ];
    }

    protected static function hasSub( $action )
    {
        if( !empty( self::$data_sub )) {
            foreach( self::$data_sub as $i ) {
                if( $i['action'] == $action ) {
                    //throw new \Exception("Core\Components::has( $action ) is duplicate", 1);
                    return true;
                }
            }
        }

        return false;
    }

    protected static function lastSubPosition( $position = null )
    {
        if( isset( $position ) && !empty( self::$data_sub ) && in_array( $position, array_column( self::$data_sub, 'position' )) == true) {
            throw new \Error("Core\Components::lastSubPosition( $position ) is duplicate", 1);
        }

        if( !empty( self::$data_sub ) && is_array( self::$data_sub )) {
            array_multisort( array_column( self::$data_sub , 'position' ), SORT_DESC, self::$data_sub );
            return ++self::$data_sub[0]['position'];
        }

        return 1;
    }
}
