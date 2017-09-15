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
			\modules\payment\Initialization::STATUS['NOT_PAYED']        => __( 'Not Payed', TEXT_DOMAIN ),
			\modules\payment\Initialization::STATUS['AWAITING_PAYMENT'] => __( 'Awaiting Payment', TEXT_DOMAIN ),
			\modules\payment\Initialization::STATUS['PAYED']            => __( 'Payed', TEXT_DOMAIN ),
		];
	}

	public static function get_bib( $id ): int {
		return (int) MetaBox::get( $id, Initialization::POST_TYPE, 'bib' );
	}

	public static function get_event( $id ): int {
		return (int) MetaBox::get( $id, Initialization::POST_TYPE, 'event' );
	}

	public static function get_additional_fileds() {
		return [
			'tel'  => 'Telephone',
			'club' => 'Club'
		];
	}


}
