<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitcd7fcb3a71bb26d2dea1d77b16acba40
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'Avatec\\Framework\\' => 17,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Avatec\\Framework\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitcd7fcb3a71bb26d2dea1d77b16acba40::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitcd7fcb3a71bb26d2dea1d77b16acba40::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitcd7fcb3a71bb26d2dea1d77b16acba40::$classMap;

        }, null, ClassLoader::class);
    }
}
