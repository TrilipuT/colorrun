jQuery(function ($) {
    var ced = {
        init: function () {
            $('#the-list')
                .on('click', '.edit', ced.edit)
                .on('click', '.cancel', ced.cancel)
                .on('click', '.save', ced.save);
        },
        edit: function (e) {
            e.preventDefault();
            var $link = $(e.target);
            var $row = $link.parents('tr');

            ced.buildForm($row);
        },
        cancel: function (e) {
            var $link = $(e.target);
            var $row = $link.parents('tr');
            ced.removeForm($row);
            ced.showInfo($row);
        },
        save: function (e) {
            var $link = $(e.target);
            var $row = $link.parents('tr');
            var data = {};
            $row.find('input, select').each(function (i, item) {
                var $item = $(item);
                if (item.type === 'checkbox') {
                    item.name = 'id';
                }
                data[item.name] = $item.val();
            });
            wp.ajax.send('coupon_save', {
                data: data,
                success: function () {
                    ced.removeForm($row);
                    ced.showInfo($row);
                },
                error: function () {

                }
            });
        },
        buildForm: function ($row) {
            var code = $row.find('.column-primary').contents()[0].data.trim();
            wp.ajax.send('coupon_get_info', {
                data: {
                    code: code
                },
                success: function (info) {
                    ced.hideInfo($row);
                    var edit = wp.template('coupon-edit');
                    $row.append(edit(info));
                },
                error: function (error) {

                }
            });
        },
        removeForm: function ($row) {
            $row.find('.editeable').remove();
        },
        hideInfo: function ($row) {
            $row.find('td').hide();
        },
        showInfo: function ($row) {
            $row.find('td:not(".editeable")').show();
        }
    };

    ced.init();
});