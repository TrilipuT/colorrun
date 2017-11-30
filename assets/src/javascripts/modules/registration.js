import $ from 'jquery';

export default () => {
    // Will be stored participant data
    let participant = {};
    let $form = $('.registration-form');
    let $stepsArea = $('.steps-area');

    $('.edit-info').on('click', goToPrev);

    $form.on('submit', function (e) {
        e.preventDefault();

        let fd = new FormData(this);

        $form.find('input,select,textarea').each(function () {
            let $input = $(this),
                value;

            if ($input.attr('type') === 'radio')
                value = $input.filter(':checked').val();
            else
                value = $input.val();

            if (typeof value !== 'undefined') {
                participant[$input.attr('name')] = value
            }
        });

        $.post({
            url: '/wp-json/register/updateInfo', //TODO: set url from settings
            data: fd,
            processData: false,  // tell jQuery not to process the data
            contentType: false,   // tell jQuery not to set contentType
            beforeSend: function () {
                $stepsArea.addClass('loading');
            }
        }).always(function () {
            $stepsArea.removeClass('loading');
        }).success(function (result) {
            if (result.success) {
                infoFill(participant);
                goToNext(e);
            } else if (result.success === false) {
                if (result.type === 'time_expired') {
                    // If we have timeout - just display popup
                    $('body').addClass('time-out');
                    return;
                }
                let $formError = $('.form-error');
                if ($formError.length) {
                    $formError.html(result.message);
                } else {
                    $form.append('<p class="form-error">' + result.message + '</p>');
                }

            }
        });
    });
    $('.promo-input').on('change, input, keyup', function () {
        let $this = $(this),
            value = $this.val();
        if (value === '') {
            $this.addClass('empty');
        } else {
            $this.removeClass('empty');
        }
        $('.promo-submit').prop('disabled', value === '');
    });
    $('.promo-submit').on('click', function (e) {
        e.preventDefault();
        let coupon = $('.promo-input').val();
        if (coupon === '') {
            return false;
        }
        $.post({
            url: '/wp-json/register/getPaymentInfo/' + participant.participant_id, //TODO: set url from settings
            data: {"coupon": coupon},
            beforeSend: function () {
                $stepsArea.addClass('loading');
            }
        }).always(function () {
            $stepsArea.removeClass('loading');
        }).success(function (result) {
            if (result.success) {
                $('.promo-error').remove();
                $('.price').text(result.price);
                $('.payment-button').prop('href', result.payment_url);
            } else if (!result.success) {
                let $promoError = $('.promo-error');
                if (result.type === 'time_expired') {
                    // If we have timeout - just display popup
                    $('body').addClass('time-out');
                    return;
                }
                if ($promoError.length) {
                    $promoError.html(result.message);
                } else {
                    $('.promo-group').after('<p class="promo-error">' + result.message + '</p>');
                }

            }
        });
    });

    function goToNext(e) {
        e.preventDefault();

        let headerOffset = $('#header').height();
        let $current = $('.steps-area, .registration-breadcrumbs').find('.active').removeClass('active').next().addClass('active');

        $('.registration-buttons .back').removeClass('hide');
        if ($current.next().length === 0) {
            $('.payment-button').removeClass('hide');
            $('.registration-buttons button').addClass('hide');
        }
        $("html, body").animate({scrollTop: $(".steps-area").offset().top - headerOffset}, "fast");
    }

    function goToPrev(e) {
        e.preventDefault();

        let headerOffset = $('#header').height();
        let $current = $('.steps-area, .registration-breadcrumbs').find('.active').removeClass('active').prev().addClass('active');

        $('.registration-buttons .next').removeClass('hide');
        if ($current.prev().length === 0) {
            $('.registration-buttons .back').addClass('hide');
        }
        $("html, body").animate({scrollTop: $(".steps-area").offset().top - headerOffset}, "fast");
    }

    function infoFill(info) {
        let $infoTable = $('.personal-info .info'),
            $user = $('.user-name');

        for (let key in info) {
            let value = info[key];
            if (key === 'gender') {
                value = $('[value="' + value + '"]').parent().text().trim();
            } else if (key === 'country') {
                value = $('[value="' + value + '"]').text().trim();
            }
            $infoTable.find('[data-id="' + key + '"]').text(value);
        }

        $user.text(info['firstname'] + ' ' + info['lastname']);
    }
}