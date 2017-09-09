(function($) {

    var $ = window.jQuery || this.jQuery;

    $.fn.textCropper = function (max) {
        max = max || 3;

        /**
         * Call function for all elements with current selector
         */
        return this.each(function () {
            var $el = $(this);

            /**
             * Call function asynchronously (at next "tick")
             */
            function cropThisText() {
                var lines = Math.ceil($el.height() / parseInt($el.css('line-height'), 10)),
                    text;

                if (!isNaN(lines) && lines > max) {
                    text = $el.text();
                    $el.text(text.slice(0, Math.floor(text.length * max / lines) - 4) + '...');
                }
                $el.removeClass('hidden');
            };
            cropThisText();
        });
    };
    
}).call(this);