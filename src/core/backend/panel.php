<?php namespace Core\Backend;

class Panel
{
    private static $title;
    private static $buttons;

    public function __call($method, $args)
    {
        return $this->call($method, $args);
    }

    public static function __callStatic($method, $args)
    {
        return (new static())->call($method, $args);
    }

    private function call($method, $args)
    {
        if (! method_exists($this , '_' . $method)) {
            throw new \Exception('Call undefined method ' . $method);
        }

        return $this->{'_' . $method}(...$args);
    }

    public static function getTitle()
    {
        return self::$title;
    }

    public static function setTitle( string $title )
    {
        self::$title = $title;
        return new self;
    }

    public function _addButton( $o )
    {
        self::$buttons[] = $o;
        return $this;
    }

    public static function getButtons()
    {
        $html = [];

        if( !empty( self::$buttons )) {
            foreach( self::$buttons as $o ) {
                if(empty($o['link'])) {
                    $html[] = '<button type="' . $o['type'] . '" form="' . $o['form'] . '"' . (empty( $o['id'] ) ? '' : ' id="' . $o['id'] . '"') . ' class="' . $o['class'] . '">';

                    if(!empty( $o['icon'] ) && !empty( $o['icon_position']) && $o['icon_position'] == 'left') {
                        $html[] = '<i class="' . $o['icon'] . '"></i>&nbsp;';
                    }

                    $html[] = $o['text'];

                    if(!empty( $o['icon'] ) && !empty( $o['icon_position']) && $o['icon_position'] == 'right') {
                        $html[] = '<i class="' . $o['icon'] . '"></i>&nbsp;';
                    }

                    $html[] = '</button>';
                } else {
                    $html[] = '<a href="' . $o['link'] . '" class="' . $o['class'] . '" target="' . (empty($o['target']) ? '_self' : $o['target']) . '">';
                    if(!empty( $o['icon'] ) && !empty( $o['icon_position']) && $o['icon_position'] == 'left') {
                        $html[] = '<i class="' . $o['icon'] . '"></i>&nbsp;';
                    }

                    $html[] = $o['text'];

                    if(!empty( $o['icon'] ) && !empty( $o['icon_position']) && $o['icon_position'] == 'right') {
                        $html[] = '<i class="' . $o['icon'] . '"></i>&nbsp;';
                    }

                    $html[] = '</a>';
                }
            }
        }

        return implode('', $html);
    }
}
