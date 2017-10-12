<?php
/**
 *
 * @package    colorrun
 * @author     vitaly
 *
 */ ?>
<section class="hero-section">
	<div class="thumbnail-container">
		<img src="<?php background_image() ?>" alt="">
	</div>
	<div class="text-container">
		<h1 class="title"><?php bloginfo('name') ?></h1>
		<h2 class="date"><?= Event::get_current_event_date() ?></h2>
		<div class="countdown">
			<div class="count days">
				<div class="pie-wrapper progress-55">
                        <span class="label">
                            <span class="value">57</span>
                            <span class="text">днів</span>
                        </span>
					<div class="pie">
						<div class="left-side half-circle"></div>
						<div class="right-side half-circle"></div>
					</div>
					<div class="shadow"></div>
				</div>
			</div>
			<div class="count hours">
				<div class="pie-wrapper progress-95">
                        <span class="label">
                            <div class="value">12</div>
					        <div class="text">годин</div>
                        </span>
					<div class="pie">
						<div class="left-side half-circle"></div>
						<div class="right-side half-circle"></div>
					</div>
					<div class="shadow"></div>
				</div>
			</div>
			<div class="count minutes">
				<div class="pie-wrapper progress-95">
                        <span class="label">
                            <div class="value">48</div>
					        <div class="text">хвилин</div>
                        </span>
					<div class="pie">
						<div class="left-side half-circle"></div>
						<div class="right-side half-circle"></div>
					</div>
					<div class="shadow"></div>
				</div>
			</div>
			<div class="count seconds">
				<div class="pie-wrapper progress-95">
                        <span class="label">
                            <div class="value">40</div>
					        <div class="text">секунд</div>
                        </span>
					<div class="pie">
						<div class="left-side half-circle"></div>
						<div class="right-side half-circle"></div>
					</div>
					<div class="shadow"></div>
				</div>
			</div>
		</div>
	</div>
</section>
