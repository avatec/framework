<?php namespace Core;

class Common
{
    public static $onoff = [
		['id' => 0, 'name' => 'wyłączone', 'label' => 'danger'],
		['id' => 1, 'name' => 'włączone', 'label' => 'success']
	];

    public static function getOnOff()
    {
        return self::$onoff;
    }

	public static $truefalse = [
		['id' => 0, 'name' => 'nie', 'label' => 'danger'],
		['id' => 1, 'name' => 'tak', 'label' => 'success']
	];

    public static function getTrueFalse()
    {
        return self::$truefalse;
    }

	// Widoczność (int) id
	public static $visibility = [
		['id' => 0, 'name' => '<span class="fa fa-times"></span> ukryte', 'label' => 'danger'],
		['id' => 1, 'name' => '<span class="fa fa-check"></span> widoczne', 'label' => 'success']
	];

    public static function getVisibility()
    {
        return self::$visibility;
    }

	// Aktywność (int) id
	public static $activity = [
		['id' => 0, 'name' => 'nieaktywny', 'label' => 'warning'],
		['id' => 1, 'name' => 'aktywny', 'label' => 'success']
	];

    public static function getActivity()
    {
        return self::$activity;
    }

/**
 *  Odczytywanie domyślnych wartości
 *  @param string $id
 *  @param array $array
 *  @param bool $label
 *  @return string
 */

    public static function read( $id, $array, $label = 'primary' )
	{
        if(is_string( $array )) {
            $array = 'get' . ucfirst( $array );
            $data = self::{$array}();
        }

        if(is_array( $array )) {
            $data = $array;
        }

		foreach( $data as $i ) {
			if( $id == $i['id'] ) {
				if( $label == false ) {
					return $i['name'];
				}

				return '<span class="label label-' . $i['label'] . '">' . $i['name'] . '</span>';
			}
		}
	}

    public static function get_ip()
    {
        if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $onlineip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $onlineip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $onlineip = getenv('REMOTE_ADDR');
        } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $onlineip = $_SERVER['REMOTE_ADDR'];
        }
        return (!empty( $onlineip ) ? $onlineip : null);
    }

    public static function random_string(int $limit = 5, bool $only_big_letters = false)
    {
        if ($only_big_letters == true) {
            $pool = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        } else {
            $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
        return substr(str_shuffle(str_repeat($pool, $limit)), 0, $limit);
    }

    public static function tooltip( string $text ): string
    {
        return 'data-toggle="tooltip" data-title="' . $text . '" data-container="body"';
    }

    public static function truncate( string $string, int $width, string $on = '[break]'): string
    {
        if (strlen($string) > $width && false !== ($p = strpos(wordwrap($string, $width, $on), $on))) {
            $string = sprintf('%.'. $p . 's', $string);
        }
        return $string;
    }

    public static function purifyHTML( string $text ): string
    {
        $htmlconfig = \HTMLPurifier_Config::createDefault();

        $htmlconfig->set('AutoFormat.RemoveSpansWithoutAttributes', false);
        $htmlconfig->set('Attr.AllowedFrameTargets', ['_blank']);
        $htmlconfig->set('CSS.AllowedProperties', ['text-decoration','font-weight','font-style','background-color','background','color','width']);
        $htmlconfig->set('URI.AllowedSchemes' , array('data' => true, 'http' => true, 'https' => true ));
        $htmlconfig->set('Core.Encoding', 'UTF-8'); // replace with your encoding
        $htmlconfig->set('HTML.Allowed', 'p[style],ul,ol,li,strong,b,em,i,u,a[href|target],br,hr,span[style],img[style|src|alt]');
        $htmlconfig->set('HTML.Doctype', 'HTML 4.01 Transitional'); // replace with your doctype
        $htmlconfig->set('HTML.TidyLevel', 'heavy'); // burn baby burn!
        $htmlconfig->set('AutoFormat.RemoveEmpty', true); // remove empty tag pairs
        $htmlconfig->set('AutoFormat.RemoveEmpty.RemoveNbsp', true); // remove empty, even if it contains an &nbsp;
        $htmlconfig->set('AutoFormat.AutoParagraph', true); // remove empty tag pairs

        $purifier = new \HTMLPurifier($htmlconfig);
        $text = $purifier->purify($text);

        return $text;
    }

    public static function parseYoutubeLink( string $source ): string
    {
        $link = 'https://www.youtube.com/embed/';

        if (self::strpos_array($source, ['youtube','youtu.be']) == true) {
            parse_str(parse_url($source, PHP_URL_QUERY), $r);

            if (isset($r['v'])) {
                return $link . $r['v'];
            }
            if (isset($r['amp;v'])) {
                return $link . $r['amp;v'];
            }

            if (isset($r['vamp;'])) {
                return $link . $r['vamp;'];
            }
        }

        return '';
    }

/**
 * Sprawdza, czy w tablicy istnieje podany string
 * @param  string $string
 * @param  array  $array
 * @return bool
 */
    public static function strpos_array( string $string, array $array ): bool
    {
        if (empty($string)) {
            die('Common::strpos_array => string param is missing');
        }

        if (empty($array)) {
            die('Common::strpos_array => array param is missing');
        } else {
            if (!is_array($array)) {
                die('Common::strpos_array => array param should be an array');
            }
        }

        foreach ($array as $i) {
            if (strpos($string, $i) == true) {
                return true;
            }
        }

        return false;
    }
}
