import $ from 'jquery';

export default () => {
    // Will be stored participant data
    var participant = {};

    let $buttons = $('.registration-buttons');

    $buttons
        .on('click', '.next', goToNext)
        .on('click', '.back', goToPrev);

    function goToNext(e) {
        e.preventDefault();
        $('.steps-area').find('.active').removeClass('active').next().addClass('active');
    }

    $('.registration-form').on('submit', function (e) {
        e.preventDefault();

        var fd = new FormData(this);
        for (const [key, value]  of fd.entries()) {
            participant[key] = value;
        }
        $.ajax({
            url: '/wp-json/register/updateInfo', //TODO: set url from settings
            type: "POST",
            data: fd,
            processData: false,  // tell jQuery not to process the data
            contentType: false   // tell jQuery not to set contentType
        }).success(function () {

        });
    });
}