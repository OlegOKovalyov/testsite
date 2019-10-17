<?php

namespace Flexi\Inc\PostTypes;

use Flexi\Core\Models\Singular;
use Flexi\Inc\MetaBoxes\MetaBox;

class CustomPostType extends Singular
{
    public $taxonomies = [];
    public $metaboxes = [];

    public function __construct($postTypeName, $args = [])
    {
        if (!$postTypeName) {
            return;
        }

        $this->name = $postTypeName;
        $labels = $this->setLabels($args);
        $this->setArgs($args, $labels);
    }

    protected function setLabels($args)
    {
        $this->slug = sanitize_title($this->name);

        $this->singularName = $args['labels']['singular_name'] ?? $args['singular_name'] ?? null;
        $singular = $this->singularName ? $this->singularName : $this->name;

        $labels = [
            'name'               => $this->name,
            'singular_name'      => $this->singularName,
            'menu_name'          => $this->name,
            'all_items'          => 'All ' . $this->name,
            'add_new'            => 'Add New',
            'edit_item'          => 'Edit',
            'update_item'        => 'Update',
            'search_items'       => 'Search' . strtolower($this->name),
            'not_found'          => 'Not Found',
            'not_found_in_trash' => 'Not found in Trash',
            'parent_item_colon'  => 'Parent ' . strtolower($singular),
            'view_item'          => 'View ' . strtolower($singular),
            'add_new_item'       => 'Add New ' . strtolower($singular),
        ];

        return isset($args['labels']) ? array_merge($labels, $args['labels']) : $labels;
    }

    protected function setArgs($args, $labels)
    {
        $this->args = array_merge([
            'labels'               => $labels,
            'supports'             => ['title', 'editor', 'author', 'thumbnail', 'custom-fields', 'excerpt'],
            'public'               => true,
            'menu_position'        => 4,
            'menu_icon'            => 'dashicons-admin-post',
        ], $args);

        if (isset($args['taxonomies'])) {
            $this->args['taxonomies'] = $args['taxonomies'];
        }
    }

    public function setModel($model)
    {
        if (!$model instanceof \WP_Post_Type) {
            return;
        }
        $this->model = $model;
    }

    public function addMetabox($name, $template = '', $context = 'side', $priority = 'default') {
        $metabox = new MetaBox($name, $this->slug, $template, $context, $priority);
        $this->metaboxes[$metabox->slug] = $metabox;
    }
}