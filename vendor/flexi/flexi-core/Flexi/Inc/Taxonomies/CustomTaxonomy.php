<?php

namespace Flexi\Inc\Taxonomies;

use Flexi\Core\Models\Singular;

class CustomTaxonomy extends Singular
{
    public $postTypes;

    public function __construct($taxonomyName, $postTypes, $args = [])
    {
        if (!$taxonomyName) {
            return;
        }

        $this->name = $taxonomyName;
        $this->postTypes = $postTypes;
        $labels = $this->setLabels($args);
        $this->setArgs($args, $labels);
    }

    protected function setLabels($args)
    {

        $this->slug = sanitize_title($this->name);

        $this->singularName = $args['labels']['singular_name'] ?? $args['singular_name'] ?? null;
        $singular = $this->singularName ? $this->singularName : $this->name;

        $labels = [
            'name'              => $this->name,
            'singular_name'     => $this->singularName,
            'menu_name'         => $this->name,
            'all_items'         => 'All ' . $this->name,
            'add_new'           => 'Add New',
            'edit_item'         => 'Edit',
            'update_item'       => 'Update',
            'search_items'      => 'Search' . strtolower($this->name),
            'popular_items'     => 'Popular' . strtolower($this->name),
            'not_found'         => 'Not Found',
            'parent_item_colon' => 'Parent ' . strtolower($singular),
            'view_item'         => 'View ' . strtolower($singular),
            'add_new_item'      => 'Add New ' . strtolower($singular),
        ];

        return isset($args['labels']) ? array_merge($labels, $args['labels']) : $labels;
    }

    protected function setArgs($args, $labels)
    {
        $this->args = array_merge([
            'labels' => $labels,
        ], $args);
    }

    public function setModel($model)
    {
        if (!$model instanceof \WP_Taxonomy) {
            return;
        }
        $this->model = $model;
    }
}