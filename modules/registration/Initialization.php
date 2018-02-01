<?php

namespace modules\registration;

use modules\logger\Functions as Log;
use modules\participant\Participant;
use Simple_REST_API\Router;
use WPKit\AdminPage\OptionPage;
use WPKit\Fields\Select;
use WPKit\Fields\Select2;
use WPKit\Fields\WPEditor;
use WPKit\Module\AbstractInitialization;
use WPKit\Options\OptionBox;

/**
 * Class Initialization
 *
 * @package modules\registration
 */
class Initialization extends AbstractInitialization {
	public function register_api() {
		$router = new Router( 'register' );

		$router->post( '/checkUser', function () {
			$email       = sanitize_email( $_POST['email'] );
			$distance    = (int) $_POST['distance'];
			$participant = Functions::get_participant( $email, $distance );

			return $this->send_success( $participant->get_info() );
		} );

		$router->post( '/startRegistration', function () {
			if ( ! isset( $_POST['distance'] ) && ! $_POST['distance'] ) {
				return $this->send_error( 'No distance id', 'no_distance' );
			}
			if ( (bool) Functions::get_participant( strtolower( $_POST['email'] ), $_POST['distance'] ) ) {
				Log::error( 'Email already registered for this distance ' . strtolower( $_POST['email'] ) );

				return $this->send_error( __( 'Email already registered for this distance', 'colorrun' ), 'email_used' );
			}

			$id          = Functions::start_registration( $_POST['distance'] );
			$participant = new Participant( $id );

			$data = $_POST;
			unset( $data['event_rules'] );
			unset( $data['personal_data'] );
			$participant->set_info( $data );

			return $this->send_success( [ 'participant_id' => $id ] );
		} );

		$router->post( '/updateInfo', function () {
			if ( ! isset( $_POST['participant_id'] ) && ! $_POST['participant_id'] ) {
				return $this->send_error( 'No participant id', 'no_participant' );
			}
			$participant = new Participant( (int) $_POST['participant_id'] );
			if ( Functions::is_expired( $participant ) ) {
				Log::error( 'Time for registration expired', $participant->get_id() );

				return $this->send_error( 'Time for registration expired', 'time_expired' );
			}
			if ( (bool) Functions::get_participant( strtolower( $_POST['email'] ), $participant->distance ) ) {
				Log::error( 'Email already registered for this distance ' . strtolower( $_POST['email'] ), $participant->get_id() );

				return $this->send_error( __( 'Email already registered for this distance', 'colorrun' ), 'email_used' );
			}
			$data = $_POST;
			unset( $data['event_rules'] );
			unset( $data['personal_data'] );
			unset( $data['participant_id'] );
			$participant->set_info( $data );

			return $this->send_success();
		} );

		$router->post( '/getPaymentInfo/{participant_id}', function ( $participant_id ) {
			$participant = new Participant( $participant_id );
			if ( Functions::is_expired( $participant ) ) {
				Log::error( 'Time for registration expired', $participant->get_id() );

				return $this->send_error( 'Time for registration expired', 'time_expired' );
			}
			if ( $participant->coupon ) {
				return $this->send_error( __( 'You have successfully used the promo code. Please, pay the registration fee.', 'colorrun' ), 'coupon_exist' );
			}
			if ( isset( $_POST['coupon'] ) && $_POST['coupon'] && ! $participant->coupon ) {
				$coupon_code = $_POST['coupon'];
				$new_price   = $participant->use_coupon( $coupon_code );
				if ( $new_price === false ) {
					return $this->send_error( __( 'Can\'t use coupon', 'colorrun' ), 'wrong_coupon' );
				}
			}

			return $this->send_success( [
				'price'       => \modules\distance\Functions::format_price( $participant->get_amount_to_pay() ),
				'payment_url' => \modules\payment\Functions::get_payment_url( $participant_id )
			] );
		} )->convert( 'participant_id', function ( $participant_id ) {
			return absint( $participant_id );
		} );

		$router->post( '/paymentSuccess', function () {
			return \modules\payment\Functions::process_success( $_POST['data'], $_POST['signature'] );
		} );

		$router->get( '/registerFree/{participant_id}', function ( $participant_id ) {
			Functions::finish_registration( $participant_id, (object) [ 'payment_id' => 'free' ] );
			wp_redirect( Functions::get_result_url() . '?participant=' . $participant_id );
		} )->convert( 'participant_id', function ( $participant_id ) {
			return absint( $participant_id );
		} );
	}

