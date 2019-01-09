<?php

namespace modules\distance;

use modules\participant\Participant;
use WPKit\Module\AbstractFunctions;
use WPKit\PostType\MetaBox;
use WPKit\PostType\MetaBoxRepeatable;

/**
 * Class Functions
 *
 * @package modules\distance
 */
class Functions extends AbstractFunctions {


	/**
	 * @param int $id
	 *
	 * @return string
	 */
	public static function get_distance( int $id = 0 ): string {
		$id = self::get_id( $id );

		return (string) MetaBox::get( $id, Initialization::POST_TYPE, 'distance' );
	}

	/**
	 * @param int $id
	 *
	 * @return int
	 */
	private static function get_id( int $id ): int {
		if ( ! $id ) {
			$id = get_the_ID();
		}

		return (int) $id;
	}

	public static function get_content( int $id = 0 ): string {
		$id = self::get_id( $id );

		return apply_filters( 'the_content', MetaBox::get( $id, Initialization::POST_TYPE . '_' . \modules\theme\Functions::get_current_language(), 'content' ) );
	}

	public static function get_age( int $id = 0 ): string {
		$id         = self::get_id( $id );
		$age        = MetaBox::get( $id, Initialization::POST_TYPE, 'min_age' ) ?: 0;
		$start_date = strtotime( "- {$age} years", self::get_date( $id, 'U' ) );

		return date( 'd/m/Y', $start_date );
	}

	/**
	 * @param int $id
	 * @param string $format
	 *
	 * @return string
	 */
	public static function get_date( int $id = 0, string $format = 'd.m.Y' ): string {
		$id = self::get_id( $id );

		return date( $format, strtotime( MetaBox::get( $id, Initialization::POST_TYPE, 'date' ) ) );
	}

	public static function is_open( int $id = 0 ): bool {
		$id  = self::get_id( $id );
		$key = 'is_open_' . $id;

		if ( ! $is_open = wp_cache_get( $key, 'distance' ) ) {
			$total      = self::get_total_slots( $id );
			$registered = self::get_registered_for_distance_count( $id );
			if ( $registered < $total ) {
				$registration_end = self::get_registration_end_date( $id );
				if ( current_time( 'mysql' ) < $registration_end ) {
					wp_cache_add( $key, true, 'distance' );

					return true;
				}
			}
			wp_cache_add( $key, false, 'distance' );

			return false;
		}

		return $is_open;
	}

	/**
	 * @param int $id
	 *
	 * @return int
	 */
	public static function get_total_slots( int $id = 0 ): int {
		$id = self::get_id( $id );

		return (int) MetaBox::get( $id, Initialization::POST_TYPE, 'slots' );
	}

	/**
	 * @param int $id
	 *
	 * @return int
	 */
	public static function get_registered_for_distance_count( int $id = 0 ): int {
		$id = self::get_id( $id );
		if ( ! $count = wp_cache_get( 'registered_for_distance_count_' . $id, 'distance' ) ) {
			$registered = new \WP_Query( [
				'post_type'      => \modules\participant\Initialization::POST_TYPE,
				'posts_per_page' => '-1',
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'post_status'    => 'any',
				'meta_query'     => [
					[
						'key'   => \modules\participant\Initialization::POST_TYPE . '_distance',
						'value' => $id,
					]
				],
			] );
			$count      = $registered->post_count;
			wp_cache_add( 'registered_for_distance_count_' . $id, $count, 'distance' );
		}

		return $count;
	}

	private static function get_registration_end_date( int $id ): string {
		$registration_end_date = MetaBox::get( $id, Initialization::POST_TYPE, 'registration_end_date' );
		if ( ! $registration_end_date ) {
			$registration_end_date = MetaBox::get( $id, Initialization::POST_TYPE, 'date' );
		}

		return $registration_end_date;
	}

	public static function get_participants( int $distance_id ) {
		$participants = self::get_registered_for_distance( $distance_id );
		$result       = [];
		foreach ( $participants->posts as $id ) {
			$p        = new Participant( $id );
			$result[] = $p->get_data_for_export();
		}

		return $result;
	}

