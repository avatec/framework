<?php namespace Core;

class Form
{
    public static $post;
    public static $get;

    public static $default_input_class = "form-control";
    public static $default_select_class = "form-control";
    public static $default_textarea_class = "form-control";
    public static $default_checkbox_class = "";
    public static $default_radio_class = "";

    public function __contruct()
    {
        global $request;

        self::$post = (!empty($request->post) ? $request->post : self::$post);
        self::$get  = (!empty($request->get) ? $request->get : self::$get);
    }

    /**
     *  Metoda generująca otwarcie formularza <form>
     *  @param array|string o (method / options)
     *  @param string $url
     *  @return string
     */


    public static function open( $o = null, $url = null )
    {
        if(!empty( $o ) && !is_array( $o )) {
            return '<form class="form-horizontal" method="' . (empty( $o ) ? 'POST' : $o) . '"' .
            (!empty( $url ) ? ' action="' . $url . '"' : '') . '>' .
            '<input type="hidden" name="module" value="1" />';
        }

        return '<form class="form-horizontal" method="' . (empty( $o['method'] ) ? 'POST' : $o['method']) . '"' .
        (!empty( $o['action'] ) ? ' action="' . $o['action'] . '"' : '') .
        (!empty( $o['files'] ) ? ' enctype="multipart/form-data"' : '') . '>' .
        '<input type="hidden" name="module" value="1" />';
    }

    /**
     *  Metoda pomocnicza generująca kod dla data-nazwa="wartosc"
     *  @param array data
     *  @return string
     */

    protected static function build_data($data)
    {
        if (!empty($data)) {
            foreach ($data as $key=>$value) {
                $html[] = ' data-' . $key . '="' . $value . '"';
            }

            return implode($html);
        }
    }

    /**
     *  Metoda sprawdzenia, czy podana wartość występuje w POST, GET. Jeżeli tak zwraca true
     *  @param string name
     *  @param string value
     *  @return string
     */

    protected static function has($name, $value)
    {
        $name = str_replace('[]' , '' , $name);

        // Sprawdzanie w POST[$name]
        if (!empty(self::$post[ $name ]) && self::$post[ $name ] == $value) {
            return true;
        }

        // Sprawdzanie czy $value znajduje się w tablicy POST[$name]
        if(!empty( self::$post[ $name ] ) && is_array( self::$post[ $name ])) {
            if(in_array($value, self::$post[ $name ])) {
                return true;
            }
        }

        // Sprawdzanie w GET[$name]
        if (isset(self::$get[ $name ]) && self::$get[ $name ] == $value) {
            return true;
        }

        // Sprawdzanie czy $value znajduje się w tablicy GET[$name]
        if(isset( self::$get[ $name ] ) && is_array( self::$get[ $name ])) {
            if(in_array($value, self::$get[ $name ])) {
                return true;
            }
        }

        preg_match('/.*?\[/', $name, $param_result);
        if (!empty($param_result)) {
            $result_name = str_replace('[', '', end($param_result));
        }

        preg_match('/\[[^\]]+\]/', $name, $param_result);
        if (!empty($param_result)) {
            $result_param = str_replace(['[',']'], '', end($param_result));
        }


        if (!empty($result_name) && !empty($result_param)) {
            if (!empty( self::$post[ $result_name ][ $result_param ] )) {
                return true;
            }

            if (!empty(self::$get[ $result_name ][ $result_param ])) {
                return true;
            }
        }

        return false;
    }

    /**
     *  Metoda pobierania wartości value z tablicy POST, GET
     *  @param string name
     *  @return string
     */

