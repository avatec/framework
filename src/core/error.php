<?php namespace Core;

class Error
{
    public static $json = false;

	public static $errors = array();
	public static function show( $title, $text )
	{
        if( self::$json == true ) {
            http_response_code(500);
            header('Content-Type: application/json; charset=utf-8');
            die( json_encode( ['error' => true, 'msg' => $title . ': ' . $text]) );
        }

        @ob_end_clean();
        http_response_code(500);

        $html[] = '<!doctype html><html><head><meta charset="utf-8">';
        $html[] = '<title>Avatec Framework - Wystąpił nieoczekiwany błąd !!</title>';
        $html[] = '<link rel="stylesheet" type="text/css" href="/include/assets/css/error.css" />';
        $html[] = '</head><body>';
        $html[] = '<div class="error-container"><div class="error-page">';
        $html[] = '<h1>' . $title . '</h1>';
        $html[] = '<h5>' . $text . '</h5>';
        $html[] = '</div></div></body></html>';

        echo implode( $html );
		exit;
	}

	public function CustomErrorHandler( $errno, $errstr, $errfile, $errline )
    {
        if (!(error_reporting() & $errno)) {
            return;
        }

        switch ($errno) {

            case E_ERROR:
                if (self::$json == true) {
                    return ['error' => true, 'msg' => "E_ERROR: {$errno} $errstr"];
                }
                echo "<b>My ERROR</b> [$errno] $errstr<br />\n";
                echo "  Fatal error on line $errline in file $errfile";
                echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
                echo "Aborting...<br />\n";
                exit(1);
                break;

            case E_WARNING:
                Core\Error::show("WARNING - [$errno] $errstr", "on line $errline in file $errfile<br />\n");
                break;

            case E_NOTICE:
                Core\Error::show("NOTICE [$errno] $errstr", "Error on line $errline in file $errfile<br/>\n");
                break;

            case E_USER_WARNING:
                Core\Error::show("FRAMEWORK WARNING [#$errno]", "<b>" . $errstr . "</b><br/>Error on line $errline in file $errfile<br/>\n");
                break;

            default:
                Core\Error::show("Unknown error type", "<b>Error #$errno</b><br/><br/>$errstr<br /><br/><em>Found in file ".__FILE__." on line ".__LINE__."</em>\n");
                break;
        }

        /* Don't execute PHP internal error handler */
        return true;
    }
}
