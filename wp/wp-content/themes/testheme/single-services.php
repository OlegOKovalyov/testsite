<?php
get_header();
?>
    <div id="primary" class="content-area">
        <main id="main" class="site-main">
            <?php
            if (have_posts()) :
                while (have_posts()) : the_post(); ?>
                    <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                        <h1><?php the_title() ?></h1>
                        <?php
                            $company_name = get_post_meta( get_the_ID(), 'company_name', true );
                        if ( !empty( $company_name ) ) {
                            echo "<div><span>Company Name: </span><span>" . $company_name . "</span></div>";
                        }
                        ?>
                        <?php
                            $service_not_available = get_post_meta( get_the_ID(), 'not_available', true );
                            if ( !empty( $service_not_available ) && $service_not_available == 'yes' ) {
                                echo "<p>Sorry, service is not available.</p>";
                            }
                        ?>
                        <?php the_post_thumbnail(); ?>
                        <div class="entry-content">
                            <?php the_content(); ?>
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

