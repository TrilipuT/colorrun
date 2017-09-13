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
		$this->data = $data;
	}

	public function __get( $key ) {
		return $this->data[ $key ];
	}

	public function set_active() {
		$this->status = self::STATUS_ACTIVE;

		return $this->wpdb->update( $this->table, [ 'status' => self::STATUS_ACTIVE ], [ 'code' => $this->code ] );
	}

	public function set_used() {
		$this->status = self::STATUS_USED;

		return $this->wpdb->update( $this->table, [ 'status' => self::STATUS_USED ], [ 'code' => $this->code ] );
	}

	public function get_status() {
		$statuses = self::get_statuses();

		return $statuses[ $this->status ];
	}

	public static function get_statuses() {
		return [
			self::STATUS_USED   => __( 'Used', TEXT_DOMAIN ),
			self::STATUS_ACTIVE => __( 'Aсtive', TEXT_DOMAIN ),
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
			self::TYPES['FIXED']      => __( 'UAH', TEXT_DOMAIN )
		];
	}

	public static function __use_coupon( string $code ): bool {
		/** @var \WP_Post $coupon */
		$coupon = get_page_by_title( $code, OBJECT, Initialization::POST_TYPE );
		if ( $coupon == null ) {
			return false;
		}
		$count = self::get_count( $coupon->ID );
		$used  = self::get_used( $coupon->ID );
		if ( $used == $count ) {
			return false;
		}
		$used = $used ++;
		if ( ! MetaBox::set( $coupon->ID, Initialization::POST_TYPE, 'used', $used ) ) {
			return false;
		}
		if ( $used == $count ) {
			wp_update_post([
				'ID' => $coupon->ID,
				'post_status' => ''
			]);
		}

		return true;
	}
}