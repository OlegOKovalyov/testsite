<?php

use Flexi\Inc\VisualComposer\VcCustomElement;
use Flexi\Inc\PostTypes\CustomPostTypes;
use Flexi\Constants;

class BuilderTemplate extends VcCustomElement
{

    private static $postType;

    public function setPaths()
    {
        $this->scriptsPath = static::getCurrentElementFileUrl('assets/scripts.js', __DIR__);
        $this->stylesPath = static::getCurrentElementFileUrl('assets/styles.css', __DIR__);
    }

    public function setMap()
    {
        static::$postType = CustomPostTypes::getCustomPostType(Constants::BUILDER_POST_TYPE);
        $iconPath = static::getCurrentElementFileUrl('assets/icon.svg', __DIR__);
        vc_map(
            [
                'name'        => __($this->name, 'flexi'),
                'base'        => $this->slug,
                'description' => __('Custom static builder template.', 'flexi'),
                'category'    => __('Flexi', 'flexi'),
                'icon'        => $iconPath,
                'params'      => [
                    [
                        'type'        => 'textfield',
                        'heading'     => __('Template ID', 'flexi'),
                        'param_name'  => 'id',
                        'description' => __('Custom template ID', 'flexi'),
                    ],
                    [
                        'type'        => 'textfield',
                        'heading'     => __('Template Class', 'flexi'),
                        'param_name'  => 'class',
                        'description' => __('Custom template Class', 'flexi'),
                    ],
                    [
                        'type'       => 'templates_param',
                        'heading'    => __('Choose Template:', 'flexi'),
                        'param_name' => 'template',
                        'post_type'  => static::$postType,
                    ],
                ],
            ]
        );
    }

    public function getHtml($args)
    {
        $args = array_merge([
            'id'       => '',
            'class'    => '',
            'template' => 0,
        ], $args);

        if (!$args['template']) {
            return __('Custom template not specified', 'flexi');
        }


        $content = get_post_field('post_content', $args['template']);
        if (is_wp_error($content)) {
            return $content->get_error_message();
        }

        $id = $args['id'] ? "id='{$args['id']}'" : '';
        $class = "class='{$args['class']} vc-custom-template-wrapper'";

        ob_start(); ?>
        <div <?php echo $id; ?>  <?php echo $class; ?>>
            <?php echo do_shortcode($content); ?>
        </div>
        <?php

        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }
}