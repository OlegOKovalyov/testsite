<?php
/**
 * Template Name: Services Page Template
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
            endif;
            ?>
<!--            <h2>Services Filter with AJAX</h2>
            --><?php
/*            $terms = get_terms( [
                'taxonomy' => 'taxonomy',
            ]);
            echo '<pre>';
            print_r($terms);
            echo '</pre>';
            if( $terms && ! is_wp_error($terms) ){
                echo "<ul>";
                foreach( $terms as $term ){
                    echo "<li>". $term->name ."</li>";

                }
                echo "</ul>";
            }
            if( $terms = get_terms( [
                'taxonomy' => 'taxonomy',
                'orderby' => 'name',
                ] ) ) : // как я уже говорил, для простоты возьму рубрики category, но get_terms() позволяет работать с любой таксономией
                echo '<select name="categoryfilter"><option>Выберите категорию...</option>';
                foreach ($terms as $term) :
                    echo '<option value="' . $term->term_id . '">' . $term->name . '</option>'; // в качестве value я взял ID рубрики
                endforeach;
                echo '</select>';
            endif;
            $taxonomies = get_taxonomies();
            foreach( $taxonomies as $taxonomy ) {
                echo '<p>'. $taxonomy. '</p>';
            }
            */?>
        </main><!-- #main -->
    </div><!-- #primary -->

<?php
get_sidebar();
get_footer();