    protected static function getValue($name)
    {
        // Sprawdzanie w POST[$name]
        if (!empty(self::$post[ $name ])) {
            return self::$post[ $name ];
        }

        // Sprawdzanie w GET[$name]
        if (!empty(self::$get[ $name ])) {
            return self::$get[ $name ];
        }

        preg_match('/.*?\[/', $name, $param_result);
        if (!empty($param_result)) {
            $result_name = str_replace('[', '', end($param_result));
        }

        preg_match('/\[[^\]]+\]/', $name, $param_result);
        if (!empty($param_result)) {
            $result_param = str_replace(['[',']'], '', end($param_result));
        }


        if (!empty($result_name) && !empty($result_param)) {
            if (!empty( self::$post[ $result_name ][ $result_param ] )) {
                return self::$post[ $result_name ][ $result_param ];
            }

            if (!empty(self::$get[ $result_name ][ $result_param ])) {
                return self::$get[ $result_name ][ $result_param ];
            }
        }

        return;
    }

    /**
     *  Alias dla Core\Form::has( $name, $value )
     *  @param string name
     *  @param string value
     *  @return bool
     */

    protected static function is_checked($name, $value)
    {
        return self::has($name, $value);
    }

    /**
     *  Alias dla Core\Form::has( $name, $value )
     *  @param string name
     *  @param string value
     *  @return bool
     */

    protected static function is_selected($name, $value)
    {
        return self::has($name, $value);
    }

    /**
     *  Generowanie input[type=hidden]. Parametry dostępne poprzez tablicę
     *  Wywołanie: Core\Form::hidden(['param1' => 'wartosc'])
     *
     *  @param string id
     *  @param string name
     *  @param string value
     *  @param array data
     */

    public static function hidden( $o, $value = null )
    {
        if( !empty( $o ) && is_array( $o )) {
            return '<input type="hidden"' .
                (!empty($o['id']) ? ' id="' . $o['id'] . '"' : '') .
                (!empty($o['name']) ? ' name="' . $o['name'] . '"' : '') .
                (!empty($o['value']) ? ' value="' . $o['value'] . '"' : '') .
                (!empty($o['data']) ? self::build_data($o['data']) : '') . '/>';
        }

        if( !empty( $o ) && is_string( $o )) {
            return '<input type="hidden" id="' . $o . '" name="' . $o . '" value="' . (!empty( $value ) ? $value : self::getValue( $o )) . '" ' .
            (!empty($o['data']) ? self::build_data($o['data']) : '') . '/>';
        }
    }

    /**
     *  Generowanie label. Parametry dostępne poprzez tablicę
     *  Wywołanie: Core\Form::label(['param1' => 'wartosc'])
     *
     *  @param string for
     *  @param string label
     *  @param string class
     *  @param string tooltip
     *  @param string data
     *  @param array data
     */

    public static function label($o)
    {
        return '<label' .
            (!empty($o['for']) ? ' for="' . $o['for'] . '"' : '') .
            (!empty($o['class']) ? ' class="' . $o['class'] . '"' : '') .
            (!empty($o['tooltip']) ? ' data-toggle="tooltip" title="' . $o['tooltip'] . '"' : '') .
            (!empty($o['data']) ? self::build_data($o['data']) : '') . '>' . $o['label'] . $o['label_addon'] . '</label>';
    }

    /**
     *  Generowanie switcha. Parametry dostępne poprzez tablicę
     *  Wywołanie: Core\Form::input(['param1' => 'wartosc'])
     *
     *  @param string type (text,number)
     *  @param string id
     *  @param string name
     *  @param string value
     *  @param string class
     *  @param string placeholder
     *  @param bool autocomplete
     *  @param int min
     *  @param int max
     *  @param int maxlength
     *  @param bool required
     *  @param bool readonly
     *  @param bool disabled
     *  @param array data
     */

