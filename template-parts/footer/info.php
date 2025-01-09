<?php
/**
 * Template part for displaying the footer info
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

?>

<div class="site-info">
    <?php
    // Fetching the current year and site name with link to homepage
    echo '<div class="copyright">';
    echo '&copy; ' . date('Y') . ' <a href="' . esc_url(home_url('/')) . '">' . get_bloginfo('name') . '</a>. All rights reserved.';
    echo '</div><!-- .copyright -->';

    // Fetching the theme author from style.css
    $theme = wp_get_theme();
    $theme_author = $theme->get('Author');
    $theme_author_uri = $theme->get('AuthorURI');
    if ( $theme_author ) {
        echo '<div class="theme-author">';
        echo 'Developed by <a href="' . esc_url( $theme_author_uri ) . '">' . esc_html( $theme_author ) . '</a>.';
        echo '</div><!-- .theme-author -->';
    }
    ?>
</div><!-- .site-info -->


