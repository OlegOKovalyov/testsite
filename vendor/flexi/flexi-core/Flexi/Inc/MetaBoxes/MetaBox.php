<?php

namespace Flexi\Inc\MetaBoxes;

use Flexi\Constants;
use Flexi\Core\View;

class MetaBox
{
    public $name;
    public $slug;
    public $postType;

    private $template;
    private $context;
    private $priority;

    public function __construct($name, $postType, $template = '', $context = 'side', $priority = 'default')
    {
        $this->name = $name;
        $this->slug = sanitize_title($name);
        $this->template = $template;
        $this->postType = $postType;
        $this->context = $context;
        $this->priority = $priority;

        add_action('add_meta_boxes', [$this,'setupMetabox']);
        MetaBoxesList::getInstance()->addMetaBox($this);
    }

    public function setupMetabox()
    {
        add_meta_box($this->slug, $this->name, [$this, 'displayMetabox'], $this->postType, $this->context, $this->priority, $this);
    }

    public function displayMetabox($post, $meta)
    {
        $template = file_exists($this->template) ? $this->template : getCoreConstant('META_BOX_DEFAULT');
        $slug = $meta['args']->slug;
        $value = get_post_meta( $post->ID, $slug, true );
        View::create($template, compact('post', 'meta', 'slug', 'value')); // TODO: Add wp_nonce_field
    }
}
