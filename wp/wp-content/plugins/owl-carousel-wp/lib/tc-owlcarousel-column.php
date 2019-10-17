<?php

add_filter('manage_edit-ocw_carousel_columns', 'add_new_ocw_carousel_columns');
function add_new_ocw_carousel_columns($ocw_carousel_columns) {


  $new_columns= array(
    'cb' => '<input type="checkbox" />',
    'title' => __( 'Title' ),
    'featured_image' => __( 'Carousel Images' ),
    'carousel_cat'=>_('Carousel Category'),
     'tcImages_url'=>_('Image Link'),
    'author' => __( 'Author' ),
    'date' => __( 'Date' )
  );


    return $new_columns;
}

add_action('manage_ocw_carousel_posts_custom_column', 'manage_ocw_carousel_columns', 10, 2);
function get_ocw_carousel($post_ID)
{
    $tccarouse_id = get_post_thumbnail_id($post_ID);
    return $tccarouse_url = wp_get_attachment_image_src($tccarouse_id, array(40,40), true);
}
function manage_ocw_carousel_columns( $column,$post_ID) {
  $tccarouse=get_ocw_carousel($post_ID);
    switch ( $column ) {
	case 'featured_image' :
		global $post;
		$slug = '' ;
		$slug = $post->ID;
    $featured_image ='<img src="' . $tccarouse[0] . '" width="90px"/>';
    echo $featured_image;
    break;
  case 'carousel_cat' :
   $carousel_cats = wp_get_post_terms($post_ID, 'carousel_category', array("fields" => "names"));
     foreach ( $carousel_cats as $carousel_cat ) {
           echo $carousel_cat.'<br>';

   }
    break;
    case 'tcImages_url' :
    $tcImages_url = get_post_meta( get_the_ID(), 'tc_imagelink', true );
   echo $tcImages_url;
    // echo 'www';
    }
}


 ?>
