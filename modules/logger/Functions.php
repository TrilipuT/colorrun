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

	public static function log( $message, $context = [] ) {
		/* @var $logger Logger */
		global $logger;
		$logger->info( $message, $context );
	}
}
