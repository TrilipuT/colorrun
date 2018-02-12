<?php
/**
 * Template name: Registration
 * Created by PhpStorm.
 * User: vitaly
 * Date: 11/19/17
 * Time: 23:18
 */
$distance_id = 0;
if ( isset( $_GET['distance'] ) && $_GET['distance'] ) {
	$distance_id = (int) $_GET['distance'];
}
$price = Distance::get_current_price( $distance_id );
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
                                <div class="input-group">
                                    <select id="distance" name="distance" required>
                                        <option value="hide"><?php _e( 'Distance', 'colorrun' ) ?></option>
										<?php $distances = Distance::get_distances();
										while ( $distances->have_posts() ):
											$distances->the_post(); ?>
                                            <option
												<?= selected( get_the_ID(), $distance_id ) ?>value="<?php the_ID() ?>"
                                                data-age="<?= Distance::get_age() ?>"
                                                data-price="<?= Distance::format_price( Distance::get_current_price() ) ?>"><?php the_title() ?></option>
										<?php endwhile;
										wp_reset_postdata(); ?>
                                    </select>
                                </div>
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
                                    <input type="text" id="dateofbirth" name="dateofbirth"
                                           data-inputmask-alias="dd/mm/yyyy"
                                           data-val="true" required>
                                    <span class="bar"></span>
                                    <label><?php _e( 'Date of birth', 'colorrun' ) ?></label>
                                </div>
								<?php if ( Registration::is_active_field( 'phone' ) ): ?>
                                    <div class="input-group">
                                        <input type="text" id="phone" name="info[phone]"
                                               data-inputmask="'mask': '(999)9999999'" required>
                                        <span class="bar"></span>
                                        <label><?php _e( 'Phone', 'colorrun' ) //Моб. телефон ?></label>
                                    </div>
								<?php endif; ?>
                                <div class="input-group">
                                    <select id="country" name="country" required>
                                        <option><?php _e( 'Country', 'colorrun' ) ?></option>
										<?php foreach ( Registration::get_country_list() as $code => $country ): ?>
                                            <option value="<?= $code ?>"><?= $country ?></option>
										<?php endforeach; ?>
                                    </select>
                                    <span class="bar"></span>
                                </div>
                                <div class="input-group">
                                    <input type="text" id="city" name="city" required>
                                    <span class="bar"></span>
                                    <label><?php _e( 'City', 'colorrun' ) ?></label>
                                </div>
								<?php if ( Registration::is_active_field( 'club' ) ): ?>
                                    <div class="input-group">
                                        <input type="text" id="club" name="info[club]">
                                        <span class="bar"></span>
                                        <label><?php _e( 'Club', 'colorrun' ) ?></label>
                                    </div>
								<?php endif; ?>
								<?php if ( Registration::is_active_field( 'tshirt_size' ) ): ?>

                                    <div class="radio-group">
                                        <span class="label"><?php _e( 'T-shirt size', 'colorrun' ) ?>:</span>
                                        <div class="options">
                                            <label class="radio">
                                                <input id="xs" value="xs" type="radio" name="info[tshirt_size]"
                                                       required>
                                                <span class="outer"><span class="inner"></span></span>
                                                XS
                                            </label>
                                            <label class="radio">
                                                <input id="s" value="s" type="radio" name="info[tshirt_size]" required>
                                                <span class="outer"><span class="inner"></span></span>
                                                S
                                            </label>
                                            <label class="radio">
                                                <input id="m" value="m" type="radio" name="info[tshirt_size]" required>
                                                <span class="outer"><span class="inner"></span></span>
                                                M
                                            </label>
                                            <label class="radio">
                                                <input id="l" value="l" type="radio" name="info[tshirt_size]" required>
                                                <span class="outer"><span class="inner"></span></span>
                                                L
                                            </label>
                                            <label class="radio">
                                                <input id="xl" value="xl" type="radio" name="info[tshirt_size]"
                                                       required>
                                                <span class="outer"><span class="inner"></span></span>
                                                XL
                                            </label>
                                        </div>
                                    </div>
								<?php endif; ?>

                                <div class="checkbox-group">
                                    <input type="checkbox" id="personal_data" name="personal_data" value="1" required/>
                                    <label for="personal_data">
										<?php printf( __( 'Yes, I confirm that the data I provided is accurate and up-to-date. %sAgreement to personal data processing%s', 'colorrun' ), '<a href="' . Registration::get_personal_data_link() . '" target="_blank"><strong>', '</strong></a>' ) ?>
                                    </label>
                                </div>
                                <div class="checkbox-group">
                                    <input type="checkbox" id="event_rules" name="event_rules" value="1" required/>
                                    <label for="event_rules">
										<?php printf( __( 'I have read the %sEvent Regulations%s', 'colorrun' ), '<a href="' . Registration::get_event_rules_link() . '" target="_blank"><strong>', '</strong></a>' ) ?>
                                    </label>
                                </div>
                                <input type="hidden" name="participant_id" value="">
                            </form>
                        </div>
                    </div>
                    <div class="step-row">
                        <h2 class="title"><?php _e( 'Personal information', 'colorrun' ) ?></h2>
                        <div class="personal-info">
                            <button class="edit-info">
                                <img src="<?= get_template_directory_uri() ?>/assets/built/images/fake/pencil.png"
                                     alt="">
                                <span><?php _e( 'Edit', 'colorrun' ) ?></span>
                            </button>
                            <dl class="info">
                                <dt><?php _e( 'Distance', 'colorrun' ) ?></dt>
                                <dd data-distance-title><?= get_the_title( $distance_id ) ?></dd>
                                <dt><?php _e( 'Last name', 'colorrun' ) ?></dt>
                                <dd data-id="lastname"></dd>
                                <dt><?php _e( 'First name', 'colorrun' ) ?></dt>
                                <dd data-id="firstname"></dd>
                                <dt><?php _e( 'Gender:', 'colorrun' ) ?></dt>
                                <dd data-id="gender"></dd>
                                <dt><?php _e( 'E-mail', 'colorrun' ) ?></dt>
                                <dd data-id="email"></dd>
								<?php if ( Registration::is_active_field( 'phone' ) ): ?>
                                    <dt><?php _e( 'Phone', 'colorrun' ) //Моб. телефон ?></dt>
                                    <dd data-id="info[phone]"></dd>
								<?php endif; ?>
                                <dt><?php _e( 'Date of birth', 'colorrun' ) ?></dt>
                                <dd data-id="dateofbirth"></dd>
                                <dt><?php _e( 'Country', 'colorrun' ) ?></dt>
                                <dd data-id="country"></dd>
                                <dt><?php _e( 'City', 'colorrun' ) ?></dt>
                                <dd data-id="city"></dd>
								<?php if ( Registration::is_active_field( 'club' ) ): ?>
                                    <dt><?php _e( 'Club', 'colorrun' ) ?></dt>
                                    <dd data-id="info[club]"></dd>
								<?php endif; ?>
								<?php if ( Registration::is_active_field( 'tshirt_size' ) ): ?>
                                    <dt><?php _e( 'T-shirt size:', 'colorrun' ) ?></dt>
                                    <dd data-id="info[tshirt_size]"></dd>
								<?php endif; ?>
                            </dl>
                        </div>
                    </div>
                    <div class="step-row">
                        <h2 class="title"><?php _e( 'Order confirmation', 'colorrun' ) ?></h2>
                        <div class="personal-info">
                            <dl class="preliminary-price">
                                <dt data-distance-title><?= get_the_title( $distance_id ) ?></dt>
                                <dd data-distance-price><?= Distance::format_price( $price ) ?></dd>
                            </dl>
                            <div class="promo-group">
                                <p><?php _e( 'Do you have a promocode?', 'colorrun' ) ?></p>
                                <input type="text" class="promo-input empty"
                                       placeholder="<?php _e( 'Enter promocode', 'colorrun' ) ?>">
                                <button class="promo-submit"
                                        disabled><?php _ex( 'Apply', 'Промокод', 'colorrun' ) ?></button>
                            </div>
                            <p class="promo-info">
		                        <?php _e( 'Please, notice! The promo code may be used only one time. If you have entered the promo code but failed to pay the registration fee, your promo code will not be valid for the next attempt!', 'colorrun' ) ?>
                            </p>
                            <dl class="final-price">
                                <dt><?php _e( 'Amount to pay', 'colorrun' ) ?></dt>
                                <dd class="price" data-distance-price><?= Distance::format_price( $price ) ?></dd>
                            </dl>
                        </div>
                    </div>
                    <div class="step-row">
                        <h2 class="title"><?php _e( 'Payment for registration', 'colorrun' ) ?></h2>
                        <div class="personal-info">
                            <h3 class="user-name"></h3>
                            <h3 class="distance" data-distance-title><?= get_the_title( $distance_id ) ?></h3>
                            <p class="price"><?php _e( 'Amount to pay', 'colorrun' ) ?>: <span
                                        class="amount price"
                                        data-distance-price><?= Distance::format_price( $price ) ?></span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="loader"></div>
            </div>
            <div class="registration-buttons">
                <button class="button back hide"><?php _e( 'Back', 'colorrun' ) ?></button>
                <button class="button next"><?php _e( 'Continue', 'colorrun' ) ?></button>
                <a class="button payment-button hide" data-free="<?php _e( 'Register', 'colorrun' ) ?>"
                   data-pay="<?php _e( 'Pay', 'colorrun' ) ?>"><?php $price == 0 ? _e( 'Register', 'colorrun' ) : _e( 'Pay', 'colorrun' ) ?></a>
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
					<?php _e( 'Unfortunately, the payment time has expired. You order has been cancelled. Please, place a new order.', 'colorrun' ) ?>
                </div>
            </div>
            <a href="<?php the_permalink() ?>" class="button"><?php _e( 'Continue', 'colorrun' ) ?></a>
        </div>
    </div>
<?php get_footer();
