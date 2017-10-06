import $ from 'jquery';
import Swiper from 'swiper';
export default () => {
    let gallerySlider = new Swiper('.gallery-widget', {
        grabCursor: true,
        loop: true,
        nextButton: '.gallery-widget .swiper-button-next',
        prevButton: '.gallery-widget .swiper-button-prev',
    });
}

