<?php

namespace Flexi\Inc\PostTypes;

class CustomPostTypes
{
    private function __construct()
    {
    }

    private static $customPostTypes = [];

    public static function createCustomPostType($postTypeName, $args = [])
    {
        $postType = new CustomPostType($postTypeName, $args);
        static::$customPostTypes[$postType->slug] = $postType;
        return $postType;
    }

    public static function getCustomPostTypes()
    {
        return static::$customPostTypes;
    }

    public static function getCustomPostType($name)
    {
        $slug = sanitize_title($name);
        if (!array_key_exists($slug, static::$customPostTypes)) {
            return null;
        }
        return static::$customPostTypes[$slug];
    }

    public static function registerPostTypes()
    {
        foreach (static::$customPostTypes as $postType) {
            static::registerPostType($postType);
        }
    }

    public static function registerPostType(CustomPostType $postType)
    {
        $model = register_post_type($postType->slug, $postType->getArgs());
        $postType->setModel($model);
    }
}