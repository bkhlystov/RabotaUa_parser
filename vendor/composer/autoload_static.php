<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit8388c236f15285976e7811cec74c8ad5
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'SocNet\\' => 7,
        ),
        'P' => 
        array (
            'Parse\\' => 6,
        ),
        'C' => 
        array (
            'Common\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'SocNet\\' => 
        array (
            0 => __DIR__ . '/../..' . '/../common/SocNet',
        ),
        'Parse\\' => 
        array (
            0 => __DIR__ . '/../..' . '/../common/Parse',
        ),
        'Common\\' => 
        array (
            0 => __DIR__ . '/../..' . '/../common/Common',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit8388c236f15285976e7811cec74c8ad5::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit8388c236f15285976e7811cec74c8ad5::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
