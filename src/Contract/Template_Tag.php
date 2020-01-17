<?php
/**
 * @package inc2734/wp-helper
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Inc2734\WP_Helper\Contract;

trait Template_Tag {

	/**
	 * Display the site logo or the site title
	 *
	 * @return void
	 */
	public static function the_site_branding() {
		?>
		<?php if ( has_custom_logo() ) : ?>
			<?php the_custom_logo(); ?>
		<?php else : ?>
			<a href="<?php echo esc_url( home_url() ); ?>"><?php bloginfo( 'name' ); ?></a>
		<?php endif; ?>
		<?php
	}

	/**
	 * Return pure post content
	 *
	 * @return string
	 */
	public static function get_pure_post_content() {
		$post = get_post();

		if ( ! $post || ! isset( $post->post_content ) ) {
			return;
		}

		if ( post_password_required( $post ) ) {
			return;
		}

		$extended = get_extended( $post->post_content );
		$content  = $extended['main'];
		$content  = do_blocks( $content );
		return $content;
	}

	/**
	 * Return pure trim excerpt
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_trim_excerpt/
	 *
	 * @return string
	 */
	public static function pure_trim_excerpt() {
		$raw_excerpt = '';

		$text = static::get_pure_post_content();
		$text = strip_shortcodes( $text );
		$text = str_replace( ']]>', ']]&gt;', $text );

		// phpcs:disable WordPress.WP.I18n.MissingArgDomain
		$excerpt_length = intval( _x( '55', 'excerpt_length' ) );
		// phpcs:enable
		$excerpt_length = apply_filters( 'excerpt_length', $excerpt_length );
		$excerpt_more   = apply_filters( 'excerpt_more', ' [&hellip;]' );

		$text = wp_trim_words( $text, $excerpt_length, $excerpt_more );

		return apply_filters( 'wp_trim_excerpt', $text, $raw_excerpt );
	}

	/**
	 * Return true when the sidebar is registerd and active
	 *
	 * @param string $sidebar_id
	 * @return boolean
	 */
	public static function is_active_sidebar( $sidebar_id ) {
		return is_active_sidebar( $sidebar_id ) && is_registered_sidebar( $sidebar_id );
	}
}
