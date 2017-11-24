<?php

namespace modules\logger;

use Monolog\Logger;
use MySQLHandler\MySQLHandler;
use WPKit\Module\AbstractInitialization;

/**
 * Class Initialization
 *
 * @package modules\theme
 */
class Initialization extends AbstractInitialization {

	public function register_logger() {
		global $logger;
		$logger = new Logger( 'registration' );
		$pdo    = new \PDO( 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD );

		$mySQLHandler = new MySQLHandler( $pdo, "log", array( 'username', 'userid' ), \Monolog\Logger::INFO );
		$logger->pushHandler( $mySQLHandler );
	}
}
