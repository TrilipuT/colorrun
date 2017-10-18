import $ from 'jquery';
import 'jquery-countdown'

export default () => {
    let $countdown = $('.countdown');
    let days, hours, minutes;
    let $days = $countdown.find('.days');
    let $hours = $countdown.find('.hours');
    let $minutes = $countdown.find('.minutes');
    let $seconds = $countdown.find('.seconds');
    $countdown.countdown(parseInt($countdown.data('time')) * 1000, function (event) {
        if (event.offset.totalDays != days) {
            days = event.offset.totalDays;
            $days.find('.pie-wrapper')[0].className = 'pie-wrapper progress-' + ~~(event.offset.totalDays * 100 / 365);
            $days.find('.value').text(event.offset.totalDays);
        }
        if (event.offset.hours != hours) {
            hours = event.offset.hours;
            $hours.find('.pie-wrapper')[0].className = 'pie-wrapper progress-' + ~~(event.offset.hours * 100 / 24);
            $hours.find('.value').text(event.offset.hours);
        }
        if (event.offset.minutes != minutes) {
            minutes = event.offset.minutes;
            $minutes.find('.pie-wrapper')[0].className = 'pie-wrapper progress-' + ~~(event.offset.minutes * 100 / 60);
            $minutes.find('.value').text(event.offset.minutes);
        }
        $seconds.find('.pie-wrapper')[0].className = 'pie-wrapper progress-' + ~~(event.offset.seconds * 100 / 60);
        $seconds.find('.value').text(event.offset.seconds);
    });
}