<?php
/**
 * Created by PhpStorm.
 * User: vitaly
 * Date: 8/21/17
 * Time: 20:07
 */

namespace modules\coupons;


class Coupon {
	const TYPES = [
		'PERCENTAGE' => 0,
		'FIXED'      => 1
	];

	const STATUS_USED = 0;
	const STATUS_ACTIVE = 1;

	const TABLE = 'coupons';
	private $data = [];
	private $wpdb;

	/**
	 * @var $id int
	 * @var $code string
	 * @var $amount int
	 * @var $used int
	 * @var $count int
	 * @var $type int
	 * @var $status int
	 * @var $created string
	 */

	/**
	 * Coupon constructor.
	 *
	 * @param array|string|int $data
	 */
	public function __construct( $data ) {
		global $wpdb;
		$this->wpdb  = $wpdb;
		$this->table = $wpdb->prefix . self::TABLE;
		if ( is_array( $data ) ) {
			$this->data = $data;
		} else if ( is_int( $data ) ) {
			$this->data = $this->get_by_id( $data );
		} else if ( is_string( $data ) ) {
			$this->data = $this->get_by_code( $data );
		}
	}

	private function get_by_id( int $id ) {
		return $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$this->table} WHERE id = %d", $id ), ARRAY_A );
	}

	private function get_by_code( string $code ) {
		return $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$this->table} WHERE code = %s", $code ), ARRAY_A );
	}

	public function use_coupon( int $price ) {
		if ( $this->used == $this->count ) {
			return false;
		}

		$this->set_data( [ 'used' => $this->used + 1 ] );
		if ( $this->used == $this->count ) {
			$this->set_data( [ 'status' => self::STATUS_USED ] );
		}

		if ( ! $this->save() ) {
			return false;
		}

		if ( $this->type == self::TYPES['PERCENTAGE'] ) {
			return floor( $price - ( $price * ( absint( $this->amount ) / 100 ) ) );
		}

		return floor( $price - $this->amount );
	}

	public function save() {
		if ( ! $this->id ) {
			return $this->wpdb->insert( $this->table, $this->get_data() );
		}

		return $this->wpdb->update( $this->table, $this->get_data(), [ 'id' => $this->id ] );
	}

	public function get_data() {
		return $this->data;
	}

	public function set_data( array $data ) {
		$this->data = array_merge( $this->data, $data );
	}

	public function __get( $key ) {
		return $this->data[ $key ];
	}

	public function set_active() {
		$this->status = self::STATUS_ACTIVE;

		return $this->save();
	}

	public function set_used() {
		$this->status = self::STATUS_USED;

		return $this->save();
	}

	public function get_status() {
		$statuses = self::get_statuses();

		return $statuses[ $this->status ];
	}

	public static function get_statuses() {
		return [
			self::STATUS_USED   => __( 'Used', 'colorrun' ),
			self::STATUS_ACTIVE => __( 'Aсtive', 'colorrun' ),
		];
	}

	public function get_used_string() {
		return $this->used . ' / ' . $this->count;
	}

	public function get_amount_string(): string {
		$types = self::get_types();

		return $this->amount . ' ' . $types[ $this->type ];
	}

	public static function get_types() {
		return [
			self::TYPES['PERCENTAGE'] => '%',
			self::TYPES['FIXED']      => __( 'UAH', 'colorrun' )
		];
	}
}