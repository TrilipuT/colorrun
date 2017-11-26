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
			\modules\payment\Initialization::STATUS['NOT_PAYED']        => __( 'Not Payed', 'colorrun' ),
			\modules\payment\Initialization::STATUS['AWAITING_PAYMENT'] => __( 'Awaiting Payment', 'colorrun' ),
			\modules\payment\Initialization::STATUS['PAYED']            => __( 'Payed', 'colorrun' ),
		];
	}

	public static function get_bib( $id ): int {
		return (int) MetaBox::get( $id, Initialization::POST_TYPE, 'bib' );
	}

	public static function get_distance( $id ): int {
		return (int) MetaBox::get( $id, Initialization::POST_TYPE, 'distance' );
	}

	public static function get_additional_fileds() {
		return [
			'tel'  => 'Telephone',
			'club' => 'Club'
		];
	}


}
