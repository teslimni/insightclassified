<?php
/**
 * Post action bar template.
 *
 * @package ClassiPress\Templates
 * @since 4.2.0
 */
?>

<div class="hero-listing-bar">

	<div class="row">

		<div class="columns">

			<?php
			/**
			 * Fires in the single listing hero footer section.
			 *
			 * @since 4.0.0
			 */
			do_action( 'cp_post_single_bar_actions' );
			?>

		</div> <!-- .columns -->

	</div> <!-- .row -->

</div> <!-- .hero-listing-bar -->
