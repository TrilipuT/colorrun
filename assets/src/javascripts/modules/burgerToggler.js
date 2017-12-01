import $ from 'jquery';

export default () => $('.burger-mobile').on('click', function () {
    $('.main-menu').toggleClass('open');
    $('body').toggleClass('burger-open');
})