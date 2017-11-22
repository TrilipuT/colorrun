<?php

namespace modules\registration;

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


	}

	public function add_action_remove_registration( $id ) {
//		if ( ( get_the_date( 'U', $id ) + ( 15 * MINUTE_IN_SECONDS ) ) >= time() ) {
		return wp_delete_post( $id );
//		}
	}
}

