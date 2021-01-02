<?php
/**
 * Template part for mobile title bar
 *
 * @since 4.0.0
 */

?>
<!-- off-canvas title bar -->
<div class="title-bar" data-responsive-toggle="wide-menu" data-hide-for="medium">

	<div class="title-bar-<?php echo is_rtl() ? 'right' : 'left'; ?>">
		<button class="menu-icon" type="button" data-open="offCanvasLeft"></button>
		<span class="title-bar-title">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
				<?php bloginfo( 'name' ); ?>
			</a>
		</span>
	</div>

	<div class="title-bar-<?php echo is_rtl() ? 'left' : 'right'; ?>">
		<button class="menu-icon" type="button" data-open="offCanvasRight"></button>
	</div>

</div>
