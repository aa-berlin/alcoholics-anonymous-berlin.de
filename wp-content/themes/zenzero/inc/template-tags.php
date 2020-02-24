<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package zenzero
 */

if ( ! function_exists( 'zenzero_paging_nav' ) ) :
/**
 * Display navigation to next/previous set of posts when applicable.
 */
function zenzero_paging_nav() {
	// Don't print empty markup if there's only one page.
	if ( $GLOBALS['wp_query']->max_num_pages < 2 ) {
		return;
	}
	?>
	<nav class="navigation paging-navigation" aria-label="<?php esc_attr_e('Posts', 'zenzero'); ?>">
		<h2 class="screen-reader-text"><?php esc_html_e( 'Posts navigation', 'zenzero' ); ?></h2>
		<div class="nav-links">

			<?php if ( get_next_posts_link() ) : ?>
			<div class="nav-previous"><?php next_posts_link( '<i class="fa fa-angle-left"></i>' ); ?></div>
			<?php endif; ?>

			<?php if ( get_previous_posts_link() ) : ?>
			<div class="nav-next"><?php previous_posts_link( '<i class="fa fa-angle-right"></i>' ); ?></div>
			<?php endif; ?>

		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;

if ( ! function_exists( 'zenzero_post_nav' ) ) :
/**
 * Display navigation to next/previous post when applicable.
 */
function zenzero_post_nav() {
	// Don't print empty markup if there's nowhere to navigate.
	$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
	$next     = get_adjacent_post( false, '', false );

	if ( ! $next && ! $previous ) {
		return;
	}
	?>
	<nav class="navigation post-navigation" aria-label="<?php esc_attr_e('Posts', 'zenzero'); ?>">
		<h2 class="screen-reader-text"><?php esc_html_e( 'Post navigation', 'zenzero' ); ?></h2>
		<div class="nav-links">
			<?php
				previous_post_link( '<div class="nav-previous">%link</div>', '<i class="fa prevNext fa-lg fa-angle-left"></i> <div class="meta-nav" aria-hidden="true"><small>' . esc_html__( 'Previous Post', 'zenzero' ) . '</small> ' . '<span class="smallPart">%title</span></div><span class="screen-reader-text">' . esc_html__( 'Previous post link', 'zenzero' ) . '</span> ' );
				next_post_link( '<div class="nav-next">%link</div>', '<div class="meta-nav" aria-hidden="true"><small>' . esc_html__( 'Next Post', 'zenzero' ) . '</small><span class="smallPart">%title</span></div> <i class="fa prevNext fa-lg fa-angle-right"></i> ' . '<span class="screen-reader-text">' . esc_html__( 'Next Post link', 'zenzero' ) . '</span> ' );
			?>
		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;

if ( ! function_exists( 'zenzero_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function zenzero_posted_on() {
	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
	}

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	);
	
	$posted_on = '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>';
	$byline = '<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>';

	echo '<span class="posted-on"><i class="fa fa-clock-o spaceLeftRight" aria-hidden="true"></i>' . $posted_on . '</span><span class="byline"><i class="fa fa-user spaceLeftRight" aria-hidden="true"></i>' . $byline . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	
	if ( 'post' == get_post_type() ) {
		$categories_list = get_the_category_list( ' / ' );
		if ( $categories_list ) {
			printf( '<span class="cat-links"><i class="fa fa-folder-open-o spaceLeftRight" aria-hidden="true"></i>' . $categories_list . '</span>'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
	
	if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) {
		echo '<span class="comments-link"><i class="fa fa-comments-o spaceLeftRight" aria-hidden="true"></i>';
		comments_popup_link( esc_html__( 'Leave a comment', 'zenzero' ), esc_html__( '1 Comment', 'zenzero' ), esc_html__( '% Comments', 'zenzero' ) );
		echo '</span>';
	}

}
endif;

if ( ! function_exists( 'zenzero_entry_footer' ) ) :
/**
 * Prints HTML with meta information for the categories, tags and comments.
 */
function zenzero_entry_footer() {
	// Hide category and tag text for pages.
	if ( 'post' == get_post_type() ) {
		$tags_list = get_the_tag_list( '', '' );
		if ( $tags_list ) {
			printf( '<span class="tags-links"><i class="fa fa-tags spaceRight" aria-hidden="true"></i>' . $tags_list . '</span>'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	edit_post_link( esc_html__( 'Edit', 'zenzero' ), '<span class="edit-link"><i class="fa fa-wrench spaceRight" aria-hidden="true"></i>', '</span>' );
}
endif;
