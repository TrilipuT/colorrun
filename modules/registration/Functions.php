<?php

namespace modules\registration;

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
	 * @param int $distance_id
	 *
	 * @return int
	 */
	public static function get_registered_for_distance_count( int $distance_id ): int {
		$participants = self::get_registered_for_distance( $distance_id );

		return $participants->post_count;
	}

	/**
	 * @param int $distance_id
	 *
	 * @return \WP_Query
	 */
	public static function get_registered_for_distance( int $distance_id, int $status = 1 ): \WP_Query {
		return new \WP_Query( [
			'post_type'     => \modules\participant\Initialization::POST_TYPE,
			'fields'        => 'ids',
			'no_found_rows' => true,
			'meta_query'    => [
				[
					'key'   => \modules\participant\Initialization::POST_TYPE . '_distance',
					'value' => $distance_id,
				],
				[
					'key'     => \modules\participant\Initialization::POST_TYPE . '_bib',
					'value'   => 0,
					'type'    => 'numeric',
					'compare' => '>',
				],
			],
		] );
	}

	public static function start_registration( int $distance_id ): int {
		$participant = Participant::create();
		$participant->set_distance( $distance_id );
		//schedule remove if not payed in 15 minutes
		wp_schedule_single_event( time() + 15 * MINUTE_IN_SECONDS, 'remove_registration', [ $participant->get_id() ] );

		return $participant->get_id();
	}
}
