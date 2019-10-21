<?php
/**
 * The main template file
 */

get_header();
?>
    <div id="primary" class="content-area">
        <main id="main" class="site-main">
            <?php
            if (have_posts()) :
                while (have_posts()) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                        <h1><?php the_title() ?></h1>
                        <?php the_post_thumbnail(); ?>
                        <div class="entry-content">
                            <?php the_content();?>
                        </div>
                    </article>
                    <!-- #post-<?php the_ID(); ?> -->
                <?php endwhile;
            endif; ?>
        </main><!-- #main -->
    </div><!-- #primary -->

<?php
get_sidebar();
get_footer();
