<?php

namespace modules\participant;

use modules\coupons\Coupon;
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

	const STATUS = [
		'NOT_PAYED'        => 0,
		'AWAITING_PAYMENT' => 1,
		'PAYED'            => 2,

	];
	/**
	 * @var PostType
	 */
	private $post_type;

	public function _add_action_add_meta_boxes() {
		remove_meta_box( 'slugdiv', [ self::POST_TYPE ], 'normal' );
	}

	public function admin_register_create_coupons_table() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();
		$table_name      = $wpdb->prefix . Coupon::TABLE;
		$sql             = "CREATE TABLE {$table_name} (
			  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `code` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
			  `amount` tinyint(4) DEFAULT NULL,
			  `used` int(11) DEFAULT '0',
			  `count` int(11) DEFAULT '1',
			  `type` tinyint(1) DEFAULT '0',
			  `status` tinyint(1) DEFAULT '1',
			  `created` datetime DEFAULT '0000-00-00 00:00:00',
			  PRIMARY KEY (`code`),
			  KEY `id` (`id`)
			) ENGINE=InnoDB {$charset_collate};";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	public function register_post_type() {
		$post_type = new PostType( self::POST_TYPE, __( 'Participant', TEXT_DOMAIN ) );
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
		$this->payment_info();
	}

	public function setup_columns() {
		$this->post_type->add_column( __( 'Distance', TEXT_DOMAIN ), function () {
			global $post;

			$distance = MetaBox::get( $post->ID, Initialization::POST_TYPE, 'distance' );
			echo get_the_title( $distance );
		} );
		$this->post_type->add_column( __( 'Bib', TEXT_DOMAIN ), function () {
			global $post;

			echo MetaBox::get( $post->ID, Initialization::POST_TYPE, 'bib' );
		} );
		$this->post_type->add_column( __( 'Email', TEXT_DOMAIN ), function () {
			global $post;

			echo MetaBox::get( $post->ID, Initialization::POST_TYPE, 'email' );
		} );
		$this->post_type->add_column( __( 'Status', TEXT_DOMAIN ), function () {
			global $post;

			$status = MetaBox::get( $post->ID, Initialization::POST_TYPE, 'status' );
			echo Functions::get_statuses()[ $status ];
		} );
	}

	public function participant_info() {
		$meta = new MetaBox( self::POST_TYPE, __( 'Details', TEXT_DOMAIN ) );
		$meta->add_field( 'firstname', __( 'First name', TEXT_DOMAIN ) );
		$meta->add_field( 'lastname', __( 'Last name', TEXT_DOMAIN ) );
		$meta->add_field( 'email', __( 'Email', TEXT_DOMAIN ), 'Email' );
		$meta->add_field( 'bib', __( 'Bib', TEXT_DOMAIN ), function () {
			global $post;
			if ( $event = Functions::get_event( $post->ID ) ) {

				$f    = new Select2();
				$bibs = \modules\distance\Functions::get_available_bib_list( $event );

				if ( $current_bib = Functions::get_bib( get_post()->ID ) ) {
					$bibs = array_merge( [ $current_bib => $current_bib ], $bibs );
				}
				$bibs = array_combine( $bibs, $bibs );
				$f->set_options( $bibs );
			} else {
				$f = new Text();
				$f->set_disabled( true );
				$f->set_value( 'Next free bib will be assigned after event choosen and participant saved' );
			}

			return $f;
		} );
		$meta->add_field( 'distance', __( 'Distance', TEXT_DOMAIN ), function () {
			$f       = new Select2();
			$options = \modules\distance\Functions::get_current_distances();
			$f->set_options( wp_list_pluck( $options->posts, 'post_title', 'ID' ) );

			return $f;
		} );
		$meta->add_field( 'status', __( 'Status', TEXT_DOMAIN ), function () {
			$f = new Select();
			$f->set_options( Functions::get_statuses() );

			return $f;
		} );

		$this->post_type->add_meta_box( $meta );
	}

	public function payment_info() {
		$meta = new MetaBox( 'payment', __( 'Payment', TEXT_DOMAIN ) );
		$this->post_type->add_meta_box( $meta );
	}

	public function admin_register_coupon_generator_page() {
		new ImportExportPage( 'import-export', __( 'Import/Export', TEXT_DOMAIN ), 'edit.php?post_type=participant' );
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
                    <option value=""><?php _e( 'Distance' ); ?></option>
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
			if ( isset( $_GET['post_type'] ) ) {
				$type = $_GET['post_type'];
			}
			if ( self::POST_TYPE == $type && is_admin() && $pagenow == 'edit.php' && isset( $_GET['distance'] ) && $_GET['distance'] != '' ) {
				$query->query_vars['meta_key']   = self::POST_TYPE . '_distance';
				$query->query_vars['meta_value'] = $_GET['distance'];
			}
		} );

	}

}

