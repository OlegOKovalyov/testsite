<?php
/**
 * The header for our theme
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="site">
	<header id="masthead" class="site-header">
		<div class="site-branding">
			<?php the_custom_logo(); ?>
		</div><!-- .site-branding -->

		<nav id="site-navigation" class="main-navigation">
			<?php wp_nav_menu( array(
				'theme_location' => 'menu-1',
				'menu_id'        => 'primary-menu',
			) );
			?>
		</nav><!-- #site-navigation -->
        <?php
           /* global $table_prefix, $shortcode_tags, $current_user;
            echo "Database Table prefix: $table_prefix<br>";
            echo '<pre>Shortcode Tags:<br>';
            print_r($shortcode_tags);
            echo 'Current User:<br><br>';
            print_r($current_user);
            echo '</pre><br>';*/
            ?>
	</header><!-- #masthead -->

	<div id="content" class="site-content">
