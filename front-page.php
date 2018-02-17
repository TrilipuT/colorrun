<?php
/**
 * Created by PhpStorm.
 * User: vitaly
 * Date: 9/19/17
 * Time: 20:01
 */
get_header();
the_post();
get_template_part( 'parts/home/hero-section' );

if ( Event::get_current_event_date( 'U' ) < time() ) {
	get_template_part( 'parts/home/about-section', 'final' );
//	get_template_part( 'parts/home/media-section' );
} else {
	get_template_part( 'parts/home/about-section' );
	get_template_part( 'parts/home/distance-section' );
	get_template_part( 'parts/home/corporate-section' );
}
get_template_part( 'parts/home/news-section' );
get_template_part( 'parts/home/sponsors-section' );
get_footer();
    