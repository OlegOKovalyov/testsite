<?php

namespace Flexi\Inc\VisualComposer;

abstract class VcCustomParameter
{
    use VcCustomTrait;

    public function __construct()
    {
        if (!$this->name) {
            $className = get_class($this);
            $this->name = static::generateName($className);
        }
        $this->slug = static::generateSlug($this->name);
        vc_add_shortcode_param($this->slug, [$this, 'setParamSettings']);
    }

    abstract public function setParamSettings($settings, $value);
}