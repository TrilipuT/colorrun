<?php

namespace modules\shortcode\shortcodes;

use modules\article\Initialization;
use modules\shortcode\AbstractShortcode;

/**
 * Class Quote
 *
 * @package modules\shortcode\shortcodes
 */
class Rating extends AbstractShortcode {
	/**
	 * @var string
	 */
	protected static $_name = 'rating';
	/**
	 * @var string
	 */
	protected static $_icon = 'dashicons-thumbs-up';

	protected static $_post_types = [ Initialization::POST_TYPE ];

	/**
	 * @return string
	 */
	protected static function _get_label() {
		return __( 'Rating', 'colorrun' );
	}

	protected function _add_fields() {
		$this->_add_field( 'value', __( 'Rating', 'colorrun' ), 'number' );
	}
}