<?php namespace Core;

class Api
{
    private static $gzip = false;
    private static $returnAsJson = false;

    public static function json()
    {
        self::$returnAsJson = true;
        return new self;
    }

    public static function gzip()
    {
        self::$gzip = true;
        return new self;
    }

/**
 * Zwraca randomowy token
 * @param int $length określa ilość znaków
 * @return string
 */

    public static function randomToken( int $length = 16 ): string
    {
        return bin2hex(openssl_random_pseudo_bytes( $length ));
    }
/**
 * Zwraca poprawny result z kodem HTTP 201
 * @param string $message
 * @param array $additional (optional)
 * @return array|string
 */
    public static function success( string $message, array $additional = null )
    {
        http_response_code(201);
        $success = ['message' => $message];
        if(!empty( $additional )) {
            $success = array_merge( $success, $additional );
        }

        if(!empty( self::$returnAsJson )) {
            return json_encode(array(
                'success' => $success
            ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        if(!empty( self::$gzip )) {
            header('Content-Encoding: gzip');
            return gzencode(json_encode(['success' => $success]));
        }

        return ['success' => $success];
    }

/**
 * Zwraca błędny result z kodem HTTP 400
 * @param string $message
 * @param array $additional (optional)
 * @return array|string
 */
    public static function error( string $message, array $additional = null )
    {
        http_response_code(400);
        $error = ['message' => $message];
        if(!empty( $additional )) {
            $error = array_merge( $error, $additional );
        }

        if(!empty( self::$returnAsJson )) {
            return json_encode([
                'error' => $error], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );
        }

        return ['error' => $error];
    }

/**
 *  Weryfikacja authentyfikacji użytkownika na podstawie przesłanego przez HTTP_X_AUTHORIZATION
 *  Access Tokena
 *  @param string $user_token
 *  @return boolean
 */

    public static function ValidateAuthorization( string $user_token ): bool
    {
        global $request;
        if( empty( $request->server['HTTP_X_AUTHORIZATION'] )) {
            return false;
        }

        $access_token = $request->server['HTTP_X_AUTHORIZATION'];

        if( $user_token !== $access_token ) {
            return false;
        }

        return true;
    }
}
