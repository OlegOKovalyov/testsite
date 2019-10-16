<?php
/**
 * Basic functions and definitions
 */
if ( ! function_exists( 'basic_setup' ) ) :
	function basic_setup() {
		load_theme_textdomain( 'basic', get_template_directory() . '/languages' );

		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );

		register_nav_menus( array(
			'menu-1' => esc_html__( 'Primary', 'basic' ),
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
add_action( 'after_setup_theme', 'basic_setup' );

/**
 * Register widget area.
 */
function basic_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'basic' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'basic' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'basic_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function basic_scripts() {
	wp_enqueue_style( 'basic-style', get_stylesheet_uri() );
}
add_action( 'wp_enqueue_scripts', 'basic_scripts' );

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';