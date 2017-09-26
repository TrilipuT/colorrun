<?php
/**
 *
 * @package    colorrun
 * @author     vitaly
 *
 */ ?>
<section class="corporate-section">
    <div class="wrapper-outer">
        <h2 class="section-title"><?php vprintf( '%s <span class="offset">%s</span>', explode( ' ', __( 'Corporate teams' ) ) ) ?></h2>
        <div class="wrapper-inner">
            <div class="corporate-container">
                <div class="image-container">
					<?= Homepage::get_corporate_image() ?>
                </div>
				<?php $texts = Homepage::get_corporate_texts();
				foreach ( $texts as $text ): ?>
                    <div class="text-container">
						<?= apply_filters( 'the_content', $text ) ?>
                    </div>
				<?php endforeach; ?>
            </div>
        </div>
    </div>
</section>