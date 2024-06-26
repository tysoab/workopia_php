<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit63b72f40050da5e869e9c135bfc14eca
{
    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'Framework\\' => 10,
        ),
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Framework\\' => 
        array (
            0 => __DIR__ . '/../..' . '/Framework',
        ),
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/App',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit63b72f40050da5e869e9c135bfc14eca::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit63b72f40050da5e869e9c135bfc14eca::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit63b72f40050da5e869e9c135bfc14eca::$classMap;

        }, null, ClassLoader::class);
    }
}
