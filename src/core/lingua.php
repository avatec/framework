<?php namespace Core;

class Lingua
{
    // Słownik odmian przez liczebniki
    protected static $dictionary = [
        'miesiac' => [
            'miesiąc', 'miesięcy', 'miesiące'
        ]
    ];

    // Obsługa opmian rzeczowników przez liczebniki
    public static function variety( string $string, int $number )
    {
        if( $string == 'miesiac' ) {
            if( $number == 1 ) {
                return self::$dictionary[$string][0];
            }

            if( in_array(substr($number, -1), [5,6,7,8,9]) || in_array(substr($number, -2), [10,11,12,13,14,15,16,17,18,19,20,21] )) {
                return self::$dictionary[$string][1];
            }

            if( in_array(substr($number, -1), [2,3,4] )) {
                return self::$dictionary[$string][2];
            }
        }
    }
}
