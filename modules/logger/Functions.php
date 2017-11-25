<?php

namespace modules\logger;

use Monolog\Logger;
use WPKit\Module\AbstractFunctions;

/**
 * Class Functions
 *
 * @package modules\theme
 */
class Functions extends AbstractFunctions {

	public static function info( string $message, int $participant_id = 0 ) {
		self::get_logger()->info( $message, self::get_context( $participant_id ) );
	}

	private static function get_logger() {
		/* @var $logger Logger */
		global $logger;

		return $logger;
	}

	private static function get_context( int $participant_id = 0 ) {
		$context = [];
		if ( $participant_id ) {
			$context = [ 'participant_id' => $participant_id ];
		}

		return $context;
	}

	public static function error( string $message, int $participant_id = 0 ) {
		self::get_logger()->error( $message, self::get_context( $participant_id ) );
	}
}
