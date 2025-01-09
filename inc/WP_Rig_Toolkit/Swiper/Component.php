<?php
/**
 * WP_Rig\WP_Rig\WP_Rig_Toolkit\Swiper\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\WP_Rig_Toolkit\Swiper;

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
		return 'swiper';
	}


	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'wp_enqueue_scripts', array( $this, 'action_activate_swiper' ), 200 );
	}


	/**
	 * Enqueues scripts and styles.
	 */
	public function action_activate_swiper() {
		// If the AMP plugin is active, return early.
		if ( wp_rig()->is_amp() ) {
			return;
		}

		// Enqueue scripts.
		wp_enqueue_script(
			'swiper',
			get_theme_file_uri( '/assets/js/vendor/swiper/swiper.min.js' ),
			array(),
			wp_rig()->get_asset_version( get_theme_file_path( '/assets/js/vendor/swiper/swiper.min.js' ) ),
			false
		);

		wp_script_add_data( 'swiper', 'defer', true );
		wp_script_add_data( 'swiper', 'precache', true );

		// Enqueue styles.
		wp_enqueue_style(
			'swiper',
			get_theme_file_uri( '/assets/css/vendor/swiper/swiper.min.css' ),
			array(),
			wp_rig()->get_asset_version( get_theme_file_path( '/assets/css/vendor/swiper/swiper.min.css' ) )
		);
	}
}
