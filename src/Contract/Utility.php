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
	 * @param array $args An array of key => value arguments to match against the post type objects.
	 * @return array
	 */
	public static function get_custom_post_types( $args = [] ) {
		$args = array_merge(
			$args,
			[
				'public'   => true,
				'_builtin' => false,
			]
		);

		$post_types = get_post_types( $args );

		return $post_types ? $post_types : [];
	}

	/**
	 * Return custom taxonomy names.
	 * This method only works correctly after the init hook.
	 *
	 * @param array $args An array of key => value arguments to match against the taxonomy objects.
	 * @return array
	 */
	public static function get_taxonomies( $args = [] ) {
		$args = array_merge(
			$args,
			[
				'public'   => true,
				'_builtin' => false,
			]
		);

		return get_taxonomies( $args );
	}
}
