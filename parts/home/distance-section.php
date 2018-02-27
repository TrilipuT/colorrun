<?php
/**
 *
 * @package    colorrun
 * @author     vitaly
 *
 */
$distances = Distance::get_current_distances(3);
if ( $distances->have_posts() ):
	$col_classes = [
		'dark',
		'medium',
		'light',
	]; ?>
    <section class="distance-section">
        <div class="wrapper-outer">
            <h2 class="section-title"><?php _e( 'Distances', 'colorrun' ) ?></h2>
            <div class="wrapper-inner">
                <div class="col image-container">
					<?= Homepage::get_distances_image(); ?>
                </div>
				<?php if ( $distances->have_posts() ):
					while ( $distances->have_posts() ):
						$distances->the_post(); ?>
                        <div class="col <?= array_shift( $col_classes ) ?>">
							<?php get_template_part( 'parts/home/distance/item' ) ?>
                        </div>
					<?php endwhile;
				endif;
				wp_reset_postdata(); ?>
            </div>
        </div>
    </section>
<?php endif;
