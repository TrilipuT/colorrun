<?php
/**
 *
 * @package    colorrun
 * @author     vitaly
 *
 */
if ( $socials = Theme::get_socials() ): ?>
    <nav class="socials">
		<?php foreach ( $socials as $id => $link ): ?>
            <a href="<?= $link ?>" target="_blank" class="social-item">
                <svg role="icon" class="sprite-icon sprite-icon-<?= $id ?>">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#<?= $id ?>"></use>
                </svg>
            </a>
		<?php endforeach; ?>
    </nav>
<?php endif; ?>

