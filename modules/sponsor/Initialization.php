<?php

namespace modules\sponsor;

use WPKit\Fields\Select;
use WPKit\Module\AbstractInitialization;
use WPKit\PostType\MetaBox;
use WPKit\PostType\PostType;

/**
 * Class Initialization
 *
 * @package modules\sponsor
 */
class Initialization extends AbstractInitialization {
	const POST_TYPE = 'sponsor';

	/**
	 * @var PostType
	 */
	private $post_type;


	public function register_post_type() {
		$post_type = new PostType( self::POST_TYPE, __( 'Sponsor', 'colorrun' ) );
		$post_type->set_supports( [ 'title', 'thumbnail' ] );
		$post_type->set_menu_icon( 'dashicons-awards' );
		$post_type->set_publicly_queryable( false );
		$post_type->set_rewrite( false );
		$post_type->set_use_archive( false );
		$post_type->set_public( false );
		$post_type->set_show_in_nav_menus( false );
		$post_type->set_menu_position( 7 );
		$this->post_type = $post_type;
		$this->setup_columns();
		$this->add_meta();
	}

	private function setup_columns() {
		$this->post_type->add_column_thumbnail();
	}

	private function add_meta() {
		$meta = new MetaBox( self::POST_TYPE, __( 'Info', 'colorrun' ) );
		$meta->add_field( 'type', __( 'Type', 'colorrun' ), function () {
			$f = new Select();
			$f->set_options( [
				'large'    => __( 'Large' ),
				'medium' => __( 'Medium' ),
				'small'  => __( 'Small' )
			] );

			return $f;
		} );
		$meta->add_field( 'url', __( 'Url', 'colorrun' ), 'Url' );
		$meta->add_post_type( $this->post_type );
	}
}

