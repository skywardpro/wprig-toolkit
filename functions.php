<?php
/**
 * WP Rig Toolkit functions and definitions
 *
 * DO NOT add ad-hoc hooks/features directly to this file's body beyond the
 * constant toggles below. See AGENTS.md for the toolkit's conventions
 * (feature-flag constants, `WP_Rig_Toolkit/*` components, `npm run create-rig-component`).
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

define('STOP_ADDING_P_TAGS', true); // Stop WordPress from adding <p> tags.
define('DISABLE_GUTENBERG', true); // Disable Gutenberg editor.
define('DISABLE_VISUAL_EDITOR', false); // Disable Visual editor completely.
define('DISABLE_VISUAL_EDITOR_ADMINS_ONLY', false); // Disable Visual editor only for admins.
define('HEADERS_CLEAN_ARCHIVE_CATEGORY', true); // Clean page headers from "Archive:" and "Category:".
define('ADD_PAGE_EXCERPTS_SUPPORT', true); // Add Post Excerpts Support for Pages.
define('DISABLE_COMMENTS', true); // Disable Comments on the site.
define('ADD_FAVICON_TO_ATTACHMENTS', true); // Add favicon to attachments. Should be always active but don't forget to set the correct path to favicon icon.
define('DISABLE_WP_ALL_THUMBNAILS_GENERATING', false); // Completely disable WordPress Image Resizing. You may decide that it's not needed in your project.
define('DISABLE_WP_DEFAULT_THUMBNAILS_SIZES', true); // Disable only certain WordPress thumbnails sizes. Customize it if needed.
define('DISABLE_WP_IMAGE_SCALING', false); // Disable WordPress Image Scaling. By default WP limits height for you images to 256px. This filter removes it.
define('ADD_CUSTOM_HEADER_SUPPORT', true); // Register shortcode for custom h1 heading for post and pages and automatically render if custom h1 is set.

/**
 * Plugins Customizations.
 *
 * @link https://docs.wprig.org/coming-soon
 */

define('CF7_DISABLE_DEFAULT_LOADING_JS_CSS', true); // Contact Form 7: Disable automatic loading of CF7 .js and .css.
define('CF7_CLEAN_MARKUP', true); // Contact Form 7: Remove <p> and <br> from Contact Form 7.
define('YOAST_BREADCRUMBS_ADD_SCHEMA', true); // YoastSEO: Add Schema to YoastSEO breadcrumb.
define('YOAST_BREADCRUMBS_CLEAN_SPAN', true); // YoastSEO: Filter the output of Yoast breadcrumbs to remove <span> tags added by the plugin.
define('YOAST_SEARCHACTION_JSON_DISABLE', true); // YoastSEO: Remove SearchAction from yoast-schema-graph JSON structured data. If you don't have a working search on your site (for example you have a generated a static site from your WP).
define('ELEMENTOR_REMOVE_STANDART_FONTS', true); // Elementor: Remove default fonts from loading.

/**
 * Optimization and code cleaning.
 *
 * @link https://docs.wprig.org/coming-soon
 */

define('DEFER_ALL_SCRIPTS', true); // Defer all scripts (exl. jquery and wp-admin scripts).
define('DISABLE_SCRIPTS_STYLES_VERSIONS', false); // Disable versions in the end of scripts and styles links.
define('DISABLE_TYPE_ATTRIBUTES', true); // Disable type attributes from scripts and styles.
define('DISABLE_DEFAULT_BLOCK_STYLES', true); // Remove default block styles.
define('DISABLE_WP_POLYFILL', true); // Remove WP Polyfill js.

/**
 * WP_Rig_Toolkit component toggles.
 *
 * These components live one directory level deeper than WP Rig's core components
 * (inc/WP_Rig_Toolkit/{Name}/Component.php), so Theme::get_default_components()'s
 * auto-discovery scan does not pick them up automatically. Toggle them here instead;
 * see functions/_functions-toolkit-components.php for the registration logic.
 */
define('ENABLE_FRESH_URL', false);
define('ENABLE_GSAP', false);
define('ENABLE_TINGLE', false);
define('ENABLE_IMASK', true);
define('ENABLE_VALIDATEJS', true);
define('ENABLE_SWIPER', false);
define('ENABLE_LIGHTGALLERY', false);
define('ENABLE_SIMPLEBAR', false);
define('ENABLE_MASONRY', false);
define('ENABLE_SPLIT_TYPE', false);
define('ENABLE_LISTJS', false);
define('ENABLE_TOCBOT', false);

/**
 * ============
 * * Basic Customizations
 *
 * Must-have and not configurable through constants above.
 * ============
 */

// Menu functions (registering new menus, menu walkers).
require get_template_directory() . '/functions/_functions-menu.php';

/**
 * ============
 * * Registering new types
 *
 * Register new thumbnails size, shortcodes etc.
 * ============
 */

// Register new thumbnail sizes.
require get_template_directory() .
	'/functions/_functions-custom-thumbnails.php';

// Register new ACF fields.
if (class_exists('ACF')) {
	require get_template_directory() . '/functions/_functions-acf-fields.php';
}

// Register custom actions.
require get_template_directory() .
	'/functions/_functions-custom-actions-filters.php';

// Register custom shortcodes.
require get_template_directory() .
	'/functions/_functions-custom-shortcodes.php';

// Enqueue/dequeue custom files.
require get_template_directory() . '/functions/_functions-custom-files.php';

// Register custom functions.
require get_template_directory() . '/functions/_functions-custom-functions.php';

// Register optional WP_Rig_Toolkit components (see ENABLE_* constants above).
require get_template_directory() . '/functions/_functions-toolkit-components.php';

// Register Schema.org structured data.
require get_template_directory() . '/functions/_functions-schema-org.php';

/**
 * ============
 * * WordPress Customizations
 * ============
 */
require get_template_directory() .
	'/functions/_functions-wp-customizations.php';

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

// @dev-only:start
/**
 * Load development-only helpers (LiveReload for dev proxy).
 * This file resides under optional/ and is not bundled for production.
 */
$__wprig_dev_helpers =
	get_template_directory() . '/optional/dev/dev-proxy-livereload.php';
if (file_exists($__wprig_dev_helpers)) {
	require_once $__wprig_dev_helpers;
}
// @dev-only:end
