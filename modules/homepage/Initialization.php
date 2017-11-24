<?php

namespace modules\homepage;

use WPKit\Fields\Select2;
use WPKit\Module\AbstractInitialization;
use WPKit\PostType\MetaBox;
use WPKit\PostType\MetaBoxRepeatable;

/**
 * Class Initialization
 *
 * @package modules\homepage
 */
class Initialization extends AbstractInitialization {
	const HOME_INFO = 'home_info';
	const HOME_DISTANCES = 'home_distances';
	const HOME_CORPORATE = 'home_corporate';

	public function register_settings() {
		$defaults = array(
			'default-color'          => '',
			'default-image'          => '',
			'default-repeat'         => '',
			'default-position-x'     => '',
			'default-attachment'     => '',
			'wp-head-callback'       => '__return_false',
			'admin-head-callback'    => '',
			'admin-preview-callback' => ''
		);

		add_theme_support( 'custom-background', $defaults );
	}

	public function admin_register_about_metabox() {
		$meta = new MetaBoxRepeatable( self::HOME_INFO, __( 'Homepage about blocks', 'colorrun' ) );
		$meta->add_field( 'icon', __( 'Icon', 'colorrun' ), function () {
			$f = new Select2();
//			$f->add_select2_option( 'templateResult', 'window.formatIcon' );
			$scanned_directory = array_diff( scandir( get_template_directory() . '/assets/src/images/sprite/' ), array(
				'..',
				'.'
			) );
			$options           = array_map( function ( $item ) {
				return str_replace( '.svg', '', $item );
			}, $scanned_directory );

			$f->set_options( array_combine( $options, array_map( function ( $item ) {
				return ucfirst( $item );
			}, $options ) ) );

			echo '<script type="application/javascript">
			var formatIcon = function(state) {
			   
  if (!state.id) {
    return state.text;
  }
  var baseUrl = "' . get_template_directory_uri() . '/assets/src/images/sprite/";
  var $state = jQuery(
    \'<span><img src="\' + baseUrl + \'/\' + state.element.value.toLowerCase() + \'.svg" class="img-flag" /> \' + state.text + \'</span>\'
  );
  return $state;
};
</script>';

			return $f;
		} );
		$meta->add_field( 'title', __( 'Title' ) );
		$meta->add_field( 'text', __( 'Text' ), 'Textarea' );
		$this->add_metabox( $meta );
	}

	private function add_metabox( MetaBox $metabox ) {
		if ( ! isset( $_GET['post'] ) && ! $_GET['post'] && get_post_type() != 'page' ) {
			return;
		}
		$post_ID = (int) $_GET['post'];
		$pages   = function_exists( 'pll_get_post_translations' ) ? array_values( pll_get_post_translations( (int) get_option( 'page_on_front' ) ) ) : [ get_option( 'page_on_front' ) ];
		if ( in_array( $post_ID, $pages ) ) {
			$metabox->add_post_type( 'page' );
		}
	}

	public function admin_register_distance_metabox() {
		$meta = new MetaBox( self::HOME_DISTANCES, __( 'Homepage distance', 'colorrun' ) );
		$meta->add_field( 'image', __( 'Image' ), 'Image' );
		$this->add_metabox( $meta );
	}

	public function admin_register_corporate_metabox() {
		$meta = new MetaBox( self::HOME_CORPORATE, __( 'Homepage corporate', 'colorrun' ) );
		$meta->add_field( 'image', __( 'Image' ), 'Image' );
		$meta->add_field( 'text_left', __( 'Text left' ), 'WPEditor' );
		$meta->add_field( 'text_right', __( 'Text right' ), 'WPEditor' );
		$this->add_metabox( $meta );
	}
}

