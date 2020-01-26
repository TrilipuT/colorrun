<?php

namespace modules\participant;

use WPKit\Module\AbstractFunctions;
use WPKit\Options\Option;
use WPKit\PostType\MetaBox;

/**
 * Class Functions
 *
 * @package modules\theme
 */
class Functions extends AbstractFunctions {

	public static function get_bib( $id ): int {
		return (int) MetaBox::get( $id, Initialization::POST_TYPE, 'bib' );
	}

	public static function get_distance( $id ): int {
		return (int) MetaBox::get( $id, Initialization::POST_TYPE, 'distance' );
	}

	public static function get_additional_fields() {
		return [
			'phone'       => __('Telephone','colorrun'),
			'club'        => __('Club','colorrun'),
			'tshirt_size' => __('T-shirt size','colorrun'),
			'pancakes'    => __('Pancakes','colorrun'),
		];
	}

	public static function get_email_subject() {
		return Option::get( 'registration_subject_' . \modules\theme\Functions::get_current_language(), 'Registration for {{distance}}' );
	}

	public static function get_email_message() {
		return Option::get( 'registration_message_' . \modules\theme\Functions::get_current_language(), 'Dear {{firstname}}! Thank you for registration on {{distance}}. Your bib is {{bib}}' );
	}
}
