<?php

/**
 * WP_Rig\WP_Rig\WP_Rig_Toolkit\GSAP\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\WP_Rig_Toolkit\GSAP;

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


class Component implements Component_Interface
{

	private array $enabled_plugins;

	public function __construct(array $enabled_plugins = [])
	{
		$this->enabled_plugins = $enabled_plugins;
	}

	public function get_slug(): string
	{
		return 'gsap';
	}

	public function initialize()
	{
		add_action('wp_enqueue_scripts', [$this, 'action_activate_gsap'], 200);
	}

	public function action_activate_gsap()
	{
		$this->enqueue_script('gsap', 'gsap/gsap.min.js');

		if (in_array('ss', $this->enabled_plugins)) {
			$this->enqueue_script('gsap-scroll-smoother', 'gsap/scroll-smoother.min.js', ['gsap']);
		}
		if (in_array('st', $this->enabled_plugins)) {
			$this->enqueue_script('gsap-scroll-trigger', 'gsap/scroll-trigger.min.js', ['gsap']);
		}
		if (in_array('sto', $this->enabled_plugins)) {
			$this->enqueue_script('gsap-scroll-to-plugin', 'gsap/scroll-to-plugin.min.js', ['gsap']);
		}
		if (in_array('ce', $this->enabled_plugins)) {
			$this->enqueue_script('gsap-custom-easy', 'gsap/custom-easy.min.js', ['gsap']);
		}
		if (in_array('ds', $this->enabled_plugins)) {
			$this->enqueue_script('gsap-draw-svg', 'gsap/draw-svg.min.js', ['gsap']);
		}
		if (in_array('fl', $this->enabled_plugins)) {
			$this->enqueue_script('gsap-flip', 'gsap/flip.min.js', ['gsap']);
		}
		if (in_array('stext', $this->enabled_plugins)) {
			$this->enqueue_script('gsap-split-text', 'gsap/split-text.min.js', ['gsap']);
		}
	}

	private function enqueue_script(string $handle, string $relative_path, array $deps = [])
	{
		$path = "/assets/js/vendor/{$relative_path}";
		$full_path = get_theme_file_path($path);
		if (file_exists($full_path)) {
			wp_enqueue_script(
				$handle,
				get_theme_file_uri($path),
				$deps,
				wp_rig()->get_asset_version($full_path),
				true
			);
			wp_script_add_data($handle, 'defer', true);
			wp_script_add_data($handle, 'precache', true);
		}
	}
}
