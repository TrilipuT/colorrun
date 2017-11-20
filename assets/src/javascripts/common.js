import $ from 'jquery';

import detectTouchSupport from "./modules/detectTouchSupport";
import shave from "shave";
import gallery from "./modules/gallery";
import countdown from "./modules/countdown";

import dropdown from './modules/dropdownToggler';
import accordion from './modules/accordionWidget';
import burger from './modules/burgerToggler';
import select from './modules/customSelect';

import sendValidation from './modules/send-validation';

$( function ( $ ) {
    detectTouchSupport();
    gallery();
    countdown();
    dropdown();
    accordion();
    burger();
    select();

    sendValidation();

    $('.shave').each(function (i, item) {
        shave(item, $(item).parent().height() * 0.6);
    });

} );

