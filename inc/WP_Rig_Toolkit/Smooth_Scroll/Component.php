<?php
/**
 * WP_Rig\WP_Rig\WP_Rig_Toolkit\Smooth_Scroll\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\WP_Rig_Toolkit\Smooth_Scroll;

use WP_Rig\WP_Rig\Component_Interface;
use WP_Rig\WP_Rig\Templating_Component_Interface;
use function WP_Rig\WP_Rig\wp_rig;
use function add_action;
use function is_singular;
use function comments_open;
use function get_option;
use function wp_enqueue_script;
use function the_ID;
use function esc_attr;
use function wp_list_comments;
use function the_comments_navigation;
use function add_filter;
use function remove_filter;
use function esc_html_e;

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
		return 'smooth_scroll';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'wp_enqueue_scripts', array( $this, 'add_smooth_scroll' ) );
	}

	/**
	 * Enqueues scripts and styles.
	 */
	public function add_smooth_scroll( $arg ) {
		// If the AMP plugin is active, return early.
		if ( wp_rig()->is_amp() ) {
			return;
		}
			// Enqueue the cookies script.
			wp_register_script(
				'smooth-scroll',
				get_theme_file_uri( '/assets/js/vendor/smooth-scroll/smooth-scroll.min.js' ),
				array(),
				wp_rig()->get_asset_version( get_theme_file_path( '/assets/js/vendor/smooth-scroll/smooth-scroll.min.js' ) ),
				false
			);

			wp_enqueue_script( 'smooth-scroll' );

			wp_script_add_data( 'smooth-scroll', 'defer', true );
			wp_script_add_data( 'smooth-scroll', 'precache', true );

			// Make it accesible for configuration.
			wp_localize_script( 'smooth-scroll', 'smoothScrollConfig', array( 'smoothScrollEnabled' => __( 'true' ) ) );
	}
}
