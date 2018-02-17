<?php
/**
 *
 * @package    skeleton
 * @author     vitaly
 *
 */
?>
<!DOCTYPE html>
<html <?php language_attributes() ?> class="no-js">
<head>
    <meta charset="<?php bloginfo( "charset" ) ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
	<?php wp_head() ?>
</head>
<body id="body" <?php body_class( 'with-hovers' ) ?>>
<div class="svg-sprite" style="display: none">
	<?php get_template_part( 'sprite' ) ?>
</div>
<div class="global-wrapper">
    <header id="header">
        <div class="top-bar">
            <div class="wrapper-outer">
				<?php get_template_part( 'parts/socials' ) ?>
				<?php get_template_part( 'parts/languages' ) ?>
            </div>
        </div>
        <div class="main-navigation">
            <div class="wrapper-outer">
				<?php the_custom_logo() ?>
                <div class="main-menu">
                    <div class="burger-mobile">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
					<?php wp_nav_menu( [ 'theme_location' => 'primary', 'container' => 'ul' ] ) ?>
                </div>
				<?php $distances = Distance::get_current_distances();
				if ( $distances->have_posts() ):
					$list = '';
					while ( $distances->have_posts() ) {
						$distances->the_post();
						if ( Distance::is_open() ) {
							$list .= "<li class=\"menu-item\"><a href=\"" . Distance::get_registration_url() . "\">" . get_the_title() . "</a></li>";
						}
					}
					wp_reset_postdata();
					if ( $list ):?>
                        <div href="#" class="registration button">
							<?php _e( 'Register', 'colorrun' ) ?>
                            <ul class="sub-menu">
								<?= $list ?>
                            </ul>
                        </div>
					<?php endif; ?>
				<?php endif; ?>
            </div>
        </div>
    </header>
    <div class="main-wrapper">