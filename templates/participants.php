<?php
/**
 * Template name: Participants list
 * Created by PhpStorm.
 * User: vitaly
 * Date: 11/19/17
 * Time: 23:18
 */
$distances = Distance::get_current_distances();
$current   = isset( $_GET['id'] ) ? (int) $_GET['id'] : $distances->post->ID;
the_post();
get_header(); ?>
<?php if ( has_post_thumbnail() ): ?>
    <section class="hero-section single" style="background-image: url('<?= Theme::get_background_image() ?>')">
        <div class="text-container">
            <h1 class="title"><?php the_title() ?></h1>
        </div>
    </section>
<?php else: ?>
    <section class="single-title">
        <div class="wrapper-outer">
            <div class="wrapper-content"><h1 class="title"><?php the_title() ?></h1></div>
        </div>
    </section>
<?php endif; ?>
    <section class="single-content">
        <div class="wrapper-outer">
            <div class="wrapper-content">
                <div class="content">
					<?php if ( has_excerpt() ): ?>
                        <div class="excerpt">
							<?php the_excerpt(); ?>
                        </div>
					<?php endif; ?>
					<?php the_content(); ?>
                    <form action="">
						<?php _e( 'Choose Distance', 'colorrun' ); ?>
                        <select id="distance" name="id">
							<?php if ( ! in_array( $current, array_keys( (array) $distances ) ) ): ?>
                                <option value="hide"><?php _e( 'Distance', 'colorrun' ) ?></option>
							<?php endif;
							$distances = Distance::get_distances();
							while ( $distances->have_posts() ):
								$distances->the_post();
								if ( $current == get_the_ID() ) {
									$title = get_the_title();
								} ?>
                                <option
									<?= selected( get_the_ID(), $current ) ?>value="<?php the_ID() ?>"
                                    data-age="<?= Distance::get_age() ?>"><?php the_title() ?></option>
							<?php endwhile;
							wp_reset_postdata(); ?>
                        </select>
                    </form>
                    <div class="black-header mtop">
						<?php printf( __( "Participants for event <strong>%s</strong> - by the <strong>%s</strong>", 'colorrun' ), $title, date_i18n( 'd.m.Y, H:i' ) ); //Список учасників <strong>%s</strong> - дані станом на <strong>%s</strong>?>
                    </div>
                    <table id="datatable"
                           class="participants dataTable cell-border compact hover order-column stripe"></table>
                    <script>
                        var el = document.getElementById('body');
                        var className = 'loading';
                        if (el.classList)
                            el.classList.add(className);
                        else
                            el.className += ' ' + className;
                    </script>
                </div>
            </div>
        </div>
    </section>
<?php get_footer();
