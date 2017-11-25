<?php

namespace modules\registration;

use modules\participant\Participant;
use Simple_REST_API\Router;
use WPKit\Module\AbstractInitialization;

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

		$router->post( '/updateInfo', function () {
			if ( ! isset( $_POST['participant_id'] ) ) {
				return $this->send_error( 'No participant id' );
			}
			$participant = new Participant( (int) $_POST['participant_id'] );
			$participant->set_info( $_POST );

			return $this->send_success();
		} );

		$router->post( '/getPaymentInfo/{participant_id}', function ( $participant_id ) {
			$participant = new Participant( $participant_id );
			$price       = \modules\distance\Functions::get_current_price( $participant->distance );
			if ( isset( $_POST['coupon'] ) && ! $participant->coupon ) {
				$coupon_code = $_POST['coupon'];
				if ( ! $new_price = $participant->use_coupon( $coupon_code ) ) {
					return $this->send_error( 'Cant use coupon' );
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
			\modules\logger\Functions::log( json_encode( $_POST ) );
			\modules\logger\Functions::log( json_encode( base64_decode( $_POST['data'] ) ) );
		} );
	}

	private function send_success( array $data = [] ): array {
		unset( $data['success'] );

		return array_merge( [
			'success' => true,
		], $data );
	}

	private function send_error( string $error = '' ): array {
		return [
			'success' => false,
			'message' => $error,
		];
	}

	public function add_action_remove_registration( $id ) {
//		if ( ( get_the_date( 'U', $id ) + ( 15 * MINUTE_IN_SECONDS ) ) >= time() ) {
		return wp_delete_post( $id );
//		}
	}
}

