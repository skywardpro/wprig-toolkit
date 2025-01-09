<?php
/**
 * WP_Rig\WP_Rig\WP_Rig_Toolkit\Sticky_Header\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\WP_Rig_Toolkit\Sticky_Header;

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
		return 'sticky_header';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'wp_enqueue_scripts', array( $this, 'add_sticky_header' ) );
	}

	/**
	 * Enqueues scripts and styles.
	 */
	public function add_sticky_header( $arg ) {
		// If the AMP plugin is active, return early.
		if ( wp_rig()->is_amp() ) {
			return;
		}
			// Enqueue the cookies script.
			wp_register_script(
				'sticky-header',
				get_theme_file_uri( '/assets/js/vendor/sticky-header/sticky-header.min.js' ),
				array(),
				wp_rig()->get_asset_version( get_theme_file_path( '/assets/js/vendor/sticky-header/sticky-header.min.js' ) ),
				false
			);

			wp_enqueue_script( 'sticky-header' );

			wp_script_add_data( 'sticky-header', 'defer', true );
			wp_script_add_data( 'sticky-header', 'precache', true );
	}
}
