<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit9513e3572cf615fae8d19517f83435b5
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

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit9513e3572cf615fae8d19517f83435b5::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit9513e3572cf615fae8d19517f83435b5::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit9513e3572cf615fae8d19517f83435b5::$classMap;

        }, null, ClassLoader::class);
    }
}