	/**
	 * @param int $distance_id
	 *
	 * @return \WP_Query
	 */
	public static function get_registered_for_distance( int $distance_id, int $status = 1 ): \WP_Query {
		return new \WP_Query( [
			'post_type'      => \modules\participant\Initialization::POST_TYPE,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'posts_per_page' => - 1,
			'meta_query'     => [
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

	/**
	 * @param int $id
	 *
	 * @return int
	 */
	public static function get_current_price( int $id = 0 ): int {
		$prices = self::get_prices( $id );
		foreach ( $prices as $price ) {
			if ( $price['active'] ) {
				return (int) $price['fee'];
			}
		}

		return 0;
	}

	/**
	 * Get list of prices
	 *
	 * @param int $id
	 *
	 * @return array
	 */
	public static function get_prices( int $id = 0 ): array {
		$id     = self::get_id( $id );
		$dates  = MetaBoxRepeatable::get( $id, Initialization::POST_TYPE . '_price', 'date' );
		$fees   = MetaBoxRepeatable::get( $id, Initialization::POST_TYPE . '_price', 'fee' );
		$prices = [];
		if ( $dates ) {
			foreach ( $dates as $i => $date ) {
				$prices[] = [
					'date'   => $date,
					'fee'    => $fees[ $i ],
					'active' => strtotime( $date . ' 23:59' ) > time(),
				];
			}
		}

		/*usort( $prices, function ( $a, $b ) {
			return strtotime( $a['date'] ) > strtotime( $b['date'] );
		} );*/

		return $prices;
	}

	/**
	 * @param int $price
	 *
	 * @return string
	 */
	public static function format_price( int $price ): string {
		return $price . __( 'UAH', 'colorrun' );
	}

	/**
	 * Get distances.
	 * If $event_id specifiend will return only distances from selected event
	 *
	 * @param int $event_id
	 *
	 * @return \WP_Query
	 */
	public static function get_distances( int $event_id = 0 ): \WP_Query {
		$args = [
			'posts_per_page' => - 1,
			'post_type'      => Initialization::POST_TYPE,
		];
		if ( $event_id ) {
			$args['meta_query'] = [
				[
					'key'   => Initialization::POST_TYPE . '_event',
					'value' => $event_id,
				],
			];
		}

		return new \WP_Query( $args );
	}

	/**
	 * @param int $count
	 *
	 * @return \WP_Query
	 */
	public static function get_current_distances( $count = 10 ): \WP_Query {
		$current_event = \modules\event\Functions::get_current_event()->post->ID;
		$key           = 'get_current_distances_' . $current_event . '_' . $count;
		if ( ! $current_distances = wp_cache_get( $key, 'distance' ) ) {
			$current_distances = new \WP_Query( [
				'posts_per_page' => $count,
				'post_type'      => Initialization::POST_TYPE,
				'meta_query'     => [
					[
						'key'   => Initialization::POST_TYPE . '_event',
						'value' => $current_event,
					],
				],
			] );
			wp_cache_add( $key, $current_distances, 'distance', 600 );
		}

		return $current_distances;
	}

	/**
	 * Return next one free bib for distance
	 *
	 * @param int $distance
	 *
	 * @return int
	 */
	public static function get_next_free_bib( int $distance ): int {
		$bibs = self::get_available_bib_list( $distance );
		if ( ! $bibs ) {
			return false;
		}

		return array_shift( $bibs );
	}

	/**
	 * @param int $distance
	 * @param int $limit
	 *
	 * @return array
	 */
	public static function get_available_bib_list( int $distance ): array {
		global $wpdb;

		$reserved_bibs = $wpdb->get_col( $wpdb->prepare( "SELECT meta_value FROM {$wpdb->postmeta} 
			WHERE post_id IN (SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'participant_distance' AND meta_value = %d) 
			AND meta_key = 'participant_bib'", $distance ) );
		$start         = (int) MetaBox::get( $distance, Initialization::POST_TYPE, 'bib_from' );
		$end           = $start + MetaBox::get( $distance, Initialization::POST_TYPE, 'slots' ) - 1;
		$range         = range( $start, $end );

		return array_diff( $range, $reserved_bibs );
	}

	/**
	 * Get link to distance registration
	 *
	 * @param int $id
	 *
	 * @return string
	 */
	public static function get_registration_url( int $id = 0 ): string {
		$id = self::get_id( $id );

		return \modules\registration\Functions::get_registration_url( $id );
	}
}
