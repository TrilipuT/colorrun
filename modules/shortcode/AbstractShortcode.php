<?php

namespace modules\shortcode;

/**
 * Class AbstractShortcode
 *
 * @package modules\shortcode
 */
abstract class AbstractShortcode {
	/**
	 * @var string|null
	 */
	protected static $_name = null;
	/**
	 * @var string
	 */
	protected static $_icon = '';
	/**
	 * @var array|null
	 */
	protected static $_post_types = [ 'post' ];
	/**
	 * @var array
	 */
	protected $_fields = [];
	/**
	 * @var array
	 */
	protected $_pairs = [];

	public function __construct() {
		if ( static::$_name !== null ) {
			$this->_add_fields();

			if ( is_admin() && function_exists( 'shortcode_ui_register_for_shortcode' ) ) {
				$this->_ui();
			}
			add_shortcode( static::$_name, function ( array $attrs = [] ) {
				return $this->_add( $attrs );
			} );
		}
	}

	abstract protected function _add_fields();

	protected function _ui() {
		shortcode_ui_register_for_shortcode( static::$_name, [
			'label'         => static::_get_label(),
			'listItemImage' => static::$_icon,
			'attrs'         => $this->_fields,
			'post_type'     => static::$_post_types,
		] );
	}

	/**
	 * @return string
	 */
	protected static function _get_label() {
		return '';
	}

	/**
	 * @param array $attrs
	 *
	 * @return string
	 */
	protected function _add( array $attrs ) {
		ob_start();
		$this->_render( shortcode_atts( $this->_pairs, static::_decode_attrs( $attrs ) ) );

		return ob_get_clean();
	}

	protected function _render( array $attrs ) {
		$file = get_template_directory() . '/parts/shortcode/' . $this::$_name . '.php';
		if ( file_exists( $file ) ) {
			require $file;
		}
	}

	/**
	 * @param array $attrs
	 *
	 * @return array
	 */
	protected static function _decode_attrs( array $attrs ) {
		$decoded = [];

		foreach ( $attrs as $key => $attr ) {
			$decoded[ $key ] = urldecode( $attr );
		}

		return $decoded;
	}

	/**
	 * @param string $key
	 * @param string $label
	 * @param string $type
	 * @param array $args
	 */
	protected function _add_field( $key, $label, $type = 'text', array $args = [] ) {
		$this->_fields[]      = wp_parse_args( [
			'label'  => $label,
			'attr'   => $key,
			'type'   => $type,
			'encode' => ! in_array( $type, [ 'attachment' ] ) && $key != 'content',
		], $args );
		$this->_pairs[ $key ] = isset( $args['default'] ) ? $args['default'] : '';
	}
}