<?php

namespace Handlers;

use Dotenv\Dotenv;

class Environment
{
    public const ENV_PRODUCTION = 'production';
    public const ENV_DEVELOPMENT = 'dev';

    public static function isProduction(): bool
    {
        $dotenv = Dotenv::createImmutable(APP_PATH);
        $dotenv->safeLoad();

        $env = $_ENV['APP_ENV'] ?? self::ENV_PRODUCTION;

        return $env === self::ENV_DEVELOPMENT;
    }
}