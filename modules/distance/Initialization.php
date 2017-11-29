<?php

namespace modules\distance;

use WPKit\Fields\DateTime;
use WPKit\Fields\Select2;
use WPKit\Module\AbstractInitialization;
use WPKit\PostType\MetaBox;
use WPKit\PostType\MetaBoxRepeatable;
use WPKit\PostType\PostType;

/**
 * Class Initialization
 *
 * @package modules\theme
 */
class Initialization extends AbstractInitialization {
	const POST_TYPE = 'distance';

	/**
	 * @var PostType
	 */
	private $post_type;


	public function register_post_type() {
		$post_type = new PostType( self::POST_TYPE, __( 'Distance', 'colorrun' ) );
		$post_type->set_supports( [ 'title' ] );
		$post_type->set_menu_icon( 'dashicons-location-alt' );
		$post_type->set_publicly_queryable( false );
		$post_type->set_rewrite( false );
		$post_type->set_use_archive( false );
		$post_type->set_public( false );
		$post_type->set_exclude_from_search( true );
		$post_type->set_show_in_nav_menus( false );
		$post_type->set_menu_position( 8 );
		$this->post_type = $post_type;
		$this->setup_columns();
		$this->event_info();
		$this->distance_price();
	}

	public function setup_columns() {
		$this->post_type->add_column( __( 'Event Date', 'colorrun' ), function () {
			global $post;
			echo MetaBox::get( $post->ID, Initialization::POST_TYPE, 'date' );
		}, true, 2 );

		$this->post_type->add_column( __( 'Slots', 'colorrun' ), function () {
			global $post;
			$registered = \modules\distance\Functions::get_registered_for_distance_count( $post->ID );
			$slots      = Functions::get_total_slots( $post->ID );
			echo "{$registered} / {$slots}";
		} );
	}

	public function event_info() {
		$meta = new MetaBox( self::POST_TYPE, __( 'Details', 'colorrun' ) );
		$meta->set_context( 'side' );
		$meta->add_field( 'date', __( 'Start date', 'colorrun' ), 'Datetime' );
		$meta->add_field( 'distance', __( 'Distance', 'colorrun' ) );
		$meta->add_field( 'slots', __( 'Count of slots', 'colorrun' ), 'Number' );
		$meta->add_field( 'bib_from', __( 'Bib start', 'colorrun' ), 'Number' );
		$meta->add_field( 'event', __( 'Event', 'colorrun' ), function () {
			$f       = new Select2();
			$options = \modules\event\Functions::get_all_events();
			$f->set_options( wp_list_pluck( $options->posts, 'post_title', 'ID' ) );

			return $f;
		} );

		$this->post_type->add_meta_box( $meta );

		$m = new MetaBox( self::POST_TYPE . '_' . \modules\theme\Functions::get_current_language(), __( 'Content', 'colorrun' ) );
		$m->add_field( 'content', __( 'Content', 'colorrun' ), 'WPEditor' );
		$m->add_post_type( $this->post_type );
	}

	public function distance_price() {
		$meta = new MetaBoxRepeatable( Initialization::POST_TYPE . '_price', __( 'Price', 'colorrun' ) );
		$meta->add_field( 'date', __( 'Period end date', 'colorrun' ), function () {
			$f = new DateTime();
			$f->set_attribute( 'data-format', 'yyyy-mm-dd' );
			$f->set_attribute( 'data-pick-time', 'false' );
			$f->set_placeholder( 'yyyy-mm-dd' );

			return $f;
		} );
		$meta->add_field( 'fee', __( 'Price in this period', 'colorrun' ), 'Number' );
		$meta->set_priority( 'high' );
		$meta->add_post_type( $this->post_type );
	}

	/*public function add_filter_pll_get_post_types( $post_types, $is_settings ) {
		if ( ! $is_settings ) {
			$post_types[ self::POST_TYPE ] = self::POST_TYPE;
		}

		return $post_types;
	}*/

	/**
	 * @param $wp_query \WP_Query
	 */
	public function add_filter_pre_get_posts( $wp_query ) {
		if ( $wp_query->is_main_query() && is_admin() && get_post_type() == Initialization::POST_TYPE ) {
			$wp_query->set( 'orderby', 'meta_value' );
			$wp_query->set( 'order', 'DESC' );
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

