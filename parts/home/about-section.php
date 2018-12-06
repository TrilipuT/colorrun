<?php
/**
 *
 * @package    colorrun
 * @author     vitaly
 *
 */ ?>
<section class="about-section" id="about-section">
    <div class="wrapper-outer">
        <div class="description">
			<?php the_content() ?>
        </div>
		<?php $items = Homepage::get_info_items();
		if ( count( $items ) ):?>
            <div class="info-container">
				<?php if ( count( $items ) ): ?>
                    <div class="column">

					<?php foreach ( $items as $i => $item ):
						if ( $i == ceil( count( $items ) / 2 ) ):?>
                            </div>
                            <div class="column">
						<?php endif; ?>

                        <div class="info-item">
                            <i class="icon">
                                <svg role="icon" class="sprite-icon sprite-icon-<?= $item['icon'] ?>">
                                    <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                         xlink:href="#<?= $item['icon'] ?>"></use>
                                </svg>
                            </i>
                            <div class="text-container">
                                <h3 class="title"><?= $item['title'] ?></h3>
								<?= $item['text'] ?>
                            </div>
                        </div>
					<?php endforeach; ?>
                    </div>
				<?php endif; ?>
            </div>
		<?php endif; ?>
    </div>
</section>