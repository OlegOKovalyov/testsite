<?php

use Flexi\Inc\VisualComposer\VcCustomParameter;

class TemplatesParam extends VcCustomParameter
{
    public function setParamSettings($settings, $value)
    {
        $postType = $settings['post_type'] ?? null;
        if(!$postType) {
            return _('Parameter "post_type" is required', 'flexi');
        }

        $paramName = esc_attr($settings['param_name']);
        $classNames = esc_attr($settings['type']) . '_field wpb_vc_param_value';
        $values = explode(',', esc_attr($value));

        $posts = get_posts([
            'numberposts' => -1,
            'post_type'   => $postType->slug,
        ]);

        ob_start(); ?>

        <select name="<?php _e($paramName); ?>" class="<?php _e($classNames); ?>">
            <option value=""> - </option>
            <?php foreach ($posts as $post) :
                $selected = in_array($post->ID, $values) ? 'selected' : ''; ?>
                <option value="<?php _e($post->ID); ?>" <?php _e($selected); ?>>
                    <?php _e($post->post_title, 'flexi'); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <?php
        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }
}