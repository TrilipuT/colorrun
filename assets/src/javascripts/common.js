import $ from 'jquery';

import detectTouchSupport from "./modules/detectTouchSupport";
import shave from "shave";
import gallery from "./modules/gallery";
import countdown from "./modules/countdown";

import dropdown from './modules/dropdownToggler';
import accordion from './modules/accordionWidget';
import burger from './modules/burgerToggler';
import select from './modules/customSelect';

import validation from './modules/validation';

$( function ( $ ) {
    detectTouchSupport();
    gallery();
    countdown();
    dropdown();
    accordion();
    burger();
    select();

    $('.registration-buttons').on('click', validation);

    $('.shave').each(function (i, item) {
        shave(item, $(item).parent().height() * 0.6);
    });

} );

