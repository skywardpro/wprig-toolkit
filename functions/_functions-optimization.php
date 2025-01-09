<?php
/**
 * Optimization functions
 *
 * Here are placed different optimization functions which can be useful for production website.
 *
 * @link https://docs.wprig.org/wp-rig-toolkit-structure/functions.php
 *
 * @package wp_rig
 */

/**
 * Defer for all scripts.
 *
 * Add defer to all javascript files (exl. jquery and wp-admin scripts).
 * https://kinsta.com/blog/defer-parsing-of-javascript/#4-defer-javascript-via-functionsphp-file
 */

if ( constant( 'DEFER_ALL_SCRIPTS' ) === true ) {

	add_filter( 'script_loader_tag', 'defer_all_scripts', 10 );

	/**
	 * Add defer attribute to script tags.
	 *
	 * @param string $url The URL of the enqueued script.
	 * @return string Modified script URL with defer attribute.
	 */
	function defer_all_scripts( $url ) {
		if ( is_user_logged_in() ) {
			return $url; // don't break WP Admin.
		}
		if ( false === strpos( $url, '.js' ) ) {
			return $url;
		}
		if ( strpos( $url, 'jquery.min.js' ) ) {
			return $url;
		}
		return str_replace( ' src', ' defer src', $url );
	}
}

/**
 * Remove version numbers from scripts and styles.
 *
 * When you update your site very rare it might be good to remove versions from html.
 * https://docs.wprig.org/coming-soon
 */

if ( constant( 'DISABLE_SCRIPTS_STYLES_VERSIONS' ) === true ) {

	add_filter( 'style_loader_src', 'switch_stylesheet_src', 10, 2 );

	/**
	 * Remove version query string from stylesheets.
	 *
	 * @param string $src The source URL of the stylesheet.
	 * @param string $handle The handle of the stylesheet.
	 * @return string The modified source URL.
	 */
	function switch_stylesheet_src( $src, $handle ) {
		$src = remove_query_arg( 'ver', $src );
		return $src;
	}
}

/**
 * Remove type attribute.
 *
 * Usually not needed if your auditory use modern browsers.
 * https://docs.wprig.org/coming-soon
 */

if ( constant( 'DISABLE_TYPE_ATTRIBUTES' ) === true ) {
	add_action(
		'after_setup_theme',
		/**
		 * Enable HTML5 support for script and style tags.
		 */
		function() {
			add_theme_support( 'html5', [ 'script', 'style' ] );
		}
	);
}

/**
 * Remove default block styles.
 *
 * You can disable it if you don't use Gutenberg.
 * https://docs.wprig.org/coming-soon
 */

if ( constant( 'DISABLE_DEFAULT_BLOCK_STYLES' ) === true ) {

	add_action( 'wp_enqueue_scripts', 'remove_wp_block_library_css' );

	/**
	 * Dequeue default block styles.
	 */
	function remove_wp_block_library_css() {
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'wp-block-library-theme-css' );
	}
}

/**
 * Remove WP Polyfill.
 *
 * Usually not needed if your auditory use modern browsers.
 * https://docs.wprig.org/coming-soon
 */

if ( constant( 'DISABLE_WP_POLYFILL' ) === true ) {

	add_action( 'wp_enqueue_scripts', 'remove_wp_polyfill_js' );

	/**
	 * Dequeue WP Polyfill scripts.
	 */
	function remove_wp_polyfill_js() {
		wp_dequeue_script( 'wp-polyfill-js' );
		wp_dequeue_script( 'wp-polyfill-js-after' );
	}
}
