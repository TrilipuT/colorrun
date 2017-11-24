<?php

namespace modules\distance;

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
	 * @return array
	 */
	public static function get_statuses(): array {
		return [
			\modules\payment\Initialization::STATUS['NOT_PAYED']        => __( 'Not Payed', 'colorrun' ),
			\modules\payment\Initialization::STATUS['AWAITING_PAYMENT'] => __( 'Awaiting Payment', 'colorrun' ),
			\modules\payment\Initialization::STATUS['PAYED']            => __( 'Payed', 'colorrun' ),
		];
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
	public static function get_total_slots( int $id = 0 ): int {
		$id = self::get_id( $id );

		return (int) MetaBox::get( $id, Initialization::POST_TYPE, 'slots' );
	}

	/**
	 * @param int $id
	 *
	 * @return int
	 */
	public static function get_available_slots( int $id = 0 ): int {
		$id = self::get_id( $id );

		return (int) MetaBox::get( $id, Initialization::POST_TYPE, 'slots' );
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
					'active' => strtotime( $date ) > time(),
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
	 * @return \WP_Query
	 */
	public static function get_distances(): \WP_Query {
		return new \WP_Query( [
			'posts_per_page' => - 1,
			'post_type'      => Initialization::POST_TYPE,
		] );
	}

	/**
	 * @return \WP_Query
	 */
	public static function get_current_distances(): \WP_Query {
		return new \WP_Query( [
			'posts_per_page' => - 1,
			'orderby'        => 'title',
			'post_type'      => Initialization::POST_TYPE,
			'meta_query'     => [
				[
					'key'   => Initialization::POST_TYPE . '_event',
					'value' => \modules\event\Functions::get_current_event()->post->ID,
				],
			],
		] );
	}

	/**
	 * @param int $distance
	 *
	 *
	 * @return array
	 */
	public static function get_available_bib_list( int $distance ): array {
		global $wpdb;
		$reserved_bibs = $wpdb->get_col( $wpdb->prepare( 'SELECT meta_value FROM wp_postmeta 
			WHERE post_id IN (SELECT post_id FROM wp_postmeta WHERE meta_key = \'participant_event\' AND meta_value = %d) 
			AND meta_key = \'participant_bib\'', $distance ) );
		$start         = MetaBox::get( $distance, Initialization::POST_TYPE, 'bib_from' );
		$end           = $start + MetaBox::get( $distance, Initialization::POST_TYPE, 'slots' ) - 1;
		$range         = range( $start, $end );

		return array_diff( $range, $reserved_bibs );
	}

}
