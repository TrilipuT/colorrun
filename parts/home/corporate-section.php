<?php
/**
 *
 * @package    colorrun
 * @author     vitaly
 *
 */
$texts = Homepage::get_corporate_texts();
if ( $texts[0] || $texts[1] ):?>
    <section class="corporate-section">
        <div class="wrapper-outer">
            <h2 class="section-title"><?php vprintf( '%s <span class="offset">%s</span>', explode( ' ', __( 'Corporate teams', 'colorrun' ) ) ) ?></h2>
            <div class="wrapper-inner">
                <div class="corporate-container">
                    <div class="image-container"><?= Homepage::get_corporate_image() ?></div>
					<?php foreach ( $texts as $text ): ?>
                        <div class="text-container">
							<?= apply_filters( 'the_content', $text ) ?>
                        </div>
					<?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
<?php endif;
