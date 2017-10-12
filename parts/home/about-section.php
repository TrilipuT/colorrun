<?php
/**
 *
 * @package    colorrun
 * @author     vitaly
 *
 */ ?>
<section class="about-section">
    <div class="wrapper-outer">
        <div class="description">
			<?php the_content() ?>
        </div>
        <div class="info-container">
			<?php $items = Homepage::get_info_items();
			$big_item    = array_pop( $items );
			if ( count( $items ) ):?>
                <div class="column">
					<?php
					foreach ( $items as $item ):?>
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
			<?php if ( $big_item ): ?>
                <div class="info-item big">
                    <i class="icon">
                        <svg role="icon" class="sprite-icon sprite-icon-<?= $big_item['icon'] ?>">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                 xlink:href="#<?= $big_item['icon'] ?>"></use>
                        </svg>
                    </i>
                    <div class="text-container">
                        <h3 class="title"><?= $big_item['title'] ?></h3>
						<?= $big_item['text'] ?>
                    </div>
                </div>
			<?php endif; ?>
        </div>
    </div>
</section>