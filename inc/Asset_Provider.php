<?php

/**
 * WP_Rig\WP_Rig\Asset_Provider interface
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

/**
 * Interface for a theme component that provides assets.
 */
interface Asset_Provider
{
	/**
	 * Gets the asset manifest for the theme component.
	 *
	 * @return array {
	 *     Asset manifest.
	 *
	 *     @type array $styles {
	 *         Optional. List of style assets.
	 *
	 *         @type string   $file      Relative path from `assets/css/src/`.
	 *         @type bool     $global    Optional. Whether the file should immediately be enqueued. Default `false`.
	 *         @type callable $preload_callback Optional. Callback determining if the asset should be preloaded.
	 *         @type string   $media     Optional. Media for which the style is defined. Default `all`.
	 *         @type array    $deps      Optional. List of dependencies.
	 *     }
	 *     @type array $scripts {
	 *         Optional. List of script assets.
	 *
	 *         @type string $file    Relative path from `assets/js/src/`.
	 *         @type string $loading Optional. Loading strategy ('async', 'defer').
	 *         @type bool   $global  Optional. Whether the file should immediately be enqueued. Default `false`.
	 *         @type bool   $footer  Optional. If `true`, the script is loaded in the footer. Default `false`.
	 *         @type array  $deps    Optional. List of dependencies.
	 *     }
	 * }
	 */
	public function get_asset_manifest(): array;
}
