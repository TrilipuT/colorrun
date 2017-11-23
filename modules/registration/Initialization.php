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
		$router->post( 'checkUser', function () {
			$email       = sanitize_email( $_POST['email'] );
			$distance    = (int) $_POST['distance'];
			$participant = Functions::get_participant( $email, $distance );

			return $participant->get_info();
		} );

		$router->post( 'updateInfo', function () {
			if ( ! isset( $_POST['participant_id'] ) ) {
				return $this->send_error( 'No participant id' );
			}
			$participant = new Participant( (int) $_POST['participant_id'] );
			$participant->set_info( $_POST );

			return $this->send_success();
		} );

	}

	private function send_error( string $error = '' ): array {
		return [
			'success' => false,
			'message' => $error,
		];
	}

	private function send_success( array $data = [] ): array {
		unset( $data['success'] );

		return array_merge( [
			'success' => true,
		], $data );
	}

	public function add_action_remove_registration( $id ) {
//		if ( ( get_the_date( 'U', $id ) + ( 15 * MINUTE_IN_SECONDS ) ) >= time() ) {
		return wp_delete_post( $id );
//		}
	}
}

