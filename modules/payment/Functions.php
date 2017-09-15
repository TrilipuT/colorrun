<?php

namespace modules\payment;

use WPKit\Module\AbstractFunctions;
use WPKit\Options\Option;

/**
 * Class Functions
 *
 * @package modules\theme
 */
class Functions extends AbstractFunctions {
	/**
	 * @return string
	 */
	public static function get_public_key(): string {
		return Option::get( 'liqpay_public_key' );
	}

	/**
	 * @return string
	 */
	public static function get_private_key(): string {
		return Option::get( 'liqpay_private_key' );
	}
}