<?php
/**
 * Created by PhpStorm.
 * User: vitaly
 * Date: 9/24/17
 * Time: 11:56
 */ ?>
<div class="distance-container">
    <div class="unit-info">
        <div class="start">
            <i class="icon">
                <svg role="icon" class="sprite-icon sprite-icon-clock">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#clock"></use>
                </svg>
            </i>
            <div class="text-container">
                <div class="label"><?php _e( 'start', 'colorrun' ) ?></div>
                <div class="value"><?= Distance::get_date( get_the_ID(), 'H:i' ) ?></div>
            </div>
        </div>
        <div class="participants">
            <i class="icon">
                <svg role="icon" class="sprite-icon sprite-icon-person">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#person"></use>
                </svg>
            </i>
            <div class="text-container">
                <div class="label"><?php _e( 'participants', 'colorrun' ); ?></div>
                <div class="value"><?= Registration::get_registered_for_distance_count( get_the_ID() ) ?>
                    /<?= Distance::get_slots() ?></div>
            </div>
        </div>
    </div>
    <span class="distance"><?= Distance::get_distance() ?></span>
	<?php the_content() ?>
    <div class="prices">
		<?php $prices = Distance::get_prices();
		foreach ( $prices as $price ):
			$icon = $price['active'] ? 'check' : 'cross'; ?>
            <div class="price-item <?= $price['active'] ? '' : 'inactive' ?>">
                <i class="icon">
                    <svg role="icon" class="sprite-icon sprite-icon-<?= $icon ?>">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#<?= $icon ?>"></use>
                    </svg>
                </i>
                <div class="text-container">
                    <span class="price"><?= Distance::format_price( $price['fee'] ) ?></span>
                    <span class="date"><?php printf( __( 'till %s', 'colorrun' ), date( 'd.m.Y', strtotime( $price['date'] ) ) ) ?></span>
                </div>
            </div>
		<?php endforeach; ?>
    </div>
    <a href="#" class="button"><?php _e( 'REGISTER NOW', 'colorrun' ); ?></a>
</div>
