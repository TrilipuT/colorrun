<?php
/**
 *
 * @package    skeleton
 * @author     vitaly
 *
 */
?>
</div>
<footer id="footer">
    <div class="wrapper-outer">
	    <?php Theme::get_footer_menu(); ?>
	    <?php if ( $socials = Theme::get_socials() ): ?>
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
        <span class="sponsor">Організатор пробігу — KM Running Club</span>
    </div>
    <div class="copyrights">
        &copy; Kyiv Color Run 2018. All rights reserved
    </div>
</footer>
<?php wp_footer(); ?>
</div>
</body>
</html>