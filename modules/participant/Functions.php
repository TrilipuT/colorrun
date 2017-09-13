<?php

namespace modules\participant;

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

	public static function get_bib( $id ): int {
		return (int) MetaBox::get( $id, Initialization::POST_TYPE, 'bib' );
	}

	public static function get_event( $id ): int {
		return (int) MetaBox::get( $id, Initialization::POST_TYPE, 'event' );
	}

	public static function get_registered_for_distance( int $distance_id ): int {
		$participants = new \WP_Query( [
			'post_type'     => Initialization::POST_TYPE,
			'fields'        => 'ids',
			'no_found_rows' => true,
			'meta_query'    => [
				[
					'key'   => Initialization::POST_TYPE . '_distance',
					'value' => $distance_id,
				],
				[
					'key'     => Initialization::POST_TYPE . '_bib',
					'value'   => 0,
					'type'    => 'numeric',
					'compare' => '>',
				],
			],
		] );

		return $participants->post_count;
	}
}
