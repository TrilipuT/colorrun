<?php
/**
 *
 * @package    colorrun
 * @author     vitaly
 *
 */
if ( $socials = Theme::get_socials() ): ?>
    <nav class="socials">
		<?php foreach ( $socials as $name => $url ): ?>
            <a href="<?= $url ?>" target="_blank" class="social-item">
                <svg role="icon" class="sprite-icon sprite-icon-<?= $name ?>">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#=$name"></use>
                </svg>
            </a>
		<?php endforeach; ?>
    </nav>
<?php endif;