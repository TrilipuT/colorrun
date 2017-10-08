import $ from 'jquery';

export default () => $('.accordion-widget .accordion-item .toggler').on('click', function (e) {
    let $this = $(this),
        $item = $this.parents('.accordion-item'),
        $items = $this.parents('.accordion-widget').find('.accordion-item');

    if ($items.length > 1) {
        if ($item.hasClass('active')) {
            $items.removeClass('active');
        } else {
            $items.removeClass('active');
            $item.addClass('active');
        }
    } else {
        $item.toggleClass('active');
    }
})