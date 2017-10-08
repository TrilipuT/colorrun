import $ from 'jquery';

import detectTouchSupport from "./modules/detectTouchSupport";
import gallery from "./modules/gallery";

import dropdown from './modules/dropdownToggler';
import accordion from './modules/accordionWidget';

$( function ( $ ) {
    detectTouchSupport();
    gallery();

    dropdown();
    accordion();
} );

