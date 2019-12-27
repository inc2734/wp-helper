<?php
/**
 * @package inc2734/wp-helper
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Inc2734\WP_Helper\Contract;

trait Utility {

	/**
	 * Return custom post type names.
	 * This method only works correctly after the init hook.
	 *
	 * @return array
	 */
	public static function get_custom_post_types() {
		$post_types = wp_cache_get( 'inc2734-wp-helper-custom-post-types' );
		if ( is_array( $post_types ) ) {
			return $post_types;
		}

		$post_types = get_post_types(
			[
				'public'   => true,
				'_builtin' => false,
			]
		);

		$post_types = $post_types ? $post_types : [];

		wp_cache_set( 'inc2734-wp-helper-custom-post-types', $post_types );
		return $post_types;
	}

	/**
	 * Return custom taxonomy names.
	 * This method only works correctly after the init hook.
	 *
	 * @return array
	 */
	public static function get_taxonomies() {
		$taxonomies = wp_cache_get( 'inc2734-wp-helper-taxonomies' );
		if ( is_array( $taxonomies ) ) {
			return $taxonomies;
		}

		$taxonomies = get_taxonomies(
			[
				'public'   => true,
				'_builtin' => false,
			]
		);

		wp_cache_set( 'inc2734-wp-helper-taxonomies', $taxonomies );
		return $taxonomies;
	}
}
