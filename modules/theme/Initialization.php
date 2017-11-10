<?php

namespace modules\theme;

use WPKit\AdminPage\OptionPage;
use WPKit\Module\AbstractThemeInitialization;
use WPKit\Options\Option;
use WPKit\Options\OptionBox;

/**
 * Class Initialization
 *
 * @package modules\theme
 */
class Initialization extends AbstractThemeInitialization {

	const OPTIONS_PAGE_SOCIAL = 'social';

	const OPTIONS_SOCIAL_KEY = self::OPTIONS_PAGE_SOCIAL . '_links';

	/**
	 * @var array
	 */
	protected static $_image_sizes = [
		'hero' => [ 1440, 530 ]
	];

	public static function register_login_url() {
		add_filter( 'login_headerurl', function () {
			return home_url( '/' );
		} );
	}


	public function add_filter_wp_calculate_image_srcset( $source ) {
		return $source;
	}

	public function register_move_excerpt_up() {
		/**
		 * Removes the regular excerpt box. We're not getting rid
		 * of it, we're just moving it above the wysiwyg editor
		 */

		add_action( 'admin_menu', function () {
			remove_meta_box( 'postexcerpt', '', 'normal' );
		} );

		/**
		 * Add the excerpt meta box back in with a custom screen location
		 *
		 * @param  string $post_type
		 */

		add_action( 'add_meta_boxes', function ( $post_type ) {

			if ( post_type_supports( $post_type, 'excerpt' ) ) {
				add_meta_box(
					'oz_postexcerpt',
					__( 'Excerpt' ),
					'post_excerpt_meta_box',
					$post_type,
					'after_title',
					'high'
				);
			}
		} );

		/**
		 * You can't actually add meta boxes after the title by default in WP so
		 * we're being cheeky. We've registered our own meta box position
		 * `after_title` onto which we've regiestered our new meta boxes and
		 * are now calling them in the `edit_form_after_title` hook which is run
		 * after the post tile box is displayed.
		 */

		add_action( 'edit_form_after_title', function () {
			global $post, $wp_meta_boxes;
			# Output the `below_title` meta boxes:
			do_meta_boxes( get_current_screen(), 'after_title', $post );
		} );
	}

	public function add_filter_img_caption_shortcode( $html, $attr, $content ) {

		$atts = shortcode_atts( array(
			'id'      => '',
			'align'   => 'alignnone',
			'width'   => '',
			'caption' => '',
			'class'   => '',
		), $attr, 'caption' );

		if ( strpos( $atts['class'], 'fullwidth' ) !== false ) {
			$content = '<div class="thumbnail-container">' . $content . '</div>';
		}

		$atts['width'] = (int) $atts['width'];
		if ( $atts['width'] < 1 || empty( $atts['caption'] ) ) {
			return $content;
		}

		if ( ! empty( $atts['id'] ) ) {
			$atts['id'] = 'id="' . esc_attr( sanitize_html_class( $atts['id'] ) ) . '" ';
		}

		$class = trim( 'wp-caption ' . $atts['align'] . ' ' . $atts['class'] );

		$html5 = current_theme_supports( 'html5', 'caption' );
		// HTML5 captions never added the extra 10px to the image width
		$width = $html5 ? $atts['width'] : ( 10 + $atts['width'] );

		/**
		 * Filters the width of an image's caption.
		 *
		 * By default, the caption is 10 pixels greater than the width of the image,
		 * to prevent post content from running up against a floated image.
		 *
		 * @since 3.7.0
		 *
		 * @see img_caption_shortcode()
		 *
		 * @param int $width Width of the caption in pixels. To remove this inline style,
		 *                         return zero.
		 * @param array $atts Attributes of the caption shortcode.
		 * @param string $content The image element, possibly wrapped in a hyperlink.
		 */
		$caption_width = apply_filters( 'img_caption_shortcode_width', $width, $atts, $content );

		$style = '';
		if ( $caption_width ) {
			$style = 'style="width: ' . (int) $caption_width . 'px" ';
		}

		if ( $html5 ) {
			$html = '<figure ' . $atts['id'] . $style . 'class="' . esc_attr( $class ) . '">'
			        . do_shortcode( $content ) . '<figcaption class="wp-caption-text">' . $atts['caption'] . '</figcaption></figure>';
		} else {
			$html = '<div ' . $atts['id'] . $style . 'class="' . esc_attr( $class ) . '">'
			        . do_shortcode( $content ) . '<p class="wp-caption-text">' . $atts['caption'] . '</p></div>';
		}

		return $html;
	}