    public static function input($o)
    {
        if ($o['type'] != "file"){
            $o['value'] = (empty( $o['value'] ) ? self::getValue( $o['name'] ) : $o['value']);

            if(empty( $o['value'] ) && !empty( $o['default'] )) {
                $o['value'] = $o['default'];
            }
        }
        $html[] = '<input type="' . $o['type'] . '"' .
            (!empty($o['id']) ? ' id="' . $o['id'] . '"' : '') .
            (!empty($o['name']) ? ' name="' . $o['name'] . '"' : '') .
            (!empty($o['value']) ? ' value="' . $o['value'] . '"' : '') .
            (!empty($o['class']) ? ' class="' . $o['class'] . '"' : ' class="' . self::$default_input_class . '"') .
            (!empty($o['placeholder']) ? ' placeholder="' . $o['placeholder'] . '"' : '') .
            (!empty($o['autocomplete']) ? ' autocomplete="' . $o['autocomplete'] . '"' : '') .
            (!empty($o['min']) ? ' min="' . $o['min'] . '"' : '') .
            (!empty($o['max']) ? ' max="' . $o['max'] . '"' : '') .
            (!empty($o['maxlength']) ? ' maxlength="' . $o['maxlength'] . '"' : '') .
            (!empty($o['step']) ? ' step="' . $o['step'] . '"' : '') .
            (!empty($o['pattern']) ? ' pattern="' . $o['pattern'] . '"' : '') .
            (!empty($o['title']) ? ' title="' . $o['title'] . '"' : '') .
            (!empty($o['required']) ? ' required' : '') .
            (!empty($o['readonly']) ? ' readonly' : '') .
            (!empty($o['disabled']) ? ' disabled' : '') .
            (!empty($o['multiple']) ? ' multiple' : '') .
            (!empty($o['accept']) ? ' accept="' . (is_array($o['accept']) ? implode(",",$o['accept']) : $o['accept']) . '"' : '') .
            (!empty($o['data']) ? self::build_data($o['data']) : '') . '/>';

        return implode($html);
    }

    /**
     *  Alias dla Core\Form::input z parametrem text
     *  Wywołanie: Core\Form::line(['param1' => 'wartosc'])
     */

    public static function line($o)
    {
        return self::input(array_merge(['type' => 'text'], $o));
    }

    /**
     *  Alias dla Core\Form::input z parametrem number
     *  Wywołanie: Core\Form::number(['param1' => 'wartosc'])
     */

    public static function number($o)
    {
        return self::input(array_merge(['type' => 'number'], $o));
    }

    /**
     *  Alias dla Core\Form::input z parametrem upload
     *  Wywołanie: Core\Form::upload(['param1' => 'wartosc'])
     */

    public static function upload($o)
    {
        return self::input(array_merge(['type' => 'file'], $o));
    }

    /**
     *  Generowanie textarea. Parametry dostępne poprzez tablicę
     *  Wywołanie: Core\Form::input(['param1' => 'wartosc'])
     *
     *  @param string id
     *  @param string name
     *  @param string value
     *  @param string class
     *  @param string placeholder
     *  @param int cols
     *  @param int rows
     *  @param int maxlength
     *  @param bool required
     *  @param bool readonly
     *  @param bool disabled
     *  @param array data
     */

    public static function textarea($o)
    {
        $o['value'] = (empty( $o['value'] ) ? self::getValue( $o['name'] ) : $o['value']);

        $html[] = '<textarea ' .
            (!empty($o['id']) ? ' id="' . $o['id'] . '"' : '') .
            (!empty($o['name']) ? ' name="' . $o['name'] . '"' : '') .
            (!empty($o['class']) ? ' class="' . $o['class'] . '"' : ' class="' . self::$default_textarea_class . '"') .
            (!empty($o['placeholder']) ? ' placeholder="' . $o['placeholder'] . '"' : '') .
            (!empty($o['cols']) ? ' cols="' . $o['cols'] . '"' : '') .
            (!empty($o['rows']) ? ' rows="' . $o['rows'] . '"' : '') .
            (!empty($o['maxlength']) ? ' maxlength="' . $o['maxlength'] . '"' : '') .
            (!empty($o['required']) ? ' required' : '') .
            (!empty($o['readonly']) ? ' readonly' : '') .
            (!empty($o['disabled']) ? ' disabled' : '') .
            (!empty($o['data']) ? self::build_data($o['data']) : '') . '>' . (!empty($o['value']) ? $o['value'] : '') . '</textarea>';

        return implode($html);
    }

