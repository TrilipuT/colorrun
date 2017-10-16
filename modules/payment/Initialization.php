<?php

namespace modules\payment;

use modules\participant\Participant;
use WPKit\AdminPage\OptionPage;
use WPKit\Module\AbstractInitialization;
use WPKit\Options\OptionBox;

/**
 * Class Initialization
 *
 * @package modules\theme
 */
class Initialization extends AbstractInitialization {
	const STATUS = [
		'NOT_PAYED'        => 0,
		'AWAITING_PAYMENT' => 1,
		'PAYED'            => 2,
	];

	public function register_payment_url() {
		add_rewrite_rule(
			'payment/([0-9]+)/?$',
			'index.php?type=payment&participant_id=$matches[1]',
			'top' );

		add_filter( 'query_vars', function ( $query_vars ) {
			$query_vars[] = 'participant_id';

			return $query_vars;
		} );


	}

	public function register_options() {
		add_action( 'init', function () {
			new OptionPage( 'payment', __( 'Payment', 'colorrun' ), 'theme_settings' );
			$options = new OptionBox( 'liqpay_options', __( 'LiqPay', 'colorrun' ) );
			$options->add_field( 'liqpay_public_key', __( 'Public key', 'colorrun' ) );
			$options->add_field( 'liqpay_private_key', __( 'Private key', 'colorrun' ) );
			$options->set_page( 'payment' );
		}, 11 );

	}

	public function add_action_template_redirect() {
		$participant_id = get_query_var( 'participant_id' );
		if ( $participant_id ) {
			$participant = new Participant( $participant_id );
			if ( $participant->get_payment_status() == self::STATUS['PAYED'] ) {
				//TODO: redirect here
				echo 'already payed.';
			}
			$public_key  = Functions::get_public_key();
			$private_key = Functions::get_private_key();
			$liqpay      = new \LiqPay( $public_key, $private_key );
			$html        = $liqpay->cnb_form( array(
				'action'      => 'pay',
				'amount'      => $participant->get_amount_to_pay(),
				'currency'    => \LiqPay::CURRENCY_UAH,
				'description' => sprintf( 'Pay for %s paticipation in event %s', $participant_id, get_the_title( $participant->get_info()['distance'] ) ),
				'order_id'    => $participant_id,
				'version'     => '3'
			) );
			echo $html;
		}
	}
}

