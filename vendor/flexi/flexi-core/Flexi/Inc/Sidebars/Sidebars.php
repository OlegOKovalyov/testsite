<?php

namespace Flexi\Inc\Sidebars;

class Sidebars
{
    private static $sidebars = [];
    private static $registerHookAdded = false;

    /**
     * @param $name
     * @param array $args
     */
    public static function createSidebar($name, $args = [])
    {
        $sidebar = new Sidebar($name, $args);
        static::$sidebars[$sidebar->slug] = $sidebar;
        if (!static::$registerHookAdded) {
            add_action('widgets_init', [get_called_class(), 'registerSidebars']);
            static::$registerHookAdded = true;
        }
    }

    public static function registerSidebars()
    {
        foreach (static::$sidebars as $sidebar) {
            $args = $sidebar->getArgs();
            register_sidebar($args);
        }
    }

    /**
     * @return Sidebar[]
     */
    public static function getSidebars() {
        return static::$sidebars;
    }

    /**
     * @param $slug
     * @return Sidebar
     */
    public static function getSidebarBySlug($slug) {
        return static::$sidebars[$slug] ?? null;
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public static function getSidebarByName($name) {
        foreach(static::$sidebars as $sidebar) {
            if($sidebar->name == $name) {
                return $sidebar;
            }
        }
        return null;
    }
}