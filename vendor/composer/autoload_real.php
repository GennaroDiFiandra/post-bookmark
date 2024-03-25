<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitaeaf7c62a0c4ec99af037ec5d7f07f47
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        require __DIR__ . '/platform_check.php';

        spl_autoload_register(array('ComposerAutoloaderInitaeaf7c62a0c4ec99af037ec5d7f07f47', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitaeaf7c62a0c4ec99af037ec5d7f07f47', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInitaeaf7c62a0c4ec99af037ec5d7f07f47::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
