<?php
/**
 * Created by PhpStorm.
 * User: vitaly
 * Date: 9/6/17
 * Time: 11:44
 */

namespace modules\participant;


use modules\coupons\Coupon;
use modules\logger\Functions as Log;
use WPKit\PostType\MetaBox;

/**
 * Class Participant
 * @package modules\participant
 */
class Participant {
	/**
	 * @var $coupon string
	 */
	private $id;
	private $data = [];
	private $additional_info;

	/**
	 * Participant constructor.
	 *
	 * @param int $id
	 *
	 */
	public function __construct( int $id ) {
		$this->id                  = $id;
		$this->data['bib']         = $this->get_meta( 'bib' );
		$this->data['email']       = strtolower( $this->get_meta( 'email' ) );
		$this->data['distance']    = $this->get_meta( 'distance' );
		$this->data['lastname']    = $this->get_meta( 'lastname' );
		$this->data['firstname']   = $this->get_meta( 'firstname' );
		$this->data['dateofbirth'] = $this->get_meta( 'dateofbirth' );
		$this->data['status']      = $this->get_meta( 'status' );
		$this->data['gender']      = $this->get_meta( 'gender' );
		$this->data['country']     = $this->get_meta( 'country' );
		$this->data['city']        = $this->get_meta( 'city' );
		$this->data['coupon']      = $this->get_meta( 'coupon' );
		$this->data['payment']     = $this->get_meta( 'payment' );
		$this->additional_info     = $this->fill_additional_info();
	}

	/**
	 * @param $name
	 *
	 * @return string
	 */
	private function get_meta( $name ) {
		return MetaBox::get( $this->id, Initialization::POST_TYPE, $name );
	}

