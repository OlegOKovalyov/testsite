<?php
foreach ($fieldsList as $key) :
    $title = ucfirst($key);
    $value = $fieldsData[$key] ?? '';

    $id = $widget->get_field_id($key);
    $name = $widget->get_field_name($key);
    ?>
    <p>
        <label for="<?php echo $id; ?>">
            <?php _e($title, 'flexi'); ?>
        </label>
        <input class="widefat" id="<?php echo $id ?>" name="<?php echo $name; ?>" type="text" value="<?php echo esc_attr($value); ?>"/>
    </p>
<?php
endforeach;