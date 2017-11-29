<?php

namespace modules\payment;

use modules\participant\Participant;
use WPKit\AdminPage\OptionPage;
use WPKit\Fields\Select2;
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
			$options->add_field( 'liqpay_sandbox', __( 'Private key', 'colorrun' ), 'Checkbox' );
			$pages = function () {
				$f = new Select2();
				$f->set_options( wp_list_pluck( get_pages(), 'post_title', 'ID' ) );
				$f->set_placeholder( 'Select page' );

				return $f;
			};
			$options->add_field( 'liqpay_result_page', __( 'Result page', 'colorrun' ), $pages );
			$options->set_page( 'payment' );
		}, 11 );
	}

	public function add_action_template_redirect() {
		if ( isset( $_POST['data'] ) && isset( $_POST['signature'] ) && $_POST['data'] && $_POST['signature'] ) {
			Functions::process_success( $_POST['data'], $_POST['signature'] );

			$data = json_decode( base64_decode( $_POST['data'] ) );
			add_filter( 'the_content', function ( $content ) use ( $data ) {
				foreach ( $data as $key => $value ) {
					$content = str_replace( "{{{$key}}}", $value, $content );
				}

				return $content;
			} );
		}
	}

	public function _add_action_template_redirect() {
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
//			$params    = $liqpay->cnb_signature($params);


			$data      = base64_encode( json_encode( $params ) );
			$signature = $liqpay->cnb_signature( $params );

			$html = "<div id=\"liqpay_checkout\"></div>
<script>
    window.LiqPayCheckoutCallback = function() {
    LiqPayCheckout.init({
	data: \"{$data}\",
	signature: \"{$signature}\",
	embedTo: \"#liqpay_checkout\",
	language: \"ru\",
	mode: \"embed\" // embed || popup
    }).on(\"liqpay.callback\", function(data){
    	console.log(data.status);
    	console.log(data);
    }).on(\"liqpay.ready\", function(data){
    	// ready
    }).on(\"liqpay.close\", function(data){
    	// close
    });
};
</script>
<script src=\"//static.liqpay.ua/libjs/checkout.js\" async></script>";
			echo $html;
		}
	}
}

