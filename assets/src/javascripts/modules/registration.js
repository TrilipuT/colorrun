import $ from 'jquery';

export default () => {
    let $buttons = $('.registration-buttons');

    $buttons
        .on('click', '.next', goToNext)
        .on('click', '.back', goToPrev);

    function goToNext(e) {
        e.preventDefault();
        $('.steps-area').find('.active').removeClass('active').next().addClass('active');
    }
}