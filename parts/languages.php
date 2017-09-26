<?php
/**
 * Created by PhpStorm.
 * User: vitaly
 * Date: 9/25/17
 * Time: 23:39
 */
if ( function_exists( 'pll_languages_list' ) ) :
	$languages = pll_the_languages( [ 'raw' => true ] );
	foreach ( $languages as $code => $language ) {
		if ( pll_current_language() == $code ) {
			$current = $language;
			break;
		}
	} ?>
    <div class="language-dropdown dropdown">
        <div class="label">
            <svg role="icon" class="sprite-icon sprite-icon-language">
                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#language"></use>
            </svg>
			<?= $current['name'] ?>
        </div>
        <div class="dropdown-container">
			<?php foreach ( $languages as $language ) :
				if ( $current == $language ) {
					continue;
				} ?>
                <a href="<?= $language['url'] ?>"><?= $language['name'] ?></a>
			<?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>