<?php
/**
 * public view functionality.
 *
 * @link       http://themescode.com
 * @since      1.0.0
 *
 * @package    Owl Carousel wp
 * themescode is used as short
 **/
 function tc_owlcarousel_get_option( $option, $section, $default = '' ) {

     $options = get_option( $section );

     if ( isset( $options[$option] ) ) {
         return $options[$option];
     }

     return $default;
 }

function themescode_ocw_trigger(){
?>
<style media="screen">

/* Navigation */
.tcowl-wrap  .owl-theme .owl-nav [class*='owl-'] {
 background-color: <?php echo tc_owlcarousel_get_option('navigation-color', 'tc-owlcarousel_others', '#000' ); ?>;
}
 .tcowl-wrap  .owl-theme .owl-nav [class*='owl-']:hover {
  background-color: <?php echo tc_owlcarousel_get_option('navigation-hover-color', 'tc-owlcarousel_others', '#343434' ); ?>;

 }
/* Dots */
.tcowl-wrap  .owl-theme .owl-dots .owl-dot span {
 background:<?php echo tc_owlcarousel_get_option('dots-color', 'tc-owlcarousel_others', '#000' ); ?>;
}
.tcowl-wrap  .owl-theme .owl-dots .owl-dot.active span, .owl-theme .owl-dots .owl-dot:hover span {
 background:<?php echo tc_owlcarousel_get_option('dots-hover-color', 'tc-owlcarousel_others', '#343434' ); ?>;
}
</style>

<script type="text/javascript">

jQuery(document).ready(function(){
    jQuery(".owl-carousel").owlCarousel({
      // control
          autoplay:<?php echo tc_owlcarousel_get_option('auto-play','tc-owlcarousel_basics', 'true' ); ?>,
          autoplayHoverPause:<?php  echo tc_owlcarousel_get_option('stop-onhover','tc-owlcarousel_basics', 'true' ); ?>,
          autoplayTimeout:<?php echo tc_owlcarousel_get_option('auto_play_timeout','tc-owlcarousel_basics', 1000 ); ?>,
          autoplaySpeed:<?php echo tc_owlcarousel_get_option('auto_play_speed','tc-owlcarousel_basics', 3000 );?>,
          navSpeed:<?php echo tc_owlcarousel_get_option('nav_speed','tc-owlcarousel_basics', 3000 ); ?>,
          dotsSpeed:<?php echo tc_owlcarousel_get_option('pagination_speed','tc-owlcarousel_basics', 3000 ); ?>,
          loop:<?php echo tc_owlcarousel_get_option('loop','tc-owlcarousel_basics', 'true' ); ?>,
        // Advances
          slideBy:<?php echo tc_owlcarousel_get_option('slideBy','tc-owlcarousel_basics', '1' ); ?>,
          slideTransition:'linear',
         
          margin:<?php echo tc_owlcarousel_get_option('margin-val','tc-owlcarousel_advanced',5); ?>,
          nav:<?php echo tc_owlcarousel_get_option('nav-val','tc-owlcarousel_advanced', 'true' ); ?>,
          navText:["&lt;","&gt;"],
          autoHeight:<?php echo tc_owlcarousel_get_option('autoheight','tc-owlcarousel_advanced', 'false' ); ?>,
          autoWidth:<?php echo tc_owlcarousel_get_option('autoheight','tc-owlcarousel_advanced', 'false' ); ?>,
          center:<?php echo tc_owlcarousel_get_option('autoheight','tc-owlcarousel_advanced', 'false' ); ?>,
          stagePadding:<?php echo tc_owlcarousel_get_option('stage-padding','tc-owlcarousel_advanced', 'false' ); ?>,
          rtl:<?php echo tc_owlcarousel_get_option('rtl-val','tc-owlcarousel_advanced', 'false' ); ?>,
          dots:<?php echo tc_owlcarousel_get_option('dots-val','tc-owlcarousel_advanced', 'false' ); ?>,
          responsiveClass:true,
          responsive:{
              0:{
                  items:1,
              },
              600:{
                  items:<?php echo tc_owlcarousel_get_option('items-tablet-val','tc-owlcarousel_basics', '3' ); ?>,

              },
              1000:{
                  items:<?php  echo tc_owlcarousel_get_option('medium-desktops','tc-owlcarousel_basics', '4' ); ?>,

              }

          }

  });

});


</script>

<?php
}
add_action('wp_footer','themescode_ocw_trigger');

add_shortcode('tc-owl-carousel', 'themescode_ocw_carousel_view' );


function themescode_ocw_carousel_view($atts) {

	// Attributes
extract( shortcode_atts(
	array(
		'posts_num' => "-1",
		'order' => 'DESC',
		 'carousel_cat'=>'',

	), $atts )
);


$args = array(
		'orderby' => 'date',
		 'order' => $order,
			'carousel_category' =>$carousel_cat,
			 'showposts' => $posts_num,
			'post_type' => 'ocw_carousel'
);
 $owlImg_id='';
 $tcowlImg_url='';
 $tcowlImg_mata='';

  $tc_owl_loop = new WP_Query($args);
  $output = '<div class="tcowl-wrap tc-carousel-container">';
  $output .= '<div class="owl-carousel owl-theme tcowl-nav">';
  $ImageLink='';
  if($tc_owl_loop->have_posts()){
      while($tc_owl_loop->have_posts()) {

          $tc_owl_loop->the_post();
          $post_id = get_the_ID();
          $ImageExLink= get_post_meta($post_id,'tc_imagelink',true);

         //Ex LInk
         $tcowlImg_id = get_post_thumbnail_id();
         $tcowlImg_alt = get_post_meta($tcowlImg_id,'_wp_attachment_image_alt',true);

         $tcowlImg_url = wp_get_attachment_image_src($tcowlImg_id,'full', true);

         $tcowlImg_src = $tcowlImg_url[0];
         
         //
          if(!empty(trim($ImageExLink))){
            $output .= '<div class="carousel-item">';
               $output.='<a href="'.$ImageExLink.'">';
                 $output.='<img src="'. $tcowlImg_src .'" alt="'. $tcowlImg_alt .'" />';
                $output.= '</a>'; // client url end
                $output .= '</div>';

           }else{
              $output .= '<div class="carousel-item">';
                $output.='<img src="'.$tcowlImg_src.'" alt="'.$tcowlImg_alt.'" />';
               $output .= '</div>';
           }
      }
  } else {
      echo 'No Carousel Was Found.';
  }
  wp_reset_postdata();
  wp_reset_query();
  $output .= '</div><!--/.tc-carousel-containe-->';
  $output .= '</div><!--/.tc-carousel-demo-->';



  ?>


  <?php
  return $output;

 }


 ?>
