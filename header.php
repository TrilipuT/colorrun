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
                <nav class="menu">
                    <a href="#">Новини</a>
                    <a href="#">Про захід</a>
                    <a href="#">Отриманя стартового пакету</a>
                    <a href="#">Список учасників</a>
                    <a href="#">Маршрути</a>
                    <a href="#">Програма</a>
                    <a href="#">Спонсори</a>
                    <a href="#">Контакти</a>
                </nav>
                <a href="#" class="registration button">
				    <?php _e( 'Register now', TEXT_DOMAIN ) ?>
                </a>
            </div>
        </div>
    </header>
    <div class="main-wrapper">