<?php
/**
 * Plugin functions
 *
 * Here you can find a lot of functions for the most popular plugins.
 *
 * @link https://docs.wprig.org/wp-rig-toolkit-structure/functions.php
 *
 * @package wp_rig
 */

/**
 * Contact Form 7: Disable automatic loading of CF7 .js and .css.
 *
 * Sometimes you want to postpone loading default CF7 styles and script
 * and load them only where CF7 is persisted on the page.
 *
 * @link https://contactform7.com/loading-javascript-and-stylesheet-only-when-it-is-necessary/
 */
if (constant('CF7_DISABLE_DEFAULT_LOADING_JS_CSS') === true) {
	add_filter('wpcf7_load_js', '__return_false');
	add_filter('wpcf7_load_css', '__return_false');
}

/**
 * Contact Form 7: Remove <p> and <br/>.
 *
 * When you want to have more control over CF7 form's code.
 * It removes default <p> and <br/> tags from generated code.
 *
 * @link https://docs.wprig.org/coming-soon
 */
if (constant('CF7_CLEAN_MARKUP') === true) {
	add_filter('wpcf7_autop_or_not', '__return_false');
}

/**
 * YoastSEO: Filter the output of Yoast breadcrumbs to remove <span> tags added by the plugin.
 *
 * Sometimes it can be really annoying when you are a fan of clean code :)
 *
 * @link https://docs.wprig.org/coming-soon
 */
if (constant('YOAST_BREADCRUMBS_CLEAN_SPAN') === true) {
	/**
	 * Filter the output of Yoast breadcrumbs to remove <span> tags added by the plugin.
	 *
	 * @param string $output The breadcrumb output.
	 * @return string Modified breadcrumb output.
	 */
	function doublee_filter_yoast_breadcrumb_output($output)
	{
		$from = '<span>';
		$to = '</span>';
		$output = str_replace($from, $to, $output);

		return $output;
	}
	add_filter(
		'wpseo_breadcrumb_output',
		'doublee_filter_yoast_breadcrumb_output',
	);
}

/**
 * YoastSEO: Remove SearchAction from yoast-schema-graph JSON structured data.
 *
 * Can be useful if you don't have a working search on your site (for example you have a generated static site).
 *
 * @link https://docs.wprig.org/coming-soon
 */
if (constant('YOAST_SEARCHACTION_JSON_DISABLE') === true) {
	add_filter('disable_wpseo_json_ld_search', '__return_true');
}

/**
 * Elementor: Remove loading standard Elementor fonts.
 *
 * You probably don't need Elementor if you use WP Rig.
 * But if you use it, most likely you connect your fonts locally through Rig so no need to load default ones.
 *
 * @link https://docs.wprig.org/coming-soon
 */
if (constant('ELEMENTOR_REMOVE_STANDART_FONTS') === true) {
	add_filter('elementor/frontend/print_google_fonts', '__return_false');
}
