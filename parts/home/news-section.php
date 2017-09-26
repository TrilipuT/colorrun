<?php
/**
 *
 * @package    colorrun
 * @author     vitaly
 *
 */
$news = Homepage::get_latest_news();
if ( $news->have_posts() ): ?>
    <section class="news-section">
        <div class="wrapper-outer">
            <h2 class="section-title"><?php _e( 'news', TEXT_DOMAIN ) ?></h2>
            <div class="wrapper-inner">
                <div class="news-grid">
					<?php while ( $news->have_posts() ) {
						$news->the_post();
						get_template_part( 'parts/home/news/item' );
					} ?>
                </div>
            </div>
        </div>
    </section>
<?php endif;