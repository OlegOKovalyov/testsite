<?php

namespace Flexi\Core;

use Flexi\Constants;
use Flexi\Inc\PostTypes\CustomPostTypes;
use Flexi\Inc\Taxonomies\CustomTaxonomies;
use Flexi\Inc\VisualComposer\VcCustomCore;
use Flexi\Inc\AdminPages\AdminPage;

class Theme
{
    private static $themeHooksAdded = false;

    public static function setup()
    {
        if (static::$themeHooksAdded) {
            return;
        }
        static::addActions();
        static::$themeHooksAdded = true;
    }

    private static function addActions()
    {
        add_action('admin_menu', [get_called_class(), 'setupThemeOptionsPanel']);
        add_action('init', [get_called_class(), 'registerCustoms']);
        add_action('after_setup_theme', [get_called_class(), 'registerVcTemplate']);
    }

    public static function vcCustomInit()
    {
        VcCustomCore::init();
    }

    static function setupThemeOptionsPanel()
    {
        $themeMainPageTemplate = getCoreConstant('THEME_MAIN_PAGE_TEMPLATE');
        if ($themeMainPageTemplate) {

            $mainThemePage = new AdminPage('Flexi Theme Page', 'Theme Options', $themeMainPageTemplate);
            $themeSettingsPageTemplate = getCoreConstant('THEME_SETTINGS_PAGE_TEMPLATE');

            if ($themeSettingsPageTemplate) {
                $mainThemePage->addSubPage('Flexi Settings Page', 'Settings Page', $themeSettingsPageTemplate);
            }
        }
    }

    public static function registerVcTemplate() {
        if (!defined('WPB_VC_VERSION')) {
            return;
        }
        $builderPostType = getCoreConstant('BUILDER_POST_TYPE');
        CustomPostTypes::createCustomPostType($builderPostType);
        add_action('vc_before_init', [get_called_class(), 'vcCustomInit']);
    }

    public static function registerCustoms()
    {
        CustomPostTypes::registerPostTypes();
        CustomTaxonomies::registerTaxonomies();
    }
}
