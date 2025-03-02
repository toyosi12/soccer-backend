<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit0c2c7e84b98f3dbc2d847dfcd447408e
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit0c2c7e84b98f3dbc2d847dfcd447408e::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit0c2c7e84b98f3dbc2d847dfcd447408e::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
