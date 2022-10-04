<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit72ccd0a0ee4c3f25404031ab719a0bcb
{
    public static $prefixLengthsPsr4 = array (
        'E' => 
        array (
            'EmailReplyParser\\' => 17,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'EmailReplyParser\\' => 
        array (
            0 => __DIR__ . '/..' . '/willdurand/email-reply-parser/src/EmailReplyParser',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit72ccd0a0ee4c3f25404031ab719a0bcb::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit72ccd0a0ee4c3f25404031ab719a0bcb::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit72ccd0a0ee4c3f25404031ab719a0bcb::$classMap;

        }, null, ClassLoader::class);
    }
}
