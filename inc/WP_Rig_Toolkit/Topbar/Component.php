<?php
/**
 * WP_Rig\WP_Rig\WP_Rig_Toolkit\Topbar\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\WP_Rig_Toolkit\Topbar;

use WP_Rig\WP_Rig\Component_Interface;
use WP_Rig\WP_Rig\Templating_Component_Interface;
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
		return 'topbar';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'wp_enqueue_scripts', array( $this, 'action_activate_topbar' ), 200 );
	}

	/**
	 * Enqueues scripts and styles.
	 */
	public function action_activate_topbar() {
		// If the AMP plugin is active, return early.
		if ( wp_rig()->is_amp() ) {
			return;
		}

			// Enqueue the cookies script.
			wp_enqueue_script(
				'vanilla_cookies',
				get_theme_file_uri( '/assets/js/vendor/vanilla-cookies/cookies.min.js' ),
				array(),
				wp_rig()->get_asset_version( get_theme_file_path( '/assets/js/vendor/vanilla-cookies/cookies.min.js' ) ),
				false
			);

			wp_script_add_data( 'vanilla_cookies', 'precache', true );

			// Enqueue the topbar script.
			wp_enqueue_script(
				'topbar',
				get_theme_file_uri( '/assets/js/vendor/topbar/topbar.min.js' ),
				array(),
				wp_rig()->get_asset_version( get_theme_file_path( '/assets/js/vendor/topbar/topbar.min.js' ) ),
				false
			);

			wp_script_add_data( 'topbar', 'defer', true );
			wp_script_add_data( 'topbar', 'precache', true );
			wp_script_add_data( 'topbar', 'data-swup-ignore-script', true );
	}
}
