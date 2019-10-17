<?php

namespace Flexi\Core\Models;

abstract class Singleton
{
    protected function __construct()
    {
    }

    private function __clone()
    {
    }

    public static function instance()
    {
        static $models = [];
        $calledClass = get_called_class();
        if (!isset($models[$calledClass])) {
            $models[$calledClass] = new $calledClass();
        }
        return $models[$calledClass];
    }
}