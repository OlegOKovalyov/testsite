<?php
/**
 * testheme functions and definitions
 */
if ( ! function_exists( 'testheme_setup' ) ) :
	function testheme_setup() {
		load_theme_textdomain( 'testheme', get_template_directory() . '/languages' );

		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );

		register_nav_menus( array(
			'menu-1' => esc_html__( 'Primary', 'testheme' ),
		) );


		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );

		add_theme_support( 'customize-selective-refresh-widgets' );

		add_theme_support( 'custom-logo', array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		) );
	}
endif;
add_action( 'after_setup_theme', 'testheme_setup' );

/**
 * Register widget area.
 */
function testheme_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'testheme' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'testheme' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

    register_sidebar( array(
        'name'          => esc_html__( 'Footer-1', 'testheme' ),
        'id'            => 'sidebar-2',
        'description'   => esc_html__( 'Add widgets here.', 'testheme' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ) );

    register_sidebar( array(
        'name'          => esc_html__( 'Footer-2', 'testheme' ),
        'id'            => 'sidebar-3',
        'description'   => esc_html__( 'Add widgets here.', 'testheme' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ) );

    register_sidebar( array(
        'name'          => esc_html__( 'Footer-3', 'testheme' ),
        'id'            => 'sidebar-4',
        'description'   => esc_html__( 'Add widgets here.', 'testheme' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ) );
}
add_action( 'widgets_init', 'testheme_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function testheme_scripts() {
    wp_enqueue_style('testheme-style', get_stylesheet_uri());

    wp_enqueue_style('new-style', get_template_directory_uri() . '/css/example.css', array('testheme-style'), '1.0.0');
    wp_enqueue_script('script-example', get_template_directory_uri() . '/js/example.js', array(), '1.0.0', true);
    wp_localize_script( 'script-example', 'object_name', array(
        'some_string' => __( 'Some string to translate' ),
        'a_value' => '10'
    ) );
    wp_localize_script('script-example', 'testheme_ajax',
        array(
            'url' => admin_url('admin-ajax.php')
        )
    );

}
add_action( 'wp_enqueue_scripts', 'testheme_scripts' );

/**
 * Plain shortcode.
 */
function testheme_plain_shcode( $atts ){
    return site_url();
}
add_shortcode( 'shcode', 'testheme_plain_shcode' );

/**
 * Shortcode with two tags.
 */
add_shortcode( 'baztag', 'baztag_func' );
function baztag_func( $atts, $content ) {
    return $content;
}

/**
 * AJAX.
 */
add_action('wp_footer', 'test_ajax_action_javascript', 99); // для фронта
function test_ajax_action_javascript() {
    ?>
    <script type="text/javascript" >
        jQuery(document).ready(function($) {
            var data = {
                action: 'test_ajax_action',
                whatever: 1234
            };

            // 'ajaxurl' не определена во фронте, поэтому мы добавили её аналог с помощью wp_localize_script()
            jQuery.post( testheme_ajax.url, data, function(response) {
                console.log('From Server Data: ' + response);
                // alert('Получено с сервера: ' + response);
            });
        });
    </script>
    <?php
}

add_action('wp_ajax_test_ajax_action', 'test_ajax_action_callback');
add_action('wp_ajax_nopriv_test_ajax_action', 'test_ajax_action_callback');
function test_ajax_action_callback() {
    $whatever = intval( $_POST['whatever'] );

    echo $whatever + 10;

    // выход нужен для того, чтобы в ответе не было ничего лишнего, только то что возвращает функция
    wp_die();
}

/**
 * Services Custom Post Type.
 */
add_action( 'init', 'createServicePostType');

function createServicePostType() {
    $labels = [
        'name' => __( 'Services', 'testheme'),
        'singular_name' => __( 'Service', 'testheme'),
        'add_new_item' => __( 'Add New Service', 'testheme'),
        'view_item' => __( 'View Service', 'testheme'),
        'search_items' =>  __( 'Search Services', 'testheme'),
        'not_found' =>  __( 'No Services found', 'testheme'),
    ];
    $args = [
        'labels' => $labels,
        'public' => true,
        'show_ui' => true, // показывать интерфейс в админке
        'has_archive' => true,
        //'menu_icon' => get_stylesheet_directory_uri() .'/img/function_icon.png', // иконка в меню
        'menu_icon' => 'dashicons-admin-tools', // иконка в меню
        'menu_position' => 20, // порядок в меню
        'supports' => array( 'title', 'editor', 'comments', 'author', 'thumbnail', 'page-attributes', 'post-formats'),
        'taxonomies' => array('category', 'post_tag'),
        'hierarchical' => true,
        'publicly_queryable' => true,
        'capability_type' => 'page',
    ];

    register_post_type('services', $args);
}

/**
 * Taxonomy Types.
 */
function createTaxonomyTypes() {

    $labels = array(
        'name'                       => _x( 'Types', 'Taxonomy General Name', 'text_domain' ),
        'singular_name'              => _x( 'Type', 'Taxonomy Singular Name', 'text_domain' ),
        'menu_name'                  => __( 'Taxonomy', 'text_domain' ),
        'all_items'                  => __( 'All Items', 'text_domain' ),
        'parent_item'                => __( 'Parent Item', 'text_domain' ),
        'parent_item_colon'          => __( 'Parent Item:', 'text_domain' ),
        'new_item_name'              => __( 'New Item Name', 'text_domain' ),
        'add_new_item'               => __( 'Add New Item', 'text_domain' ),
        'edit_item'                  => __( 'Edit Item', 'text_domain' ),
        'update_item'                => __( 'Update Item', 'text_domain' ),
        'view_item'                  => __( 'View Item', 'text_domain' ),
        'separate_items_with_commas' => __( 'Separate items with commas', 'text_domain' ),
        'add_or_remove_items'        => __( 'Add or remove items', 'text_domain' ),
        'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
        'popular_items'              => __( 'Popular Items', 'text_domain' ),
        'search_items'               => __( 'Search Items', 'text_domain' ),
        'not_found'                  => __( 'Not Found', 'text_domain' ),
        'no_terms'                   => __( 'No items', 'text_domain' ),
        'items_list'                 => __( 'Items list', 'text_domain' ),
        'items_list_navigation'      => __( 'Items list navigation', 'text_domain' ),
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => false,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
    );
    register_taxonomy( 'taxonomy', array( 'services' ), $args );

}
add_action( 'init', 'createTaxonomyTypes', 0 );