    /**
     *  Aliast dla Core\Form::textarea
     *  Wywołanie: Core\Form::text(['param1' => 'wartosc'])
     */

    public static function text($o)
    {
        return self::textarea($o);
    }

    /**
     *  Generowanie select. Parametry dostępne poprzez tablicę
     *  Wywołanie: Core\Form::select(['param1' => 'wartosc'])
     *
     *  @param string id
     *  @param string name
     *  @param string options [id,name,selected,data]
     *  @param string class
     *  @param bool required
     *  @param bool readonly
     *  @param bool disabled
     *  @param array data
     */

    public static function select($o)
    {
        $html[] = '<select ' .
            (!empty($o['id']) ? ' id="' . $o['id'] . '"' : '') .
            (!empty($o['name']) ? ' name="' . $o['name'] . '"' : '') .
            (!empty($o['class']) ? ' class="' . $o['class'] . '"' : ' class="' . self::$default_select_class . '"') .
            (!empty($o['required']) ? ' required' : '') .
            (!empty($o['readonly']) ? ' readonly' : '') .
            (!empty($o['disabled']) ? ' disabled' : '') .
            (!empty($o['multiple']) ? ' multiple' : '') .
            (!empty($o['placeholder']) ? ' placeholder="' . $o['placeholder'] . '"' : '') .
            (!empty($o['data']) ? self::build_data($o['data']) : '') . '>';

        if (!empty($o['empty'])) {
            $html[] = '<option' .
                (isset($o['empty']['value']) ? ' value="' . $o['empty']['value'] . '"' : '') . '>' . $o['empty']['name'] . '</option>';
        }

        if (!empty($o['options'])) {
            foreach ($o['options'] as $option) {
                if (empty($option['selected'])) {
                    $option['selected'] = self::is_selected($o['name'], $option['id']);
                }
                $html[] = '<option' .
                    (isset($option['id']) ? ' value="' . $option['id'] . '"' : '') .
                    (!empty($option['subtext']) ? ' data-subtext="' . $option['subtext'] . '"' : '') .
                    (!empty($option['selected']) ? ' selected' : '') .
                    (!empty($option['data']) ? self::build_data($option['data']) : '') . '>' . $option['name'] . '</option>';
            }
        }

        $html[] = '</select>';

        return implode($html);
    }

    /**
     *  Generowanie checkboxa. Parametry dostępne poprzez tablicę
     *  Wywołanie: Core\Form::checkbox(['param1' => 'wartosc'])
     *
     *  @param string id
     *  @param string name
     *  @param string value
     *  @param string class
     *  @param bool required
     *  @param bool readonly
     *  @param bool disabled
     *  @param bool checked
     *  @param array data
     */

