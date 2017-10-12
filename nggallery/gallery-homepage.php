<?php
/**
 * Created by PhpStorm.
 * User: vitaly
 * Date: 10/9/17
 * Time: 23:28
 *
 * @var $gallery : Contain all about the gallery
 * @var $images : Contain all images, path, title
 * @var $pagination : Contain the pagination content
 */ ?>
<section class="media-section">
    <div class="wrapper-outer">
        <h2 class="section-title"><?php _e( 'Photo and video', TEXT_DOMAIN ) ?></h2>
        <div class="media-grid">
			<?php foreach ( $images as $i => $image ) :?>
				<?php if ( $i % 2 == 0 ): ?>
                    <div class="column">
				<?php endif; ?>
                <article class="media-item image-<?= $image->pid ?>">
                    <div class="thumbnail-container">
                        <img src="<?php echo $image->thumbnailURL ?>" alt="<?php echo $image->alttext ?>">
                    </div>
                    <div class="hover-container">
                        <a href="<?php echo $image->imageURL ?>" class="button">БІЛЬШЕ ФОТО/ВІДЕО</a>
                    </div>
                </article>
				<?php if ( $i % 2 != 0 ): ?>
                    </div>
				<?php endif; ?>
			<?php endforeach; ?>
        </div>
    </div>
</section>
