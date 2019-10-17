<?php

namespace Flexi\Core;

use Automatic_Upgrader_Skin;
use Plugin_Upgrader;

class Plugin
{

    public $slug;
    private $path;

    public function __construct(String $slug, String $path)
    {
        $this->slug = $slug;
        $this->path = $path;
    }

    public function install()
    {
        if ($this->isPluginInstalled()) {
            return;
        }

        $this->installationIncludes();
        $result = $this->getPluginUpgrader()->install($this->path);

        if (is_wp_error($result)) {
            return Error::showMessage($result);
        }
    }

    public function update()
    {
        if (!$this->isPluginInstalled()) {
            return Error::showMessage('Plugin ' . $this->slug . ' not installed');
        }

        $this->upgradeIncludes();
        $result = $this->getPluginUpgrader()->upgrade($this->slug);

        if (is_wp_error($result)) {
            return Error::showMessage($result);
        }
    }

    public function activate()
    {
        if (!$this->isPluginInstalled()) {
            return Error::showMessage('Plugin ' . $this->slug . ' not installed');
        }

        if (is_plugin_active($this->slug)) {
            return;
        }

        $result = activate_plugin($this->slug);

        if (is_wp_error($result)) {
            return Error::showMessage($result);
        }
    }

    public function hide()
    {
        $slug = $this->slug;
        add_action('all_plugins', function ($pluginsList) use ($slug) {
            unset($pluginsList[$slug]);
            return $pluginsList;
        });
    }

    private function isPluginInstalled()
    {
        if (!function_exists('get_plugins')) {
            include_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }

        $allPlugins = get_plugins();
        return isset($allPlugins[$this->slug]);
    }

    private function getPluginUpgrader()
    {
        wp_cache_flush();
        return new Plugin_Upgrader(new Automatic_Upgrader_Skin());
    }

    private function installationIncludes()
    {
        include_once(ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');
        include_once(ABSPATH . 'wp-admin/includes/file.php');
        include_once(ABSPATH . 'wp-admin/includes/misc.php');
        include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }

    private function upgradeIncludes()
    {
        include_once(ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');
        include_once(ABSPATH . 'wp-admin/includes/file.php');
        include_once(ABSPATH . 'wp-admin/includes/misc.php');
        include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }
}
