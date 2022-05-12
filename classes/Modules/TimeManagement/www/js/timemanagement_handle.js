var TimeManagementHandle = (function ($) {
    'use strict';

    var me = {
        isInitialized: false,

        selector: {
            handleDialog: '#timemanagement-handle-dialog',
            form: '#timemanagement-handle-form',
            msg: '#timemanagement-handle-msg',
            overviewTable: '#requesteddaystatus',
            clickClass: '.handle-day-status',
            commentSpan: '#comment',
            fromSpan: '#from',
            tillSpan: '#till',
            amountSpan: '#amount',
            employeeNameSpan: '#employee-name',
            employeeNumberSpan: '#employee-number',
            requestTokenHidden: '#request-token',
            requestAddressIdHidden: '#request-address-id',
            requestRejectHidden: '#request-reject',
            deleteTitle: '#delete-title',
            requestTitle: '#request-title',
            defaultNoteVacation: '#default-note-vacation',
            defaultNoteSick: '#default-note-sick',
            internalComment: '#internal-comment'

        },

        storage: {
            $dialog: null
        },

        init: function () {
            if (me.isInitialized === true) {
                return;
            }

            me.storage.$dialog = $(me.selector.handleDialog);
            me.dialogInit();
            me.registerEvents();
            me.isInitialized = true;
        },

        registerEvents: function () {

            $(me.selector.overviewTable).on('click', me.selector.clickClass, function (event) {
                event.preventDefault();
                me.dialogOpen(this.id.replace('vac-', ''));
            });
        },

        dialogInit: function () {
            me.storage.$dialog.dialog({
                modal: true,
                bgiframe: true,
                closeOnEscape: false,
                minWidth: 650,
                minHeight: 200,
                autoOpen: false,
                open: function () {
                    $(me.selector.inputKey).trigger('focus');
                },
                close: function () {
                    me.dialogReset();
                },
                buttons: {

                    ZUSTIMMEN: function () {
                        $(me.selector.form).submit();
                    },

                    ABLEHNEN: function () {
                        $(me.selector.requestRejectHidden).val(1);
                        $(me.selector.form).submit();
                    }
                }
            });
        },

        dialogOpen: function (requestToken) {

            $.ajax({
                url: 'index.php?module=mitarbeiterzeiterfassung&action=timemanagementhandle&cmd=timemanagementhandleinfo',
                type: 'POST',
                dataType: 'json',
                data: {
                    requestToken: requestToken
                },
                success: function (data) {

                    if (data.error) {
                        me.storage.$dialog.find(me.selector.msg).text(data.error);
                    } else {

                        me.dialogReset();

                        let title = '';
                        if (data.type === 'L' || data.type === 'V') {
                            title = $(me.selector.deleteTitle).text();
                        } else {
                            title = $(me.selector.requestTitle).text();
                        }

                        let defaultNote = '';
                        if(data.type === 'S'){
                            defaultNote = $(me.selector.defaultNoteSick).text();
                        }

                        if(data.type === 'R'){
                            defaultNote = $(me.selector.defaultNoteVacation).text();
                        }

                        me.storage.$dialog.find(me.selector.commentSpan).text(data.comment);
                        me.storage.$dialog.find(me.selector.fromSpan).text(data.min_date);
                        me.storage.$dialog.find(me.selector.tillSpan).text(data.max_date);
                        me.storage.$dialog.find(me.selector.amountSpan).text(data.amount);
                        me.storage.$dialog.find(me.selector.employeeNameSpan).text(data.employee_name);
                        me.storage.$dialog.find(me.selector.employeeNumberSpan).text(data.employee_number);
                        me.storage.$dialog.find(me.selector.internalComment).val(defaultNote);

                        me.storage.$dialog.find(me.selector.requestTokenHidden).val(requestToken);
                        me.storage.$dialog.find(me.selector.requestAddressIdHidden).val(data.employee_id);

                        me.storage.$dialog.dialog('option', 'title', title);
                        me.storage.$dialog.dialog('open');
                    }
                },
                beforeSend: function () {}
            });

        },

        dialogClose: function () {
            me.storage.$dialog.dialog('close');
        },

        dialogReset: function () {

            me.storage.$dialog.find(me.selector.commentSpan).text('');
            me.storage.$dialog.find(me.selector.fromSpan).text('');
            me.storage.$dialog.find(me.selector.tillSpan).text('');
            me.storage.$dialog.find(me.selector.amountSpan).text('');
            me.storage.$dialog.find(me.selector.employeeNameSpan).text('');
            me.storage.$dialog.find(me.selector.employeeNumberSpan).text('');
            me.storage.$dialog.find(me.selector.internalComment).val('');

            me.storage.$dialog.find(me.selector.requestRejectHidden).val(0);
            me.storage.$dialog.find(me.selector.requestTokenHidden).val(null);
            me.storage.$dialog.find(me.selector.requestAddressIdHidden).val(null);
        }
    };

    return {
        init: me.init
    };

})(jQuery);

$(document).ready(function () {
    TimeManagementHandle.init();
});
