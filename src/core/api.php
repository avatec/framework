<?php namespace Core;

class Api
{
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
 * Zwraca błędny result z kodem HTTP 400
 * @param string $message
 * @param array $additional (optional)
 * @return array
 */
    public static function error( string $message, array $additional = null ): array
    {
        http_response_code(400);
        $error = ['message' => $message];
        if(!empty( $additional )) {
            array_merge( $error, $additional );
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
