<?php
/**
 * Created by PhpStorm.
 * User: vitaly
 * Date: 10/10/17
 * Time: 22:47
 *
 * @var $attrs
 */ ?>
<div class="accordion-item">
    <div class="label">
        <h4 class="title"><?= $attrs['title'] ?></h4>
        <button class="toggler"></button>
    </div>
    <div class="accordion-content">
		<?= apply_filters( 'the_content', $attrs['text'] ) ?>
    </div>
</div>
