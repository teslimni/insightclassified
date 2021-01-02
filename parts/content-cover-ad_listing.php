<?php
/**
 * Listing single head cover template.
 *
 * @package ClassiPress\Templates
 * @since 4.0.0
 */

?>

<div <?php echo apply_filters( 'cp_background_cover', 'hero-listing listing-cover text-center', array( 'size' => 'full' ) ); ?>>

	<div class="hero-listing-wrap row text-center">

		<div class="columns">

			<header class="entry-header">

				<?php appthemes_before_post_title( get_post_type() ); ?>

				<?php
				/**
				 * Fires in the listing single page head.
				 *
				 * @since 4.0.0
				 */
				do_action( 'cp_listing_single_head' );
				?>

				<?php appthemes_after_post_title( get_post_type() ); ?>

				<div class="entry-actions">
					<?php
					/**
					 * Fires in the listing single page head action block.
					 *
					 * @since 4.0.0
					 */
					do_action( 'cp_listing_single_head_actions' );
					?>
				</div><!-- .entry-actions -->

				<div class="entry-meta-sub">

					<span class="entry-category">
						<i class="fa fa-folder-open-o" aria-hidden="true"></i> <?php echo get_the_term_list( $post->ID, APP_TAX_CAT, '', ', ', '' ); ?>
					</span>

					<?php if ( get_comments_number() ) { ?>
					<span class="entry-comments sep-l">
						<i class="fa fa-comments-o" aria-hidden="true"></i>  <?php printf( _nx( '1 Comment', '%1$s Comments', get_comments_number(), 'comments title', APP_TD ), number_format_i18n( get_comments_number() ) ); ?>
					</span>
					<?php } ?>

					<?php
					/**
					 * Fires in the single listing hero meta section.
					 *
					 * @since 4.0.0
					 */
					do_action( 'cp_listing_single_hero_meta' );
					?>

				</div> <!-- .entry-meta-sub -->

			</header>

		</div> <!-- .columns -->

	</div> <!-- .row -->

	<?php get_template_part( 'parts/content-action-bar', get_post_type() ); ?>

</div>
