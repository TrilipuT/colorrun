<?php
/**
 *
 * @package    colorrun
 * @author     vitaly
 *
 */ ?>
<?php $sponsors = Sponsor::get_sponsors(); ?>
<section class="sponsors-section">
    <div class="wrapper-outer">
    <div class="section-title">
	    <?php _e( 'Sponsors and partners', 'colorrun' ) ?>
    </div>
<?php if ( $sponsors->have_posts() ): ?>
        <div class="logos-container">
	        <?php while ( $sponsors->have_posts() ):
		        $sponsors->the_post();
		        $url  = Sponsor::get_url();
		        $type = Sponsor::get_type();
		        ?>
                <a href="<?= $url ?>" class="logo-item <?= $type ?>">
	                <?php the_post_thumbnail( 'large' ) ?>
                </a>
	        <?php endwhile; ?>
        </div>
    </div>
</section>
<?php endif; ?>