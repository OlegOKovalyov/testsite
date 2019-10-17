<?php

namespace Flexi;

use Flexi\Core\Plugin;

class Constants
{
    const DB_SETUP_OPTION = 'wp_database_flexi_core_setup';
    const BUILDER_POST_TYPE = 'Builder Templates';

    const FLEXI_PATH = __DIR__;
    const FLEXI_VIEWS_PATH = self::FLEXI_PATH . '/Views/';
    const FLEXI_INC_PATH = self::FLEXI_PATH . '/Inc/';

    const FLEXI_METABOXES_PATH = self::FLEXI_VIEWS_PATH . 'metaboxes/';
    const FLEXI_WIDGETS_PATH = self::FLEXI_VIEWS_PATH . 'widgets/';

    const FLEXI_VC_PATH = self::FLEXI_INC_PATH . 'VisualComposer/';
    const CUSTOM_VC_ELEMENTS_PATH = self::FLEXI_VC_PATH . 'Elements';
    const CUSTOM_VC_PARAMS_PATH = self::FLEXI_VC_PATH . 'Params';
    const CUSTOM_VC_ELEMENTS_PATHS = [
        self::CUSTOM_VC_ELEMENTS_PATH,
        self::CUSTOM_VC_PARAMS_PATH,
    ];

    const META_BOX_DEFAULT = self::FLEXI_METABOXES_PATH . 'default.php';
    const META_BOX_TEXT = self::FLEXI_METABOXES_PATH . 'text.php';
    const META_BOX_TEXTAREA = self::FLEXI_METABOXES_PATH . 'textarea.php';
    const META_BOX_CHECKBOX = self::FLEXI_METABOXES_PATH . 'checkbox.php';

    const DEFAULT_WIDGET_FRONT_TEMPLATE = self::FLEXI_WIDGETS_PATH . 'default/front.php';
    const DEFAULT_WIDGET_BACK_TEMPLATE = self::FLEXI_WIDGETS_PATH . 'default/back.php';

    const THEME_MAIN_PAGE_TEMPLATE = false;
    const THEME_SETTINGS_PAGE_TEMPLATE = false;
}
