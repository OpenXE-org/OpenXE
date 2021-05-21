

var SharingDialog = (function ($) {
    'use strict';

    var me = {
        isInitialized: false,

        selector: {
            dialog: '#editSharingDialog',
            dataTable: '#report_shareduser',
            dialogMessage: '#dialogMessage',
            addUserButton: '#shareUserAddBtn',
            editUserButton: 'a.table-button-edit',
            deleteUserButton: 'a.table-button-delete',
            pageInputUser: '#sharedUserFind',
            inputUser: '#inputDialogUser',
            inputUserId: '#inputDialogUserId',
            inputReportId: '#inputDialogReportId',
            checkChart: '#chkDialogShareChart',
            checkFile: '#chkDialogShareFile',
            checkMenu: '#chkDialogShareMenu',
            checkTab: '#chkDialogShareTab'
        },

        url: {
            ajaxGetSharedUser: 'index.php?module=report&action=share&cmd=ajaxGetShareUser',
            ajaxSaveSharedUser: 'index.php?module=report&action=share&cmd=ajaxSaveShareUser',
            ajaxDeleteSharedUser: 'index.php?module=report&action=share&cmd=ajaxDeleteShareUser'
        },

        storage: {
            $dialog: null,
            formId: 0,
        },

        init: function () {
            if (me.isInitialized === true) {
                return;
            }
            console.log('init sharing dialog');
            me.storage.$dialog = $(me.selector.dialog);
            if (me.storage.$dialog.length !== 1) {
                console.log('stop init sharing dialog');
                return;
            }

            me.dialogInit();
            me.registerEvents();
            me.isInitialized = true;
        },

        /**
         * @return {void}
         */
        dialogInit: function () {
            me.storage.$dialog.dialog({
                modal: true,
                bgiframe: true,
                closeOnEscape: false,
                minWidth: 600,
                maxWidth: 600,
                minHeight: 120,
                maxHeight: 700,
                autoOpen: false,
                buttons: [
                    {
                        text: 'ABBRECHEN',
                        click: function () {
                            me.dialogClose();
                        }
                    },
                    {
                        text: 'SPEICHERN',
                        click: function () {
                            me.dialogSave();
                        }
                    }],
                open: function () {
                    $(me.selector.inputUser).trigger('focus');
                },
                close: function () {
                    me.dialogReset();
                }
            });
        },

        /**
         * @param {number} id data-id of the Column to be edited
         *
         * @return {void}
         */
        dialogOpen: function (id = 0) {
            me.storage.formId = id;

            if (id > 0) {
                $.ajax({
                    url: me.url.ajaxGetSharedUser,
                    data: {
                        id: id
                    },
                    method: 'post',
                    dataType: 'json',
                    beforeSend: function () {
                        App.loading.open();
                    },
                    success: function (data) {
                        me.setDialogData(data);
                        me.storage.$dialog.dialog('open');
                    },
                    error: function (xhr, status, httpStatus) {
                        console.log(status + ' ' + httpStatus + ': ' + xhr.responseText);
                    },
                    complete: function () {
                        App.loading.close();
                    }
                });
            } else {
                me.dialogReset();
                var user = $(me.selector.pageInputUser).val();
                $(me.selector.inputUser).val(user);

                me.storage.$dialog.dialog('open');
            }
        },

        /**
         * @return {void}
         */
        dialogClose: function () {
            me.storage.$dialog.dialog('close');
        },

        /**
         * @return {void}
         */
        dialogReset: function () {
            me.setDialogData(null);
        },

        /**
         * @return {void}
         */
        dialogSave: function () {
            $.ajax({
                url: me.url.ajaxSaveSharedUser,
                data: me.getDialogData(),
                method: 'post',
                dataType: 'json',
                beforeSend: function () {
                    App.loading.open();
                },
                success: function (data) {
                    me.tableRefresh();
                },
                error: function (xhr, status, httpStatus) {
                    if (xhr.status === 400) {
                        alert('Der Bericht ist für diesen Mitarbeiter schon freigegeben.');
                    } else {
                        alert('Fehler beim Speichern');
                        console.log(status + ' ' + httpStatus + ': ' + xhr.responseText);
                    }
                },
                complete: function () {
                    App.loading.close();
                }
            });
            me.dialogClose();
        },

        /**
         * @return {Object}
         */
        getDialogData: function() {
            return {
                report_id: parseInt(me.storage.$dialog.find(me.selector.inputReportId).val()),
                id: me.storage.formId,
                user_id: me.storage.$dialog.find(me.selector.inputUserId).val(),
                name: me.storage.$dialog.find(me.selector.inputUser).val(),
                chart_enabled: (($(me.selector.checkChart).prop('checked') === true) ? 1 : 0),
                file_enabled: (($(me.selector.checkFile).prop('checked') === true) ? 1 : 0),
                menu_enabled: (($(me.selector.checkMenu).prop('checked') === true) ? 1 : 0),
                tab_enabled: (($(me.selector.checkTab).prop('checked') === true) ? 1 : 0)
            };
        },

        /**
         * @param {Object} data
         */
        setDialogData: function(data) {
            if (data == null) {
                data = {
                    report_id: 0,
                    name: '',
                    user_id: 0,
                    chart_enabled: 0,
                    file_enabled: 0,
                    menu_enabled: 0,
                    tab_enabled: 0
                };
            }
            $(me.selector.inputUser).val(data.name);
            $(me.selector.inputUserId).val(data.user_id);
            if (data.name !== '') {
                $(me.selector.inputUser).attr('disabled', true);
            } else {
                $(me.selector.inputUser).attr('disabled', false);
            }
            $(me.selector.checkChart).prop('checked', data.chart_enabled > 0);
            $(me.selector.checkFile).prop('checked', data.file_enabled > 0);
            $(me.selector.checkMenu).prop('checked', data.menu_enabled > 0);
            $(me.selector.checkTab).prop('checked', data.tab_enabled > 0);
        },

        /**
         * @return {void}
         */
        registerEvents: function () {

            $(document).on('click', me.selector.deleteUserButton, function (event) {
                var id = parseInt($(event.currentTarget).data('id'));
                if (id > 0) {
                    var confirmValue = confirm('Wirklich Löschen?');
                    if (confirmValue === false) {
                        return;
                    }
                    me.ajaxDeleteSharedUser(id);
                }
            });

            $(document).on('click', me.selector.editUserButton, function (event) {
                var id = $(event.currentTarget).data('id');
                me.dialogOpen(id);
            });
            $(me.selector.addUserButton).on('click', function (event) {
                event.preventDefault();
                me.dialogOpen(0);
            });
        },

        tableRefresh: function () {
            if ($.fn.DataTable.isDataTable(me.selector.dataTable)) {
                $(me.selector.dataTable).DataTable().ajax.reload();
            }
        },

        ajaxDeleteSharedUser: function (id) {
            $.ajax({
                url: me.url.ajaxDeleteSharedUser,
                data: {
                    id: id
                },
                method: 'post',
                dataType: 'json',
                beforeSend: function () {
                    App.loading.open();
                },
                success: function () {
                  me.tableRefresh();
                },
                error: function (xhr, status, httpStatus) {
                    alert('Fehler beim Löschen');
                    console.log(status + ' ' + httpStatus + ': ' + xhr.responseText);
                },
                complete: function () {
                    App.loading.close();
                }
            });
        }
    };
    return {
        init: me.init
    };

})(jQuery);

$(document).ready(function () {
    SharingDialog.init();
});
