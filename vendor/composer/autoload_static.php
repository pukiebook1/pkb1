<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit0bf9265fb203cf6c6f0a6e82cb3fdc37
{
    public static $files = array (
        '44346ed4770424a275986d5af3e25c75' => __DIR__ . '/../..' . '/app/files/password.php',
    );

    public static $fallbackDirsPsr4 = array (
        0 => __DIR__ . '/../..' . '/app',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->fallbackDirsPsr4 = ComposerStaticInit0bf9265fb203cf6c6f0a6e82cb3fdc37::$fallbackDirsPsr4;

        }, null, ClassLoader::class);
    }
}