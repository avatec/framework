<?php namespace Core;

use HTMLPurifier;

class Common
{
    public static $_onoff = [
		['id' => 0, 'name' => 'wyłączone', 'label' => 'danger'],
		['id' => 1, 'name' => 'włączone', 'label' => 'success']
	];

	// Aktywność (int) id
	public static $_activity = [
		['id' => 0, 'name' => 'nieaktywny', 'label' => 'warning'],
		['id' => 1, 'name' => 'aktywny', 'label' => 'success']
	];

    public static $_visibility = [
        ['id' => 0, 'name' => 'niewidoczne', 'label' => 'danger'],
        ['id' => 1, 'name' => 'widoczne', 'label' => 'success']
    ];

    public static $_truefalse = [
        ['id' => 0, 'name' => 'nie', 'label' => 'danger'],
        ['id' => 1, 'name' => 'tak', 'label' => 'success']
    ];

    public static $_status = [
        ['id' => 0, 'name' => 'wyłączone', 'label' => 'danger', 'tooltip' => 'Pozycja została wyłączona i nie będzie dostępna'],
        ['id' => 1, 'name' => 'włączone', 'label' => 'success', 'tooltip' => 'Pozycja jest włączona i dostępna']
    ];

    public static $_metaindex = [
        ['id' => 0, 'name' => 'noindex', 'label' => 'danger', 'tooltip' => 'Indeksowanie zostało wyłączone'],
        ['id' => 1, 'name' => 'index', 'label' => 'success', 'tooltip' => 'Indeksowanie jest włączone']
    ];

    public static $_metafollow = [
        ['id' => 0, 'name' => 'nofollow', 'label' => 'danger', 'tooltip' => 'Podążanie za linkami zostało wyłączone'],
        ['id' => 1, 'name' => 'follow', 'label' => 'success', 'tooltip' => 'Podążanie za linkami jest włączone']
    ];

/**
 *  Odczytywanie domyślnych wartości
 *  @param string $id
 *  @param array $array
 *  @param bool $label
 *  @return string
 */

    public static function read( $id, $array, $label = 'primary' )
	{
        if( !empty( $array ) && !is_array( $array )) {
            $data = get_class_vars( get_class(  ) );
            $array = $data['_' . $array];
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

        $purifier = new HTMLPurifier($htmlconfig);
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

    public static function getCountry( string $code2 )
    {
        global $app_path;
        $cl = file_get_contents($app_path . 'include/json/eu-' . Language::get_selected() . '.json');
        $cl = json_decode( $cl, true );
        if(!empty( $cl )) {
            foreach( $cl as $code=>$name ) {
                if( $code == $code2) {
                    return $name;
                }

                if( $code2 == $name ) {
                    return $code;
                }
            }
        }
    }

/**
 * Konwertuje wartość float i zwraca wartość walutową
 * np. 199.99 zwraca 199,99
 * @return string
 */
    public static function currency( float $amount ): string
    {
        return str_replace(".", ",", sprintf("%2.2f", $amount));
    }

/**
 * Zwraca mime dla ikon (png, gif, svg)
 * @return array
 */
    public static function getMimeForIcons(): array
    {
        return [
            'image/png', 'image/gif', 'image/svg+xml'
        ];
    }
}
