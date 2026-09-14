<?php
/**
 * Registers optional WP_Rig_Toolkit components based on the ENABLE_* constants
 * defined in the root functions.php.
 *
 * inc/Theme.php's default component auto-discovery only scans one directory level
 * deep under inc/ (inc/{Name}/Component.php), so the WP_Rig_Toolkit/{Name}/Component.php
 * wrapper components are not picked up automatically. This file registers the enabled
 * ones through the `wprig_theme_components` filter instead.
 *
 * @link https://docs.wprig.org/wp-rig-toolkit-structure/functions.php
 *
 * @package wp_rig
 */

use WP_Rig\WP_Rig\WP_Rig_Toolkit;

add_filter('wprig_theme_components', function (array $components): array {
	if (defined('ENABLE_FRESH_URL') && ENABLE_FRESH_URL) {
		$components[] = new WP_Rig_Toolkit\Fresh_URL\Component();
	}

	if (defined('ENABLE_GSAP') && ENABLE_GSAP) {
		$plugins = defined('GSAP_ENABLED_PLUGINS') ? GSAP_ENABLED_PLUGINS : [];
		$components[] = new WP_Rig_Toolkit\GSAP\Component($plugins);
	}

	if (defined('ENABLE_TINGLE') && ENABLE_TINGLE) {
		$components[] = new WP_Rig_Toolkit\Tingle\Component();
	}

	if (defined('ENABLE_IMASK') && ENABLE_IMASK) {
		$components[] = new WP_Rig_Toolkit\iMask\Component();
	}

	if (defined('ENABLE_VALIDATEJS') && ENABLE_VALIDATEJS) {
		$components[] = new WP_Rig_Toolkit\Validatejs\Component();
	}

	if (defined('ENABLE_SWIPER') && ENABLE_SWIPER) {
		$components[] = new WP_Rig_Toolkit\Swiper\Component();
	}

	if (defined('ENABLE_LIGHTGALLERY') && ENABLE_LIGHTGALLERY) {
		$components[] = new WP_Rig_Toolkit\Lightgallery\Component();
	}

	if (defined('ENABLE_SIMPLEBAR') && ENABLE_SIMPLEBAR) {
		$components[] = new WP_Rig_Toolkit\Simplebar\Component();
	}

	if (defined('ENABLE_MASONRY') && ENABLE_MASONRY) {
		$components[] = new WP_Rig_Toolkit\Masonry\Component();
	}

	if (defined('ENABLE_SPLIT_TYPE') && ENABLE_SPLIT_TYPE) {
		$components[] = new WP_Rig_Toolkit\SplitType\Component();
	}

	if (defined('ENABLE_LISTJS') && ENABLE_LISTJS) {
		$components[] = new WP_Rig_Toolkit\Listjs\Component();
	}

	if (defined('ENABLE_TOCBOT') && ENABLE_TOCBOT) {
		$components[] = new WP_Rig_Toolkit\Tocbot\Component();
	}

	return $components;
});
