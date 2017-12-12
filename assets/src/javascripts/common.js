import $ from 'jquery';

import detectTouchSupport from "./modules/detectTouchSupport";
import shave from "shave";
import gallery from "./modules/gallery";
import countdown from "./modules/countdown";

import dropdown from './modules/dropdownToggler';
import smoothScroll from './modules/smooth-scroll';
import accordion from './modules/accordionWidget';
import burger from './modules/burgerToggler';
import select from './modules/customSelect';
import inputSelection from './modules/input-selection';
import registration from './modules/registration';
import datatables from './modules/datatables';

import sendValidation from './modules/send-validation';

$(function ($) {
    detectTouchSupport();
    gallery();
    countdown();
    dropdown();
    smoothScroll();
    accordion();
    burger();
    select();

    let $registration = $('.registration-form');
    if ($registration.length) {
        $registration.find('[name="email"], [name="info[club]"]').on('change', function () {
            let $item = $(this);
            inputSelection($item);
        });
        sendValidation();
        registration();
    }

    $('.shave').each(function (i, item) {
        shave(item, $(item).parent().height() * 0.6);
    });

    // if ($('#datatable').length) {
        datatables();
    // }
});

