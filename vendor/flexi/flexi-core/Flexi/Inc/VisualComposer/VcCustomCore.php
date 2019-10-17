<?php

namespace Flexi\Inc\VisualComposer;
use Flexi\Inc\PostTypes\CustomPostTypes;

class VcCustomCore
{
    private static $included = false;
    private static $elements = [];
    private static $params = [];

    public static function init()
    {
        static::loadClasses();
        static::$included = true;
    }

    private static function loadClasses()
    {
        $paths = getCoreConstant('CUSTOM_VC_ELEMENTS_PATHS');
        foreach ($paths as $path) {
            $recursiveIterator = new \RecursiveDirectoryIterator($path);
            $mode = \RecursiveIteratorIterator::SELF_FIRST;

            $objects = new \RecursiveIteratorIterator($recursiveIterator, $mode);
            foreach ($objects as $name => $object) {
                static::getRealPath($object);
            }
        }
        static::createCustoms();
    }

    private static function getRealPath(\SplFileInfo $fileInfo)
    {
        if (!$fileInfo->isFile() || $fileInfo->getExtension() !== 'php') {
            return;
        }
        static::includeCustomFiles($fileInfo->getRealPath());
    }

    private static function includeCustomFiles($file)
    {
        require_once $file;
        $class = basename($file, '.php');

        if (!class_exists($class)) {
            return;
        }

        if (is_subclass_of($class, 'Flexi\Inc\VisualComposer\VcCustomElement', true)) {
            static::$elements[] = $class;
            return;
        }

        if (is_subclass_of($class, 'Flexi\Inc\VisualComposer\VcCustomParameter', true)) {
            static::$params[] = $class;
        }

        return;
    }

    private static function createCustoms()
    {
        foreach (static::$params as $param) {
            new $param;
        }
        foreach (static::$elements as $element) {
            new $element;
        }
    }
}
