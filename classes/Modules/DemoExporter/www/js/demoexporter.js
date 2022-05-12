var DemoExporterModule = function ($) {
    'use strict';

    var me = {

        isInitialized: false,

        storage: {
            $backupDialog: null,
            $createItemDialog: null,
            $demoExporterDialog: null
        },

        /**
         * @return void
         */
        init: function () {
            if (me.isInitialized === true) {
                return;
            }

            me.storage.$demoExporterDialog = $('#demo-exporter-dialog');
            me.storage.$createItemDialog = $('#add-dump-configurator');

            me.addDialog();
            me.demoDialog();
            me.registerEvents();

            me.isInitialized = true;
        },

        registerEvents: function () {
            $('#add-more-table').on('click', function (e) {
                e.preventDefault();
                me.addNewFields();
            });

            $(document).on('click', '.remove-me', function (e) {
                e.preventDefault();
                me.removeField(this);
            });

            $(document).on('click', '#delete-demo-exporter', function (e) {
                e.preventDefault();
                me.delete();
            });

            $(document).on('click', '#download-demo-exporter', function (e) {
                e.preventDefault();
                me.removeDemoCache('file_name');
                me.removeDemoCache('refresh_id');
                if (me.hasProcessStarterEnabled() === true) {
                    me.export();
                } else {
                    me.showProcessStarterMissingError();
                }
            });
        },

        /**
         * @return {void}
         */
        addDialog: function () {
            me.storage.$createItemDialog.dialog({
                modal: true,
                bgiframe: true,
                closeOnEscape: false,
                minWidth: 650,
                maxHeight: 700,
                autoOpen: false,
                buttons: {
                    ABBRECHEN: function () {
                        me.resetAdd();
                        $(this).dialog('close');
                    },
                    SPEICHERN: function () {
                        me.saveItem();
                        $(this).dialog('close');
                    }
                }
            });
        },

        /**
         * @return {void}
         */
        demoDialog: function () {
            me.storage.$demoExporterDialog.dialog({
                modal: true,
                bgiframe: true,
                closeOnEscape: false,
                minWidth: 650,
                maxHeight: 700,
                autoOpen: false,
                buttons: {
                    ABBRECHEN: function () {
                        me.resetAdd();
                        $(this).dialog('close');
                    }
                }
            });
        },

        /**
         * @return {void}
         */
        saveItem: function () {
            var $form = $('#dump-configurator-form');
            $form.action = 'index.php?module=demoexporter&action=create';
            $form.submit();
        },

        /**
         * @return void
         */
        resetAdd: function () {
            $('.geklonnt').remove();
            $('#add-dump-configurator').find('.d_sql').val('');
        },

        /**
         * @return {void}
         */
        createDump: function () {
            if (me.isInitialized === false) {
                me.init();
            }

            me.resetAdd();
            me.storage.$createItemDialog.dialog('open');
        },

        addNewFields: function () {
            $('#configurator-container tbody').append('<tr class=\'geklonnt\'>' +
                '<td> <label><strong>Table name</strong></label>' +
                '<input type="text" name="table[]" class="d_table" size="20" placeholder="artikel" required></td>' +
                '<td><label><strong>Where Kondition</strong></label>' +
                '<textarea name="where[]" class="d_sql" rows="5" cols="50"></textarea></td>' +
                '<td class=\'remove-me\'> [-] </td>' +
                '</tr> ');
        },

        removeField: function (src) {
            var $tr = $(src).closest('tr');
            $tr.remove();
        },

        delete: function () {
            var value = 'index.php?module=demoexporter&action=delete';
            if (!confirm('Soll der Eintrag wirklich gelöscht oder storniert werden?')) {
                return false;
            } else {
                window.location.href = value;
            }
        },

        export: function () {

            var refreshId = me.setInterval();
            me.setDemoCache('refresh_id', refreshId);
            $('#demoExporterModalTimer').removeClass('hide').loadingOverlay('show').dialog({
                modal: true, minWidth: 1200, resizable: false, closeOnEscape: false,
                dialogClass: 'no-titlebar',
                open: function (event, ui) {
                    $('.ui-dialog-titlebar').hide();
                    $('#demoExporterModalTimer').css({'overflow': 'hidden'});
                }
            });

            $.ajax({
                url: 'index.php?module=demoexporter&action=export',
                data: {},
                method: 'get',
                dataType: 'json',
                success: function (data) {
                    if (data.status === false) {
                        clearInterval(refreshId);
                        me.reloadUrl(data.message);
                    }
                    me.setDemoCache('generic_error', data.generic_error);
                    me.setDemoCache('file_name', data.file_name);
                },
                error: function ($xhr, textStatus, errorThrown) {
                    alert('Demo konnte nicht exportiert werden');
                }
            });

        },
        /**
         * @return {void}
         */
        readStatus: function () {

            var fileName = me.getCacheDemoValue('file_name');
            var sData = {};
            if (fileName != null) {
                sData = {'file_name': fileName};
            }

            $.ajax({
                url: 'index.php?module=demoexporter&action=readstatus',
                data: sData,
                method: 'post',
                dataType: 'json',
                success: function (data) {
                    if (data.finished === true) {
                        var refreshId = me.getCacheDemoValue('refresh_id');
                        clearInterval(refreshId);
                        $('#demoExporterModalTimer').addClass('hide').loadingOverlay('remove').dialog('close');
                        var reloadLink = 'index.php?module=demoexporter&action=list&cmd=download&file=' + fileName;
                        me.reloadUrl(reloadLink);
                        return;
                    }
                    if ($('.demo-exporter-status-message').length > 0) {
                        if ($('.demo-exporter-status-message').hasClass('hide')) {
                            $('.demo-exporter-status-message').removeClass('hide');
                        }
                        if (data.finished === false && $('#live-status').length > 0) {
                            $('#live-status').html($.trim(data.message) + ' ...');
                        }
                    } else {
                        $('#demoExporterModalTimer div.loading-back').after(
                            '<div class="demo-exporter-status-message hide"><p id="live-status"></p></div>');
                    }
                    //console.log(data);
                },
                error: function ($xhr, textStatus, errorThrown) {
                    var interValId = me.getCacheDemoValue('refresh_id');
                    var genericErrorMsg = me.getCacheDemoValue('generic_error');
                    if (interValId) {
                        clearInterval(interValId);
                    }
                    var errorReloadLink = 'index.php?module=demoexporter&action=list&msg=' + genericErrorMsg;
                    me.reloadUrl(errorReloadLink);
                }
            });
        },
        /**
         * @return {number}  refreshId
         */
        setInterval: function () {
            return setInterval(function () {
                me.readStatus();
            }, 5000);
        },

        /**
         * @param {string} data
         * @return {string}|{null}
         */
        getCacheDemoValue: function (data) {
            return typeof $('#demo-exporter-dialog').data(data) !== 'undefined' ?
                $('#demo-exporter-dialog').attr('data-' + data) : null;
        },

        /**
         *
         * @param {string} data
         * @param {number} value
         * @param {string} value
         */
        setDemoCache: function (data, value) {
            $('#demo-exporter-dialog').attr('data-' + data, value);
        },

        /**
         * @param {string} data
         */
        removeDemoCache: function (data) {
            $('#demo-exporter-dialog').removeAttr(data);
        },

        /**
         *Reloads current page with parameter message
         * @param {string} value
         */
        reloadUrl: function (value) {
            window.location.href = value;
        },

        /**
         * @return {boolean}
         */
        hasProcessStarterEnabled: function () {
            var $demoModalStorage = $('#demo-exporter-dialog');
            return parseInt($demoModalStorage.data('ps')) === 1;
        },

        /**
         * @return {void}
         */
        showProcessStarterMissingError: function () {
            var message = 'Es sieht so aus, als ob der Prozessstarter Cronjob nicht regelm&auml;&szlig;ig ' +
                'ausgef&uuml;hrt wird! Bitte aktivieren Sie diesen ' +
                '(<a href="http://helpdesk.wawision.de/doku.php?id=entwickler:grundinstallation#einrichten_des_heartbeat-cronjobs_optional" target="_blank">Link zu Helpdesk</a>)!';
            me.storage.$demoExporterDialog.dialog('open');
            $('#demo-exporter-message').addClass('error').html(message);
        }

    };

    return {
        init: me.init,
        createDump: me.createDump
    };

}(jQuery);

$(function () {
    if ($('#add-dump-configurator').length > 0) {
        DemoExporterModule.init();
    }
});