<?php
// Custom Post Type Setup
function themescode_ocw_post_type() {
	$labels = array(
		'name' => __('All Owl Carousels', 'owl-carousel-wp'),
		'singular_name' => __('Owl Carousel', 'owl-carousel-wp'),
		'add_new' => __('Add New Carousel', 'owl-carousel-wp'),
		'add_new_item' => __('Add New Carousel', 'owl-carousel-wp'),
		'all_items' => __('All Carousels', 'owl-carousel-wp' ),
		'edit_item' => __('Edit Carousel', 'owl-carousel-wp'),
		'new_item' => __('New Carousel', 'owl-carousel-wp'),
		'view_item' => __('View Carousel', 'owl-carousel-wp'),
		'search_items' => __('Search Carousel', 'owl-carousel-wp'),
		'not_found' => __('No Carousel', 'owl-carousel-wp'),
		'not_found_in_trash' => __('No Carousel found in Trash', 'owl-carousel-wp'),
		'parent_item_colon' => '',
		'menu_name' => __('Owl Carousels', 'owl-carousel-wp') // this name will be shown on the menu
	);
	$args = array(
		'labels' => $labels,
		'public' => true,
		'exclude_from_search' => true,
		'publicly_queryable' => false,
		'show_ui' => true,
		'show_in_menu' => true,
		'query_var' => true,
		'rewrite' => true,
		'capability_type' => 'page',
		'has_archive' => true,
		'hierarchical' => false,
		'menu_position' => 21,
		'menu_icon' =>plugins_url('/owl-carousel-wp/assets/images/icons.png'),
		'supports' => array('title','thumbnail')
	);
	register_post_type('ocw_carousel', $args);
}
 add_action( 'init', 'themescode_ocw_post_type' );

// Adding a taxonomy for the carousel post type
function themescode_carousel_taxonomy() {
		$args = array('hierarchical' => true);
		register_taxonomy( 'carousel_category', 'ocw_carousel', $args );
	}
 add_action( 'init', 'themescode_carousel_taxonomy', 0 );

 // Ad for PRO version

function tc_owl_pro_add_meta_box() {

		add_meta_box(
			'tc_owl_sectionid_pro',
			__( "OWL Carousel WP - PRO" , 'owl-carousel-wp' ),
			'tc_owl_meta_box_pro',
			'ocw_carousel',
			'side',
			'low'
		);
}
add_action( 'add_meta_boxes', 'tc_owl_pro_add_meta_box' );

function tc_owl_meta_box_pro() {  ?>

	<p>
	<h3 style="padding-left:0">Available features at OWL Carousel WP - PRO</h3>
    <ol class="pro-features">
      <li> 7 Different Layout Style.</li>
			<li>7 Different Navigation Style and Position</li>
	    <li> 5 Nice Image Hover Effects.</li>
	    <li> Shortcodes Generator.</li>
	    <li> Different Styling Option For Blog Post Carousel.</li>
	    <li> Light Box effect.</li>
			<li>Two Pop Up Light box Style</li>
	    <li> Advance settings panel with all necessary options.</li>
	    <li> Multiple Design Carousel can be shown from different Carousel categories.</li>
	    <li> Support within 6 hours.</li>
	    <li> 20 Shortcode Attribute.</li>
	    <li> Unlimited Number of Carousel Items.</li>
	    <li> Background Color Changeable.</li>
	    <li> Title Color Changeable.</li>
	    <li> changeable Navigation and Pagination color.</li>
	    <li> Carousel item stop on hover option.</li>
	    <li> Carousel items auto & fixed height option.</li>
	    <li> Carousel from post with image only.</li>
	    <li> Advance settings panel with all necessary options.</li>

	    <li>Control Carousel sliding speed.</li>
	    <li>Enable / disable infinite loop.</li>
	    <li>Stop on hover control.</li>
	    <li>Display Carousel including / excluding Title.</li>
	    <li>Number of Carousel to move on transition.</li>
	    <li>Tons of shortcode parameters</li>
	    <li>Category wise Carousel</li>
	    <li>Works with any WordPress Theme.</li>
	    <li>Easy and user-friendly setup.</li>
	    <li>Online documentation and support.</li>
    	<li>And many more.</li>
    </ol>
  </p>
  <p><a class="ph-button ph-btn-red"
    target="_blank" href="https://www.themescode.com/items/owl-carousel-wp-pro/">Upgrade To PRO ! Now Only $16</a></p>
<?php
}

// Learn WordPress with wpbrim

function tc_owlcwp_learn_wpbrim_meta_box() {
	add_meta_box(
		'tc_learn_wp_sidebar',
		__( "Video Tutorials" , 'tc-logo-slider' ),
		'tc_owlcwp_learn_wp_link',
		'ocw_carousel',
		'side',
		'high'
	);
}
add_action( 'add_meta_boxes', 'tc_owlcwp_learn_wpbrim_meta_box' );

function tc_owlcwp_learn_wp_link() { ?>
	 <p> Watch wpbrim Online Courses on Youtube and brush up your wordpress skills. Ready ? </p>

	<p><a class="ph-button ph-btn-green" href="https://goo.gl/PFkmUu" target="_blank">Watch Video Tutorials</a></p>
	<p><a class="ph-button ph-btn-orange" href="http://owlcarousel.themescode.com" target="_blank">Live Demo</a></p>
	<p><a class="ph-button ph-btn-lime" href="http://docs.themescode.com/owl-carousel-wp-pro/#Basic_Shortcode_Free_Version" target="_blank">Documentation</a></p>
	<p><a class="ph-button ph-btn-blue" href="https://www.themescode.com/items/owl-carousel-wp-pro/" target="_blank">Plugin Home</a></p>
	<div style="clear:both"></div>

<?php
}

// End Learn WordPress with wpbrim