	public function register_image_sizes() {
		foreach ( static::$_image_sizes as $key => $data ) {
			$width  = isset( $data[0] ) ? $data[0] : 0;
			$height = isset( $data[1] ) ? $data[1] : 0;
			$crop   = isset( $data[2] ) ? $data[2] : false;

			add_image_size( $key, $width, $height, $crop );
		}
	}

	public function register_nav_menus() {
		register_nav_menus( [
			'primary' => __( 'Primary', 'colorrun' ),
		] );
	}

	public function register_dynamic_sidebars() {
		register_sidebar( array(
			'name'          => __( 'Single news', 'colorrun' ),
			'id'            => 'single-news',
			'description'   => __( 'Widgets in this area will be shown on all news.', 'colorrun' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h5 class="title">',
			'after_title'   => '</h5>',
		) );
	}

	public function _register_remove_admin_bar_nodes_hook() {
		add_action( 'admin_bar_menu', function ( \WP_Admin_Bar $wp_admin_bar ) {
			$wp_admin_bar->remove_node( 'comments' );
		}, 999 );
	}

	public function _register_async_scripts_loading() {
		add_filter( 'script_loader_tag', function ( $tag ) {
			return ! is_admin() ? str_replace( ' src', ' defer src', $tag ) : $tag;
		}, 10, 2 );
		/**
		 * For cases when jQuery is used before it will be loaded (in content)
		 * Also added code snippet in the end of last script which call all saved functions
		 */
		add_action( 'wp_head', function () {
			?>
            <script>
                (function (w, d, u) {
                    var alias,
                        pushToQ;

                    w.bindReadyQ = [];
                    w.bindLoadQ = [];

                    pushToQ = function (x, y) {

                        switch (x) {
                            case 'load':
                                w.bindLoadQ.push(y);

                                break;
                            case 'ready':
                                w.bindReadyQ.push(y);

                                break;
                            default:
                                w.bindReadyQ.push(x);

                                break;
                        }
                    };

                    alias = {
                        load: pushToQ,
                        ready: pushToQ,
                        bind: pushToQ,
                        on: pushToQ
                    };

                    w.$ = w.jQuery = function (handler) {

                        if (handler === d || handler === u || handler === w) {
                            return alias;
                        } else {
                            pushToQ(handler);
                        }
                    };
                })(window, document);
            </script>
			<?php
		}, 1 );
	}

	public function add_action_wp_enqueue_scripts() {
		static::_enqueue_styles();
		static::_enqueue_scripts();
	}

	protected function _enqueue_styles() {
		wp_enqueue_style(
			'theme',
			$this->get_theme_assets_url() . "/built/stylesheets/screen.css"
		);
		wp_enqueue_style(
			'theme-print',
			$this->get_theme_assets_url() . "/built/stylesheets/print.css",
			[],
			null,
			'print'
		);
	}

	protected function _enqueue_scripts() {
		wp_register_script(
			'theme',
			$this->get_theme_assets_url() . '/built/javascripts/common.js',
			[ 'jquery', ],
			null,
			true
		);
//		wp_localize_script('theme', 'theme_settings', []);
		wp_enqueue_script( 'theme' );
	}

	public function add_action_after_setup_theme() {
		add_theme_support( 'post-thumbnails', [ 'post', 'page' ] );
		add_theme_support( 'title-tag' );
		add_theme_support( 'custom-logo', [
			'height'      => 190,
			'flex-height' => true,
			'flex-width'  => true,
		] );
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'rsd_link' );
		remove_action( 'wp_head', 'rest_output_link_wp_head' );
		remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
		add_editor_style( $this->get_theme_assets_url() . "/built/stylesheets/editor.css" );
	}

