<?php

namespace Core;

use Exception;

class Request
{
    public $get = array();
    public $post = array();
    public $any = array();
    public $cookie = array();
    public $files = array();
    public $server = array();
    public $input = array();

    public function __construct()
    {
        $this->get = $this->clean($_GET);
        $this->post = $this->clean($_POST);
        $this->any = $this->clean($_REQUEST);
        $this->cookie = $this->clean($_COOKIE);
        $this->files = $this->clean($_FILES);
        $this->server = $this->clean($_SERVER);

        $this->input = file_get_contents('php://input');
        $input_src = $this->input;
        $this->input = json_decode( $this->input, true );
        if( json_last_error() ) {
            $this->input = json_encode([
                'error' => true,
                'msg' => json_last_error_msg(),
                'response' => $input_src
            ]);
        }
    }

    public function clean( $data )
    {
        if(empty( $data )) {
            return [];
        }
        
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if(!empty( $data[$key] ) && is_array( $data[$key] ) && !empty( $key )) {
                    $data[$this->clean($key)] = $this->clean($value);
                }
            }
        } else {
            $data = htmlspecialchars($data, ENT_COMPAT, 'UTF-8');
            $data = htmlspecialchars_decode( $data );
        }

        return $data;
    }

    public static function redirect( string $url, int $code = 301 )
    {
        if (!is_null( $code )) {
            header("HTTP/1.1 {$code} See Other");
        }

        header("Location: " . $url);
        exit;
    }

    public static function rewrite( string $text ): string
    {
        $string = strtolower($text);
        $polskie = array(',', ' - ',' ','ę', 'Ę', 'ó', 'Ó', 'Ą', 'ą', 'Ś', 's', 'ł', 'Ł', 'ż', 'Ż', 'Ź', 'ź', 'ć', 'Ć', 'ń', 'Ń','-',"'","/","?", '"', ":", 'ś', '!','.', '&', '&amp;', '#', ';', '[',']','domena.pl', '(', ')', '`', '%', '”', '„', '…');
        $miedzyn = array('-','-','-','e', 'e', 'o', 'o', 'a', 'a', 's', 's', 'l', 'l', 'z', 'z', 'z', 'z', 'c', 'c', 'n', 'n','-',"","","","","",'s','','', '', '', '', '', '', '', '', '', '', '', '', '');
        $string = str_replace($polskie, $miedzyn, $string);

        $string = preg_replace('/[\-]+/', '-', $string);
        $string = trim($string, '-');
        $string = stripslashes($string);
        $string = urlencode($string);

        $encoded = array(
            "%E4%98","%E4%99","%E3%B3","%E3%93","%E4%85","%E4%84",
            "%E5%9B","%E5%9A","%E5%82","%E5%81","%E5%BE","%E5%BB",
            "%E5%BA","%E5%B9","%E4%87","%E4%86","%E5%84","%E5%83",
            "%C3%BC","%C3%9C","%C3%B6","%C3%96","%C3%A4","%C3%84",
            "%C3%9F","%C3%9F"
        );
        $new = array(
            "e","e","o","o","a","a",
            "s","s","l","l","z","z",
            "z","z","c","c","n","n",
            "u","U","o","O","a","A",
            "ss","ss" 
        );

        $string = str_replace($encoded, $new, $string);

        if (strlen($string > 50)) {
            return substr($string, 0, 50);
        }

        return $string;
    }

/**
 * Function to call API
 * @param string $url
 * @param string $method (GET|POST)
 * @param array $data
 * @param string $authToken
 * @return array
 */
    public static function send( string $url, string $method = 'POST', array $data = [], string $authToken = '')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $headers = [
            'Content-Type: multipart/form-data',
            'Auth: ' . $authToken
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        $result = curl_exec($ch);
        if ($result === false) {
            throw new Exception(curl_error($ch));
        }

        curl_close($ch);
        if( is_string( $result )) {
            return json_decode( $result );
        }

        return (array) $result;
    }
}
