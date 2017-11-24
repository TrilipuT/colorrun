<?php

namespace modules\payment;

use modules\participant\Participant;
use WPKit\Module\AbstractFunctions;
use WPKit\Options\Option;

/**
 * Class Functions
 *
 * @package modules\theme
 */
class Functions extends AbstractFunctions {
	public static function get_payment_url( int $participant_id ): string {
		$participant = new Participant( $participant_id );
		if ( $participant->get_payment_status() == Initialization::STATUS['PAYED'] ) {
			//TODO: redirect here
			echo 'already payed.';
		}
		$public_key  = Functions::get_public_key();
		$private_key = Functions::get_private_key();
		$liqpay      = new \LiqPay( $public_key, $private_key );

		$description = sprintf( 'Pay for order #%s paticipation in event %s.', $participant_id, get_the_title( $participant->distance ) );
		if ( $participant->coupon ) {
			$description .= ' ' . sprintf( 'Coupon %s applied', $participant->coupon );
		}

		$params = array(
			'action'      => 'pay',
			'amount'      => $participant->get_amount_to_pay(),
			'currency'    => \LiqPay::CURRENCY_UAH,
			'description' => $description,
			'order_id'    => 'participant_' . $participant_id,
			'version'     => '3',
			'public_key'  => $public_key,
			'private_key' => $private_key,
		);

		$query_params = [
			'data'      => base64_encode( json_encode( $params ) ),
			'signature' => $liqpay->cnb_signature( $params )
		];

		return "https://www.liqpay.ua/api/3/checkout?" . http_build_query( $query_params );
	}

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