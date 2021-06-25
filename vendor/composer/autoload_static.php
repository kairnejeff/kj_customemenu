<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitda4f978407a7db532f17435684246664
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PrestaShop\\Module\\ProductCarrousel\\' => 35,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PrestaShop\\Module\\ProductCarrousel\\' => 
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
            $loader->prefixLengthsPsr4 = ComposerStaticInitda4f978407a7db532f17435684246664::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitda4f978407a7db532f17435684246664::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitda4f978407a7db532f17435684246664::$classMap;

        }, null, ClassLoader::class);
    }
}
