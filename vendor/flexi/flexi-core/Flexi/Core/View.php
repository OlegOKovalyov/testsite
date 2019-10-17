<?php

namespace Flexi\Core;

class View
{
    private $path;
    private $variables;

    public static function create($path, Array $variables = [])
    {
        $view = self::setup($path, $variables);
        $view = $view->include();
        return $view;
    }

    private static function setup($path, $variables)
    {
        $view = new self;
        $view->path = $path;
        $view->variables = $variables;
        return $view;
    }

    private function include()
    {
        if (!file_exists($this->path)) {
            return __('View not found', 'flexi');
        }

        extract($this->variables);
        include $this->path;
        return $this;
    }
}