    public static function checkbox($o, $v = null)
    {
        if(!is_array( $o ) && is_string( $o ) && !empty( $v )) {
            $name = $o;

            $o = [
                'name' => $name,
                'id' => $name,
                'value' => $v
            ];
        }

        if (empty($o['checked'])) {
            $o['checked'] = self::is_checked($o['name'], $o['value']);
        }

        if(!empty( $o['schema'] ) && $o['schema'] == 'bootstrap4' ) {
            $html[] = '<div class="form-check">';
            $o['class'] = 'form-check-input';
        }

        if(!empty( $o['schema'] ) && $o['schema'] == 'bootstrap4custom' ) {
            $html[] = '<div class="custom-control custom-checkbox">';
            $o['class'] = 'form-check-input';
            $o['label']['class'] = 'form-check-label';
        }

        if(empty( $o['schema'] )) {
            if (!empty($o['label'])) {
                $html[] = '<label for="' . $o['id'] . '"' . (!empty( $o['label']['class'] ) ? ' class="' . $o['label']['class'] . '"' : '') . '>';
            }
        }

        $html[] = '<input type="checkbox"' .
            (!empty($o['id']) ? ' id="' . $o['id'] . '"' : '') .
            (!empty($o['name']) ? ' name="' . $o['name'] . '"' : '') .
            (!empty($o['value']) ? ' value="' . $o['value'] . '"' : '') .
            (!empty($o['class']) ? ' class="' . $o['class'] . '"' : ' class="' . self::$default_checkbox_class . '"') .
            (!empty($o['required']) ? ' required' : '') .
            (!empty($o['readonly']) ? ' readonly' : '') .
            (!empty($o['disabled']) ? ' disabled' : '') .
            (!empty($o['checked']) ? ' checked' : '') .
            (!empty($o['data']) ? self::build_data($o['data']) : '') . '/>';

        if(!empty( $o['schema'] ) && $o['schema'] == 'bootstrap4' ) {
            if (!empty($o['label'])) {
                $html[] = '<label class="form-check-label" for="' . $o['id'] . '">';
                $html[] = $o['label']['text'] . '</label>';
            }
            $html[] = '</div>';
        }

        if(!empty( $o['schema'] ) && $o['schema'] == 'bootstrap4custom' ) {
            if (!empty($o['label'])) {
                $html[] = '<label class="' . $o['label']['class'] . '" for="' . $o['id'] . '">';
                $html[] = $o['label']['text'] . '</label>';
            }
            $html[] = '</div>';
        }

        if(empty( $o['schema'] )) {
            if (!empty($o['label'])) {
                $html[] = $o['label']['text'] . '</label>';
            }
        }

        return implode($html);
    }

    /**
     *  Generowanie switcha (alias dla checkboxa). Parametry dostępne poprzez tablicę
     *  Wywołanie: Core\Form::switch(['param1' => 'wartosc'])
     *
     *  @param string id
     *  @param string name
     *  @param string value
     *  @param string class
     *  @param bool required
     *  @param bool readonly
     *  @param bool disabled
     *  @param bool checked
     *  @param array data
     */

    public static function switch($o)
    {
        $o['class'] = 'bt_checkbox' . (!empty($o['class']) ? ' class="' . $o['class'] . '"' : ' class="' . self::$default_textarea_class . '"');
        return self::checkbox($o);
    }

    /**
     *  Generowanie radio buttona. Parametry dostępne poprzez tablicę
     *  Wywołanie: Core\Form::radio(['param1' => 'wartosc'])
     *
     *  @param string id
     *  @param string name
     *  @param string value
     *  @param string class
     *  @param bool required
     *  @param bool readonly
     *  @param bool disabled
     *  @param bool checked
     *  @param array data
     */

    public static function radio($o)
    {
        if (empty($o['checked'])) {
            $o['checked'] = self::is_checked($o['name'], $o['value']);
        }

        if (!empty($o['label']) && !empty($o['label']['outside']))  {
            $html[] = '<label for="' . $o['id'] . '">';
        }

        $html[] = '<input type="radio"' .
            (!empty($o['id']) ? ' id="' . $o['id'] . '"' : '') .
            (!empty($o['name']) ? ' name="' . $o['name'] . '"' : '') .
            (!empty($o['value']) ? ' value="' . $o['value'] . '"' : '') .
            (!empty($o['class']) ? ' class="' . $o['class'] . '"' : ' class="' . self::$default_radio_class . '"') .
            (!empty($o['required']) ? ' required' : '') .
            (!empty($o['readonly']) ? ' readonly' : '') .
            (!empty($o['disabled']) ? ' disabled' : '') .
            (!empty($o['checked']) ? ' checked' : '') .
            (!empty($o['data']) ? self::build_data($o['data']) : '') . '/>';

        if (!empty($o['label']) && !empty($o['label']['outside']))  {
            $html[] = $o['label']['text'] . '</label>';
        }

        if (!empty($o['label']) && empty($o['label']['outside']))  {
            $html[] = '<label for="' . $o['id'] . '"' . (!empty( $o['label']['class'] ) ? ' class="' . $o['label']['class'] . '"' : '') . '>' . $o['label']['text'] . '</label>';
        }

        return implode($html);
    }

}