	private function send_success( array $data = [] ): array {
		unset( $data['success'] );

		return array_merge( [
			'success' => true,
		], $data );
	}

	private function send_error( string $error = '', string $type = '' ): array {
		return [
			'success' => false,
			'message' => $error,
			'type'    => $type,
		];
	}

	public function add_action_remove_registration( $id ) {
		$participant = new Participant( $id );
		if ( ! $participant->bib && $participant->get_payment_status() != \modules\payment\Initialization::STATUS['PAYED'] ) {
			Log::info( 'Registration timed out. Participant moved to trash.', $id );

			return wp_trash_post( $id );
		} else {
			Log::error( 'False registration deletion. Payed and bib already assigned.', $id );

			return true;
		}
	}

	public function register_options() {
		add_action( 'init', function () {
			$settings = new OptionPage( 'registration', __( 'Registration', 'colorrun' ), 'theme_settings' ); //Запросити друга

			$box = new OptionBox( 'template', __( 'New registration notification template', 'colorrun' ) ); //Шаблони
			$box->add_field( 'registration_subject_' . \modules\theme\Functions::get_current_language(), __( 'Subject', 'colorrun' ) ); //Тема
			$box->add_field( 'registration_message_' . \modules\theme\Functions::get_current_language(), __( 'Message', 'colorrun' ), function () { //Повідомлення
				$f = new WPEditor();
				$f->set_description( 'Participant variables: <br>
									{{name}}, 
									{{firstname}}, 
									{{lastname}}, 
									{{distance}}, 
									{{event}}, 
									{{bib}}, 
									{{email}}, 
									{{gender}}, 
									{{dateofbirth}}, 
									{{info_phone}}, 
									{{info_club}}, 
									{{info_tshirt_size}}, 
									{{country}}, 
									{{city}} <br>
                                    Payment info:<br>
                                    {{payment_payment_id}},
                                    {{payment_status}},
                                    {{payment_version}},
                                    {{payment_type}},
                                    {{payment_paytype}},
                                    {{payment_acq_id}},
                                    {{payment_order_id}},
                                    {{payment_liqpay_order_id}},
                                    {{payment_description}},
                                    {{payment_ip}},
                                    {{payment_amount}},
                                    {{payment_currency}},
                                    {{payment_sender_commission}},
                                    {{payment_receiver_commission}},
                                    {{payment_agent_commission}},
                                    {{payment_amount_debit}},
                                    {{payment_amount_credit}},
                                    {{payment_commission_debit}},
                                    {{payment_commission_credit}},
                                    {{payment_currency_debit}},
                                    {{payment_currency_credit}},
                                    {{payment_sender_bonus}},
                                    {{payment_amount_bonus}},
                                    {{payment_mpi_eci}},
                                    {{payment_is_3ds}}, 
                                    {{payment_create_date}},
                                    {{payment_end_date}},
                                    {{payment_transaction_id}}' );

				return $f;
			} );
			$settings->add_box( $box );

			$reg   = new OptionBox( 'registration', __( 'Form settings', 'colorrun' ) );
			$pages = function () {
				$f = new Select2();
				$f->set_options( wp_list_pluck( get_pages(), 'post_title', 'ID' ) );
				$f->set_placeholder( 'Select page' );

				return $f;
			};
			$reg->add_field( 'registration_free_result', __( 'Free registration result page', 'colorrun' ), $pages );
			$reg->add_field( 'registration_personal_data', __( 'Personal data page', 'colorrun' ), $pages );
			$reg->add_field( 'registration_event_rules', __( 'Event rules page', 'colorrun' ), $pages );
			$reg->add_field( 'registration_additional_fields', __( 'Additional form fields', 'colorrun' ), function () {
				$f = new Select();
				$f->set_multiple( true );
				$f->set_options( \modules\participant\Functions::get_additional_fields() );

				return $f;
			} );
			$settings->add_box( $reg );
		}, 11 );
	}

	public function register_participants_list() {
		add_action( 'wp_enqueue_scripts', function () {
			$settings = [
				'language' => [
					"sEmptyTable"     => __( "No data available in table", 'colorrun' ),
					"sInfo"           => __( "Showing _START_ to _END_ of _TOTAL_ entries", 'colorrun' ),
					"sInfoEmpty"      => __( "Showing 0 to 0 of 0 entries", 'colorrun' ),
					"sInfoFiltered"   => __( "(filtered from _MAX_ total entries)", 'colorrun' ),
					"sInfoPostFix"    => "",
					"sInfoThousands"  => _x( ",", 'datatables thousands separator', 'colorrun' ),
					"sLengthMenu"     => __( "Show _MENU_ entries", 'colorrun' ),
					"sLoadingRecords" => __( "Loading...", 'colorrun' ),
					"sProcessing"     => __( "Processing...", 'colorrun' ),
					"sSearch"         => __( "Search:", 'colorrun' ),
					"sZeroRecords"    => __( "No matching records found", 'colorrun' ),
					"oPaginate"       => [
						"sFirst"    => __( "First", 'colorrun' ),
						"sLast"     => __( "Last", 'colorrun' ),
						"sNext"     => __( "Next", 'colorrun' ),
						"sPrevious" => __( "Previous", 'colorrun' )
					],
					"oAria"           => [
						"sSortAscending"  => __( ": activate to sort column ascending", 'colorrun' ),
						"sSortDescending" => __( ": activate to sort column descending", 'colorrun' )
					]
				],
				'fields'   => [
					'gender' => [
						'1'      => __( 'M', 'colorrun' ),
						'male'   => __( 'Male', 'colorrun' ),
						'2'      => __( 'F', 'colorrun' ),
						'female' => __( 'Female', 'colorrun' ),
					]
				],
				'search'   => isset( $_GET['search'] ) ? $_GET['search'] : '',
				'titles'   => [
					'name'            => __( "Name", 'colorrun' ), //Ім'я Прізвище
					'gender'          => __( "Sex", 'colorrun' ), //Стать
					'bib'             => __( "BIB", 'colorrun' ), //Стартовий номер
					'dateofbirth'     => __( "Year of birth", 'colorrun' ), //Рік народження
					'age_group'       => __( "Age group", 'colorrun' ), //Вікова група
					'club'            => __( "Club", 'colorrun' ), //Клуб
					'team'            => __( "Team", 'colorrun' ), //Команда
					'city'            => __( "City", 'colorrun' ), //Місто
					'country'         => __( "Country", 'colorrun' ), //Країнаб
					'category'        => __( "Category", 'colorrun' ), //Країнаб
					'place'           => __( "Place", 'colorrun' ),
					'age_group_place' => __( "AG place", 'colorrun' ),
					'net_time'        => __( "Net time", 'colorrun' ),
					'gross_time'      => __( "Gross time", 'colorrun' ),
					'km5'             => __( "5 km", 'colorrun' ),
					'km10'            => __( "10 km", 'colorrun' ),
					'km15'            => __( "15 km", 'colorrun' ),
					'km20'            => __( "20 km", 'colorrun' ),
					'km21'            => __( "21 km", 'colorrun' ),
					'km30'            => __( "30 km", 'colorrun' ),
				]
			];

			$distances = $dist = \modules\distance\Functions::get_distances();
			$id        = isset( $_GET['id'] ) ? (int) $_GET['id'] : (int) $distances->post->ID;
//		if ( isset( $distances[ $id ] ) ) {
//			$settings['distance'] = $distances[ $id ];
//		}
			$settings['data'] = Functions::get_registered_participants( $id );

			wp_localize_script( 'theme', 'table_settings', $settings );
		}, 11 );
	}
}

