<?php namespace Core;

class Api
{
/**
 * Zwraca błędny result z kodem HTTP 400
 * @param string $message
 * @param array $additional (optional)
 * @return array
 */
    public static function error( string $message, array $additional = null ): array
    {
        http_response_code(400);
        return ['error' => ['message' => $message, $additional]];
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
