import $ from 'jquery';

export default () => {
    let names = ['distance', 'surname', 'name', 'gender', 'email', 'phone', 'country', 'city', 'tshirt_size', 'personal_data', 'event_rules'],
        errors = [];

    $('.registration-buttons').on('click', function () {
        sendValidation(names);
        errorsChecker(errors)
    });

    $('.registration-form').find('select, input').on('change', function () {
        let inputName = $(this).attr('name'),
            $item = $(this);

        checkInputs(false, inputName, $item);
        errorsChecker(errors)
    });

    function sendValidation(array) {
        array.forEach(function (name) {
            checkInputs(true, name)
        });
    }

    function checkInputs(action, itemName, item) {
        let $input;

        if (action) {
            $input = $('[name="' + itemName + '"]');
        } else {
            $input = item;
        }

        let type = $input.attr('type');
        let $wrapper;

        switch (type) {

            case undefined:
                $wrapper = $input.parents('.select');
                break;

            case 'radio':
                $wrapper = $input.parents('.radio-group');
                break;

            case 'checkbox':
                $wrapper = $input.parents('.checkbox-group');
                break;

            case 'text':
            case 'email':
            case 'number':
                $wrapper = $input.parents('.input-group');
                break;
        }

        if (!type && $input.val() === 'hide') {
            $wrapper.addClass('error');
            errors.push(itemName);
        } else if (!type && $input.val() !== 'hide') {
            $wrapper.removeClass('error');
            removeItem(errors, itemName);
        }

        if ((type === 'radio' || type === 'checkbox') && !$input.filter(':checked').length) {
            $wrapper.addClass('error');
            errors.push(itemName)
        } else if ((type === 'radio' || type === 'checkbox') && $input.filter(':checked').length > 0) {
            $wrapper.removeClass('error');
            removeItem(errors, itemName);
        }

        if ((type === 'text' || type === 'email' || type === 'number') && !$input.val()) {
            $wrapper.addClass('error');
            errors.push(itemName)
        } else if ((type === 'text' || type === 'email' || type === 'number') && $input.val()) {
            $wrapper.removeClass('error');
            removeItem(errors, itemName);
        }
    }

    function errorsChecker(array) {
        if (array.length)
            $('.registration-buttons .next').addClass('disable');
        else
            $('.registration-buttons .next').removeClass('disable');
    }

    function removeItem(arr) {
        let what, a = arguments, L = a.length, ax;
        while (L > 1 && arr.length) {
            what = a[--L];
            while ((ax= arr.indexOf(what)) !== -1) {
                arr.splice(ax, 1);
            }
        }
        return arr;
    }
}