<?php

namespace modules\registration;

use modules\logger\Functions as Log;
use modules\participant\Participant;
use WPKit\Module\AbstractFunctions;

/**
 * Class Functions
 *
 * @package modules\registration
 */
class Functions extends AbstractFunctions {
	/**
	 * @param string $email
	 * @param int $distance_id
	 *
	 * @return Participant | boolean
	 */
	public static function get_participant( string $email, int $distance_id ) {

		$registered = self::get_registered_for_distance_by_email( $email, $distance_id );
		if ( ! $registered->have_posts() ) {
			return false;
		}

		return new Participant( $registered->post->ID );
	}

	/**
	 * @param $email
	 * @param $distance_id
	 *
	 * @return \WP_Query
	 */
	public static function get_registered_for_distance_by_email( $email, $distance_id ): \WP_Query {
		return new \WP_Query( [
			'post_type'     => \modules\participant\Initialization::POST_TYPE,
			'no_found_rows' => true,
			'meta_query'    => [
				[
					'key'   => \modules\participant\Initialization::POST_TYPE . '_distance',
					'value' => $distance_id,
				],
				[
					'key'   => \modules\participant\Initialization::POST_TYPE . '_email',
					'value' => $email,
				],
			],
		] );
	}

	/**
	 * Check if still valid timestamp
	 *
	 * @param Participant $participant
	 *
	 * @return bool
	 */
	public static function is_expired( Participant $participant ) {
		$registration_end_time = get_the_time( 'U', $participant->get_id() ) + 15 * MINUTE_IN_SECONDS;
		$current_time          = current_time( 'timestamp' );

		return $current_time > $registration_end_time;
	}

	/**
	 * Start registration for distance
	 *
	 * @param int $distance_id
	 *
	 * @return int
	 */
	public static function start_registration( int $distance_id ): int {
		$participant = Participant::create();
		$participant->set_distance( $distance_id );
		//schedule remove if not payed in 15 minutes
		wp_schedule_single_event( time() + 15 * MINUTE_IN_SECONDS, 'remove_registration', [ $participant->get_id() ] );

		return $participant->get_id();
	}

	public static function finish_registration( int $participant_id, \stdClass $data ) {
		$participant = new Participant( $participant_id );
		if ( $participant->get_payment_status() == \modules\payment\Initialization::STATUS['PAYED'] ) {
			return;
		}
		Log::info( 'Success payment: ' . $data->payment_id, $participant_id );
		$participant->finish_registration();
		$participant->payment = $data;
		// Lets delete remove_registration schedule
		$event_time = wp_next_scheduled( 'remove_registration', [ $participant->get_id() ] );
		wp_unschedule_event( $event_time, 'remove_registration', [ $participant->get_id() ] );
	}

	public static function get_registration_url( int $distance ): string {
		return \modules\theme\Functions::get_page_url_by_template( 'registration.php' ) . '?distance=' . $distance;
	}
}
