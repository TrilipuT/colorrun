<?php

namespace modules\shortcode\shortcodes;

use modules\article\Initialization;
use modules\shortcode\AbstractShortcode;

/**
 * Class Quote
 *
 * @package modules\shortcode\shortcodes
 */
class Quote extends AbstractShortcode {
	/**
	 * @var string
	 */
	protected static $_name = 'quote';
	/**
	 * @var string
	 */
	protected static $_icon = 'dashicons-format-quote';

	protected static $_post_types = [ 'news', 'page', Initialization::POST_TYPE ];

	/**
	 * @return string
	 */
	protected static function _get_label() {
		return __( 'Quote', 'colorrun' );
	}

	protected function _add_fields() {
		$this->_add_field( 'text', __( 'Text', 'colorrun' ), 'textarea' );
		$this->_add_field( 'author', __( 'Author', 'colorrun' ) );
		$this->_add_field( 'align', __( 'Align', 'colorrun' ), 'select', [
			'options' => array(
				'center'    => 'Center',
				'small'     => 'Small',
				'left'      => 'Small left',
				'right'     => 'Small right',
				'fullwidth' => 'Full width',
			),
		] );
	}
}