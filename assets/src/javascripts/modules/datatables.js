import $ from 'jquery';

require('datatables.net');

export default () => {
    let $body = $('body');
    let $datatables = $('#datatable');
    let $distance_select = $('select#distance');
    if (!$datatables.length) {
        return;
    }
    $distance_select.change(function () {
        $(this).parents('form').submit();
    });

    if ($datatables.length) {
        let age_groups = [[18, 22], [23, 29], [30, 39], [40, 49], [50, 59], [60, 69], [70, 79]];
        let columns = [];
        if ($datatables.hasClass('results')) {
            columns.push({
                name: 'place',
                title: table_settings.titles.place,
                data: 'place',
                responsivePriority: 1
            });
        }
        $.merge(columns, [
            {
                name: 'name',
                title: table_settings.titles.name,
                data: 'name',
                responsivePriority: 2
            },
            {
                name: 'gender',
                title: table_settings.titles.gender,
                class: 'selectable',
                data: function (row, type, set, meta) {
                    if (row.gender) {
                        return table_settings.fields.gender[row.gender];
                    }

                    return '';
                },
                responsivePriority: 19,
                orderable: false

            },
            {
                name: 'bib',
                title: table_settings.titles.bib,
                data: 'bib',
                responsivePriority: 1
            },
            {
                name: 'dateofbirth',
                title: table_settings.titles.dateofbirth,
                data: function (row, type, val, meta) {
                    if (row.dateofbirth) {
                        return row.dateofbirth.slice(0, 4);
                    }
                    return '';
                },
                searchable: false,
                responsivePriority: 12
            }
        ]);
        //Show only for 21km, 42km
        /*if (table_settings.distance.length > 21000) {
            let eventDate = new Date(theme_settings.clock.date);
            columns.push({
                name: 'age_group',
                title: table_settings.titles.age_group,
                data: function (row, type, full, meta) {
                    let ageDate = new Date(eventDate - new Date(row.dateofbirth));
                    let age = Math.abs(ageDate.getUTCFullYear() - 1970);
                    for (let i = 0; i < age_groups.length; i++) {
                        let current = age_groups[i];
                        if (current[0] <= age && age <= current[1]) {
                            return current[0] + ' - ' + current[1];
                        }
                    }
                    return '';
                },
                class: 'selectable',
                orderable: false,
                responsivePriority: 3
            });
        }*/

        columns.push({
            name: 'club',
            title: table_settings.titles.club,
            data: 'club',
            orderable: false,
            class: 'selectable',
            responsivePriority: 9
        });

        /* if (parseInt(table_settings.distance.is_team) || parseInt(table_settings.distance.family_run)) {
             columns.push({
                 name: 'team',
                 title: table_settings.titles.team,
                 data: 'team',
                 responsivePriority: 8
             });
         }*/
        columns.push({
            name: 'city',
            title: table_settings.titles.city,
            class: 'selectable',
            orderable: false,
            data: 'city',
            responsivePriority: 7
        });
        columns.push({
            name: 'country',
            title: table_settings.titles.country,
            data: 'country',
            class: 'country selectable',
            orderable: false,
            width: "20%",
            responsivePriority: 6
        });
        if ($datatables.hasClass('results')) {
            $.merge(columns, [
                {
                    title: table_settings.titles.age_group_place,
                    name: 'age_group_place',
                    data: 'age_group_place',
                    responsivePriority: 4
                },
                {
                    title: table_settings.titles.net_time,
                    name: 'net_time',
                    data: 'net_time',
                    responsivePriority: 5
                },
                {
                    title: table_settings.titles.gross_time,
                    name: 'gross_time',
                    data: 'gross_time',
                    responsivePriority: 3
                }

            ]);
            //Show only for 21km, 42km
            /*if (table_settings.distance.length > 21000) {
                columns.unshift({
                    data: function () {
                        return '+';
                    },
                    className: 'more',
                    orderable: false,
                    targets: 0,
                    responsivePriority: 1
                });
                //Show only for 42km
                if (table_settings.distance.length > 42000) {
                    //
                }

                $datatables.on('click', 'td.more', function () {
                    let $this = $(this),
                        tr = $this.closest('tr'),
                        row = table.row(tr);
                    if (table.isResponsive) {
                        return false;
                    }
                    if (row.child.isShown()) {
                        // This row is already open - close it
                        $this.text('+');
                        row.child.hide();
                        tr.removeClass('shown');
                    } else {
                        // Open this row
                        $this.text('-');
                        row.child(format(row.data())).show();
                        tr.addClass('shown');
                    }
                });
            }*/
        }

        let settings = {
            dom: '<"black-header"fp><t><"black-header"ip>',
            data: table_settings.data,
            pageLength: 100,
            order: [],
            responsive: {
                details: {
                    type: 'column',
                    target: 'tr'
                }
            },
            lengthMenu: [[100, 500, 1000, -1], [100, 500, 1000, "All"]],
            // scrollY: 600,
            // deferRender: true,
            // scroller: true,
            language: table_settings.language,
            fixedHeader: true,
            columns: columns,
            initComplete: function () {
                this.api().columns('.selectable').every(function () {
                    let column = this;
                    let select = $('<select><option value=""></option></select>')
                        .appendTo($(column.header()))
                        .on('change', function () {
                            let val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                            );

                            column
                                .search(val ? '^' + val + '$' : '', true, false)
                                .draw();
                        });
                    column.data().unique().sort().each(function (d, j) {
                        if (d == '' || d == null) {
                            return;
                        }
                        select.append("<option value='" + d + "'>" + d + '</option>')
                    });
                });
                $body.removeClass('loading');

            }
        };
        if (table_settings.search) {
            settings.search = {
                search: table_settings.search
            };
        }
        let table = $datatables.DataTable(settings);

        function format(d) {
            let columns = {
                    'p_5': 'km5',
                    'p_10': 'km10',
                    'p_15': 'km15',
                    'p_20': 'km20',
                    'p_21': 'km21',
                    'p_30': 'km30'
                },
                text = '';
            for (let key in columns) {
                if (typeof d[key] == 'undefined' || d[key] == null || d[key] == '' || typeof table_settings.titles[columns[key]] == 'undefined') {
                    continue;
                }
                text += '<li><span class="dtr-title">' + table_settings.titles[columns[key]] + '</span> <span class="dtr-data">' + d[key] + '</span></li>';
            }
            if (text != '') {
                text = '<ul class="intermediate">' + text + "</ul>";

            }
            return text;
        }

        table.on('responsive-display', function (e, datatable, row, showHide, update) {
            if (showHide) {
                row.child().find('ul').append($(format(row.data())).html());
                table.isResponsive = true;
            }
        });
    }
}