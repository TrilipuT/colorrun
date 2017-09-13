<?php

namespace modules\distance;

use WPKit\Module\AbstractFunctions;
use WPKit\PostType\MetaBox;

/**
 * Class Functions
 *
 * @package modules\theme
 */
class Functions extends AbstractFunctions {

	public static function get_statuses(): array {
		return [
			Initialization::STATUS['NOT_PAYED']        => __( 'Not Payed', TEXT_DOMAIN ),
			Initialization::STATUS['AWAITING_PAYMENT'] => __( 'Awaiting Payment', TEXT_DOMAIN ),
			Initialization::STATUS['PAYED']            => __( 'Payed', TEXT_DOMAIN ),
		];
	}

	public static function get_distances(): \WP_Query {
		return new \WP_Query( [
			'posts_per_page' => - 1,
			'post_type'      => Initialization::POST_TYPE,
		] );
	}

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
