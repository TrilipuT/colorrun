<?php

namespace modules\event;

use WPKit\Module\AbstractFunctions;
use WPKit\PostType\MetaBox;

/**
 * Class Functions
 *
 * @package modules\theme
 */
class Functions extends AbstractFunctions {

	/**
	 * Get all events
	 *
	 * @return \WP_Query
	 */
	public static function get_all_events() {
		return self::get_events( - 1 );
	}

	private static function get_events( $count ): \WP_Query {
		if ( ! $events = wp_cache_get( 'get_events_' . $count, 'event' ) ) {
			$events = new \WP_Query( [
				'post_type'      => Initialization::POST_TYPE,
				'posts_per_page' => $count,
				'orderby'        => 'meta_value',
				'meta_key'       => Initialization::POST_TYPE . '_date',
			] );
			wp_cache_add( 'get_events_' . $count, $events, 'event' );
		}

		return $events;
	}

	/**
	 * @param string $format
	 *
	 * @return string
	 */
	public static function get_current_event_date( string $format = 'd/m/Y' ): string {
		$event = self::get_current_event();
		if ( ! $event->have_posts() ) {
			return date( $format );
		}
		$date = MetaBox::get( $event->post->ID, Initialization::POST_TYPE, 'date' );


		return date( $format, strtotime( $date ) );
	}

	/**
	 * Get current active event
	 * Return latest event by start date.
	 *
	 * @return \WP_Query
	 */
	public static function get_current_event(): \WP_Query {
		return self::get_events( 1 );
	}
}
