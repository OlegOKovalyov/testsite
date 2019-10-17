<?php

namespace Flexi\Inc\Widgets;
use Flexi\Core\View;

class Widget extends \WP_Widget
{
    public $name;
    public $slug;
    public $desc;

    protected $front;
    protected $back;

    private $args;

    function __construct($name, $desc, $frontendTemplate, $backendTemplate, $args = [])
    {
        if (!$name) {
            return;
        }

        $this->setArgs($name, $desc, $frontendTemplate, $backendTemplate, $args);
        parent::__construct($this->slug, __($this->name, 'flexi'), ['description' => __($this->desc, 'flexi')]);
    }

    public function widget($args, $fieldsData)
    {
        $template = $this->front ? $this->front : getCoreConstant('DEFAULT_WIDGET_FRONT_TEMPLATE');
        $this->setWidgetView($template, $fieldsData, $args);
    }

    public function form($fieldsData)
    {
        $template = $this->back ? $this->back : getCoreConstant('DEFAULT_WIDGET_BACK_TEMPLATE');
        $this->setWidgetView($template, $fieldsData);
    }

    private function setWidgetView($template, $fieldsData, $args = []) {
        $widget = $this;
        $fieldsList = $this->args;
        View::create($template, compact('widget', 'fieldsData', 'fieldsList', 'args'));
    }

    public function update($newFieldsData, $oldFieldsData)
    {
        foreach ($this->args as $key) {
            $newFieldsData[$key] = $newFieldsData[$key] ? strip_tags($newFieldsData[$key]) : '';
        }
        return $newFieldsData;
    }

    private function setArgs($name, $desc, $front, $back, $args)
    {
        $this->name = $name;
        $this->slug = sanitize_title($name);
        $this->desc = $desc;

        $this->front = $front;
        $this->back = $back;

        $this->args = apply_filters('setupWidgetArgs', $args);
    }

    public function getArgs()
    {
        return $this->args;
    }
}
