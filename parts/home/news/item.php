<?php
/**
 * Created by PhpStorm.
 * User: vitaly
 * Date: 9/25/17
 * Time: 23:16
 */ ?>
<article class="news-item">
    <div class="image-container">
		<?php the_post_thumbnail('thumbnail') ?>
    </div>
    <div class="text-container">
        <h3 class="title"><?php the_title() ?></h3>
        <div class="date">
            <i class="icon">
                <svg role="icon" class="sprite-icon sprite-icon-calendar">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#calendar"></use>
                </svg>
            </i>
            <time><?= date_i18n( 'd F Y', get_the_date( 'U' ) ); ?></time>
        </div>
        <a href="<?php the_permalink() ?>" class="button"><?php _e( 'Read more' ) ?></a>
    </div>
</article>
