<?php

namespace Flexi\Inc\Sidebars;

class Sidebar
{
    public $name;
    public $slug;
    private $args;

    function __construct($name, $args = [])
    {
        if (!$name) {
            return;
        }

        $this->name = $name;
        $this->slug = sanitize_title($name);
        $this->setArgs($args);
    }

    private function setArgs($args)
    {
        $this->args = array_merge([
            'name' => __($this->name, 'flexi'),
            'id'   => $this->slug,
        ], $args);
    }

    public function getArgs()
    {
        return $this->args;
    }

    public function getSidebar() {
        if (!is_active_sidebar($this->slug)) {
            _e( $this->name . ' sidebar is inactive', 'flexi');
        }
        dynamic_sidebar($this->name);
    }
}