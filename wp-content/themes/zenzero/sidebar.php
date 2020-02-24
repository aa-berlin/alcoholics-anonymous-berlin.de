<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package zenzero
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>

<div id="secondary" class="widget-area smallPart nano">
	<div class="nano-content"><?php dynamic_sidebar( 'sidebar-1' ); ?></div>
</div><!-- #secondary -->
