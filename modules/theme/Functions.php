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
			'twitter'  => Option::get( 'twitter' ),
			'inst'     => Option::get( 'instagram' ),
			'telegram' => Option::get( 'telegram' ),
		];

		return array_filter( $socials );
	}

	/**
	 * @param $wp_query
	 */
	public static function filter_home_query( \WP_Query &$wp_query ) {
		$featured_id = \modules\article\Functions::get_featured();
		if ( ! is_paged() ) {
			if ( $featured_id ) {
				$featured = get_post( $featured_id );
				$wp_query->post_count ++;
				array_unshift( $wp_query->posts, $featured );
			} else if ( $wp_query->posts[0]->post_type != \modules\article\Initialization::POST_TYPE ) {
				foreach ( $wp_query->posts as $i => $post ) {
					if ( $post->post_type == \modules\article\Initialization::POST_TYPE && $i != 0 ) {
						$featured = $post;
						unset( $wp_query->posts[ $i ] );
						break;
					}
				}
				array_unshift( $wp_query->posts, $featured );
			}
		}
	}

	public static function get_pagination() {
		return paginate_links( [
				'prev_text' => "<svg class=\"sprite-icon sprite-icon-arrow-big-left\"><use xlink:href=\"#arrow-big-left\"/></svg>",
				'next_text' => "<svg class=\"sprite-icon sprite-icon-arrow-big-right\"><use xlink:href=\"#arrow-big-right\"/></svg>",
			]
		);
	}

}
