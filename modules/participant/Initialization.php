<?php

namespace modules\participant;

use WPKit\Fields\Button;
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
		$this->payment_info();
		$this->custom_info();
		$this->actions_metabox();
	}

	public function setup_columns() {
		$this->post_type->add_column( [ 'distance', __( 'Distance', 'colorrun' ) ], function () {
			global $post;

			$distance = MetaBox::get( $post->ID, Initialization::POST_TYPE, 'distance' );
			echo get_the_title( $distance );
		} );
		$this->post_type->add_column( [ 'bib', __( 'Bib', 'colorrun' ) ], function () {
			global $post;

			echo MetaBox::get( $post->ID, Initialization::POST_TYPE, 'bib' );
		} );
		$this->post_type->add_column( [ 'email', __( 'Email', 'colorrun' ) ], function () {
			global $post;

			echo MetaBox::get( $post->ID, Initialization::POST_TYPE, 'email' );
		} );
		$this->post_type->add_column( [ 'status', __( 'Status', 'colorrun' ) ], function () {
			global $post;

			$status = MetaBox::get( $post->ID, Initialization::POST_TYPE, 'status' );
			echo isset( \modules\payment\Functions::get_statuses()[ $status ] ) ? \modules\payment\Functions::get_statuses()[ $status ] : '';
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
				array_unshift( $bibs, '' );
				$bibs     = array_combine( $bibs, $bibs );
				$bibs[''] = '-- No bib --';
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
			$f->set_options( \modules\payment\Functions::get_statuses() );

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

	public function payment_info() {
		$meta = new MetaBox( self::POST_TYPE . '_payment', __( 'Payment', 'colorrun' ) );
		$meta->set_context( 'side' );
		$meta->add_field( 'event', __( 'Log', 'colorrun' ), function () {
			$f = new Text();
			$f->set_attribute( 'style', 'display: none;' );
			$info = \modules\logger\Functions::get_log_by_participant( get_the_ID() );
			$text = '';
			foreach ( $info as $row ) {
				$time = date_i18n( 'd.m.Y H:i', $row['time'] );
				$text .= "{$time}: <b>{$row['message']}</b><br>";
			}
			$p = new Participant( get_the_ID() );
			if ( $p->payment ) {
				$text .= '<hr><b>Payment details</b><br><br>';
				foreach ( $p->payment as $key => $value ) {
					$text .= "{$key}: <b>{$value}</b><br>";
				}
				$f->set_description( $text );
			}

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

	public function actions_metabox() {
		$actions = new MetaBox( self::POST_TYPE . '_actions', __( 'Actions', 'colorrun' ) );
		$actions->set_context( 'side' );
		$actions->set_priority( 'core' );
		$post_id = isset( $_GET['post'] ) ? $_GET['post'] : get_the_ID();
		// We need this in case of bulk actions. They are also fires this function.
		if ( is_array( $post_id ) ) {
			$post_id = $post_id[0];
		}
		$participant = new Participant( $post_id );
		$actions->add_field( 'resend_email', sprintf( __( 'Send registration email to %s', 'colorrun' ), $participant->email ), function () {
			$f = new Button();
			$f->set_classes( [ 'button', 'button-primary' ] );
			$f->set_text( __( 'Send email', 'colorrun' ) );
			$f->set_attribute( 'data-post_id', get_the_ID() );
			$f->set_description( sprintf( __( 'Email will be send in %s', 'colorrun' ), pll_current_language( 'name' ) ) );

			return $f;
		} );
		add_action( 'wp_ajax_' . self::POST_TYPE . '_actions_resend_email', function () {
			$id = $_POST['post_id'];

			$participant = new Participant( $id );
			if ( ! $participant->send_notification_email() ) {
				wp_send_json_error( 'Some problem with sending email' );
			}
			wp_send_json_success();
		} );

		$this->post_type->add_meta_box( $actions );
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
				$current_event    = isset( $_GET['event'] ) ? $_GET['event'] : \modules\event\Functions::get_current_event()->post->ID;
				$current_distance = isset( $_GET['distance'] ) ? $_GET['distance'] : 0; ?>
				<?php $values = wp_list_pluck( \modules\event\Functions::get_all_events()->posts, 'post_title', 'ID' ); ?>
                <select name="event">
					<?php foreach ( $values as $value => $label ) {
						printf(
							'<option value="%s"%s>%s</option>',
							$value,
							selected( $value, $current_event ),
							$label
						);
					} ?>
                </select>
				<?php if ( $current_event ):
					$values = wp_list_pluck( \modules\distance\Functions::get_distances( $current_event )->posts, 'post_title', 'ID' ); ?>
                    <select name="distance">
                        <option value=""><?php _e( 'All distances', 'colorrun' ); ?></option>
						<?php foreach ( $values as $value => $label ) {
							printf(
								'<option value="%s"%s>%s</option>',
								$value,
								selected( $value, $current_distance ),
								$label
							);
						} ?>
                    </select>
				<?php endif;
			}
		} );


		add_filter( 'parse_query', function ( $query ) {
			global $pagenow;
			if ( isset( $_GET['post_type'] ) && self::POST_TYPE == $_GET['post_type'] && is_admin() && $pagenow == 'edit.php' && $query->is_main_query() ) {
				if ( isset( $_GET['event'] ) && $_GET['event'] != '' ) {
					$distances = wp_list_pluck( \modules\distance\Functions::get_distances( $_GET['event'] )->posts, 'ID' );
				} else {
					$distances = wp_list_pluck( \modules\distance\Functions::get_current_distances( - 1 )->posts, 'ID' );
				}
				$query->query_vars['meta_key']     = self::POST_TYPE . '_distance';
				$query->query_vars['meta_compare'] = 'IN';
				$query->query_vars['meta_value']   = $distances;

				if ( isset( $_GET['distance'] ) && $_GET['distance'] != '' ) {
					$query->query_vars['meta_value'] = $_GET['distance'];
				}
			}

			return $query;
		} );

	}

	/**
	 * @param $wp_query \WP_Query
	 */
	public function add_filter_pre_get_posts( $wp_query ) {
		if ( $wp_query->is_main_query() && is_admin() && ! isset( $_GET['orderby'] ) && $wp_query->get( 'post_type' ) == self::POST_TYPE ) {
			$wp_query->set( 'orderby', 'date' );
			$wp_query->set( 'order', 'DESC' );
		}
	}

}

