<?php

namespace modules\registration;

use modules\logger\Functions as Log;
use modules\participant\Participant;
use Simple_REST_API\Router;
use WPKit\AdminPage\OptionPage;
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
			$id = Functions::start_registration( $_POST['distance'] );

			$participant = new Participant( $id );
			if ( Functions::is_expired( $participant ) ) {
				Log::error( 'Time for registration expired', $id );

				return $this->send_error( 'Time for registration expired', 'time_expired' );
			}
			if ( (bool) Functions::get_participant( strtolower( $_POST['email'] ), $participant->distance ) ) {
				Log::error( 'Email already registered for this distance ' . strtolower( $_POST['email'] ), $id );

				return $this->send_error( __( 'Email already registered for this distance', 'colorrun' ), 'email_used' );
			}
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
			$price = \modules\distance\Functions::get_current_price( $participant->distance );
			if ( isset( $_POST['coupon'] ) && $_POST['coupon'] && ! $participant->coupon ) {
				$coupon_code = $_POST['coupon'];
				if ( ! $new_price = $participant->use_coupon( $coupon_code ) ) {
					return $this->send_error( __( 'Can\'t use coupon', 'colorrun' ), 'wrong_coupon' );
				}
				$price = $new_price;
			}

			return $this->send_success( [
				'price'       => \modules\distance\Functions::format_price( $price ),
				'payment_url' => \modules\payment\Functions::get_payment_url( $participant_id )
			] );
		} )->convert( 'participant_id', function ( $participant_id ) {
			return absint( $participant_id );
		} );

		$router->post( '/paymentSuccess', function () {
			return \modules\payment\Functions::process_success( $_POST['data'], $_POST['signature'] );
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
//		if ( ( get_the_date( 'U', $id ) + ( 15 * MINUTE_IN_SECONDS ) ) >= time() ) {
		return wp_trash_post( $id );
//		}
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

			$reg   = new OptionBox( 'registration', __( '', 'colorrun' ) );
			$pages = function () {
				$f = new Select2();
				$f->set_options( wp_list_pluck( get_pages(), 'post_title', 'ID' ) );
				$f->set_placeholder( 'Select page' );

				return $f;
			};
			$reg->add_field( 'registration_personal_data', __( 'Personal data page', 'colorrun' ), $pages );
			$reg->add_field( 'registration_event_rules', __( 'Event rules page', 'colorrun' ), $pages );
			$settings->add_box( $reg );
		}, 11 );
	}
}

