<?php
/**
 *
 * @package    colorrun
 * @author     vitaly
 *
 */ ?>
<?php $sponsors = Sponsor::get_sponsors();
if ( $sponsors->have_posts() ): ?>
    <section class="sponsors-section">
        <div class="wrapper-outer">
            <div class="section-title">
				<?php _e( 'Sponsors and partners', 'colorrun' ) ?>
            </div>
            <div class="logos-container">
				<?php while ( $sponsors->have_posts() ):
					$sponsors->the_post(); ?>
                    <a href="<?= Sponsor::get_url() ?>" class="logo-item <?= Sponsor::get_type() ?>">
						<?php the_post_thumbnail( 'large' ) ?>
                    </a>
				<?php endwhile; ?>
            </div>
        </div>
    </section>
<?php endif; ?>