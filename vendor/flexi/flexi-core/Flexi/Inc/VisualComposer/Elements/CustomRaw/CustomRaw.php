<?php

use Flexi\Inc\VisualComposer\VcCustomElement;

class CustomRaw extends VcCustomElement
{
    public function setPaths()
    {
        $this->scriptsPath = static::getCurrentElementFileUrl('assets/scripts.js', __DIR__);
        $this->stylesPath = static::getCurrentElementFileUrl('assets/styles.css', __DIR__);
    }

    public function setMap()
    {
        $iconPath = static::getCurrentElementFileUrl('assets/icon.svg', __DIR__);
        vc_map(
            [
                'name'            => __($this->name, 'flexi'),
                'base'            => $this->slug,
                'description'     => __('Custom theme raw.', 'flexi'),
                'category'        => __('Flexi', 'flexi'),
                'icon'            => $iconPath,
                'content_element' => true,
                'as_parent'       => ['except' => ''],
                'js_view'         => 'VcColumnView',
                'params'          => [
                    [
                        'type'        => 'textfield',
                        'heading'     => __('Raw ID', 'flexi'),
                        'param_name'  => 'id',
                        'description' => __('Custom raw ID', 'flexi'),
                    ],
                    [
                        'type'        => 'textfield',
                        'heading'     => __('Raw Title', 'flexi'),
                        'param_name'  => 'title',
                        'description' => __('Custom raw title', 'flexi'),
                    ],
                    [
                        'type'        => 'dropdown',
                        'heading'     => __('Row position', 'flexi'),
                        'param_name'  => 'position',
                        'description' => __('Custom raw ID', 'flexi'),
                        'admin_label' => true,
                        'value'       => [
                            'Default'    => '',
                            'Left Side'  => 'left-side-container',
                            'Right Side' => 'right-side-container',
                        ],
                    ],
                    [
                        'type'        => 'textfield',
                        'heading'     => __('Raw Class', 'flexi'),
                        'param_name'  => 'class',
                        'description' => __('Custom raw Class', 'flexi'),
                    ],
                ],
            ]
        );
    }

    public function getHtml($args, $content = null)
    {
        $args = array_merge([
            'id'       => '',
            'class'    => '',
            'position' => '',
            'title'    => '',
        ], (array)$args);

        $id = $args['id'] ? "id='{$args['id']}'" : '';
        $positionClasses = $args['position'] ? "full-side-container {$args['position']}" : '';
        $class = "class='{$args['class']} {$positionClasses} vc-custom-row-wrapper'";
        ob_start(); ?>

        <div <?php echo $id; ?>  <?php echo $class; ?>>
            <?php if ($args['title']) : ?>
                <h2 class="vc-custom-row-title">
                    <?php _e($args['title'], 'flexi'); ?></h2>
            <?php endif; ?>
            <?php
            echo do_shortcode($content);
            ?>
        </div>
        <?php

        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }
}