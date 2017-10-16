<?php

namespace modules\shortcode\shortcodes;

use modules\article\Initialization;
use modules\shortcode\AbstractShortcode;

/**
 * Class Product
 *
 * @package modules\shortcode\shortcodes
 */
class Related extends AbstractShortcode {
	/**
	 * @var string
	 */
	protected static $_name = 'related';
	/**
	 * @var string
	 */
	protected static $_icon = 'dashicons-welcome-add-page';

	protected static $_post_types = [ Initialization::POST_TYPE, 'news' ];

	/**
	 * @return string
	 */
	protected static function _get_label() {
		return __( 'Related', 'colorrun' );
	}

	protected function _add_fields() {
		$this->_add_field( 'post', __( 'Post', 'colorrun' ), 'post_select', [
			'query' => [
				'post_type'   => [ Initialization::POST_TYPE, 'news' ],
				'post_status' => 'publish'
			],
		] );
	}
}