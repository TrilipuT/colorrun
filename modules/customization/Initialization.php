<?php

namespace modules\customization;

use WPKit\Module\AbstractModuleInitialization;

class Initialization extends AbstractModuleInitialization {
	public function register_custom_post_types() {
	}

	public function register_customizer_hooks() {
		add_action( 'customize_register', function ( $wp_customize ) {
			/**
			 * @var $wp_customize \WP_Customize_Manager
			 */
			$this->customize_options( $wp_customize );
		} );

		add_action( 'customize_preview_init', array(
			$this,
			'customizer_live_preview_js'
		) );
	}

	/**
	 * Register builder (panel etc.)
	 *
	 * @param \WP_Customize_Manager $wp_customize
	 */
	public function customize_options( $wp_customize ) {
		function add_lang_to_customizer_previewer() {
			if ( function_exists( 'pll_current_language' ) ) {
				$src = get_stylesheet_directory_uri() . '/js/dss-add-lang-to-template.js';
				$deps = array( 'customize-controls' );
				$version = rand();
				$in_footer = 1;
				wp_enqueue_script( 'dss-add-lang-to-template', $src, $deps, $version , $in_footer );
				$lang = pll_current_language();
				if ( empty( $lang ) ) {
					$lang = pll_default_language();
				}
				$url = add_query_arg( 'lang', $lang, pll_home_url( $lang ) );
				add_lang_to_template( $url);
			}
		}
		/**
		 * Set the previewer url
		 *
		 * @author soderlind
		 * @version 1.0.0
		 */
		function add_lang_to_template( $url ) {
			wp_add_inline_script(
				'dss-add-lang-to-template',
				sprintf( 'PSPolyLang.init( %s );', wp_json_encode( array( 'url' => $url ) ) ),
				'after'
			);
		}

		$customize = new Customizer( $wp_customize );
		$wp_customize->remove_section( 'static_front_page' );
		$wp_customize->remove_section( 'nav' );

		$wp_customize->get_setting( 'blogname' )->transport = 'postMessage';
		$wp_customize->selective_refresh->add_partial( 'blogname', [
			'selector'        => '#header .title',
			'render_callback' => function () {
				return get_bloginfo( 'name', 'display' );
			}
		] );
		$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';
		$wp_customize->selective_refresh->add_partial( 'blogdescription', [
			'selector'        => '#header .description',
			'render_callback' => function () {
				return get_bloginfo( 'description', 'display' );
			}
		] );

		// Header
		$customize->add_section( 'header' );
		$customize->add_field( 'place', __( 'Place', TEXT_DOMAIN ), 'header', 'Виставковий центр', 'textarea' );
//		          ->add_partial_refresh( 'place', '#header .place', [ '\modules\theme\Functions', 'get_place' ] );
//		$customize->add_field( 'social_fb', __( 'Facebook', TEXT_DOMAIN ), 'header', '#' );
//		$customize->add_field( 'social_vk', __( 'Vkontakte', TEXT_DOMAIN ), 'header', '#' );
//		$customize->add_field( 'social_gplus', __( 'Google plus', TEXT_DOMAIN ), 'header', '#' );
//		$customize->add_field( 'social_youtube', __( 'YouTube', TEXT_DOMAIN ), 'header', '#' );
//		$customize->add_field( 'social_instagram', __( 'Instagram', TEXT_DOMAIN ), 'header', '#' );

		$customize->add_field( 'date', __( 'Date', TEXT_DOMAIN ), 'title_tagline', '7 - 8 квітня 2017' )->add_partial_refresh( 'date', '#header .date' );
//		$customize->add_field( 'schedule', __( 'Schedule', TEXT_DOMAIN ), 'title_tagline', 'Графік роботи:', 'textarea' )->add_partial_refresh( 'schedule', '#header .schedule' );

	}

	public function register_custom_styles() {
		$style = file_get_contents( get_template_directory() . '/parts/customizer/wp-tmpls.php' );
		$re    = "/\{\{data\['([^\}]*)'\]\}\}/";

		preg_match_all( $re, $style, $matches );

		$matches[1] = array_unique( $matches[1] );
		$matches[0] = array_unique( $matches[0] );
		$mods       = Functions::get_theme_mod_filtered();

		foreach ( $matches[1] as $i => $key ) {
			$val   = isset( $mods[ $key ] ) ? $mods[ $key ] : '';
			$style = str_replace( $matches[0][ $i ], $val, $style );
		}
		$style = str_replace( "<script type='text/html' id='tmpl-styles'>", '', $style );
		$style = str_replace( '</script>', '', $style );
		add_action( 'wp_enqueue_scripts', function () use ( $style ) {
			wp_add_inline_style( 'theme', $style );
		}, 999 );

		add_action( 'wp_ajax_customizer_styles', function () use ( $style ) {
			header( 'Content-Type:text/css' );
			echo $style;
			die();
		} );

		add_action( 'admin_init', function () {
			add_editor_style( admin_url() . 'admin-ajax.php?action=customizer_styles' );

		}, 999 );
	}

	public function customizer_live_preview_js() {
		wp_enqueue_script(
			'theme-customizer',
			get_template_directory_uri() . '/modules/customization/assets/javascript/customizer.js',
			array(
				'jquery',
				'customize-preview'
			),
			'',
			true
		);
		wp_localize_script( 'theme-customizer', 'customizer', array( 'options' => Functions::get_theme_mod_filtered() ) );
	}

	public function add_action_wp_footer() {
		if ( is_customize_preview() ) {
			get_template_part( 'parts/customizer/wp-tmpls' );
		}
	}
}