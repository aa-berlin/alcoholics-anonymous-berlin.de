<?php
/**
 * @package zenzero
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
		if ( '' != get_the_post_thumbnail() ) {
			echo '<div class="entry-featuredImg">';
			the_post_thumbnail('zenzero-normal-post');
			echo '</div>';
		}
	?>
	<header class="entry-header">
		<?php the_title( sprintf( '<h1 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h1>' ); ?>

		<div class="entry-meta smallPart">
			<?php zenzero_posted_on(); ?>
		</div><!-- .entry-meta -->
		<div class="beforeContent"></div>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php the_content(); ?>
		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links smallPart"><i class="fa fa-files-o spaceRight"></i><span>',
				'after'  => '</span></div>',
			) );
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer smallPart">
		<?php zenzero_entry_footer(); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
