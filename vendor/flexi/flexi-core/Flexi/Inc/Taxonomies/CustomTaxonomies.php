<?php

namespace Flexi\Inc\Taxonomies;

use Flexi\Inc\PostTypes\CustomPostType;
use Flexi\Inc\PostTypes\CustomPostTypes;

class CustomTaxonomies
{
    private function __construct()
    {
    }

    private static $customTaxonomies = [];

    public static function createCustomTaxonomy($taxonomyName, $postTypes = [], $args = [])
    {
        if (static::customTaxonomyExists($taxonomyName)) {
            return null;
        }
        $customTaxonomy = new CustomTaxonomy($taxonomyName, $postTypes, $args);
        static::$customTaxonomies[$customTaxonomy->slug] = $customTaxonomy;
        return $customTaxonomy;
    }

    public static function getCustomTaxonomies()
    {
        return static::$customTaxonomies;
    }

    public static function registerTaxonomies()
    {
        foreach (static::$customTaxonomies as $customTaxonomy) {
            static::registerTaxonomy($customTaxonomy);
        }
    }

    public static function customTaxonomyExists(String $taxonomyName)
    {
        $slug = sanitize_title($taxonomyName);
        return array_key_exists($slug, static::getCustomTaxonomies());
    }

    private static function registerTaxonomy(CustomTaxonomy $customTaxonomy)
    {
        $result = register_taxonomy($customTaxonomy->slug, $customTaxonomy->postTypes, $customTaxonomy->getArgs());
        if (is_wp_error($result)) {
            return;
        }
        foreach (CustomPostTypes::getCustomPostTypes() as $postType) {
            if ($postType->slug == $customTaxonomy->postTypes || (is_array($customTaxonomy->postTypes) && in_array($postType->slug, $customTaxonomy->postTypes))) {
                static::setPostTypeRelation($postType, $customTaxonomy);
            }
        }
        $model = get_taxonomy($customTaxonomy->slug);
        $customTaxonomy->setModel($model);
    }

    private static function setPostTypeRelation(CustomPostType $postType, CustomTaxonomy $customTaxonomy)
    {
        if (array_key_exists($customTaxonomy->slug, $postType->taxonomies)) {
            return;
        }
        $postType->taxonomies[$customTaxonomy->slug] = $customTaxonomy;
    }
}
