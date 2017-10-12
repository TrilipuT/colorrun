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
	    <?php wp_nav_menu( [ 'id' => 'footer', 'container_class' => 'footer-nav', 'container' => 'nav' ] ) ?>
        <nav class="socials">
            <a href="#" class="social-item">
                <svg role="icon" class="sprite-icon sprite-icon-fb">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#fb"></use>
                </svg>

            </a>
            <a href="#" class="social-item">
                <svg role="icon" class="sprite-icon sprite-icon-youtube">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#youtube"></use>
                </svg>

            </a>
            <a href="#" class="social-item">
                <svg role="icon" class="sprite-icon sprite-icon-inst">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#inst"></use>
                </svg>

            </a>
        </nav>
        <span class="sponsor">Організатор пробігу — KM Running Club</span>
    </div>
    <div class="copyrights">
        &copy;
        Kyiv Color Run 2018. All rights reserved
    </div>
</footer>
<?php wp_footer(); ?>
</div>
</body>
</html>