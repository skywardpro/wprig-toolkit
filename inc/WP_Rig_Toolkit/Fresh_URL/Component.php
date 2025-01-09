<?php
/**
 * WP_Rig\WP_Rig\WP_Rig_Toolkit\Fresh_URL\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\WP_Rig_Toolkit\Fresh_URL;

use WP_Rig\WP_Rig\Component_Interface;
use function WP_Rig\WP_Rig\wp_rig;
use WP_Post;
use function add_action;
use function add_filter;
use function wp_enqueue_script;
use function get_theme_file_uri;
use function get_theme_file_path;
use function wp_script_add_data;
use function wp_localize_script;

class Component implements Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'fresh_url';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'wp_enqueue_scripts', array( $this, 'action_activate_fresh_url' ), 200 );
	}

	/**
	 * Enqueues a script.
	 */
	public function action_activate_fresh_url() {
		// If the AMP plugin is active, return early.
		if ( wp_rig()->is_amp() ) {
			return;
		}

		// Enqueue the Fresh_URL script.
		wp_enqueue_script(
			'fresh-url',
			get_theme_file_uri( '/assets/js/vendor/fresh-url/fresh-url.min.js' ),
			array(),
			wp_rig()->get_asset_version( get_theme_file_path( '/assets/js/vendor/fresh-url/fresh-url.min.js' ) ),
			false
		);
		wp_script_add_data( 'fresh-url', 'defer', true );
		wp_script_add_data( 'fresh-url', 'precache', true );

	}
}

