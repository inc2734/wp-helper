<?php
/**
 * @package inc2734/wp-helper
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Inc2734\WP_Helper\Contract;

trait Assets {

	/**
	 * Generate script dependencies.
	 *
	 * @param array $maybe_dependencies An array of registered handles this stylesheet depends on.
	 * @return array
	 */
	public static function generate_script_dependencies( $maybe_dependencies ) {
		$dependencies = [];
		foreach ( $maybe_dependencies as $dependency ) {
			if ( ! wp_script_is( $dependency, 'enqueued' ) && ! wp_script_is( $dependency, 'registered' ) ) {
				continue;
			}
			$dependencies[] = $dependency;
		}
		return $dependencies;
	}

	/**
	 * Generate style dependencies.
	 *
	 * @param array $maybe_dependencies An array of registered handles this stylesheet depends on.
	 * @return array
	 */
	public static function generate_style_dependencies( $maybe_dependencies ) {
		$dependencies = [];
		foreach ( $maybe_dependencies as $dependency ) {
			if ( ! wp_style_is( $dependency, 'enqueued' ) && ! wp_style_is( $dependency, 'registered' ) ) {
				continue;
			}
			$dependencies[] = $dependency;
		}
		return $dependencies;
	}

	/**
	 * Returns main script handle.
	 *
	 * @return string
	 */
	public static function get_main_script_handle() {
		$handle = get_template();

		if ( is_child_theme() && file_exists( get_stylesheet_directory() . '/assets/js/app.min.js' ) ) {
			$handle = get_stylesheet();
		}

		return $handle;
	}

	/**
	 * Returns main style handle.
	 *
	 * @return string
	 */
	public static function get_main_style_handle() {
		$handle = get_template();

		if ( is_child_theme() && file_exists( get_stylesheet_directory() . '/assets/css/style.min.css' ) ) {
			$handle = get_stylesheet();
		}

		return $handle;
	}
}
