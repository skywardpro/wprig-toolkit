<?php
/**
 * WP_Rig\WP_Rig\WP_Rig_Toolkit\Splidejs\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\WP_Rig_Toolkit\Splidejs;

use WP_Rig\WP_Rig\Component_Interface;
use WP_Rig\WP_Rig\Templating_Component_Interface;
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
 * Class for adding slider.
 */
class Component implements Component_Interface, Templating_Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'splidejs';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'wp_enqueue_scripts', array( $this, 'use_splidejs' ) );
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
			'use_splidejs' => array( $this, 'use_splidejs' ),
		);
	}


	/**
	 * Enqueues scripts and styles.
	 */
	public function use_splidejs( $arg ) {
		// If the AMP plugin is active, return early.
		if ( wp_rig()->is_amp() ) {
			return;
		}
		// Enqueue script.
		wp_enqueue_script(
			'splidejs',
			get_theme_file_uri( '/assets/js/vendor/splidejs/splide.min.js' ),
			array(),
			wp_rig()->get_asset_version( get_theme_file_path( '/assets/js/vendor/splidejs/splide.min.js' ) ),
			false
		);
		wp_script_add_data( 'splidejs', 'defer', true );
		wp_script_add_data( 'splidejs', 'precache', true );

		// Enqueue styles.
		wp_enqueue_style(
			'splidejs',
			get_theme_file_uri( '/assets/css/vendor/splidejs/splide.min.css' ),
			array(),
			wp_rig()->get_asset_version( get_theme_file_path( '/assets/css/vendor/splidejs/splide.min.css' ) )
		);
	}
}
