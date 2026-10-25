<?php

use Inc2734\WP_Helper\Helper;

class Helper_Test extends WP_UnitTestCase {

	public function test_script_dependencies_only_include_available_handles() {
		wp_register_script( 'wp-helper-registered', false );
		wp_register_script( 'wp-helper-enqueued', false );
		wp_enqueue_script( 'wp-helper-enqueued' );

		$this->assertSame(
			[ 'wp-helper-registered', 'wp-helper-enqueued' ],
			Helper::generate_script_dependencies( [ 'wp-helper-missing', 'wp-helper-registered', 'wp-helper-enqueued' ] )
		);

		wp_deregister_script( 'wp-helper-registered' );
		wp_dequeue_script( 'wp-helper-enqueued' );
		wp_deregister_script( 'wp-helper-enqueued' );
	}

	public function test_style_dependencies_only_include_available_handles() {
		wp_register_style( 'wp-helper-registered', false );
		wp_register_style( 'wp-helper-enqueued', false );
		wp_enqueue_style( 'wp-helper-enqueued' );

		$this->assertSame(
			[ 'wp-helper-registered', 'wp-helper-enqueued' ],
			Helper::generate_style_dependencies( [ 'wp-helper-missing', 'wp-helper-registered', 'wp-helper-enqueued' ] )
		);

		wp_deregister_style( 'wp-helper-registered' );
		wp_dequeue_style( 'wp-helper-enqueued' );
		wp_deregister_style( 'wp-helper-enqueued' );
	}

	public function test_child_pages_are_published_and_sorted_by_menu_order() {
		$parent = self::factory()->post->create( [ 'post_type' => 'page' ] );
		$second = self::factory()->post->create( [ 'post_type' => 'page', 'post_parent' => $parent, 'menu_order' => 2 ] );
		$first  = self::factory()->post->create( [ 'post_type' => 'page', 'post_parent' => $parent, 'menu_order' => 1 ] );
		self::factory()->post->create( [ 'post_type' => 'page', 'post_parent' => $parent, 'post_status' => 'draft' ] );
		self::factory()->post->create( [ 'post_type' => 'page' ] );

		$query = Helper::get_child_pages_query( $parent );

		$this->assertSame( [ $first, $second ], wp_list_pluck( $query->posts, 'ID' ) );
	}

	public function test_related_posts_share_terms_and_exclude_the_current_post() {
		$category = self::factory()->category->create();
		$current  = self::factory()->post->create( [ 'post_category' => [ $category ] ] );
		$related  = self::factory()->post->create( [ 'post_category' => [ $category ] ] );
		self::factory()->post->create();

		$query = Helper::get_related_posts_query( $current );

		$this->assertSame( [ $related ], wp_list_pluck( $query->posts, 'ID' ) );
	}

	public function test_related_posts_return_null_for_a_missing_post() {
		$this->assertNull( Helper::get_related_posts_query( -1 ) );
	}

	public function test_custom_post_types_only_include_public_custom_types() {
		register_post_type( 'wp_helper_public', [ 'public' => true ] );
		register_post_type( 'wp_helper_private', [ 'public' => false ] );

		$post_types = Helper::get_custom_post_types();

		$this->assertContains( 'wp_helper_public', $post_types );
		$this->assertNotContains( 'wp_helper_private', $post_types );
		$this->assertNotContains( 'post', $post_types );

		unregister_post_type( 'wp_helper_public' );
		unregister_post_type( 'wp_helper_private' );
	}

	public function test_taxonomies_only_include_public_custom_taxonomies() {
		register_taxonomy( 'wp_helper_public', 'post', [ 'public' => true ] );
		register_taxonomy( 'wp_helper_private', 'post', [ 'public' => false ] );

		$taxonomies = Helper::get_taxonomies();

		$this->assertContains( 'wp_helper_public', $taxonomies );
		$this->assertNotContains( 'wp_helper_private', $taxonomies );
		$this->assertNotContains( 'category', $taxonomies );

		unregister_taxonomy( 'wp_helper_public' );
		unregister_taxonomy( 'wp_helper_private' );
	}

	public function test_post_content_stops_at_the_more_tag() {
		$post_id = self::factory()->post->create( [ 'post_content' => 'Introduction<!--more-->Remaining content' ] );
		$this->go_to( get_permalink( $post_id ) );

		$this->assertSame( 'Introduction', Helper::get_pure_post_content() );
	}
}
