<?php
/**
 * WP_Rig\WP_Rig\WP_Rig_Toolkit\Tocbot\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\WP_Rig_Toolkit\Tocbot;

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
 * Class for managing Table of contents
 */
class Component implements Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'table-of-contents__tocbot';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'wp_enqueue_scripts', array( $this, 'action_enqueue_toc_files' ) );
	}

	/**
	 * Enqueues scripts and styles.
	 */
	public function action_enqueue_toc_files() {
		// If the AMP plugin is active, return early.
		if ( wp_rig()->is_amp() ) {
			return;
		}

		if (is_singular( 'blog_post' ) ) {
			// Enqueue script.
			wp_enqueue_script(
				'table_of_contents__tocbot',
				get_theme_file_uri( '/assets/js/vendor/tocbot/tocbot.min.js' ),
				array(),
				wp_rig()->get_asset_version( get_theme_file_path( '/assets/js/vendor/tocbot/tocbot.min.js' ) ),
				false
			);
		}

		wp_script_add_data( 'table_of_contents__tocbot', 'defer', true );
		wp_script_add_data( 'table_of_contents__tocbot', 'precache', true );

		if (is_singular( 'blog_post' ) ) {
			// Enqueue styles.
			wp_enqueue_style(
				'table_of_contents__tocbot',
				get_theme_file_uri( '/assets/css/vendor/tocbot/tocbot.min.css' ),
				array(),
				wp_rig()->get_asset_version( get_theme_file_path( '/assets/css/vendor/tocbot/tocbot.min.css' ) )
			);
		}
	}
}
