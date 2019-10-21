<?php
/**
 * The template for displaying the footer
 */
?>

	</div><!-- #content -->

	<footer id="colophon" class="site-footer">
		<div class="site-info">
            <h3>This is The Footer</h3>
            <div class="footer-widgets"">
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
