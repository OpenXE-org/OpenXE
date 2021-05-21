var TimeManagementRequest = (function ($) {
    'use strict';

    var me = {
        isInitialized: false,

        selector: {
            msg: '#timemanagement-msg',
            easyCalendar: '#easycalendar',
            form: '#timemanagement-form',
            newEdit: '#timemanagement-new-edit',
            fromInput: '#from',
            tillInput: '#till',
            remainingVacationSpan: '#remaining-vacation',
            totalVacationSpan: '#total-vacation',
            acceptedVacationSpan: '#accepted-vacation',
            commentTextarea: '#comment',
            newDialog: '#timemanagement-new-dialog',
            requestTitle: '#request-title',
            deleteTitle: '#delete-title',
            calendarattributes: '#calendarattributes',
            buttonOk: '#button-ok',
            plannedVacationSpan: '#planned-vacation',
            statusOldTypeHidden: '#status-old-type',
            statusWishTypeDiv: '#status-wish-type-box',
            //internalComment: '#internal-comment',
            halfday: '#halfday'
        },

        storage: {
            $dialog: null,
            monthNames: [
                'Januar', 'Februar', 'März', 'April', 'Mai',
                'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'],
            dayNames: ['SO', 'MO', 'DI', 'MI', 'DO', 'FR', 'SA']
        },

        init: function () {
            if (me.isInitialized === true) {
                return;
            }

            me.storage.$dialog = $(me.selector.newEdit);
            me.dialogInit();
            me.registerEvents();
            me.registerDatepicker(me.selector.fromInput);
            me.registerDatepicker(me.selector.tillInput);

            me.isInitialized = true;
        },

        registerDatepicker: function (field) {

            if ($(me.selector.calendarattributes).length !== 0) {
                try {
                    var calendarattributes = JSON.parse($(me.selector.calendarattributes).html());

                    me.storage.monthNames = calendarattributes.monthNames;

                    let dayNames = calendarattributes.dayNames;

                    let shortNames = [];
                    me.storage.dayNames = [];
                    for (let i = 0; i <= dayNames.length; i++) {
                        let shortname = dayNames[i].toUpperCase().substring(0, 2);
                        me.storage.dayNames.push(shortname);
                    }
                }
                catch (e) {
                    //do nothing, fallback from storage
                }
            }

            $(field).datepicker({
                dateFormat: 'dd.mm.yy',
                dayNamesMin: me.storage.dayNames,
                firstDay: 1,
                showWeek: false,
                monthNames: me.storage.monthNames
            });
        },

        registerEvents: function () {

            let clickables = [
                'monclick',
                'tueclick',
                'wedclick',
                'thuclick',
                'friclick',
                'satclick',
                'sunclick'
            ];

            let clickClasses = [
                '.monday',
                '.tuesday',
                '.wednesday',
                '.thursday',
                '.friday',
                '.saturday',
                '.sunday'
            ];

            for (let i = 0; i <= clickables.length; i++) {
                if ($(me.selector.easyCalendar).hasClass(clickables[i])) {
                    $(me.selector.easyCalendar).on('click', clickClasses[i], function (event) {
                        let day = $(this).data('day');
                        let month = $(this).data('month');
                        let year = $(this).data('year');
                        me.dialogOpen(day, month, year);
                    });
                }
            }

            $(me.selector.newDialog).on('click', function (event) {
                event.preventDefault();
                me.dialogOpen();
            });
        },

        dialogInit: function () {
            me.storage.$dialog.dialog({
                modal: true,
                bgiframe: true,
                closeOnEscape: false,
                minWidth: 650,
                minHeight: 350,
                maxHeight: 500,
                autoOpen: false,
                open: function () {
                    if ($(me.selector.fromInput).val() === '') {
                        $(me.selector.fromInput).trigger('focus');
                    } else {
                        $(me.selector.tillInput).trigger('focus');
                    }
                },

                close: function () {
                    me.dialogReset();
                },
                buttons: [
                    {
                        id: 'button-ok',
                        text: 'SPEICHERN',
                        click: function () {
                            $(me.selector.form).submit();
                        }
                    }
                ]
            });
        },

        dialogOpen: function (day, month, year) {

            $.ajax({
                url: 'index.php?module=mitarbeiterzeiterfassung&action=timemanagementrequest&cmd=timemanagementinfo',
                type: 'POST',
                dataType: 'json',
                data: {
                    year: year,
                    month: month,
                    day: day
                },
                success: function (data) {

                    if (data.error) {
                        me.storage.$dialog.find(me.selector.msg).text(data.error);
                        $(me.selector.msg).addClass('error');
                    } else {

                        me.dialogReset();
                        var date = '';

                        if (day && month && year) {
                            date = day + '.' + month + '.' + year;
                        }

                        let title = '';
                        if (
                            data.day_type !== '' &&
                            data.day_type !== 'C' &&
                            data.day_type !== 'J'
                        ) {
                            title = $(me.selector.deleteTitle).text();
                            me.storage.$dialog.find(me.selector.statusOldTypeHidden).val(data.day_type);
                            $(me.selector.statusWishTypeDiv).css('display', 'none');
                        } else {
                            title = $(me.selector.requestTitle).text();
                        }

                        if (me.isInPast(day, month, year)) {
                            $(me.selector.msg).text('Der Tag liegt in der Vergangenheit.');
                            if (data.is_accepted_type) {
                                $(me.selector.msg).addClass('error');
                            } else {
                                $(me.selector.msg).addClass('warning');
                            }
                        }

                        me.storage.$dialog.find(me.selector.totalVacationSpan).text(data.vacation_total);
                        me.storage.$dialog.find(me.selector.acceptedVacationSpan).text(data.vacation_accepted);
                        me.storage.$dialog.find(me.selector.plannedVacationSpan).text(data.planned);
                        me.storage.$dialog.find(me.selector.remainingVacationSpan).text(
                            data.vacation_total - data.vacation_accepted - data.planned
                        );
                        //me.storage.$dialog.find(me.selector.internalComment).text(data.internal_comment);

                        me.storage.$dialog.find(me.selector.fromInput).val(date);
                        me.storage.$dialog.find(me.selector.tillInput).val(date);

                        if (me.isInPast(day, month, year) && data.is_accepted_type) {
                            $(me.selector.buttonOk).button('disable');
                        }

                        me.storage.$dialog.dialog('option', 'title', title);
                        me.storage.$dialog.dialog('open');
                    }
                },
                beforeSend: function () {}
            });
        },

        isInPast: function (day, month, year) {
            let dateString = year + '-' + month + '-' + day;
            let date = new Date(dateString).setHours(0, 0, 0, 0);
            let now = new Date().setHours(0, 0, 0, 0);
            return date < now;
        },

        dialogClose: function () {
            me.storage.$dialog.dialog('close');
        },

        dialogReset: function () {
            me.storage.$dialog.find(me.selector.fromInput).val(null);
            me.storage.$dialog.find(me.selector.tillInput).val(null);
            me.storage.$dialog.find(me.selector.commentTextarea).val(null);
            me.storage.$dialog.find(me.selector.statusOldTypeHidden).val('');
            $(me.selector.statusWishTypeDiv).css('display', 'inline');
            me.storage.$dialog.find(me.selector.halfday).prop('checked', false);

            $(me.selector.msg).removeClass('error');
            $(me.selector.msg).removeClass('warning');
            me.storage.$dialog.find(me.selector.msg).text('');
            $(me.selector.buttonOk).button('enable');

            let title = $(me.selector.requestTitle).text();
            me.storage.$dialog.dialog('option', 'title', title);
        }
    };

    return {
        init: me.init
    };

})(jQuery);

$(document).ready(function () {
    TimeManagementRequest.init();
});
