<?php

namespace Core;

use \Exception;

/**
 *	Klasa obsługi funkcji na datach
 *  @author Grzegorz Miśkiewicz <biuro@avatec.pl>
 *  @version 1.8
 *  @copyright Avatec.pl
 */

class Date
{
    public static $date;

    protected static $_instance;
    protected static $_months = [
        1 => [ "Styczeń", "Sty", "Stycznia" ],
        2 => [ "Luty", "Lut", "Lutego" ],
        3 => [ "Marzec", "Mar", "Marca" ],
        4 => [ "Kwiecień", "Kwi", "Kwietnia" ],
        5 => [ "Maj", "Maj", "Maja" ],
        6 => [ "Czerwiec", "Cze", "Czerwca" ],
        7 => [ "Lipiec", "Lip", "Lipca" ],
        8 => [ "Sierpień", "Sie", "Sierpnia" ],
        9 => [ "Wrzesień", "Wrz", "Września" ],
        10 => [ "Październik", "Paź" , "Października"],
        11 => [ "Listopad", "Lis", "Listopada" ],
        12 => [ "Grudzień", "Gru", "Grudnia" ]
    ];
    protected static $_weeks = [
        1 => [ "Poniedziałek", "Pon", "Pn" ],
        2 => [ "Wtorek", "Wto" , "Wt" ],
        3 => [ "Środa", "Śro" , "Śr" ],
        4 => [ "Czwartek", "Czw" , "Cz" ],
        5 => [ "Piątek", "Pią" , "Pt" ],
        6 => [ "Sobota", "Sob" , "So" ],
        7 => [ "Niedziela", "Nie" , "Nd" ]
    ];

    public static function init()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

/**
 *  Ustawienie daty początkowej
 *  @param string $date
 *  @return instance
 */

    public static function set( $date )
    {
        self::init();

        self::$date = $date;

        return self::$_instance;
    }

/**
 *  Dodanie dni do daty
 *  @param int $days
 *  @return instance
 */

    public static function addDays( $days )
    {
        self::init();

        self::$date = date('Y-m-d' , strtotime( self::$date . ' +' . $days . ' DAYS'));

        return self::$_instance;
    }

/**
 * [public description]
 * @var [type]
 */
    public static $workingDaysArray;

/**
 * [addWorkingDays description]
 * @param int $days
 */
    public static function addWorkingDays( int $days )
    {
        self::init();

        for( $i=0; $i<=$days; $i++ ) {
            $checkDate = date('Y-m-d' , strtotime(self::$date. ' +' . $i . ' DAYS'));
            if( self::isWorkingDay( $checkDate ) == false ) {
                $days += 1;
                //self::addWorkingDays( $days );
            } else {
                self::$workingDaysArray[] = $checkDate;
            }
        }

        return self::$_instance;
    }

/**
 * [getWorkingDates description]
 * @return [type]
 */
    public static function getWorkingDates()
    {
        if(!empty( self::$workingDaysArray )) {
            return self::$workingDaysArray;
        }
    }

/**
 * [get description]
 * @return [type]
 */
    public static function get()
    {
        return self::$date;
    }

/**
 * [public description]
 * @var [type]
 */
    public static $freedays = ['01-01', '01-06','05-01','05-03','08-15','11-01','11-11','12-25','12-26'];

/**
 * [isWorkingDay description]
 * @param  string $date
 * @return bool
 */
    public static function isWorkingDay( string $date )
    {
        $time = strtotime($date);
        $dayOfWeek = (int)date('w',$time);
        $year = (int)date('Y',$time);

        // sprawdzenie czy to nie weekend
        if( $dayOfWeek==6 || $dayOfWeek==0 ) {
            return false;
        }

        // lista swiat stalych
        $holiday = self::$freedays;

        // Lista świąt ruchomych
        // wialkanoc
        $easter = date('m-d', easter_date( $year ));
        // poniedzialek wielkanocny
        $easterSec = date('m-d', strtotime('+1 day', strtotime( $year . '-' . $easter) ));
        // boze cialo
        $cc = date('m-d', strtotime('+60 days', strtotime( $year . '-' . $easter) ));
        // Zesłanie Ducha Świętego
        $p = date('m-d', strtotime('+49 days', strtotime( $year . '-' . $easter) ));

        $holiday[] = $easter;
        $holiday[] = $easterSec;
        $holiday[] = $cc;
        $holiday[] = $p;

        $md = date('m-d',strtotime($date));
        if(in_array($md, $holiday)) return false;

        return true;
    }

/**
 * [getMonth description]
 * @param  int $number
 * @param  int    $short_level
 * @return [type]
 */
    public static function getMonth( int $number = null, int $short_level = 0 )
    {
        if( $number <= 0 || $number > 12 ) {
            throw Exception('Core\Date::getMonth require month number between 1 and 12');
            exit;
        }
        if (is_null($number)) {
            foreach (self::$_months as $mn => $ma) {
                $s[] = [
                    'id' => $mn,
                    'name' => $ma[$short_level]
                ];
            }

            if (!empty($s)) {
                return $s;
            }

            return;
        }

        foreach (self::$_months as $mn => $ma) {
            if ($mn == $number) {
                return $ma[$short_level];
            }
        }
    }

    public static function formatDate( $date, $short_level = 2, $func = null )
    {
        $d = [
            'year' 	=> date('Y', strtotime($date)),
            'month' => date('m', strtotime($date)),
            'day' 	=> date('j', strtotime($date))
        ];

        if(!empty( $func )) {
            return $d['day'] . ' ' . call_user_func( $func, self::getMonth($d['month'], $short_level)) . ' ' . $d['year'];
        }

        return $d['day'] . ' ' . self::getMonth($d['month'], $short_level) . ' ' . $d['year'];
    }

/**
 * [countDays description]
 * @param  string $end
 * @param  string $start (optional / default today)
 * @return float
 */
    public static function countDays( string $end, string $start = null ): float
    {
        if(empty( $start )) {
            $start = date('Y-m-d H:i:s');
        }

        $diff = strtotime($end) - strtotime($start);
        return (float) ($diff / 86400);
    }

    public static function dayName( int $dayNumber ): string
    {
        return self::$_weeks[$dayNumber];
    }
}
