<?php

namespace modules\coupons;

use WPKit\AdminPage\TablePage;
use WPKit\Module\AbstractInitialization;
use WPKit\PostType\PostType;
use WPKit\Table\Table;

/**
 * Class Initialization
 *
 * @package modules\theme
 */
class Initialization extends AbstractInitialization {
	const TYPES = [
		'PERCENTAGE' => 0,
		'FIXED'      => 1
	];
	/**
	 * @var PostType
	 */
	private $post_type;


	public function admin_register_coupons_table() {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( 'CREATE TABLE `'.$wpdb->prefix.'coupons` (
							`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
							`code` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT \'\',
							`amount` tinyint(4) DEFAULT NULL,
							`used` int(11) DEFAULT \'0\',
							`count` int(11) DEFAULT \'1\',
							`type` tinyint(1) DEFAULT \'0\',
							`status` tinyint(1) DEFAULT \'1\',
							`created` datetime DEFAULT \'0000-00-00 00:00:00\',
							PRIMARY KEY (`code`),
							KEY `id` (`id`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;' );

		$table = new TablePage( 'coupons', 'Coupons' );
		$table->set_menu_position(6);
		$table->set_menu_icon( 'dashicons-tickets-alt' );
		$t = new Table( 'coupons', 'id' );
		$t->setup_general_column( 'coupon_code', 'Code', function ( $data ) {
			return $data['code'];
		}, false, true );

		$t->setup_column( 'amount', 'Amount', function ( $item, $key ) {
			$coupon = new Coupon( $item );

			return $coupon->get_amount_string();
		} );
		$t->setup_column( 'used', __( 'Used', 'colorrun' ), function ( $item, $key ) {
			$coupon = new Coupon( $item );

			return $coupon->get_used_string();
		} );
		$t->setup_column( 'status', __( 'Status' ), function ( $item, $key ) {
			$coupon = new Coupon( $item );

			return $coupon->get_status();
		} );
		$t->setup_column( 'created', __( 'Created' ), null, true, true );
		$t->add_action( 'Edit', function ( $action, $item ) {
		} );
		$t->add_action( 'Activate', function ( $action, $id ) {
			$coupon = new Coupon( (int) $id );
			$coupon->set_active();
		}, true );
		$t->add_action( 'Deactivate', function ( $action, $id ) {
			$coupon = new Coupon( (int) $id );
			$coupon->set_used();
		}, true );

		$table->set_table( $t );
	}

	public function admin_register_ajax() {
		add_action( 'wp_ajax_coupon_get_info', function () {
			$code   = (string) $_POST['code'];
			$coupon = new Coupon( $code );
			if ( ! $coupon->get_data() ) {
				wp_send_json_error( 'No coupon found' );
			}
			wp_send_json_success( $coupon->get_data() );
		} );

		add_action( 'wp_ajax_coupon_save', function () {
			unset( $_POST['action'] );
			$coupon = new Coupon( $_POST );
			if ( ! $coupon->save() ) {
				wp_send_json_error( 'Can\'t save coupon' );
			}
			wp_send_json_success();
		} );
	}

	public function add_action_admin_enqueue_scripts( $hook ) {
		if ( $hook == 'toplevel_page_coupons' ) {
			wp_register_script( 'coupons_script', Functions::get_module_assets_url() . '/javascript/quick-edit.js', [ 'wp-util' ] );
			wp_enqueue_script( 'coupons_script' );

			wp_register_style( 'coupons_styles', Functions::get_module_assets_url() . '/stylesheets/coupons-edit.css' );
			wp_enqueue_style( 'coupons_styles' );
		}
	}

	public function add_action_admin_footer() {
		include __DIR__ . '/view/edit-template.php';
	}

	public function admin_register_coupon_generator_page() {
		new GeneratorPage( 'coupons_generator', __( 'Coupons generator', 'colorrun' ), 'coupons' );
	}
}

