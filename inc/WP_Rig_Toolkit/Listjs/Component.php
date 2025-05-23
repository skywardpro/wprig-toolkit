<?php
/**
 * WP_Rig\WP_Rig\WP_Rig_Toolkit\Listjs\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\WP_Rig_Toolkit\Listjs;

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
		return 'listjs';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'wp_enqueue_scripts', array( $this, 'use_listjs' ) );
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
			'use_listjs' => array( $this, 'use_listjs' ),
		);
	}


	/**
	 * Enqueues scripts and styles.
	 */
	public function use_listjs( $arg ) {
		// Enqueue script.
		wp_enqueue_script(
			'listjs',
			get_theme_file_uri( '/assets/js/vendor/listjs/list.min.js' ),
			array(),
			wp_rig()->get_asset_version( get_theme_file_path( '/assets/js/vendor/listjs/list.min.js' ) ),
			false
		);
		wp_script_add_data( 'listjs', 'defer', true );
		wp_script_add_data( 'listjs', 'precache', true );
	}
}
