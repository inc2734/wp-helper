<?php
/**
 * @package inc2734/wp-helper
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Inc2734\WP_Helper\Contract;

trait Editor {

	/**
	 * Return true when the page has block editor.
	 *
	 * @return boolean
	 */
	public static function is_block_editor() {
		return static::is_gutenberg_page()
					|| function_exists( '\use_block_editor_for_post' ) && \use_block_editor_for_post( get_post() );
	}

	/**
	 * Return true when active the Gutenberg plugin.
	 *
	 * @return boolean
	 */
	public static function is_gutenberg_page() {
		$post = get_post();
		if ( ! $post ) {
			return false;
		}

		return function_exists( '\is_gutenberg_page' ) && \is_gutenberg_page();
	}
}
