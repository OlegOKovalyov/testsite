<?php

namespace Flexi\Inc\Widgets;

class Widgets
{
    private static $widgets = [];
    private static $registerHookAdded = false;

    public static function createWidget($name, $desc, $frontendTemplate, $backendTemplate, $args = [])
    {
        $widget = new Widget($name, $desc, $frontendTemplate, $backendTemplate, $args);
        static::$widgets[$widget->slug] = $widget;
        if (!static::$registerHookAdded) {
            add_action('widgets_init', [get_called_class(), 'registerWidgets']);
            static::$registerHookAdded = true;
        }
    }

    public static function registerWidgets()
    {
        foreach (static::$widgets as $widget) {
            register_widget($widget);
        }
    }

    public static function getWidgets() {
        return static::$widgets;
    }
}