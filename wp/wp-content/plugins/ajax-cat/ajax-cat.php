<?php
/**
 * Plugin Name: Ajax Cat
 * Description: Ajax подгрузка постов из рубрик. Плагин создан в рамках изучения Ajax в Wordpress. Полный цикл уроков
 * смотрите на канале <a href="http://www.youtube.com/c/wpplus">wp-plus</a>.
 * Author: Campusboy
 * Author URI: https://wp-plus.ru/
 * Plugin URI: https://github.com/campusboy87/lessons-ajax-wordpress
 */
add_action( 'wp_ajax_get_cat', 'ajax_show_services_posts' );
add_action( 'wp_ajax_nopriv_get_cat', 'ajax_show_services_posts' );
function ajax_show_services_posts() {

    WPBMap::addAllMappedShortcodes();
    $link = ! empty( $_POST['link'] ) ? esc_attr( $_POST['link'] ) : false;
    $slug = $link ? wp_basename( $link ) : false;

    $args = array(
        'name'        => $slug,
        'post_type'   => 'services',
        'post_status' => 'publish',
        'numberposts' => 1,
    );
    $post = get_posts($args);

    if ( ! $post ) {
        die( 'Post not found' );
    }

    foreach( $post as $item ){
        setup_postdata($item);
        echo '<h1>' . $post[0]->post_title . '</h1>';
        echo get_the_post_thumbnail($post[0]->ID, array(640, 423), array('class' => 'alignleft'));

        $test = $post[0]->post_content;
        $test = get_the_content($post[0]->ID);
        echo do_shortcode($test);
    }

    wp_die();
}
add_action( 'wp_enqueue_scripts', 'my_assets' );
function my_assets() {
    wp_enqueue_script( 'custom', plugins_url( 'custom.js', __FILE__ ), array( 'jquery' ) );

    wp_localize_script( 'custom', 'localizeScriptObject', array(
        'ajaxurl' => admin_url( 'admin-ajax.php' )
    ) );
}