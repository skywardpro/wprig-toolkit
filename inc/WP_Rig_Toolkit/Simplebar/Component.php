<?php
/**
 * WP_Rig\WP_Rig\WP_Rig_Toolkit\Simplebar\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\WP_Rig_Toolkit\Simplebar;

use WP_Rig\WP_Rig\Component_Interface;
use WP_Post;
use function WP_Rig\WP_Rig\wp_rig;
use function add_action;
use function add_filter;
use function wp_enqueue_script;
use function wp_register_script;
use function wp_print_scripts;
use function wp_enqueue_style;
use function wp_register_style;
use function wp_print_styles;
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
	public function get_slug(): string {
		return 'simplebar';
	}


	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'wp_enqueue_scripts', array( $this, 'action_activate_simplebar' ), 200 );
		add_action( 'wp_footer', array( $this, 'action_enqueue_simplebar_assets' ), 1 );
	}


	/**
	 * Registers scripts and styles for footer output.
	 */
	public function action_activate_simplebar() {
		// Register scripts for footer output.
		wp_register_script(
			'simplebar',
			get_theme_file_uri( '/assets/js/vendor/simplebar/simplebar.min.js' ),
			array(),
			wp_rig()->get_asset_version( get_theme_file_path( '/assets/js/vendor/simplebar/simplebar.min.js' ) ),
			false
		);

		wp_script_add_data( 'simplebar', 'defer', true );
		wp_script_add_data( 'simplebar', 'precache', true );

		// Register styles for footer output.
		wp_register_style(
			'simplebar',
			get_theme_file_uri( '/assets/css/vendor/simplebar/simplebar.min.css' ),
			array(),
			wp_rig()->get_asset_version( get_theme_file_path( '/assets/css/vendor/simplebar/simplebar.min.css' ) )
		);
	}

	/**
	 * Prints scripts and styles in the footer to avoid blocking page rendering.
	 */
	public function action_enqueue_simplebar_assets() {
		// Print scripts and styles in footer.
		wp_print_scripts( 'simplebar' );
		wp_print_styles( 'simplebar' );
	}
}
