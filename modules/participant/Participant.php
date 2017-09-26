<?php
/**
 * Created by PhpStorm.
 * User: vitaly
 * Date: 9/6/17
 * Time: 11:44
 */

namespace modules\participant;


use WPKit\PostType\MetaBox;

/**
 * Class Participant
 * @package modules\participant
 */
class Participant {
	private $id;
	private $firstname;
	private $lastname;
	private $dateofbirth;

	private $distance;
	private $bib;
	private $email;
	private $status_code;
	private $additional_info;

	/**
	 * Participant constructor.
	 *
	 * @param int $id
	 */
	public function __construct( int $id ) {
		$this->id              = $id;
		$this->bib             = $this->get_meta( 'bib' );
		$this->email           = $this->get_meta( 'email' );
		$this->distance        = $this->get_meta( 'distance' );
		$this->lastname        = $this->get_meta( 'lastname' );
		$this->firstname       = $this->get_meta( 'firstname' );
		$this->dateofbirth     = $this->get_meta( 'dateofbirth' );
		$this->status_code     = $this->get_meta( 'status' );
		$this->additional_info = $this->get_additional_info();
	}

	public function get_amount_to_pay(): int {
		$amount = \modules\distance\Functions::get_current_amount( $this->distance );

		return (int) $amount;
	}

	/**
	 * @param $name
	 *
	 * @return string
	 */
	private function get_meta( $name ): string {
		return (string) MetaBox::get( $this->id, Initialization::POST_TYPE, $name );
	}

	public function get_payment_status() {
		return $this->status_code;
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
	 * @return array
	 */
	public function get_info(): array {
		return [
			'ID'              => $this->id,
			'email'           => $this->email,
			'firstname'       => $this->firstname,
			'lastname'        => $this->lastname,
			'status'          => Functions::get_statuses()[ $this->status_code ],
			'status_code'     => $this->status_code,
			'additional_info' => $this->get_additional_info(),
		];
	}

}