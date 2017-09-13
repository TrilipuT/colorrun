<?php
the_post();
get_header(); ?>
    <div class="wrapper">
        <div class="is-container">
	        <?php get_template_part( 'content', get_post_type() ); ?>
        </div>
    </div>
<?php get_footer();
