<?php namespace Core;

class Api
{
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
