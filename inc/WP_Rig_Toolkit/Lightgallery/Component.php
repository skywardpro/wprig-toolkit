<?php

/**
 * WP_Rig\WP_Rig\WP_Rig_Toolkit\Lightgallery\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\WP_Rig_Toolkit\Lightgallery;

use WP_Rig\WP_Rig\Component_Interface;
use WP_Post;
use function WP_Rig\WP_Rig\wp_rig;
use function add_action;
use function add_filter;
use function wp_enqueue_script;
use function wp_enqueue_style;
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
class Component implements Component_Interface
{

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug(): string
	{
		return 'lightgallery';
	}


	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize()
	{
		add_action('wp_enqueue_scripts', array($this, 'action_activate_lightgallery'), 200);
	}


	/**
	 * Enqueues scripts and styles.
	 */
	public function action_activate_lightgallery()
	{
		// Enqueue scripts.
		wp_enqueue_script(
			'lightgallery',
			get_theme_file_uri('/assets/js/vendor/lightgallery/lightgallery.umd.min.js'),
			array(),
			wp_rig()->get_asset_version(get_theme_file_path('/assets/js/vendor/lightgallery/lightgallery.umd.min.js')),
			true // Load in footer
		);

		wp_script_add_data('lightgallery', 'defer', true);
		wp_script_add_data('lightgallery', 'precache', true);

		// Enqueue styles.
		wp_enqueue_style(
			'lightgallery',
			get_theme_file_uri('/assets/css/vendor/lightgallery/lightgallery-bundle.min.css'),
			array(),
			wp_rig()->get_asset_version(get_theme_file_path('/assets/css/vendor/lightgallery/lightgallery-bundle.min.css'))
		);
	}
}
