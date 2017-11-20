import $ from 'jquery';

export default () => {
    let names = ['distance', 'surname', 'name', 'gender', 'email', 'phone', 'country', 'city', 'tshirt_size', 'personal_data', 'event_rules'];

    names.forEach(function (name) {
        let $input = $('[name="' + name + '"]'),
            type = $input.attr('type'),
            $wrapper;

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

        if (!type && $input.val() === 'hide')
            $wrapper.addClass('error');
        else if (!type && $input.val() !== 'hide')
            $wrapper.removeClass('error');

        if ((type === 'radio' || type === 'checkbox') && !$input.filter(':checked').length)
            $wrapper.addClass('error');
        else if ((type === 'radio' || type === 'checkbox') && $input.filter(':checked').length > 0)
            $wrapper.removeClass('error');

        if ((type === 'text' || type === 'email' || type === 'number') && !$input.val())
            $wrapper.addClass('error');
        else if ((type === 'text' || type === 'email' || type === 'number') && $input.val())
            $wrapper.removeClass('error');
    });
}