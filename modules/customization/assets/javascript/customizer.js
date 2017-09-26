jQuery(function ($) {
    var $body = $('body');
    var tmpl = wp.template('styles');
    var logo = $('#logo');

    $body.append('<style id="cz-style">' + tmpl(customizer.options) + '</style>');

    function hexToRGB(hex) {
        var r = parseInt(hex.slice(1, 3), 16),
            g = parseInt(hex.slice(3, 5), 16),
            b = parseInt(hex.slice(5, 7), 16);

        return r + ", " + g + ", " + b;
    }

    for (name in customizer.options) {
        if (!customizer.options.hasOwnProperty(name)) continue;

        if (!wp.customize) continue;

        wp.customize(name, function (value) {
            var item_name = name;
            value.bind(function (to) {
                customizer.options[item_name] = to;
                if (item_name.indexOf('font') === 0) {
                    customizer.options[item_name + '-web'] = to.replace(' ', '+');
                } else if (item_name.indexOf('#') === 0) {
                    customizer.options[item_name + '-rgb'] = hexToRGB(to);
                }

                $('#cz-style').text(wp.template('styles')(customizer.options));
            });
        });
    }
});