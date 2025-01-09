<?php
/**
 * WP Rig functions and definitions
 *
 * This file must be parseable by PHP 5.2.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package wp_rig
 */

require get_template_directory() . '/functions/_functions-rig-init.php';


/**
 * WordPress Customizations.
 *
 * @link https://docs.wprig.org/coming-soon
 */

define( 'STOP_ADDING_P_TAGS', true ); // Stop WordPress from adding <p> tags.
define( 'DISABLE_GUTENBERG', true ); // Disable Gutenberg editor.
define( 'DISABLE_VISUAL_EDITOR', false ); // Disable Visual editor completely.
define( 'DISABLE_VISUAL_EDITOR_ADMINS_ONLY', false ); // Disable Visual editor only for admins.
define( 'HEADERS_CLEAN_ARCHIVE_CATEGORY', true ); // Clean page headers from "Archive:" and "Category:".
define( 'ADD_PAGE_EXCERPTS_SUPPORT', true ); // Add Post Excerpts Support for Pages.
define( 'DISABLE_COMMENTS', true ); // Disable Comments on the site.
define( 'ADD_FAVICON_TO_ATTACHMENTS', true ); // Add favicon to attachments. Should be always active but don't forget to set the correct path to favicon icon.
define( 'DISABLE_WP_ALL_THUMBNAILS_GENERATING', false ); // Completely disable WordPress Image Resizing. You may decide that it's not needed in your project.
define( 'DISABLE_WP_DEFAULT_THUMBNAILS_SIZES', true ); // Disable only certain WordPress thumbnails sizes. Customize it if needed.
define( 'DISABLE_WP_IMAGE_SCALING', false ); // Disable WordPress Image Scaling. By default WP limits height for you images to 256px. This filter removes it.
define( 'ADD_CUSTOM_HEADER_SUPPORT', true ); // Register shortcode for custom h1 heading for post and pages and automatically render if custom h1 is set.

/**
 * Plugins Customizations.
 *
 * @link https://docs.wprig.org/coming-soon
 */

define( 'CF7_DISABLE_DEFAULT_LOADING_JS_CSS', true ); // Contact Form 7: Disable automatic loading of CF7 .js and .css.
define( 'CF7_CLEAN_MARKUP', true ); // Contact Form 7: Remove <p> and <br> from Contact Form 7.
define( 'YOAST_BREADCRUMBS_ADD_SCHEMA', true ); // YoastSEO: Add Schema to YoastSEO breadcrumb.
define( 'YOAST_BREADCRUMBS_CLEAN_SPAN', true ); // YoastSEO: Filter the output of Yoast breadcrumbs to remove <span> tags added by the plugin.
define( 'YOAST_SEARCHACTION_JSON_DISABLE', true ); // YoastSEO: Remove SearchAction from yoast-schema-graph JSON structured data. If you don't have a working search on your site (for example you have a generated a static site from your WP).
define( 'ELEMENTOR_REMOVE_STANDART_FONTS', true ); // Elementor: Remove default fonts from loading.

/**
 * Optimization and code cleaning.
 *
 * @link https://docs.wprig.org/coming-soon
 */

define( 'DEFER_ALL_SCRIPTS', true ); // Defer all scripts (exl. jquery and wp-admin scripts).
define( 'DISABLE_SCRIPTS_STYLES_VERSIONS', false ); // Disable versions in the end of scripts and styles links.
define( 'DISABLE_TYPE_ATTRIBUTES', true ); // Disable type attributes from scripts and styles.
define( 'DISABLE_DEFAULT_BLOCK_STYLES', true ); // Remove default block styles.
define( 'DISABLE_WP_POLYFILL', true ); // Remove WP Polyfill js.

/**
 * Components settings.
 *
 * @link https://docs.wprig.org/coming-soon
 */

/**
 * ============
 * * Basic Customizations
 *
 * Must-have and not configurable through contsants above.
 * ============
 */

// Menu functions (registering new menus, menu walkers).
require get_template_directory() . '/functions/_functions-menu.php';


/**
 * ============
 * * Registeting new types
 *
 * Register new thumbnails size, shortcodes etc.
 * ============
 */

// Register new thumbnail sizes.
require get_template_directory() . '/functions/_functions-custom-thumbnails.php';

// Register new ACF fields.
if ( class_exists( 'ACF' ) ) {
	require get_template_directory() . '/functions/_functions-acf-fields.php';
}

// Register custom actions.
require get_template_directory() . '/functions/_functions-custom-actions-filters.php';

// Register custom shortcodes.
require get_template_directory() . '/functions/_functions-custom-shortcodes.php';

// Enqueue/dequeue custom files.
require get_template_directory() . '/functions/_functions-custom-files.php';

/**
 * ============
 * * WordPress Customizations
 * ============
 */
require get_template_directory() . '/functions/_functions-wp-customizations.php';

/**
 * ============
 * * Plugins Customizations
 * ============
 */
require get_template_directory() . '/functions/_functions-plugins.php';

/**
 * ============
 * * Optimization and Cleaning
 * ============
 */
require get_template_directory() . '/functions/_functions-optimization.php';
