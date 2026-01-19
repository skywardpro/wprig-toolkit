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

    <div class="container">
        <div class="mx-auto" style="max-width: 600px;">
        <?php get_template_part('template-parts/forms/form-example'); ?>
        <div class="my-60">
        <?php get_template_part('template-parts/forms/form-example-floating-label'); ?>
        </div>
        
        </div>
    </div>

</main><!-- #primary -->
<?php
get_footer();
