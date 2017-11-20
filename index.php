<?php
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
					<?php the_content() ?>
                </div>
            </div>
        </div>
    </section>
<?php get_footer();
