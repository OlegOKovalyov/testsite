<?php

namespace Flexi\Inc\MetaBoxes;

class MetaBoxesList
{
    private $list = [];
    private static $instance = null;

    private function __construct()
    {
        add_action('save_post', [$this, 'saveMetaData']);
    }

    public static function getInstance(): MetaBoxesList
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public function addMetaBox(MetaBox $metaBox)
    {
        $this->list[$metaBox->slug . '-' . $metaBox->postType] = $metaBox;
    }

    public function saveMetaData($post_id)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;

        if (!current_user_can('edit_post', $post_id))
            return;

        $postType = get_post_type($post_id);
        foreach ($this->list as $metabox) {
            if ($postType !== $metabox->postType) {
                continue;
            }
            if(!array_key_exists($metabox->slug, $_POST)) {
                delete_post_meta($post_id, $metabox->slug);
            } else {
                update_post_meta($post_id, $metabox->slug, $_POST[$metabox->slug]);
            }
        }
    }
}