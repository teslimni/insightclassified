<?php

/**
 * Header Ad Block template.
 *
 * @package ClassiPress\Templates
 * @since 4.0.0
 */

?>

<?php if ( $cp_options->adcode_468x60_enable ) { ?>

	<div class="row adblock">

		<div class="columns text-center">

			<?php appthemes_advertise_header(); ?>

		</div>

	</div><!-- /adblock -->

<?php }
