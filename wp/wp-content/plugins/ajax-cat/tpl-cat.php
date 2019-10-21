<?php
/**
 * Шаблон вывода названия рубрики и постов из неё
 */

if ( have_posts() ) : ?>

    <header class="page-header">
        <?php
        the_archive_title( '<h1 class="page-title">', '</h1>' );
        the_archive_description( '<div class="taxonomy-description">', '</div>' );
        ?>
    </header><!-- .page-header -->

<!--    --><?php //$posts = get_posts([
//    'post_type'  => 'services',
//    'numberposts' => -1,
//    'order'      => 'ASC',
//    'orderby'   => 'title',
//    ]); ?>
<!--    <ul class="services-listing">-->
<!--        --><?php //foreach( $posts as $post ){
//            setup_postdata($post); ?>
<!--            <li id="post---><?php //the_ID(); ?><!--" --><?php //post_class(); ?><!-->-->
<!--                <a href="--><?php //echo $post->guid; ?><!--">--><?php //echo $post->post_title; ?><!--</a>-->
<!--            </li>-->
<!--            --><?php //wp_reset_postdata();
//        } ?>
<!--    </ul>-->

    <?php
    while ( have_posts() ) : the_post();
    the_title();
    the_post_thumbnail();

        get_template_part( 'template-parts/content', get_post_format() );

    endwhile;

    $pagination = get_the_posts_pagination( array(
        'prev_text'          => __( 'Previous page', 'testheme' ),
        'next_text'          => __( 'Next page', 'testheme' ),
        'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'testheme' ) . ' </span>',
    ) );

    echo str_replace( admin_url( 'admin-ajax.php/' ), get_category_link( $cat->term_id ), $pagination );
else :
    get_template_part( 'template-parts/content', 'none' );
endif;