<?php

namespace modules\sponsor;

use WPKit\Module\AbstractFunctions;
use WPKit\PostType\MetaBox;

/**
 * Class Functions
 *
 * @package modules\sponsor
 */
class Functions extends AbstractFunctions {
	public static function get_sponsors() {
		return new \WP_Query( [
			'post_type'      => Initialization::POST_TYPE,
			'posts_per_page' => - 1,
		] );
	}

	public static function get_url(): string {
		return MetaBox::get( get_the_ID(), Initialization::POST_TYPE, 'url' );
	}

	public static function get_type(): string {
		return MetaBox::get( get_the_ID(), Initialization::POST_TYPE, 'type' );
	}
}
