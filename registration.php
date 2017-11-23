<?php
/**
 * Template name: Registration
 * Created by PhpStorm.
 * User: vitaly
 * Date: 11/19/17
 * Time: 23:18
 */
if ( ! isset( $_GET['distance'] ) && ! $_GET['distance'] ) {
	wp_redirect( home_url() );
	exit();
}
$distance_id    = (int) $_GET['distance'];
$participant_id = Registration::start_registration( $distance_id );
get_header(); ?>
    <div class="hero-section registration" style="background-image: url('<?= Theme::get_background_image() ?>')">
        <div class="text-container">
            <h1 class="title"><?php the_title() ?></h1>
        </div>
    </div>
    <section class="registration-section">
        <div class="wrapper-inner">
            <div class="registration-breadcrumbs">
                <div class="item first active">
                    <span>1. <?php _ex( 'Personal info', 'Внесіть особисті дані', 'colorrun' ) ?></span>
                </div>
                <div class="item second">
                    <span>2. <?php _ex( 'Check personal info', 'Перевірка даних', 'colorrun' ) ?></span>
                </div>
                <div class="item third">
                    <span>3. <?php _ex( 'Payment', 'Оплата реєстрації', 'colorrun' ) ?></span>
                </div>
            </div>
            <div class="steps-area">
                <div class="registration-countdown" data-time="<?= time() ?>">
                    <time class="timer">15 : 00</time>
                    <div class="text-container">
						<?php _ex( 'You have <strong>15 minutes</strong><br> to complete your registration', 'У вас є <strong>15 хвилин</strong><br> щоб завершити реєстрацію', 'colorrun' ); ?>
                    </div>
                </div>
                <div class="step">
                    <div class="step-row active">
                        <h2 class="title"><?php _ex( 'Your information', 'Форма заповнення даних', 'colorrun' ) ?></h2>
                        <div class="form-area">
                            <form class="registration-form">
                                <!--<select id="distance" name="distance" required>
                                    <option value="hide"><?php /*_e( 'Distance', 'colorrun' ) */ ?></option>
									<?php /*$distances = Distance::get_distances();
									while ( $distances->have_posts() ):
										$distances->the_post(); */ ?>
                                        <option value="<?php /*the_ID() */ ?>"><?php /*the_title() */ ?></option>
									<?php /*endwhile;
									wp_reset_postdata(); */ ?>
                                </select>-->
                                <div class="input-group">
                                    <input type="text" name="firstname" id="firstname" required>
                                    <span class="bar"></span>
                                    <label><?php _e( 'First name', 'colorrun' ) ?></label>
                                </div>
                                <div class="input-group">
                                    <input type="text" name="lastname" id="lastname" required>
                                    <span class="bar"></span>
                                    <label><?php _e( 'Last name', 'colorrun' ) ?></label>
                                </div>
                                <div class="radio-group">
                                    <span class="label"><?php _e( 'Gender:', 'colorrun' ) ?></span>
                                    <div class="options">
                                        <label class="radio">
                                            <input id="male" type="radio" name="gender" value="male" required>
                                            <span class="outer">
                                            <span class="inner"></span>
                                        </span>
											<?php _e( 'Male', 'colorrun' ) ?>
                                        </label>
                                        <label class="radio">
                                            <input id="female" type="radio" name="gender" value="female" required>
                                            <span class="outer">
                                            <span class="inner"></span>
                                        </span>
											<?php _e( 'Female', 'colorrun' ) ?>
                                        </label>
                                    </div>
                                </div>
                                <div class="input-group">
                                    <input type="email" id="email" name="email" required>
                                    <span class="bar"></span>
                                    <label><?php _e( 'E-mail', 'colorrun' ) ?></label>
                                </div>
                                <div class="input-group">
                                    <input type="text" id="dateofbirth" name="dateofbirth" required>
                                    <span class="bar"></span>
                                    <label><?php _e( 'Date of birth', 'colorrun' )   ?></label>
                                </div>
                                <div class="input-group">
                                    <input type="text" id="phone" name="info[phone]" required>
                                    <span class="bar"></span>
                                    <label><?php _e( 'Phone', 'colorrun' ) //Моб. телефон ?></label>
                                </div>
                                <div class="input-group">
                                    <input type="text" id="country" name="country" required>
                                    <span class="bar"></span>
                                    <label><?php _e( 'Country', 'colorrun' ) ?></label>
                                </div>
                                <div class="input-group">
                                    <input type="text" id="city" name="city" required>
                                    <span class="bar"></span>
                                    <label><?php _e( 'City', 'colorrun' ) ?></label>
                                </div>
                                <div class="input-group">
                                    <input type="text" id="club" name="info[club]">
                                    <span class="bar"></span>
                                    <label><?php _e( 'Club', 'colorrun' ) ?></label>
                                </div>
                                <div class="radio-group">
                                    <span class="label"><?php _e( 'T-shirt size:', 'colorrun' ) ?></span>
                                    <div class="options">
                                        <label class="radio">
                                            <input id="xs" value="xc" type="radio" name="info[tshirt_size]" required>
                                            <span class="outer">
                                            <span class="inner"></span>
                                        </span>
                                            XS
                                        </label>
                                        <label class="radio">
                                            <input id="s" value="s" type="radio" name="info[tshirt_size]" required>
                                            <span class="outer">
                                            <span class="inner"></span>
                                        </span>
                                            S
                                        </label>
                                        <label class="radio">
                                            <input id="m" value="m" type="radio" name="info[tshirt_size]" required>
                                            <span class="outer">
                                            <span class="inner"></span>
                                        </span>
                                            M
                                        </label>
                                        <label class="radio">
                                            <input id="l" value="l" type="radio" name="info[tshirt_size]" required>
                                            <span class="outer">
                                            <span class="inner"></span>
                                        </span>
                                            L
                                        </label>
                                        <label class="radio">
                                            <input id="xl" value="xl" type="radio" name="info[tshirt_size]" required>
                                            <span class="outer">
                                            <span class="inner"></span>
                                        </span>
                                            XL
                                        </label>
                                    </div>
                                </div>
                                <div class="checkbox-group">
                                    <input type="checkbox" id="personal_data" name="personal_data" value="1" required/>
                                    <label for="personal_data">
										<?php printf( __( 'Так, я підтверджую, що вказані мною дані є вірними та актуальними. %sЗгода на обробку персональних даних%s.', 'colorrun' ), '<a href="" target="_blank"><strong>', '</strong></a>' ) ?>
                                    </label>
                                </div>
                                <div class="checkbox-group">
                                    <input type="checkbox" id="event_rules" name="event_rules" value="1" required/>
                                    <label for="event_rules">
										<?php printf( __( 'Я ознайомлений/ознайомлена з %sРегламентом Заходу%s.', 'colorrun' ), '<a href="" target="_blank"><strong>', '</strong></a>' ) ?>
                                    </label>
                                </div>
                                <input type="hidden" name="participant_id" value="<?= $participant_id ?>">
                                <button type="submit">send</button>
                            </form>
                        </div>
                    </div>
                    <div class="step-row">
                        <h2 class="title">Персональна інформація</h2>
                        <div class="personal-info">
                            <button class="edit-info">
                                <img src="../images/fake/pencil.png" alt="">
                                <span>РЕДАГУВАТИ</span>
                            </button>
                            <dl class="info">
                                <dt>Дистанція</dt>
                                <dd>Turkish airlines Color Run 6 km</dd>
                                <dt>Прізвище</dt>
                                <dd>Andrushkevich</dd>
                                <dt>Ім’я</dt>
                                <dd>Olga</dd>
                                <dt>Стать</dt>
                                <dd>Жіноча</dd>
                                <dt>E-mail</dt>
                                <dd>o.andrushkevych@gmail.com</dd>
                                <dt>Моб. телефон</dt>
                                <dd>+380930334567</dd>
                                <dt>Дата народження</dt>
                                <dd>10/05/1992</dd>
                                <dt>Країна</dt>
                                <dd>Україна</dd>
                                <dt>Місто</dt>
                                <dd>Київ</dd>
                                <dt>Клуб</dt>
                                <dd>KM Runing Club</dd>
                                <dt>Розмір футболки</dt>
                                <dd>M</dd>
                            </dl>
                        </div>
                    </div>
                    <div class="step-row">
                        <h2 class="title">Підтвердження замовлення</h2>
                        <div class="personal-info">
                            <dl class="preliminary-price">
                                <dt>Turkish airlines Color Run 6 km</dt>
                                <dd>150 грн.</dd>
                            </dl>
                            <div class="promo-group">
                                <input type="text" class="promo-input" placeholder="Ввести промокод">
                                <button class="promo-submit">ЗАСТОСУВАТИ</button>
                            </div>
                            <dl class="final-price">
                                <dt>Сума до сплати</dt>
                                <dd>150 грн.</dd>
                            </dl>
                        </div>
                    </div>
                    <div class="step-row">
                        <h2 class="title">Оплата реєстрації</h2>
                        <div class="personal-info">
                            <h3 class="user-name">Olga Andrushkevich</h3>
                            <h3 class="distance">Turkish airlines Color Run 6 km</h3>
                            <p class="price">Сума до сплати: <span class="amount">150 грн.</span></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="registration-buttons">
                <button class="button back hide">НАЗАД</button>
                <button class="button next">ПРОДОВЖИТИ</button>
            </div>
        </div>
    </section>
    <div class="popup-wrapper">
        <div class="popup">
            <div class="container">
                <div class="icon">
                    <svg role="icon" class="sprite-icon sprite-icon-attention">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#attention"></use>
                    </svg>

                </div>
                <div class="text-container">
                    На жаль, час оплати вийшов. Замовлення анульоване. Будь ласка, створіть нове замовлення.
                </div>
            </div>
            <a href="#" class="button">ПРОДОВЖИТИ</a>
        </div>
    </div>
<?php get_footer();
