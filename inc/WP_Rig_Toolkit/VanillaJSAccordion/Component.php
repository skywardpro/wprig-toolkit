<?php
/**
 * WP_Rig\WP_Rig\WP_Rig_Toolkit\VanillaJSAccordion\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\WP_Rig_Toolkit\VanillaJSAccordion;

use WP_Rig\WP_Rig\Component_Interface;
use function WP_Rig\WP_Rig\wp_rig;
use function add_action;
use function add_filter;
use function wp_enqueue_script;
use function get_theme_file_uri;
use function get_theme_file_path;
use function wp_script_add_data;
use function wp_localize_script;

/**
 * Class for managing comments UI.
 *
 * Exposes template tags:
 * * `wp_rig()->the_comments( array $args = array() )`
 *
 * @link https://wordpress.org/plugins/amp/
 */
class Component implements Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'accordion';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'wp_enqueue_scripts', array( $this, 'action_enqueue_accordion_files' ) );
	}

	/**
	 * Gets template tags to expose as methods on the Template_Tags class instance, accessible through `wp_rig()`.
	 *
	 * @return array Associative array of $method_name => $callback_info pairs. Each $callback_info must either be
	 *               a callable or an array with key 'callable'. This approach is used to reserve the possibility of
	 *               adding support for further arguments in the future.
	 */
	public function template_tags() : array {
		return array(
			'use_accordion' => array( $this, 'use_accordion' ),
		);
	}

	/**
	 * Enqueues scripts and styles.
	 */
	public function action_enqueue_accordion_files() {
		// If the AMP plugin is active, return early.
		if ( wp_rig()->is_amp() ) {
			return;
		}

		// Enqueue styles.
		wp_enqueue_style(
			'vanilla-accordion',
			get_theme_file_uri( '/assets/js/vendor/vanilla-js-accordion/vanilla-js-accordion.min.css' ),
			array(),
			wp_rig()->get_asset_version( get_theme_file_path( '/assets/js/vendor/vanilla-js-accordion/vanilla-js-accordion.min.css' ) ),
		);
		wp_script_add_data( 'vanilla-accordion', 'defer', true );
		wp_script_add_data( 'vanilla-accordion', 'precache', true );

		// Enqueue the script.
		wp_enqueue_script(
			'vanilla-accordion',
			get_theme_file_uri( '/assets/js/vendor/vanilla-js-accordion/vanilla-js-accordion.min.js' ),
			array(),
			wp_rig()->get_asset_version( get_theme_file_path( '/assets/js/vendor/vanilla-js-accordion/vanilla-js-accordion.min.js' ) ),
			true
		);
		wp_script_add_data( 'vanilla-accordion', 'defer', true );
		wp_script_add_data( 'vanilla-accordion', 'precache', true );
	}
}
