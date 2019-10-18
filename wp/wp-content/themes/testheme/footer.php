<?php
/**
 * The template for displaying the footer
 */
?>

	</div><!-- #content -->

	<footer id="colophon" class="site-footer"  style="display: flex; justify-content: center">
		<div class="site-info" style="width: 1440px;">
            <h3 style=" display: flex; justify-content: center;">This is The Footer</h3>
            <div class="footer-widgets" style="display: flex; justify-content: space-between">
                <?php if ( is_active_sidebar( 'sidebar-2' ) ) : ?>
                    <div id="true-foot" class="sidebar">
                        <?php dynamic_sidebar( 'sidebar-2' ); ?>
                    </div>
                <?php endif; ?>
                <?php if ( is_active_sidebar( 'sidebar-3' ) ) : ?>
                    <div id="true-foot" class="sidebar">
                        <?php dynamic_sidebar( 'sidebar-3' ); ?>
                    </div>
                <?php endif; ?>
                <?php if ( is_active_sidebar( 'sidebar-4' ) ) : ?>
                    <div id="true-foot" class="sidebar">
                        <?php dynamic_sidebar( 'sidebar-4' ); ?>
                    </div>
                <?php endif; ?>
            </div>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
