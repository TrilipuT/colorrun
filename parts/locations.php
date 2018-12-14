<?php
if ( $sites = get_sites() ) :?>
    <div class="language-dropdown dropdown">
        <div class="label">
            <svg role="icon" class="sprite-icon sprite-icon-point">
                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#point"></use>
            </svg>
			<?= get_bloginfo( 'description' ) ?>
        </div>
        <div class="dropdown-container">
			<?php foreach ( $sites as $site ) :
				switch_to_blog( $site->blog_id ); ?>
                <a href="<?= home_url() ?>"><?= get_bloginfo( 'description' ) ?></a>
			<?php endforeach; ?>
        </div>
    </div>
	<?php restore_current_blog(); ?>
<?php endif; ?>