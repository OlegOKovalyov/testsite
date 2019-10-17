<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <?php the_post_thumbnail(); ?>
    <div class="entry-content">
        <?php the_content(); ?>
    </div>
</article>
<!-- #post-<?php the_ID(); ?> -->