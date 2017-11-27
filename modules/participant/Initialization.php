<?php

namespace modules\participant;

use WPKit\Fields\Select;
use WPKit\Fields\Select2;
use WPKit\Fields\Text;
use WPKit\Module\AbstractInitialization;
use WPKit\PostType\MetaBox;
use WPKit\PostType\PostType;

/**
 * Class Initialization
 *
 * @package modules\theme
 */
class Initialization extends AbstractInitialization {
	const POST_TYPE = 'participant';

	/**
	 * @var PostType
	 */
	private $post_type;

	public function register_post_type() {
		$post_type = new PostType( self::POST_TYPE, __( 'Participant', 'colorrun' ) );
		$post_type->set_supports( [ 'title' ] );
		$post_type->set_menu_icon( 'dashicons-universal-access' );
		$post_type->set_rewrite( false );
		$post_type->set_use_archive( false );
		$post_type->set_public( false );
		$post_type->set_show_in_nav_menus( false );
		$post_type->set_publicly_queryable( false );
		$post_type->set_menu_position( 5 );
		$this->post_type = $post_type;
		$this->setup_columns();
		$this->participant_info();
		$this->custom_info();
	}

	public function setup_columns() {
		$this->post_type->add_column( __( 'Distance', 'colorrun' ), function () {
			global $post;

			$distance = MetaBox::get( $post->ID, Initialization::POST_TYPE, 'distance' );
			echo get_the_title( $distance );
		} );
		$this->post_type->add_column( __( 'Bib', 'colorrun' ), function () {
			global $post;

			echo MetaBox::get( $post->ID, Initialization::POST_TYPE, 'bib' );
		} );
		$this->post_type->add_column( __( 'Email', 'colorrun' ), function () {
			global $post;

			echo MetaBox::get( $post->ID, Initialization::POST_TYPE, 'email' );
		} );
		$this->post_type->add_column( __( 'Status', 'colorrun' ), function () {
			global $post;

			$status = MetaBox::get( $post->ID, Initialization::POST_TYPE, 'status' );
			echo isset( Functions::get_statuses()[ $status ] ) ? Functions::get_statuses()[ $status ] : '';
		} );
	}

	public function participant_info() {
		$meta = new MetaBox( self::POST_TYPE, __( 'Details', 'colorrun' ) );
		$meta->add_field( 'firstname', __( 'First name', 'colorrun' ) );
		$meta->add_field( 'lastname', __( 'Last name', 'colorrun' ) );
		$meta->add_field( 'email', __( 'Email', 'colorrun' ), 'Email' );
		$meta->add_field( 'dateofbirth', __( 'Date of birth', 'colorrun' ), 'Date' );
		$meta->add_field( 'gender', __( 'Gender', 'colorrun' ), function () {
			$f = new Select();
			$f->set_options( [
				'male'   => __( 'Male', 'colorrun' ),
				'female' => __( 'Female', 'colorrun' ),
			] );

			return $f;
		} );
		$meta->add_field( 'country', __( 'Country', 'colorrun' ) );
		$meta->add_field( 'city', __( 'City', 'colorrun' ) );
		$meta->add_field( 'bib', __( 'Bib', 'colorrun' ), function () {
			global $post;
			if ( $distance = Functions::get_distance( $post->ID ) ) {
				$f    = new Select2();
				$bibs = \modules\distance\Functions::get_available_bib_list( $distance );
				if ( $current_bib = Functions::get_bib( $post->ID ) ) {
					$bibs = array_merge( [ $current_bib => $current_bib ], $bibs );
				}
				$bibs = array_combine( $bibs, $bibs );
				$f->set_options( $bibs );
			} else {
				$f = new Text();
				$f->set_disabled( true );
				$f->set_value( 'Next free bib will be assigned after event chosen and participant saved' );
			}

			return $f;
		} );
		$meta->add_field( 'distance', __( 'Distance', 'colorrun' ), function () {
			$f       = new Select2();
			$options = \modules\distance\Functions::get_current_distances();
			$f->set_options( wp_list_pluck( $options->posts, 'post_title', 'ID' ) );

			return $f;
		} );
		$meta->add_field( 'status', __( 'Status', 'colorrun' ), function () {
			$f = new Select();
			$f->set_options( Functions::get_statuses() );

			return $f;
		} );
		$meta->add_field( 'coupon', __( 'Coupon', 'colorrun' ), function () {
			$f = new Text();
			$f->set_attribute( 'readonly', true );
			$f->set_description( 'Only if used during order' );

			return $f;
		} );

		$this->post_type->add_meta_box( $meta );
	}

	public function custom_info() {
		$meta = new MetaBox( self::POST_TYPE . '_info', __( 'Additional info', 'colorrun' ) );
		foreach ( Functions::get_additional_fields() as $key => $title ) {
			$meta->add_field( $key, __( $title, 'colorrun' ) );
		}
		$meta->set_priority( 'high' );
		$meta->add_post_type( $this->post_type );
	}

	public function admin_register_coupon_generator_page() {
		new ImportExportPage( 'import-export', __( 'Import/Export', 'colorrun' ), 'edit.php?post_type=participant' );
	}

	public function admin_register_add_filters() {

		add_action( 'restrict_manage_posts', function () {
			if ( isset( $_GET['post_type'] ) ) {
				$type = $_GET['post_type'];
			}
			if ( self::POST_TYPE == $type ) {
				$values = wp_list_pluck( \modules\distance\Functions::get_current_distances()->posts, 'post_title', 'ID' );
				?>
                <select name="distance">
                    <option value=""><?php _e( 'Distance', 'colorrun' ); ?></option>
					<?php
					$current_v = isset( $_GET['distance'] ) ? $_GET['distance'] : '';
					foreach ( $values as $value => $label ) {
						printf
						(
							'<option value="%s"%s>%s</option>',
							$value,
							selected( $value, $current_v ),
							$label
						);
					}
					?>
                </select>
				<?php
			}
		} );


		add_filter( 'parse_query', function ( $query ) {
			global $pagenow;
			if ( isset( $_GET['post_type'] ) && self::POST_TYPE == $_GET['post_type'] && is_admin() && $pagenow == 'edit.php' && isset( $_GET['distance'] ) && $_GET['distance'] != '' ) {
				$query->query_vars['meta_key']   = self::POST_TYPE . '_distance';
				$query->query_vars['meta_value'] = $_GET['distance'];
			}
		} );

	}

}

