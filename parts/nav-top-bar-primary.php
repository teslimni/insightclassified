<?php
/**
 * The template for displaying the top navigation
 *
 * @package ClassiPress\Templates
 * @author  AppThemes
 * @since   ClassiPress 4.0.0
 */

$expanded = get_theme_mod( 'header_full_width', 1 ) ? ' expanded' : '';
?>
<div id="top-bar-primary" class="top-bar" role="navigation">

	<div class="row column<?php echo $expanded; ?>">

		<div class="primary-header-wrap">

			<div class="site-branding">

				<?php
				if ( function_exists( 'the_custom_logo' ) ) { // Since 4.0.0
					the_custom_logo();
				}

				if ( is_front_page() ) { ?>

					<h1 class="site-title">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
							<?php bloginfo( 'name' ); ?>
						</a>
					</h1>

				<?php } else { ?>

					<span class="h1 site-title">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
							<?php bloginfo( 'name' ); ?>
						</a>
					</span>

				<?php } ?>

				<p class="site-description"><?php bloginfo( 'description' ); ?></p>

			</div><!-- .site-branding -->

			<div class="top-bar-left">

				<?php
				/**
				 * Fires in the header next to the logo.
				 *
				 * @since 4.0.0
				 */
				do_action( 'cp_header_top_bar_left' );
				?>

				<?php dynamic_sidebar( 'sidebar_header' ); ?>

			</div>

			<?php cp_header_menu_primary(); ?>

		</div><!-- .primary-header-wrap -->

	</div><!-- .row -->

</div><!-- .top-bar -->
