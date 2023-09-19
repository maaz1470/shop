<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit6772195b70f98844164e6a755b162c83
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Package\\' => 8,
        ),
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Package\\' => 
        array (
            0 => __DIR__ . '/../..' . '/package',
        ),
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit6772195b70f98844164e6a755b162c83::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit6772195b70f98844164e6a755b162c83::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit6772195b70f98844164e6a755b162c83::$classMap;

        }, null, ClassLoader::class);
    }
}