<?php

namespace modules\registration;

use modules\logger\Functions as Log;
use modules\participant\Participant;
use WPKit\Module\AbstractFunctions;
use WPKit\Options\Option;

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

	public static function get_result_url() {
		$page_id = Option::get( 'registration_free_result' );
		if ( ! $page_id ) {
			return \modules\payment\Functions::get_result_url();
		}

		return get_permalink( (int) $page_id );
	}

	public static function get_registered_participants( int $distance_id ): array {
		$participants = new \WP_Query( [
			'post_type'      => \modules\participant\Initialization::POST_TYPE,
			'no_found_rows'  => true,
			'posts_per_page' => - 1,
			'meta_query'     => [
				[
					'key'   => \modules\participant\Initialization::POST_TYPE . '_distance',
					'value' => $distance_id,
				]
			],
		] );
		$result       = [];
		foreach ( $participants->posts as $post ) {
			$p        = new Participant( $post->ID );
			$result[] = [
				'name'        => $p->firstname . ' ' . $p->lastname,
				'gender'      => $p->gender,
				'bib'         => $p->bib,
				'dateofbirth' => $p->dateofbirth,
				'club'        => $p->get_additional_info( 'club' ),
				'country_id'  => $p->country,
				'country'     => isset( self::get_country_list()[ $p->country ] ) ? self::get_country_list()[ $p->country ] : $p->country,
				'city'        => $p->city,
			];
		}

		return $result;
	}

	public static function get_country_list() {
		$countries = include 'countries/' . \modules\theme\Functions::get_current_language() . '.php';

		return $countries;
	}

	public static function get_personal_data_link(): string {
		$translations = pll_get_post_translations( Option::get( 'registration_personal_data' ) );

		return isset( $translations[ pll_current_language() ] ) ? get_permalink( $translations[ pll_current_language() ] ) : '';
	}

	public static function get_event_rules_link(): string {
		$translations = pll_get_post_translations( Option::get( 'registration_event_rules' ) );

		return isset( $translations[ pll_current_language() ] ) ? get_permalink( $translations[ pll_current_language() ] ) : '';
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
	 * @return int Created participant id
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
		$participant->payment = $data;
		$participant->finish_registration();
		// Lets delete remove_registration schedule
		$event_time = wp_next_scheduled( 'remove_registration', [ $participant->get_id() ] );
		wp_unschedule_event( $event_time, 'remove_registration', [ $participant->get_id() ] );
	}

	public static function get_registration_url( int $distance ): string {
		return \modules\theme\Functions::get_page_url_by_template( 'registration.php' ) . '?distance=' . $distance;
	}

	public static function is_active_field( string $name ): bool {
		return in_array( $name, self::get_active_additional_fields() );
	}

	public static function get_active_additional_fields(): array {
		return Option::get( 'registration_additional_fields' ) ?: [];
	}
}
