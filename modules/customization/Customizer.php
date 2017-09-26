<?php
namespace modules\customization;


class Customizer {

	/**
	 * @var $wp_customize \WP_Customize_Manager
	 */
	private $wp_customize;

	/**
	 * Customizer constructor.
	 */
	public function __construct( $wp_customize ) {
		add_theme_support( 'customize-selective-refresh-widgets' );
		$this->wp_customize = $wp_customize;
	}

	public function add_partial_refresh( $field_key, $selector, $render_callback = false ) {
		$args = array(
			'selector'        => $selector,
			'render_callback' => function () use ( $field_key ) {
				return get_theme_mod( $field_key );
			}
		);
		if ( $render_callback ) {
			$args['render_callback'] = $render_callback;
		}
		$this->wp_customize->selective_refresh->add_partial( sanitize_key( $field_key ), $args );

		return $this;
	}

	public function add_image_field( $field_key, $section_key, $default, $transport = 'postMessage' ) {
		$field_key = sanitize_key( $field_key );
		$this->wp_customize->add_setting( $field_key, array(
			'default'   => $default,
			'transport' => $transport
		) );

		$this->wp_customize->add_control( new \WP_Customize_Image_Control( $this->wp_customize, $field_key, array(
			'label'    => ucwords( str_replace( '_', ' ', sanitize_text_field( $field_key ) ) ),
			'section'  => sanitize_key( $section_key ),
			'settings' => $field_key,
		) ) );

		return $this;
	}

	public function add_color_field( $field_key, $field_title, $section_key, $default, $transport = 'postMessage' ) {
		$this->wp_customize->add_setting( sanitize_key( $field_key ), array(
			'default'   => $default,
			'transport' => $transport
		) );
		$this->wp_customize->add_control( new \WP_Customize_Color_Control( $this->wp_customize, sanitize_key( $field_key ),
			array(
				'label'    => $field_title,
				'section'  => sanitize_key( $section_key ),
				'settings' => sanitize_key( $field_key )
			) ) );

		return $this;
	}

	public function add_theme_switch( $field_key, $section_key, $default, $field_type = 'text', $options = array(), $transport = 'postMessage' ) {
		$this->wp_customize->add_setting(
			sanitize_key( $field_key ),
			array(
				'default'   => $default,
				'transport' => $transport
			)
		);
		$this->wp_customize->add_control(
			new WP_Customize_Theme_Switcher( $this->wp_customize, sanitize_key( $field_key ),
				array(
					'label'    => ucwords( str_replace( '_', ' ', sanitize_text_field( $field_key ) ) ),
					'section'  => sanitize_key( $section_key ),
					'settings' => sanitize_key( $field_key )
				) )
		);

		return $this;
	}

	public function add_field( $field_key, $field_title, $section_key, $default, $field_type = 'text', $options = array(), $transport = 'postMessage' ) {
		$fields = array(
			'checkbox',
			'radio',
			'select',
			'text',
			'textarea'
		);
		if ( in_array( $field_type, $fields ) ) {
			$languages = pll_the_languages( [ 'raw' => 1, 'echo' => 0, 'hide_if_empty' => 0 ] );
			foreach ( $languages as $key => $lang ) {
				$new_field_key = sanitize_key($field_key . '_' . $lang['slug']);


				$this->wp_customize->add_setting(
					$new_field_key,
					array(
						'default'   => $default,
						'transport' => $transport
					)
				);
				$this->wp_customize->add_control(
					$new_field_key,
					array(
						'type'    => $field_type,
						'label'   => $field_title,
						'section' => sanitize_key( $section_key ),
						'choices' => $options
					)
				);
			}
		} elseif ($field_type == 'image'){
			$this->wp_customize->add_setting(
				sanitize_key( $field_key ),
				array(
					'default'   => $default,
					'transport' => $transport
				)
			);
			$this->wp_customize->add_control(
				new \WP_Customize_Image_Control(
					$this->wp_customize,
					sanitize_key( $field_key ),
					array(
						'label'      => $field_title,
						'section'    => sanitize_key( $section_key ),
					)
				)
			);
		}

		return $this;
	}

	public function add_section( $section_key ) {
		$this->wp_customize->add_section(
			sanitize_key( $section_key ),
			array(
				'title' => ucwords( str_replace( '_', ' ', sanitize_text_field( $section_key ) ) ),
			)
		);
	}
}