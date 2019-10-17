<?php

namespace Flexi\Inc\VisualComposer;

trait VcCustomTrait
{
    protected $slug;
    protected $name;

    protected static function generateSlug($name)
    {
        $slug = sanitize_title($name);
        return str_replace('-', '_', $slug);
    }

    protected static function generateName($className)
    {
        $class = str_replace('WPBakeryShortCode_', '', $className);
        $chunks = preg_split('/(?=[A-Z])/', $class);
        $name = implode(' ', $chunks);
        return trim($name);
    }

    protected static function getCurrentElementFileUrl($filePath = '', $dirPath = __DIR__)
    {
        return static::getCurrentDirUrl($dirPath) . '/' . $filePath;
    }


    private static function getCurrentDirUrl($dirPath)
    {
        $dirPath = str_replace('\\', '/', $dirPath);
        $templatePath = str_replace('\\', '/',  get_template_directory());
        $path = str_replace($templatePath, '', $dirPath);
        return get_template_directory_uri() . $path;
    }
}