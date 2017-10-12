<?php

namespace modules\shortcode\shortcodes;

use modules\shortcode\AbstractShortcode;

/**
 * Class Quote
 *
 * @package modules\shortcode\shortcodes
 */
class Acordeon extends AbstractShortcode {
	/**
	 * @var string
	 */
	protected static $_name = 'acordeon';
	/**
	 * @var string
	 */
	protected static $_icon = 'dashicons-format-quote';

	protected static $_post_types = [ 'post', 'page' ];

	/**
	 * @return string
	 */
	protected static function _get_label() {
		return __( 'Acordeon', TEXT_DOMAIN );
	}

	protected function _add_fields() {
		$this->_add_field( 'title', __( 'Title', TEXT_DOMAIN ) );
		$this->_add_field( 'content', __( 'Text', TEXT_DOMAIN ), 'textarea' );
	}
}