<?php
/**
 * WordPress customization functions
 *
 * Here are placed functions for WordPress adjustment.
 *
 * @link https://docs.wprig.org/wp-rig-toolkit-structure/functions.php
 *
 * @package wp_rig
 */

/**
 * Stop WordPress from adding <p> tags.
 *
 * By default, WordPress inserts a paragraph tag for every new line.
 * You may want to get rid of it for better code control.
 *
 * https://docs.wprig.org/coming-soon
 */
if ( constant( 'STOP_ADDING_P_TAGS' ) === true ) {
	// Remove the wpautop filter from the content and excerpt.
	remove_filter( 'the_content', 'wpautop' );
	remove_filter( 'the_excerpt', 'wpautop' );
}

/**
 * Disable Gutenberg editor.
 *
 * If you don't like you can disable it.
 *
 * https://docs.wprig.org/coming-soon
 */
if ( constant( 'DISABLE_GUTENBERG' ) === true ) {
	// Disable Gutenberg on the back end.
	add_filter( 'use_block_editor_for_post', '__return_false' );

	// Disable Gutenberg for widgets.
	add_filter( 'use_widgets_block_editor', '__return_false' );

	add_action( 'wp_enqueue_scripts', function() {
		// Remove CSS on the front end.
		wp_dequeue_style( 'wp-block-library' );

		// Remove Gutenberg theme.
		wp_dequeue_style( 'wp-block-library-theme' );

		// Remove inline global CSS on the front end.
		wp_dequeue_style( 'global-styles' );

		// Remove classic-themes CSS for backwards compatibility for button blocks.
		wp_dequeue_style( 'classic-theme-styles' );
	}, 20 );
}

/**
 * Disable Visual editor completely.
 *
 * Sometimes it is worth completely disabling the visual editor for better code control.
 * You may want to get rid of it for better code control.
 *
 * https://docs.wprig.org/coming-soon
 */
if ( constant( 'DISABLE_VISUAL_EDITOR' ) === true ) {
	// Disable the visual editor.
	add_filter( 'user_can_richedit', function () { return false; }, 50 );
}

/**
 * Disable Visual editor only for admins.
 *
 * Sometimes it is worth completely disabling the visual editor for better code control.
 * You may want to get rid of it for better code control.
 *
 * https://docs.wprig.org/coming-soon
 */
if ( constant( 'DISABLE_VISUAL_EDITOR_ADMINS_ONLY' ) === true ) {
	if ( wp_get_current_user()->has_cap( 'administrator' ) ) {
		// Disable the visual editor for administrators.
		add_filter( 'user_can_richedit', function () { return false; }, 50 );
	}
}

/**
 * Clean page headers from "Archive:" and "Category:".
 *
 * Removes default labels from archive titles.
 *
 * https://docs.wprig.org/coming-soon
 */
if ( constant( 'HEADERS_CLEAN_ARCHIVE_CATEGORY' ) === true ) {
	// Modify the archive title.
	add_filter(
		'get_the_archive_title',
		function ( $title ) {
			if ( is_category() ) {
				$title = single_cat_title( '', false );
			} elseif ( is_tag() ) {
				$title = single_tag_title( '', false );
			} elseif ( is_author() ) {
				$title = '<span class="vcard">' . get_the_author() . '</span>';
			} elseif ( is_tax() ) { // for custom post types.
				$title = sprintf( __( '%1$s' ), single_term_title( '', false ) );
			} elseif ( is_post_type_archive() ) {
				$title = post_type_archive_title( '', false );
			}
			return $title;
		}
	);
}

/**
 * Add Post Excerpts Support for Pages.
 *
 * By default, excerpts are enabled only for posts.
 * Can be useful, for example, if you show excerpts in search results.
 *
 * https://docs.wprig.org/coming-soon
 */
if ( constant( 'ADD_PAGE_EXCERPTS_SUPPORT' ) === true ) {
	// Add excerpt support for pages.
	add_action( 'init', 'add_excerpt_support_for_pages' );

	/**
	 * Add excerpt support for pages.
	 */
	function add_excerpt_support_for_pages() {
		add_post_type_support( 'page', 'excerpt' );
	}
}

/**
 * Disable Comments.
 *
 * Disable comments if you don't need them on the website.
 * Don't forget to remove existing comments if you have some.
 *
 * https://docs.wprig.org/coming-soon
 */
