<?php
use Flexi\Inc\VisualComposer\VcCustomElement;

class CustomText extends VcCustomElement
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
                'description'     => __('Custom Text.', 'flexi'),
                'category'        => __('Flexi', 'flexi'),
                'icon'            => $iconPath,
                'params'          => [
                    [
                        'type'        => 'textfield',
                        'heading'     => __('Text', 'flexi'),
                        'param_name'  => 'text',
                        'description' => __('Custom text', 'flexi'),
                    ]
                ],
            ]
        );
    }

    public function getHtml($args, $content = null)
    {
        $args = array_merge([
            'text' => ''
        ], (array)$args);

        $text = $args['text'] ? $args['text'] : '';

        ob_start(); ?>

        <div class="custom-text">
            <?php echo $text; ?>
        </div>
        <?php

        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }
}