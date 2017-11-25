import $ from 'jquery';

export default () => {
    // Will be stored participant data
    let participant = {};

    $('.edit-info').on('click', goToPrev);

    $('.registration-form').on('submit', function (e) {
        e.preventDefault();

        let fd = new FormData(this);
        for (const [key, value]  of fd.entries()) {
            participant[key] = value;
        }
        $.post({
            url: '/wp-json/register/updateInfo', //TODO: set url from settings
            data: fd,
            processData: false,  // tell jQuery not to process the data
            contentType: false   // tell jQuery not to set contentType
        }).success(function (result) {
            if (result.success) {
                infoFill(participant);
                goToNext(e);
            }
        });
    });

    $('.promo-submit').on('click', function (e) {
        e.preventDefault();

        $.post({
            url: '/wp-json/register/getPaymentInfo/' + participant.participant_id, //TODO: set url from settings
            data: {"coupon": $('.promo-input').val()},
        }).success(function (result) {
            if (result.success) {
                $('.price').text(result.price);
                $('.payment-button').prop('href', result.payment_url);
            } else if (!result.success) {
                let $promoError = $('promo-error');
                if ($promoError.length) {
                    $promoError.html(result.message);
                } else {
                    $promoError = $('.promo-group').after('<p class="promo-error">' + result.message + '</p>');
                }

            }
        });
    });

    function goToNext(e) {
        e.preventDefault();
        let $current = $('.steps-area, .registration-breadcrumbs').find('.active').removeClass('active').next().addClass('active');
        $('.registration-buttons .back').removeClass('hide');
        if ($current.next().length === 0) {
            $('.payment-button').removeClass('hide');
            $('.registration-buttons button').addClass('hide');
        }
    }

    function goToPrev(e) {
        e.preventDefault();
        let $current = $('.steps-area, .registration-breadcrumbs').find('.active').removeClass('active').prev().addClass('active');
        $('.registration-buttons .next').removeClass('hide');
        if ($current.prev().length === 0) {
            $('.registration-buttons .back').addClass('hide');
        }
    }

    function infoFill(info) {
        let $infoTable = $('.personal-info .info'),
            $user = $('.user-name');

        for (let key in info) {
            $infoTable.find('[data-id="' + key + '"]').text(info[key])
        }

        $user.text(info['firstname'] + ' ' + info['lastname']);
    }
}