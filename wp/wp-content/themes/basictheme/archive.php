<?php
/**
 * The template for displaying archive pages
 */

get_header();
?>

    <div id="primary" class="content-area">
        <main id="main" class="site-main">

            <?php if (have_posts()) : ?>
                <header class="page-header">
                    <?php
                    the_archive_title('<h1 class="page-title">', '</h1>');
                    the_archive_description('<div class="archive-description">', '</div>');
                    ?>
                </header><!-- .page-header -->

                <?php
                while (have_posts()) : the_post();
                    get_template_part('template-parts/content', get_post_type());
                endwhile;
            endif;
            ?>

        </main><!-- #main -->
    </div><!-- #primary -->

<?php
get_sidebar();
get_footer();