	/**
	 * @return array
	 */
	private function fill_additional_info(): array {
		$info = [];
		foreach ( Functions::get_additional_fields() as $key => $title ) {
			$info[ $key ] = MetaBox::get( $this->id, Initialization::POST_TYPE . '_info', $key );
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
		Log::info( 'New participant registered', $participant_id );
		$participant->set_status( \modules\payment\Initialization::STATUS['NOT_PAYED'] );

		return $participant;
	}

	/**
	 * @param string $status
	 *
	 * @return Participant
	 */
	public function set_status( string $status ): Participant {
		$this->status = $status;

		return $this;
	}

	/**
	 * @return array
	 */
	public function get_data_for_export() {
		$data = $this->data;
		$data = array_merge( $data, $this->additional_info );
		unset( $data['payment'] );
		unset( $data['status'] );
		$data['distance'] = get_the_title( $data['distance'] );

		return $data;
	}

	/**
	 * @param string $name
	 *
	 * @return mixed|string
	 */
	public function get_additional_info( string $name ) {
		return isset( $this->additional_info[ $name ] ) ? $this->additional_info[ $name ] : '';
	}

	/**
	 * Finish registration - assign bib
	 *
	 * @param bool $is_send_email
	 */
	public function finish_registration( $is_send_email = true ) {
		$this->set_status( \modules\payment\Initialization::STATUS['PAYED'] );
		wp_publish_post( $this->id );
		$this->assign_bib( $is_send_email );
		wp_cache_delete( 'registered_for_distance_count_' . $this->distance, 'distance' );
	}

	/**
	 * Assign bib and send email if needed
	 *
	 * @param bool $is_send_email
	 *
	 * @return bool
	 */
	private function assign_bib( $is_send_email = true ) {
		$bib = \modules\distance\Functions::get_next_free_bib( $this->distance );
		if ( ! $bib ) {
			Log::error( 'No bib found' . $bib, $this->get_id() );

			return false;
		}
		$this->bib = $bib;
		Log::info( 'Assigned bib ' . $bib, $this->get_id() );
		if ( $is_send_email ) {
			$this->send_notification_email();
		}

		return true;
	}

	/**
	 * @return int|string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * @return bool
	 */
	public function send_notification_email() {
		$subject = $this->replace_placeholders( Functions::get_email_subject() );
		$message = nl2br( $this->replace_placeholders( Functions::get_email_message() ) );
		$headers = [
			'Content-Type: text/html; charset=UTF-8',
			'From: ' . get_option( 'blogname' ) . ' <' . get_option( 'admin_email' ) . '>',
		];

		return wp_mail( $this->email, $subject, $message, $headers );
	}

	/**
	 * @param $content
	 *
	 * @return mixed
	 */
	public function replace_placeholders( $content ) {
		$data = (array) $this->data;
		foreach ( $this->additional_info as $key => $value ) {
			$data[ 'info_' . $key ] = $value;
		}
		$data['distance'] = get_the_title( $this->distance );
		$data['event']    = get_the_title( \modules\event\Functions::get_current_event()->post );
		$data['status']   = \modules\payment\Functions::get_statuses()[ $data['status'] ];
		foreach ( $this->payment as $key => $value ) {
			$data[ 'payment_' . $key ] = $value;
			if ( in_array( $key, [ 'create_date', 'end_date' ] ) ) {
				$data[ 'payment_' . $key ] = get_date_from_gmt( date( 'Y-m-d H:i:s', (int) $value / 1000 ) );
			}
		}
		unset( $data['payment'] );
		$data['name'] = $this->firstname . ' ' . $this->lastname;

		foreach ( $data as $key => $value ) {
			$content = str_replace( "{{{$key}}}", $value, $content );
		}

		return $content;
	}

	public function __get( $name ) {
		return $this->data[ $name ];
	}

	public function __set( $name, $value ) {
		$this->data[ $name ] = $value;
		if ( $name == 'dateofbirth' ) {
			$value = date( 'Y-m-d', strtotime( $value ) );
		} elseif ( $name == 'email' ) {
			$value               = strtolower( $value );
			$this->data[ $name ] = $value;
		}

		return (bool) MetaBox::set( $this->id, Initialization::POST_TYPE, $name, $value );
	}

	/**
	 * @param int $id
	 *
	 * @return Participant
	 */
	public function set_distance( int $id ): self {
		$this->distance = $id;

		return $this;
	}

	/**
	 * @return int
	 */
	public function get_amount_to_pay(): int {
		$price = \modules\distance\Functions::get_current_price( $this->distance );
		if ( $this->coupon ) {
			$coupon = new Coupon( $this->coupon );
			$price  = $coupon->apply_to_price( $price );
		}

		return (int) $price;
	}

	/**
	 * @return mixed
	 */
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
			'status_name'     => \modules\payment\Functions::get_statuses()[ $this->status ],
			'status'          => $this->status,
			'additional_info' => $this->fill_additional_info(),
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
		if ( ! $coupon->use_coupon() ) {
			return false;
		}
		$this->coupon = $coupon_code;
		$new_price    = $coupon->apply_to_price( $price );
		Log::info( "Coupon used {$coupon_code} for {$coupon->get_amount_string()}", $this->get_id() );

		return $new_price;
	}

	/**
	 * @param $format
	 *
	 * @return string
	 */
	public function get_expired_time( $format = 'Y-m-d H:i:s' ): string {
		$start_time     = get_the_time( 'Y-m-d H:i:s', $this->id );
		$start_time_utc = get_gmt_from_date( $start_time, 'U' );

		return date( $format, $start_time_utc + ( 15 * MINUTE_IN_SECONDS ) );
	}

	/**
	 * @param array $info
	 *
	 * @throws \WPKit\Exception\WpException
	 */
	public function set_info( array $info ) {
		foreach ( $info as $key => $value ) {
			if ( $key == 'info' ) {
				foreach ( $value as $k => $v ) {
					MetaBox::set( $this->id, Initialization::POST_TYPE . '_info', $k, $v );
					$this->additional_info[ $k ] = $v;
				}
			} else if ( in_array( $key, array_keys( Functions::get_additional_fields() ) ) ) {
				MetaBox::set( $this->id, Initialization::POST_TYPE . '_info', $key, $value );
				$this->additional_info[ $key ] = $value;
			} else {
				$this->$key = $value;
			}
		}
		if ( isset( $info['firstname'] ) && isset( $info['lastname'] ) ) {
			wp_update_post( [
				'ID'         => $this->id,
				'post_title' => $info['firstname'] . ' ' . $info['lastname'],
			] );
		}
	}
}