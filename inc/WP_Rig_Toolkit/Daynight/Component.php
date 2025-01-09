<?php
/**
 * WP_Rig\WP_Rig\WP_Rig_Toolkit\Daynight\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\WP_Rig_Toolkit\Daynight;

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
 * Class for adding daynight feature.
 */
class Component implements Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'daynight';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'wp_enqueue_scripts', array( $this, 'action_activate_daynight_mode' ) );
	}

	/**
	 * Enqueues scripts and styles.
	 */
	public function action_activate_daynight_mode() {
		// If the AMP plugin is active, return early.
		if ( wp_rig()->is_amp() ) {
		return;
		}
		if ( defined( 'WP_RIG_DAYNIGHT_DEFAULT_MODE' ) ) {
			if ( constant( 'WP_RIG_DAYNIGHT_DEFAULT_MODE' ) === 'day' || constant( 'WP_RIG_DAYNIGHT_DEFAULT_MODE' ) === 'night' ) {
				// Enqueue the cookies script.
				wp_enqueue_script(
					'vanilla-cookies',
					get_theme_file_uri( '/assets/js/vendor/vanilla-cookies/cookies.min.js' ),
					array(),
					wp_rig()->get_asset_version( get_theme_file_path( '/assets/js/vendor/vanilla-cookies/cookies.min.js' ) ),
					false
				);

				wp_script_add_data( 'vanilla_cookies', 'precache', true );

				// Enqueue the daynight script.
				wp_enqueue_script(
					'daynight-mode',
					get_theme_file_uri( '/assets/js/vendor/vanilla-cookies/daynight.min.js' ),
					array( 'vanilla-cookies' ),
					wp_rig()->get_asset_version( get_theme_file_path( '/assets/js/vendor/vanilla-cookies/daynight.min.js' ) ),
					false
				);
				wp_script_add_data( 'daynight-mode', 'defer', true );
				wp_script_add_data( 'daynight-mode', 'precache', true );
				wp_localize_script(
					'daynight-mode',
					'wpRigScreenReaderText',
					array(
						'switchtoday'   => __( 'Switch to Day mode', 'wp-rig' ),
						'switchtonight' => __( 'Switch to Night mode', 'wp-rig' ),
					)
				);
				wp_localize_script(
					'daynight-mode',
					'wpRigVars',
					array(
						'daynightDefaultMode'   => __( constant( 'WP_RIG_DAYNIGHT_DEFAULT_MODE' ), 'wp-rig' ),
					)
				);

				// Enqueue styles.
				wp_enqueue_style(
					'daynight',
					get_theme_file_uri( '/assets/css/components/daynight.min.css' ),
					array(),
					wp_rig()->get_asset_version( get_theme_file_path( '/assets/css/components/daynight.min.css' ) )
				);
			}
		}
	}
}
