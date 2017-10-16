<?php

namespace modules\theme;

use WPKit\Module\AbstractThemeFunctions;
use WPKit\Options\Option;

/**
 * Class Functions
 *
 * @package modules\theme
 */
class Functions extends AbstractThemeFunctions {
	/**
	 * Get array of socials
	 *
	 * @return array
	 */
	public static function get_socials() {
		$socials = [
			'fb'       => Option::get( 'facebook' ),
			'youtube'  => Option::get( 'youtube' ),
			'twitter'  => Option::get( 'twitter' ),
			'inst'     => Option::get( 'instagram' ),
			'telegram' => Option::get( 'telegram' ),
		];

		return array_filter( $socials );
	}

	/**
	 *
	 */
	public static function get_footer_menu() {
		wp_nav_menu( [
			'id'              => 'footer',
			'container_class' => 'footer-nav',
			'container'       => 'nav',
			'depth'           => 1
		] );
	}
}
