<?php

/**
 * WP_Rig\WP_Rig\Versioning_Trait trait
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

/**
 * Trait for components that need versioning methods.
 */
trait Versioning_Trait
{
	/**
	 * Gets the theme version.
	 *
	 * @return string Theme version number.
	 */
	public function get_version(): string
	{
		static $theme_version = null;

		if (null === $theme_version) {
			$theme_version = wp_get_theme()->get('Version');
		}

		return $theme_version;
	}

	/**
	 * Gets the version for a given asset.
	 *
	 * Returns filemtime when in local or debug mode, otherwise the theme version.
	 *
	 * @param string $filepath Asset file path.
	 * @return string Asset version number.
	 */
	public function get_asset_version(string $filepath): string
	{
		if ($this->is_debug() && file_exists($filepath)) {
			return (string) filemtime($filepath);
		}

		return $this->get_version();
	}

	/**
	 * Checks if the current environment is local or debug mode.
	 *
	 * @return bool True if local or debug, false otherwise.
	 */
	public function is_debug(): bool
	{
		static $is_debug = null;

		if (null === $is_debug) {
			$env_type = function_exists('wp_get_environment_type')
				? wp_get_environment_type()
				: (defined('WP_ENVIRONMENT_TYPE')
					? WP_ENVIRONMENT_TYPE
					: 'production');
			$is_debug = 'local' === $env_type || (defined('WP_DEBUG') && WP_DEBUG);
		}

		return $is_debug;
	}
}
