<?php
$title = $fieldsData['title'] ?? '';
$text = $fieldsData['text'] ?? '';

echo $args['before_widget'];
if (!empty($title)) {
    echo $args['before_title'] . $title . $args['after_title'];
}
if (!empty($text)) {
    echo __($text, 'flexi');
}
echo $args['after_widget'];
