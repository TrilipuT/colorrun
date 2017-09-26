<?php

namespace modules\homepage;

use WPKit\Module\AbstractFunctions;
use WPKit\PostType\MetaBox;
use WPKit\PostType\MetaBoxRepeatable;

/**
 * Class Functions
 *
 * @package modules\homepage
 */
class Functions extends AbstractFunctions {
	public static function get_info_items(): array {
		$id     = get_the_ID();
		$icons  = MetaBoxRepeatable::get( $id, Initialization::HOME_INFO, 'icon' );
		$titles = MetaBoxRepeatable::get( $id, Initialization::HOME_INFO, 'title' );
		$texts  = MetaBoxRepeatable::get( $id, Initialization::HOME_INFO, 'text' );
		$items  = [];
		foreach ( $titles as $i => $title ) {
			$items[] = [
				'icon'  => $icons[ $i ],
				'title' => $title,
				'text'  => wpautop( $texts[ $i ] ),
			];
		}

		return $items;
	}

	public static function get_distances_image( string $size = 'full' ): string {
		$id    = get_the_ID();
		$image = MetaBox::get( $id, Initialization::HOME_DISTANCES, 'image' );
		if ( ! $image ) {
			return '<img src="' . get_template_directory_uri() . '/assets/built/images/fake/distance-photo.jpg" alt="">';
		}

		return wp_get_attachment_image( $image, $size );
	}

	public static function get_corporate_image( string $size = 'full' ): string {
		$id    = get_the_ID();
		$image = MetaBox::get( $id, Initialization::HOME_CORPORATE, 'image' );
		if ( ! $image ) {
			return '<img src="' . get_template_directory_uri() . '/assets/built/images/fake/corporate.jpg" alt="">';
		}

		return wp_get_attachment_image( $image, $size );
	}

	public static function get_corporate_texts(): array {
		return [
			MetaBox::get( get_the_ID(), Initialization::HOME_CORPORATE, 'text_left' ),
			MetaBox::get( get_the_ID(), Initialization::HOME_CORPORATE, 'text_right' ),
		];
	}

	public static function get_latest_news( int $count = 4 ): \WP_Query {
		return new \WP_Query( [
			'posts_per_page' => $count
		] );
	}
}
