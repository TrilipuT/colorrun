import $ from 'jquery';

import detectTouchSupport from "./modules/detectTouchSupport";
import shave from "shave";
import gallery from "./modules/gallery";
import countdown from "./modules/countdown";

import dropdown from './modules/dropdownToggler';
import accordion from './modules/accordionWidget';
import burger from './modules/burgerToggler';
import select from './modules/customSelect';
import inputSelection from './modules/input-selection';
import registration from './modules/registration';

import sendValidation from './modules/send-validation';

$(function ($) {
    detectTouchSupport();
    gallery();
    countdown();
    dropdown();
    accordion();
    burger();
    select();

    sendValidation();
    let $registration = $('.registration-form');
    if ($registration.length) {
        $registration.find('[name="email"], [name="info[club]"]').on('change', function () {
            let $item = $(this);
            inputSelection($item);
        });
        registration();
    }

    $('.shave').each(function (i, item) {
        shave(item, $(item).parent().height() * 0.6);
    });

});

