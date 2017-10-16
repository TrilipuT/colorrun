<?php

namespace modules\event;

use WPKit\Module\AbstractInitialization;
use WPKit\PostType\MetaBox;
use WPKit\PostType\PostType;

/**
 * Class Initialization
 *
 * @package modules\theme
 */
class Initialization extends AbstractInitialization {
	const POST_TYPE = 'event';

	/**
	 * @var PostType
	 */
	private $post_type;

	public function register_post_type() {
		$post_type = new PostType( self::POST_TYPE, __( 'Event', 'colorrun' ) );
		$post_type->set_supports( [ 'title' ] );
		$post_type->set_menu_icon( 'dashicons-calendar' );
		$post_type->set_rewrite( false );
		$post_type->set_publicly_queryable( false );
		$post_type->set_use_archive( false );
		$post_type->set_public( false );
		$post_type->set_show_in_nav_menus( false );
		$post_type->set_menu_position( 9 );
		$this->post_type = $post_type;
		$this->event_info();
		$this->setup_columns();
	}

	public function event_info() {
		$meta = new MetaBox( self::POST_TYPE, __( 'Details', 'colorrun' ) );
		$meta->add_field( 'date', __( 'Event date', 'colorrun' ), 'DateTime' );

		$this->post_type->add_meta_box( $meta );
	}

	public function setup_columns() {
		$this->post_type->add_column( __( 'Event Date', 'colorrun' ), function () {
			global $post;
			echo MetaBox::get( $post->ID, Initialization::POST_TYPE, 'date' );

		}, true, 2 );
	}

	/**
	 * @param $wp_query \WP_Query
	 */
	public function add_filter_pre_get_posts( $wp_query ) {
		if ( $wp_query->is_main_query() && is_admin() && get_post_type() == Initialization::POST_TYPE ) {
			$wp_query->set( 'orderby', 'meta_value' );
		}
	}

	public function admin_register_remove_columns() {
		add_filter( "manage_posts_columns", function ( $columns ) {
			if ( isset( $_GET['post_type'] ) && $_GET['post_type'] == self::POST_TYPE ) {
				unset( $columns['date'] );
			}

			return $columns;
		} );
	}
}

