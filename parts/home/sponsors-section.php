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
		<?php _e( 'Sponsors and partners', TEXT_DOMAIN ) ?>
    </div>
<?php if ( $sponsors->have_posts() ): ?>
        <div class="logos-container">
	        <?php while ( $sponsors->have_posts() ):
		        $sponsors->the_post();
		        $url  = Sponsor::get_url();
		        $type = Sponsor::get_type();
		        ?>
                <a href="<?= $url ?>" class="logo-item <?= $type ?>">
                    <img src="<?= get_template_directory_uri() ?>/assets/built/images/fake/turkish.jpg" alt="">
                </a>
	        <?php endwhile; ?>
        </div>
    </div>
</section>
<?php endif; ?>