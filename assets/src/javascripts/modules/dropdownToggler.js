import $ from 'jquery'

export default () => $('.dropdown').on('click', function () {
    $(this).toggleClass('open');
})