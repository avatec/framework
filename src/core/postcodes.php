<?php namespace Core;

class Postcodes
{
    public static function getCity( $postcode )
    {
        if( strlen( $postcode ) == 6 ) {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'http://kodpocztowy.intami.pl/api/' . $postcode);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


            $headers = array();
            $headers[] = 'Accept: application/json';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result = curl_exec($ch);
            if (curl_errno($ch) !== 0) {
                echo 'Error:' . curl_error($ch);
            }

            curl_close($ch);

            return json_decode( $result );
        }

        return;
    }
}
