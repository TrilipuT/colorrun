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
			$options->add_field( 'liqpay_sandbox', __( 'Sandbox', 'colorrun' ), 'Checkbox' );
			$pages = function () {
				$f     = new Select2();
				$query = new \WP_Query( [ 'post_type' => 'page', 'lang' => '' ] );
				$f->set_options( wp_list_pluck( $query->posts, 'post_title', 'ID' ) );
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

			$data           = json_decode( base64_decode( $_POST['data'] ) );
			$participant_id = (int) array_pop( explode( '_', $data->order_id ) );
			$participant    = new Participant( $participant_id );
			add_filter( 'the_content', function ( $content ) use ( $participant ) {
				$content = $participant->replace_placeholders( $content );

				return $content;
			} );
		} elseif ( isset( $_GET['participant'] ) && $_GET['participant'] ) {
			$participant = new Participant( (int) $_GET['participant'] );
			add_filter( 'the_content', function ( $content ) use ( $participant ) {
				$content = $participant->replace_placeholders( $content );

				return $content;
			} );
		}
	}
}
