<?php

namespace modules\shortcode;

/**
 *
 * @package    budstikka
 * @author     vitaly
 *
 */
class Gallery {
	public function __construct() {
		add_filter( 'post_gallery', function ( $output, $attr, $instance ) {
			$post = get_post();
			$atts = shortcode_atts( [
				'order'   => 'ASC',
				'orderby' => 'menu_order ID',
				'id'      => $post ? $post->ID : 0,
				'columns' => 1,
				'size'    => '',
				'include' => '',
				'exclude' => '',
				'link'    => ''
			], $attr, 'gallery' );
			$id   = intval( $atts['id'] );
			$args = [
				'post_status'    => 'inherit',
				'post_type'      => 'attachment',
				'post_mime_type' => 'image',
				'order'          => $atts['order'],
				'orderby'        => $atts['orderby']
			];

			if ( ! empty( $atts['include'] ) ) {
				$args['post__in'] = explode( ',', $atts['include'] );
			} else {
				$args['post_parent'] = $id;

				if ( ! empty( $atts['exclude'] ) ) {
					$args['post__not_in'] = explode( ',', $atts['exclude'] );
				}
			}
			$the_query = new \WP_Query( $args );
			if ( $the_query->have_posts() ) {
				if ( is_feed() ) {
					while ( $the_query->have_posts() ) {
						$the_query->the_post();
						$output .= wp_get_attachment_link( get_the_ID(), $atts['size'], true ) . "\n";
					}
				} else {

					$output .= "<div id='gallery-$instance' class='swiper-container gallery-widget'>";
					$output .= "<div class='swiper-wrapper'>";

					while ( $the_query->have_posts() ) {
						$the_query->the_post();
						$output  .= '<div class="swiper-slide">';
						$output  .= '<div class="thumbnail-container">';
						$output  .= wp_get_attachment_image( get_the_ID(), 'gallery' );
						$output  .= '</div>';
						$caption = trim( get_the_excerpt() );

						if ( ! empty( $caption ) ) {
							$output .= '<div class="text-container">';
							$output .= '<p class="title">' . wptexturize( $caption ) . '</p>';
							$output .= '</div>';
						}
						$output .= '</div>';
					}
					$output .= "</div>"; // .swiper-wrapper
					$output .= "<div class=\"swiper-button-prev\"></div> <div class=\"swiper-button-next\"></div>";
					$output .= "</div>"; // .slider
				}
				wp_reset_postdata();
			}

			return $output;
		}, 10, 3 );
	}
}