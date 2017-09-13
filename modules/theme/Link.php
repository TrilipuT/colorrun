<?php

namespace modules\theme;

/**
 * Class Link
 *
 * @package modules\builder\controls
 */
class Link extends AbstractControl
{
    /**
     * Type of control
     * @var string
     */
    public $type = 'link';
    /**
     * @var string
     */
    public $url = '';
    /**
     * @var string
     */
    public $target = '';

    /**
     * Render the control's content
     */
    public function render_content()
    {
        if ( !empty( $this->label ) && !empty( $this->url ) ) : ?>
            <a
                href="<?= esc_url( $this->url ) ?>"
                title="<?= esc_attr( !empty( $this->description ) ? $this->description : $this->label ) ?>"
                target="<?= esc_attr( $this->target ) ?>"
            ><?= esc_html( $this->label ) ?></a>
        <?php endif;
    }
}