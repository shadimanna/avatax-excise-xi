<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitbaf7c4b3a16ba5e9a69b5e39f9ac3fbb
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'AvataxWooCommerce\\' => 18,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'AvataxWooCommerce\\' => 
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
            $loader->prefixLengthsPsr4 = ComposerStaticInitbaf7c4b3a16ba5e9a69b5e39f9ac3fbb::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitbaf7c4b3a16ba5e9a69b5e39f9ac3fbb::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitbaf7c4b3a16ba5e9a69b5e39f9ac3fbb::$classMap;

        }, null, ClassLoader::class);
    }
}
