var ImportMasterdata = function ($) {
    'use strict';

    var me = {
        url: {
            activateJob: 'index.php?module=importvorlage&action=list&cmd=activatejob',
            deleteJob: 'index.php?module=importvorlage&action=list&cmd=deletejob',
            batchJob: 'index.php?module=importvorlage&action=list&cmd=batch'
        },
        selector: {
            table: 'table#importvorlage_list',
            deleteIcon: 'table#importvorlage_list img.deletejob',
            activateIcon: 'table#importvorlage_list img.activatejob',
            selectAll: 'input#selectall',
            selectBoxes: 'table#importvorlage_list input.select',
            selectBoxesSelected: 'table#importvorlage_list input.select:checked',
            actionSelect: 'select#selaction',
            actionSend: 'input#send'
        },
        updateTable: function () {
            $(me.selector.table).DataTable().ajax.reload();
        },
        sendBatch: function () {
            var $selection = $(me.selector.selectBoxesSelected);

            if ($($selection).length === 0) {
                alert('Bitte Importe auswählen');
                return;
            }
            var ids = [];
            $.each($selection, function () {
                ids.push($(this).data('id'));
            });
            var select = $(me.selector.actionSelect).val();
            switch (select) {
                case 'delete':
                    if (!confirm('Wirklich löschen?')) {
                        return;
                    }
                    break;

                case 'activate':
                    if (!confirm('Wirklich aktivieren?')) {
                        return;
                    }
                    break;
                default:

                    return;
            }
            $.ajax({
                url: me.url.batchJob,
                method: 'post',
                data: {
                    selection: select,
                    jobIds: ids
                },
                dataType: 'json',
                success: function (data) {
                    me.updateTable();
                },
                error: function (xhr, status, httpStatus) {
                    console.log(status + ' ' + httpStatus + ': ' + xhr.responseText);
                }
            });
        },
        activateJob: function (id) {
            $.ajax({
                url: me.url.activateJob,
                method: 'post',
                data: {jobid: id},
                dataType: 'json',
                success: function (data) {
                    me.updateTable();
                },
                error: function (xhr, status, httpStatus) {
                    console.log(status + ' ' + httpStatus + ': ' + xhr.responseText);
                }
            });
        },
        deleteJob: function (id) {
            if (!confirm('Wirklich löschen?')) {
                return;
            }
            $.ajax({
                url: me.url.deleteJob,
                method: 'post',
                data: {jobid: id},
                dataType: 'json',
                success: function (data) {
                    me.updateTable();
                },
                error: function (xhr, status, httpStatus) {
                    console.log(status + ' ' + httpStatus + ': ' + xhr.responseText);
                }
            });
        },
        init: function () {
            $(me.selector.table).on('afterreload', function () {
                $(me.selector.activateIcon).on('click', function () {
                    me.activateJob($(this).data('id'));
                });
                $(me.selector.deleteIcon).on('click', function () {
                    me.deleteJob($(this).data('id'));
                });
            });
            $(me.selector.selectAll).on('click', function () {
                $(me.selector.selectBoxes).prop('checked', $(this).prop('checked'));
            });
            $(me.selector.actionSend).on('click', function () {
                me.sendBatch();
            });
        }
    };

    return {
        init: me.init
    };
}(jQuery);

$(document).ready(function () {
    ImportMasterdata.init();
});
