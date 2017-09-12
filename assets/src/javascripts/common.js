import $ from 'jquery';

import detectTouchSupport from "./modules/detectTouchSupport";
import gallery from "./modules/gallery";

import dropdown from './modules/dropdownToggler';
import radialProgress from './modules/drawRadialProgress';

$( function ( $ ) {
    detectTouchSupport();
    gallery();

    dropdown();
    radialProgress();
} );