if ( constant( 'DISABLE_COMMENTS' ) === true ) {
	add_action(
		'admin_init',
		function () {
			global $pagenow;

			// Redirect any user trying to access the comments page.
			if ( 'edit-comments.php' === $pagenow ) {
				wp_redirect( admin_url() );
				exit;
			}

			// Remove comments metabox from dashboard.
			remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );

			// Disable support for comments and trackbacks in post types.
			foreach ( get_post_types() as $post_type ) {
				if ( post_type_supports( $post_type, 'comments' ) ) {
					remove_post_type_support( $post_type, 'comments' );
					remove_post_type_support( $post_type, 'trackbacks' );
				}
			}
		}
	);

	// Close comments on the front-end.
	add_filter( 'comments_open', '__return_false', 20, 2 );
	add_filter( 'pings_open', '__return_false', 20, 2 );

	// Hide existing comments.
	add_filter( 'comments_array', '__return_empty_array', 10, 2 );

	// Remove comments page in menu.
	add_action(
		'admin_menu',
		function () {
			remove_menu_page( 'edit-comments.php' );
		}
	);

	// Remove comments links from admin bar.
	add_action(
		'init',
		function () {
			if ( is_admin_bar_showing() ) {
				remove_action( 'admin_bar_menu', 'wp_admin_bar_comments_menu', 60 );
			}
		}
	);
}

/**
 * Completely disable WordPress Image Resizing.
 *
 * Rare cases you want to disable it.
 * A good example is if you use only default image sizes or vector images.
 *
 * https://docs.wprig.org/coming-soon
 */
if ( constant( 'DISABLE_WP_ALL_THUMBNAILS_GENERATING' ) === true ) {
	// Remove all intermediate image sizes.
	add_action( 'init', 'remove_all_image_sizes' );

	/**
	 * Remove all intermediate image sizes.
	 */
	function remove_all_image_sizes() {
		foreach ( get_intermediate_image_sizes() as $size ) {
			remove_image_size( $size );
		}
	}
}

/**
 * Disable default thumbnails sizes.
 *
 * If you have activated responsive images, you probably have custom thumbnail sizes.
 * In this case, we recommend disabling original sizes as they are just a waste of your server space.
 *
 * https://docs.wprig.org/coming-soon
 */
if ( constant( 'DISABLE_WP_DEFAULT_THUMBNAILS_SIZES' ) === true ) {
	// Remove default image sizes.
	function add_image_insert_override( $sizes ) {
		unset( $sizes['thumbnail'] );
		unset( $sizes['medium'] );
		unset( $sizes['medium-large'] );
		unset( $sizes['large'] );
		unset( $sizes['1536x1536'] );
		unset( $sizes['2048x2048'] );
		return $sizes;
	}
	add_filter( 'intermediate_image_sizes_advanced', 'add_image_insert_override' );
}

/**
 * Disable WordPress image scaling.
 *
 * In cases if you want to serve original photos in high resolution or screenshots of webpages,
 * you need to disable image scaling before uploading them.
 *
 * https://docs.wprig.org/coming-soon
 * Source: https://make.wordpress.org/core/2019/10/09/introducing-handling-of-big-images-in-wordpress-5-3/
 */
if ( constant( 'DISABLE_WP_IMAGE_SCALING' ) === true ) {
	// Disable the big image size threshold.
	add_filter( 'big_image_size_threshold', '__return_false' );
}

/**
 * Custom header support.
 *
 * Sometimes you want to provide a custom <h1> tag for the page instead of default page title.
 * 1) It prints h1 tag with hidden custom h1 right after opening the body.
 * 2) Register shortcode for correct title output if custom h1 is set.
 *
 * https://docs.wprig.org/coming-soon
 *
 * TODO: Make the same functionality through hooks.
 * TODO: Get title's classes throught comma (from array not string).
 */

if ( constant( 'ADD_CUSTOM_HEADER_SUPPORT' ) === true ) {
	if ( class_exists( 'ACF' ) ) {
		// Print hidden custom h1 tag after opening the body.
		add_action( 'wp_body_open', 'display_hidden_custom_h1', 0 );
		function display_hidden_custom_h1() {
			if ( get_field( 'custom_h1' ) ) {
				echo '<h1 class="visuallyhidden">' . esc_html( get_field( 'custom_h1' ) ) . '</h1>';
			}
		}
	}

	// Register shortcode for correct title outputs.
	add_shortcode( 'custom_header', 'custom_header_shortcode' );
	function custom_header_shortcode( $atts = '' ) {

		$custom_header_atts = shortcode_atts(
			array(
				'visibility' => 'visible',
				'classes'    => '',
			),
			$atts
		);
		if ( class_exists( 'ACF' ) ) {
			// Print original title if title is not hidden.
			if ( 'hidden' !== $custom_header_atts['visibility'] && get_field( 'custom_h1' ) ) {
				$out  = '<span class="entry-title entry-title-singular';
				if ( '' !== $custom_header_atts['classes'] ) {
					$out .= ' ' . $custom_header_atts['classes'];
				}
				$out .= '">' . get_the_title() . '</span>';
			}
			else {
				$out  = '<h1 class="entry-title entry-title-singular';
				if ( '' !== $custom_header_atts['classes'] ) {
					$out .= ' ' . $custom_header_atts['classes'];
				}
				$out .= '">' . get_the_title() . '</h1>';
			}
		}

		wp_reset_postdata();

		return $out;
	}
}