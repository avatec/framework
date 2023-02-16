<?php namespace Core;

use Core\Exceptions\ApiExceptions;

class ApiBase 
{
    private $authToken;
    private $siteAuthToken;

    public function __construct( string $authToken )
    {
        $this->authToken = $authToken;
        if (isset($_SERVER['HTTP_SITEAUTH'])) {
            $this->siteAuthToken = $_SERVER['HTTP_SITEAUTH'];
        }
    }

    public function validateAuth()
    {
        print_r($_SERVER);
        if( $this->authToken !== $this->siteAuthToken ) {
            throw new ApiExceptions('Invalid auth token provided' , 401);
        }
    }
}