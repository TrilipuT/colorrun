<?php
namespace modules\customization;

use WPKit\Module\AbstractFunctions;


class Functions extends AbstractFunctions {

	public static function get_theme_mod_filtered() {
		$def_keys = array(
			'theme-light-color'      	=> '#ffffff',
			'theme-dark-color'       	=> '#000000',
			'bg-color'               	=> '#2c2e37',
			'header-elements-bg-color' 	=> '#1c1d23',
			'main-color'             	=> '#f84444',
			'content-color'          	=> '#333333',
			'content-elements-color' 	=> '#ccbd9a',
			'separator-color' 			=> '#f2f2f2',
			'font-title'             	=> 'PT Sans Narrow',
			'font-content'           	=> 'PT Sans',
			'font-content-serif'     	=> 'PT Serif',
			'font-time'              	=> 'PT Mono',
		);
		$mods     = get_theme_mods();
		if ( $mods ) {
			foreach ( $def_keys as $key => $mod ) {
				if ( ! isset( $mods[ $key ] ) ) {
					$mods[ $key ] = $mod;
				}
			}
		}

		foreach ( $mods as $key => $value ) {
			if ( ! is_string( $value ) ) {
				continue;
			}
			if ( strpos( $key, 'font' ) === 0 ) {
				$mods[ $key . '-web' ] = str_replace( ' ', '+', $value );
			} else if ( strpos( $value, '#' ) === 0 ) {
				list( $r, $g, $b ) = array_map( 'hexdec', str_split( ltrim( $value, '#' ), 2 ) );
				$mods[ $key . '-rgb' ] = $r . ',' . $g . ',' . $b;
			}
		}

		return $mods;
	}
}