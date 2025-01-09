<?php
/**
 * WP_Rig\WP_Rig\WP_Rig_Toolkit\Tingle\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\WP_Rig_Toolkit\Tingle;

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

/**
 * Class for managing comments UI.
 *
 * Exposes template tags:
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
		return 'popup-tingle';
	}


	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'wp_enqueue_scripts', array( $this, 'action_activate_popup_tingle' ), 200 );
	}


	/**
	 * Enqueues scripts and styles.
	 */
	public function action_activate_popup_tingle() {
		// If the AMP plugin is active, return early.
		if ( wp_rig()->is_amp() ) {
			return;
		}

		// Enqueue scripts.
		wp_enqueue_script(
			'popup__tingle',
			get_theme_file_uri( '/assets/js/vendor/tingle/tingle.min.js' ),
			array(),
			wp_rig()->get_asset_version( get_theme_file_path( '/assets/js/vendor/tingle/tingle.min.js' ) ),
			false
		);

		wp_script_add_data( 'popup__tingle', 'defer', true );
		wp_script_add_data( 'popup__tingle', 'precache', true );

		// Enqueue styles.
		wp_enqueue_style(
			'popup__tingle',
			get_theme_file_uri( '/assets/css/vendor/tingle/tingle.min.css' ),
			array(),
			wp_rig()->get_asset_version( get_theme_file_path( '/assets/css/vendor/tingle/tingle.min.css' ) )
		);

	}
}