	public function add_action_login_enqueue_scripts() {
		wp_enqueue_style( 'theme-login', $this->get_theme_assets_url() . '/built/stylesheets/login.css' );
	}

	public function add_action_admin_init() {
		remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_quick_press', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
		remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
	}

	public function add_action_admin_menu() {
		remove_submenu_page( 'options-general.php', 'options-discussion.php' );
		remove_submenu_page( 'edit-comments.php', 'edit-comments.php' );
		remove_menu_page( 'edit-comments.php' );
		if ( ! current_user_can('manage_options') ) {
			remove_menu_page( 'plugins.php' );
			remove_menu_page( 'tools.php' );
			remove_submenu_page( 'themes.php', 'themes.php' );
		}
	}

	/**
	 * @param \WP_Screen $current_screen
	 */
	public function _add_action_current_screen( $current_screen ) {
		if ( $current_screen->base == 'edit' ) {
			$columns_hook = function ( $columns ) {
				unset( $columns['comments'] );

				return $columns;
			};

			foreach ( [ 'posts', 'pages' ] as $post_type ) {
				add_filter( "manage_{$post_type}_columns", $columns_hook );
			}
		}
	}

	public function add_action_wp_head() {
		echo Option::get( 'head_code' );
	}

	public function add_action_admin_enqueue_scripts() {
//		wp_enqueue_style( 'theme-admin', $this->get_theme_assets_url() . '/built/stylesheets/admin.css' );
	}

	public function add_action_wp_footer() {
		echo "<noscript>
            <div style=\"position: absolute; bottom: 0; left: 0; right: 0; padding: 10px 20px; background-color: #FFF; text-align: center; color: #000; z-index: 999; border-top: 1px solid #000;\">
                " . __( 'JavaScript is disabled on your browser. Please enable JavaScript or upgrade to a JavaScript-capable browser to use this site.', 'colorrun' ) . "
            </div>
        </noscript>
        <script>
            document.getElementsByTagName('html')[0].className = document.getElementsByTagName('html')[0].className.replace(/\b(no-js)\b/,'');
        </script>";
		echo Option::get( 'footer_code' );
	}

	/**
	 * @return string
	 */
	public function add_filter_network_home_url() {
		return home_url( '/' );
	}

	/**
	 * @return string
	 */
	public function add_filter_excerpt_more() {
		return '...';
	}

	public function add_filter_excerpt_length() {
		return 12;
	}

	/**
	 * @param string $html
	 *
	 * @return string
	 */
	public function add_filter_embed_oembed_html( $html ) {
		if ( preg_match( '(youtube|vimeo|twitter|instagram)', $html, $matches ) ) {
			$class = $matches[0];
			if ( in_array( $matches[0], [ 'youtube', 'vimeo' ] ) ) {
				$class .= ' video-wrapper';
			}

			return "<div class=\"{$class} aligncenter\">$html</div>";
		}

		return $html;
	}

	/**
	 * @param array $settings
	 *
	 * @return array
	 */
	public function add_filter_tiny_mce_before_init( $settings ) {
		$settings['block_formats'] = 'Paragraph=p;Heading 2=h2;Heading 3=h3;Heading 4=h4;Heading 5=h5;Heading 6=h6;Preformatted=pre';

		return $settings;
	}

	public function admin_register_option_pages() {
		new OptionPage( 'theme_settings', __( 'Theme Settings', 'colorrun' ) );
		new OptionPage( 'social', __( 'Social', 'colorrun' ), 'theme_settings' );
	}

	public function admin_register_options() {
		$this->_add_theme_options();
		$this->_add_social_options();
	}

	protected function _add_theme_options() {

		$option_box = new OptionBox( 'homepage', __( 'Home page', 'colorrun' ) );

		$option_box = new OptionBox( 'general', __( 'General Options', 'colorrun' ) );
		$option_box->add_field( 'head_code', __( 'Head code', 'colorrun' ), 'Textarea' );
		$option_box->add_field( 'footer_code', __( 'Footer code', 'colorrun' ), 'Textarea' );
		$option_box->set_page( 'theme_settings' );
	}

