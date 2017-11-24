<?php
/**
 * Created by PhpStorm.
 * User: vitaly
 * Date: 9/6/17
 * Time: 11:44
 */

namespace modules\participant;


use modules\coupons\Coupon;
use WPKit\PostType\MetaBox;

/**
 * Class Participant
 * @package modules\participant
 */
class Participant {
	private $id;
	private $data = [];
	private $additional_info;

	/**
	 * Participant constructor.
	 *
	 * @param int $id
	 *
	 * @return Participant
	 */
	public function __construct( int $id ) {
		$this->id                  = $id;
		$this->data['bib']         = $this->get_meta( 'bib' );
		$this->data['email']       = $this->get_meta( 'email' );
		$this->data['distance']    = $this->get_meta( 'distance' );
		$this->data['lastname']    = $this->get_meta( 'lastname' );
		$this->data['firstname']   = $this->get_meta( 'firstname' );
		$this->data['dateofbirth'] = $this->get_meta( 'dateofbirth' );
		$this->data['status']      = $this->get_meta( 'status' );
		$this->data['coupon']      = $this->get_meta( 'coupon' );
		$this->additional_info     = $this->get_additional_info();
	}

	/**
	 * @param $name
	 *
	 * @return string
	 */
	private function get_meta( $name ): string {
		return (string) MetaBox::get( $this->id, Initialization::POST_TYPE, $name );
	}

	/**
	 * @return array
	 */
	private function get_additional_info(): array {
		$info = [];
		foreach ( Functions::get_additional_fileds() as $key => $title ) {
			$info[ $key ] = MetaBox::get( $this->id, Initialization::POST_TYPE . '_additional', $key );
		}

		return $info;
	}

	/**
	 * Create new participant. Set status to NOT_PAYED
	 * @return Participant
	 */
	public static function create(): Participant {
		$participant_id = wp_insert_post( [ 'post_type' => Initialization::POST_TYPE ] );
		$participant    = new Participant( $participant_id );
		$participant->set_status( \modules\payment\Initialization::STATUS['NOT_PAYED'] );

		return $participant;
	}

	public function set_status( string $status ): Participant {
		$this->status = $status;

		return $this;
	}

	public function get_id() {
		return $this->id;
	}

	public function __get( $name ) {
		return $this->data[ $name ];
	}

	public function __set( $name, $value ) {
		$this->data[ $name ] = $value;

		return (bool) MetaBox::set( $this->id, Initialization::POST_TYPE, $name, $value );
	}

	public function set_distance( int $id ): self {
		$this->distance = $id;

		return $this;
	}

	public function get_amount_to_pay(): int {
		$amount = \modules\distance\Functions::get_current_price( $this->distance );

		return (int) $amount;
	}

	public function get_payment_status() {
		return $this->status;
	}

	/**
	 * @return array
	 */
	public function get_info(): array {
		return [
			'id'              => $this->id,
			'email'           => $this->email,
			'firstname'       => $this->firstname,
			'lastname'        => $this->lastname,
			'status_name'     => Functions::get_statuses()[ $this->status ],
			'status'          => $this->status,
			'additional_info' => $this->get_additional_info(),
		];
	}

	/**
	 * @param string $coupon_code
	 *
	 * @return bool|float|int|mixed
	 */
	public function use_coupon( string $coupon_code ) {
		$coupon = new Coupon( $coupon_code );
		$price  = \modules\distance\Functions::get_current_price( $this->distance );
		if ( ! $new_price = $coupon->use_coupon( $price ) ) {
			return false;
		}
		$this->coupon = $coupon_code;

		return $new_price;
	}

	public function set_info( array $info ) {
		foreach ( $info as $key => $value ) {
			$this->$key = $value;
		}
		if ( isset( $info['firstname'] ) && isset( $info['lastname'] ) ) {
			wp_update_post( [
				'ID'         => $this->id,
				'post_title' => $info['firstname'] . ' ' . $info['lastname'],
			] );
		}
	}
}