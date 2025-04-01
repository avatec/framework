<?php
namespace Helpers;

use Dotenv\Dotenv;

class Environment
{
    public const IS_PRODUCTION = 'production';
    public const IS_DEV = 'dev';

    public static function isProduction(): bool
    {
        $dotenv = Dotenv::createImmutable(APP_PATH);
        $dotenv->safeLoad();
        $env = $_ENV['APP_ENV'] ?? self::IS_DEV;
        return $env === self::IS_PRODUCTION;
    }
}