	protected function _add_social_options() {
		$option_box = new OptionBox( self::OPTIONS_SOCIAL_KEY, __( 'Social Links', 'colorrun' ) );
		$option_box->add_field( 'facebook', 'Facebook', 'Url' );
//		$option_box->add_field( 'twitter', 'Twitter', 'Url' );
		$option_box->add_field( 'youtube', 'Youtube', 'Url' );
		$option_box->add_field( 'instagram', 'Instagram', 'Url' );
//		$option_box->add_field( 'telegram', 'Telegram', 'Url' );
		$option_box->set_page( self::OPTIONS_PAGE_SOCIAL );
	}

	public function register_widgets_hook() {
		add_action( 'widgets_init', function () {
			unregister_widget( 'WP_Widget_Calendar' );
			unregister_widget( 'WP_Widget_Archives' );
			unregister_widget( 'WP_Widget_Links' );
			unregister_widget( 'WP_Widget_Meta' );
			unregister_widget( 'WP_Widget_Search' );
			unregister_widget( 'WP_Widget_Categories' );
			unregister_widget( 'WP_Widget_Pages' );
			unregister_widget( 'WP_Widget_Recent_Posts' );
			unregister_widget( 'WP_Widget_Recent_Comments' );
			unregister_widget( 'WP_Widget_RSS' );
			unregister_widget( 'WP_Widget_Tag_Cloud' );
			unregister_widget( 'WP_Nav_Menu_Widget' );
		}, 20 );
	}

	public function admin_register_remove_from_nav_menus() {
		add_action( 'admin_head-nav-menus.php', function () {
			remove_meta_box( 'add-post_tag', 'nav-menus', 'side' );
			remove_meta_box( 'add-post', 'nav-menus', 'side' );
			remove_meta_box( 'add-category', 'nav-menus', 'side' );
		} );
	}

	public function register_remove_rel_next_links() {
		add_filter( 'index_rel_link', '__return_false' );
		add_filter( 'parent_post_rel_link', '__return_false' );
		add_filter( 'start_post_rel_link', '__return_false' );
		add_filter( 'previous_post_rel_link', '__return_false' );
		add_filter( 'next_post_rel_link', '__return_false' );
	}

	public function admin_register_default_image_link_to() {
		add_action( 'after_setup_theme', function () {
			update_option( 'image_default_link_type', 'none' );
		} );
	}

	/*public function add_action_customize_register( $wp_customize )
	{
		$languages = [];

		if ( function_exists( 'pll_languages_list' ) ) {
			$languages = pll_languages_list( [
				'hide_empty' => 0,
			] );
		}
		if ( ! empty( $languages ) ) {
			$preview_url = $wp_customize->get_preview_url();

			if ( $preview_url == home_url( '/' ) && isset( $_GET['url'] ) ) {
				$preview_url = wp_unslash( $_GET['url'] );
			}
			$preview_language = pll_current_language( 'slug' );

			foreach ( $languages as $current_language ) {
			    var_dump($current_language->home_url , $preview_url);
				if ( $current_language->home_url !== $preview_url ) {
					$wp_customize->add_section( 'languages', [
						'title'           => sprintf( __( 'Languages | %s', 'colorrun' ), $current_language->name ),
						'description'     => $current_language->name,
						'active_callback' => function () {
							return is_home() || is_front_page();
						},
					] );
//				$this->_builder->set_setting( 'lang', $current_language->slug );

					foreach ( $languages as $language ) {
						if ( $current_language->slug != $language->slug || false !== $preview_language && $current_language->slug != $preview_language ) {
							$key = "_customizer_lang_$language->slug";
							$wp_customize->add_setting( $key, [
								'type' => 'option',
							] );
							$wp_customize->add_control( new Link( 'dd', $wp_customize, $key, [
								'label'   => $language->name,
								'section' => 'languages',
								'url'     => add_query_arg( [
									'url'  => rawurlencode( pll_home_url( $language->slug ) ),
									'lang' => $language->slug,
								] ),
							] ) );
						}
					}

					break;
				}
			}
			$wp_customize->remove_section( 'static_front_page' );
		}
	}*/
}
