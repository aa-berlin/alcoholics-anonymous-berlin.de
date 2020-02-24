<?php
/**
 * @package zenzero
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
		if ( '' != get_the_post_thumbnail() ) {
			echo '<div class="entry-featuredImg"><a href="' .esc_url(get_permalink()). '" title="' .esc_attr(the_title_attribute('echo=0')). '"><span class="overlay-img"></span>';
			the_post_thumbnail('zenzero-normal-post', array( 'alt' => get_the_title()));
			echo '</a></div>';
		}
	?>
	<header class="entry-header">
		<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

		<?php if ( 'post' == get_post_type() ) : ?>
		<div class="entry-meta smallPart">
			<?php zenzero_posted_on(); ?>
		</div><!-- .entry-meta -->
		<?php endif; ?>
		<div class="beforeContent"></div>
	</header><!-- .entry-header -->

	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div><!-- .entry-summary -->

	<footer class="entry-footer smallPart">
		<?php edit_post_link( esc_html__( 'Edit', 'zenzero' ), '<span class="edit-link"><i class="fa fa-wrench spaceRight"></i>', '</span>' ); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->

<div class="betweenPost"></div>
