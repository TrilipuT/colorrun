import $ from 'jquery';
import Inputmask from 'inputmask';

export default () => {
    let dateBirth = document.getElementById('dateofbirth'),
        im = new Inputmask();

    im.mask(dateBirth);


    let names = ['lastname', 'firstname', 'gender', 'email','dateofbirth', 'info[phone]', 'country', 'city', 'info[tshirt_size]', 'personal_data', 'event_rules'],
        errors = [];

    $('.registration-buttons .next').on('click', function (e) {
        sendValidation(names);
        errorsChecker(errors);

        if(errors.length === 0){
            $('.steps-area').addClass('loading');
            $('.registration-form').submit();
        }
    });

    $('.registration-form').find('select[required], input[required]').on('change', function () {
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

        if (type === 'email' && !validEmail($input.val())) {
            $wrapper.addClass('error');
            errors.push(itemName);
        } else if (type === 'email' && validEmail($input.val())) {
            $wrapper.removeClass('error');
            removeItem(errors, itemName);
        }

        if (type === 'text' && itemName === 'dateofbirth' && !validDate($input.val())) {
            $wrapper.addClass('error');
            errors.push(itemName);
        } else if (type === 'text' && itemName === 'dateofbirth' && validDate($input.val())) {
            $wrapper.removeClass('error');
            removeItem(errors, itemName);
        }

        if ((type === 'text' || type === 'number') && !$input.val() && itemName !== 'dateofbirth') {
            $wrapper.addClass('error');
            errors.push(itemName)
        } else if ((type === 'text' || type === 'number') && $input.val() && itemName !== 'dateofbirth') {
            $wrapper.removeClass('error');
            removeItem(errors, itemName);
        }
    }

    function errorsChecker(array) {
        $('.registration-buttons .next').prop('disabled', array.length > 0);
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
    
    function validEmail(value) {
        let reg = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

        return reg.test(value)
    }

    function validDate(value) { {
        let reg = /^(?:(?:31(\/|-|\.)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(\/|-|\.)(?:0?[1,3-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(\/|-|\.)0?2\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(\/|-|\.)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/;

        return reg.test(value)
    }

    }
}
