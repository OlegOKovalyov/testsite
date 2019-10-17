<?php

namespace Flexi\Inc\VisualComposer;

abstract class VcCustomElement implements VcCustomInterface
{
    use VcCustomTrait;

    protected $scriptsPath;
    protected $stylesPath;

    function __construct()
    {
        if (!$this->name) {
            $className = get_class($this);
            $this->name = static::generateName($className);
        }
        $this->slug = static::generateSlug($this->name);
        $this->setMap();
        $this->setPaths();

        add_shortcode($this->slug, [$this, 'getHtml']);

        if ($this->scriptsPath) {
            wp_enqueue_script("vc_{$this->slug}_script", $this->scriptsPath, ['jquery']);
        }

        if ($this->stylesPath) {
            wp_enqueue_style("vc_{$this->slug}_style", $this->stylesPath);
        }
    }

    protected function generateStylesAttr($params = [])
    {
        if (empty($params)) {
            return '';
        }
        $styles = '';
        foreach ($params as $style => $param) {
            $styles .= !empty($param) ? "{$style}:{$param};" : "";
        }
        $styles = "style='{$styles}'";
        return $styles;
    }
}