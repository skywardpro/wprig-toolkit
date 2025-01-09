<?php
/**
 * Auto creation of ACF fields functions
 *
 * Sometimes you want to register ACF fields automatically so you can register them here
 *
 * @link https://docs.wprig.org/wp-rig-toolkit-structure/functions.php
 *
 * @package wp_rig
 */

// Check if ACF exists.
if ( class_exists( 'ACF' ) ) {

	/**
	 * Register field for custom H1.
	 *
	 * Adds a custom field for overriding the default H1 tag.
	 */
	function add_custom_acf_post_and_page_options() {

		acf_add_local_field_group(
			array(
				'key'      => 'group_posts_and_pages_options',
				'title'    => 'Post & Pages Options',
				'fields'   => array(
					array(
						'key'          => 'field_custom_h1',
						'label'        => 'Custom H1',
						'name'         => 'custom_h1',
						'type'         => 'text',
						'instructions' => 'Fill this field if you want to override the default H1 tag (usually post or page name). Max length is 80 symbols but for SEO purposes it\'s better to use headers under 60-70.',
						'maxlength'    => '80',
					),
				),
				'location' => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => 'page',
						),
					),
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => 'post',
						),
					),
				),
			)
		);
	}

	add_action( 'acf/init', 'add_custom_acf_post_and_page_options' );
}
