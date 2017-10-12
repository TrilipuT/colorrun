<?php

namespace modules\shortcode;

use modules\shortcode\shortcodes\Acordeon;
use WPKit\Module\AbstractInitialization;

/**
 * Class Initialization
 *
 * @package modules\shortcode
 */
class Initialization extends AbstractInitialization {
	public function register_shortcodes() {
//		new Acordeon();
		new Gallery();
	}
}