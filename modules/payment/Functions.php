<?php

namespace modules\payment;

use modules\logger\Functions as Log;
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
		// If already payed - return empty
		if ( $participant->get_payment_status() == Initialization::STATUS['PAYED'] ) {
			return '';
		}
		// If we have free registration - return direct registration link
		if ( $participant->get_amount_to_pay() == 0 ) {
			return home_url( '/wp-json/register/registerFree/' . $participant->get_id() );
		}
		$public_key  = Functions::get_public_key();
		$private_key = Functions::get_private_key();
		if ( ! $private_key || ! $public_key ) {
			throw new \Exception( 'You should enter LiqPay keys' );
		}
		$liqpay = new \LiqPay( $public_key, $private_key );

		$description = sprintf( '#%s Pay charitable contribution for event %s. ', $participant_id, get_the_title( $participant->distance ) );
		if ( $participant->coupon ) {
			$description .= ' ' . sprintf( 'Coupon %s applied', $participant->coupon );
		}
		$order_id = implode( '_', [
			\modules\event\Functions::get_current_event()->posts[0]->post_name,
			get_post( $participant->distance )->post_name,
			$participant_id
		] );

		$params = array(
			'action'       => 'pay',
			'amount'       => $participant->get_amount_to_pay(),
			'currency'     => \LiqPay::CURRENCY_UAH,
			'description'  => $description,
			'order_id'     => $order_id,
			'version'      => '3',
			'language'     => \modules\theme\Functions::get_current_language(),
			'public_key'   => $public_key,
			'server_url'   => home_url( '/wp-json/register/paymentSuccess' ),
			'expired_date' => $participant->get_expired_time(),
		);
		if ( self::is_sandbox() ) {
			$params['sandbox'] = 1;
		}
		if ( $result_url = self::get_result_url() ) {
			$params['result_url'] = $result_url;
		}

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

	/**
	 * @return bool
	 */
	public static function is_sandbox(): bool {
		return (bool) Option::get( 'liqpay_sandbox' );
	}

	/**
	 * @return string
	 */
	public static function get_result_url(): string {
		$translations = pll_get_post_translations( Option::get( 'liqpay_result_page' ) );

		return isset( $translations[ pll_current_language() ] ) ? get_permalink( $translations[ pll_current_language() ] ) : get_permalink( (int) Option::get( 'liqpay_result_page' ) );
	}

	/**
	 * @param $raw_data
	 * @param $signature
	 */
	public static function process_success( $raw_data, $signature ) {
		$data           = json_decode( base64_decode( $_POST['data'] ) );
		$participant_id = (int) array_pop( explode( '_', $data->order_id ) );
		if ( ! self::is_valid_request( $raw_data, $signature ) ) {
			Log::error( 'Not valid request: ' . $data->payment_id, $participant_id );

			return;
		}
		Log::info( 'Payment info received: ' . json_encode( $data ), $participant_id );
		if ( in_array( $data->status, [ 'sandbox', 'success', 'hold_wait', 'wait_lc', 'wait_accept' ] ) ) {
			\modules\registration\Functions::finish_registration( $participant_id, $data );
		}
	}

	private static function is_valid_request( string $data, string $signature ): bool {
		$private_key     = self::get_private_key();
		$valid_signature = base64_encode( sha1( $private_key . $data . $private_key, 1 ) );

		return $valid_signature == $signature;
	}

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
}