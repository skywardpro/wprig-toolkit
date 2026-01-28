<?php

/**
 * Render your site front page, whether the front page displays the blog posts index or a static page.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#front-page-display
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

get_header();

// Use grid layout if blog index is displayed.
if (is_home()) {
	wp_rig()->print_styles('wp-rig-content', 'wp-rig-front-page');
} else {
	wp_rig()->print_styles('wp-rig-content');
}
?>
<main id="primary" class="site-main">
  <h1 class=" typo--body">Front Page 1</h1>
  <?php wp_rig()->the_svg_icon_from_sprite('baths', [
  	'width' => '30',
  	'height' => '30',
  	'style' => 'fill: red; margin-right: 10px;',
  ]); ?>


  <svg width="20" height="20" style="fill: red;">
    <use href="<?php echo esc_url(
    	get_stylesheet_directory_uri(),
    ); ?>/assets/images/icons/sprite-svg/sprite.svg?ver=<?php echo esc_attr(
	wp_get_theme()->get('Version'),
); ?>#icon-baths"></use>
  </svg>
  <svg width="20" height="20" style="fill: red;">
    <use href="<?php echo esc_url(
    	get_stylesheet_directory_uri(),
    ); ?>/assets/images/icons/sprite-svg/sprite.svg?ver=<?php echo esc_attr(
	wp_get_theme()->get('Version'),
); ?>#advantage-wifi"></use>
  </svg>
  <svg width="20" height="20" style="fill: red;">
    <use href="<?php echo esc_url(
    	get_stylesheet_directory_uri(),
    ); ?>/assets/images/icons/sprite-svg/sprite.svg?ver=<?php echo esc_attr(
	wp_get_theme()->get('Version'),
); ?>#close-icon"></use>
  </svg>

  <div class="container">
    <?php get_template_part('template-parts/forms/form-example'); ?>
  </div>
</main><!-- #primary -->
<?php get_footer();
