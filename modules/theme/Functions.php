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
			'theme_location'  => 'footer',
			'id'              => 'footer',
			'container_class' => 'footer-nav',
			'container'       => 'nav',
			'depth'           => 1
		] );
	}

	/**
	 * @return string
	 */
	public static function get_background_image(): string {
		if ( $bg = get_background_image() ) {
			return $bg;
		}

		return get_the_post_thumbnail_url( get_the_ID(), 'hero' );
	}

	/**
	 * @return string
	 */
	public static function get_current_language(): string {
		$lang = 'uk';
		if ( function_exists( 'pll_current_language' ) ) {
			$lang = pll_current_language();
		}
		if ( ! $lang ) {
			$lang = 'uk';
		}

		return $lang;
	}
}
