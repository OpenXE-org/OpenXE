var SystemTemplatesModule = function ($) {
    'use strict';

    /**
     * @type {{readStatus: readStatus, init: init, setTemplateCache: setTemplateCache, runLoadTemplate:
     *     runLoadTemplate, getCacheTemplateValue: (function(string): *), isInitialized: boolean, storage:
     *     {$systemTemplatesDialog: null}, initDialog: initDialog, initLoadTemplate: initLoadTemplate,
     *     showTemplateInfo: showTemplateInfo, setInterval: (function(): number), reloadUrl: reloadUrl, registerEvents:
     *     registerEvents}}
     */
    var me = {

        isInitialized: false,

        storage: {
            $dialog: null,
            $confirmResetWithWrittenUsernameDialog: null
        },

        /**
         * @return void
         */
        init: function () {
            if (me.isInitialized === true) {
                return;
            }

            me.storage.$dialog = $('#system-templates-dialog');
            me.storage.$confirmResetWithWrittenUsernameDialog = $('#system-templates-confirm-reset-with-written-username');

            if (me.storage.$dialog.length === 0) {
                throw 'Could not initialize DataTableLabelsUi. Required elements are missing.';
            }

            me.initDialog();
            me.registerEvents();

            me.isInitialized = true;
        },

        /**
         * @return {void}
         */
        initDialog: function () {
            me.storage.$dialog.dialog({
                modal: true,
                bgiframe: true,
                closeOnEscape: false,
                minWidth: 650,
                maxHeight: 700,
                autoOpen: false,
                buttons: {
                    ABBRECHEN: {
                        id: 'cancel-recovery-btn',
                        text: 'ABBRECHEN',
                        click: function () {
                            $(this).dialog('close');
                        }
                    },
                    SPEICHERN: {
                        text: 'DATEN LÖSCHEN',
                        id: 'run-recovery-btn',
                        click: function () {
                            $(this).dialog('close');
                            me.storage.$confirmResetWithWrittenUsernameDialog.dialog('open');
                        }
                    }
                },
                open: function (event, ui) {
                },
                close: function (event, ui) {
                }
            });

            me.storage.$confirmResetWithWrittenUsernameDialog.dialog({
                modal: true,
                bgiframe: true,
                closeOnEscape: false,
                minWidth: 650,
                maxHeight: 700,
                autoOpen: false,
                buttons: {
                    ABBRECHEN: {
                        id: 'cancel-reset-confirmation',
                        text: 'ABBRECHEN',
                        click: function () {
                            $(this).dialog('close');
                        }
                    },
                    SPEICHERN: {
                        text: 'DATEN LÖSCHEN',
                        id: 'run-reset',
                        click: function () {
                            $(this).dialog('close');
                            var iTid = parseInt(me.getCacheTemplateValue('tid'));
                            me.confirmWrittenUserForReset(iTid);
                        }
                    }
                },
                open: function (event, ui) {
                },
                close: function (event, ui) {
                }
            });
        },

        /**
         * @return {void}
         */
        registerEvents: function () {

            // LOAD TEMPLATE
            $('.load-template-badge').on('click', function (e) {
                e.preventDefault();
                var fieldId = $(this).data('id');
                if (me.hasProcessStarterEnabled() === true) {
                    me.initLoadTemplate(fieldId);
                } else {
                    me.showProcessStarterMissingError();
                }
            });

            // LOAD TEMPLATE INFO
            $('.load-template-info').on('click', function (e) {
                e.preventDefault();
                var fieldId = $(this).data('id');
                me.showTemplateInfo(fieldId);
            });
        },

        /**
         *
         * @param {number} id
         */
        showTemplateInfo: function (id) {
            //console.log('DUMMY' + id);
            //@TODO implement Show Info
        },

        /**
         * @return {boolean}
         */
        hasProcessStarterEnabled: function () {
            var $templateModalStorage = $('#system-templates-dialog');
            return parseInt($templateModalStorage.data('ps')) === 1;
        },

        /**
         * @return {void}
         */
        showProcessStarterMissingError: function () {
            var message = 'Es sieht so aus, als ob der Prozessstarter Cronjob nicht regelm&auml;&szlig;ig ' +
                'ausgef&uuml;hrt wird! Bitte aktivieren Sie diesen ' +
                '(<a href="http://helpdesk.wawision.de/doku.php?id=entwickler:grundinstallation#einrichten_des_heartbeat-cronjobs_optional" target="_blank">Link zu Helpdesk</a>)!';
            me.storage.$dialog.dialog('open');
            $('#run-recovery-btn').hide();
            $('#bck-message').addClass('error').html(message);
        },

        /**
         * Initiates SystemTemplates loading
         *
         * @param {number} id
         * @return {void}
         */
        initLoadTemplate: function (id) {
            var tmpId = parseInt(id);
            if (isNaN(tmpId) || (tmpId <= 0 && tmpId !== -1)) {
                return;
            }
            me.setTemplateCache('tid', id);
            if ($('#run-recovery-btn').prop('disabled')) {
                $('#run-recovery-btn').prop('disabled', false);
            }
            if ($('#run-recovery-btn').css('display') === 'none') {
                $('#run-recovery-btn').show();
            }
            // Check Meta
            $.ajax({
                url: 'index.php?module=systemtemplates&action=load&cmd=check-meta',
                data: {id: tmpId},
                method: 'post',
                dataType: 'json',
                beforeSend: function () {
                    App.loading.open();
                },
                success: function (data) {
                    me.storage.$dialog.dialog('open');
                    if (data.status === false) {
                        $('#systemTemplatesModalTimer').addClass('hide').loadingOverlay('remove');
                        if (data.missing_file) {
                            $('#run-recovery-btn').prop('disabled', true);
                            $('#run-recovery-btn').hide();
                            setTimeout(function () {
                                me.storage.$dialog.dialog('close');
                            }, 5000);
                            $('#bck-message').addClass('error').html(data.message_missing_file);
                            return;
                        }
                        $('#bck-message').addClass('error').html(data.message);

                    } else {
                        $('#bck-message').addClass('warning').html(data.message);
                    }
                },
                error: function ($xhr, textStatus, errorThrown) {
                    alert('Template konnte nicht geladen werden');
                }
            });

            console.log('RunLoad DUMMY' + id);
        },

        /**
         *
         * @param {string} data
         * @param {number} value
         * @param {string} value
         */
        setTemplateCache: function (data, value) {
            $('#system-templates-dialog').attr('data-' + data, value);
        },

        /**
         * Runs System Templates loading
         *
         * @param {number} id
         */
        runLoadTemplate: function (id) {
            $('#run-recovery-btn').prop('disabled', true);

            var refreshId = me.setInterval();
            me.setTemplateCache('refreshId', refreshId);
            // run recovery
            $('#systemTemplatesModalTimer').removeClass('hide').loadingOverlay('show').dialog({
                modal: true, minWidth: 1200, resizable: false, closeOnEscape: false,
                dialogClass: 'no-titlebar',
                open: function (event, ui) {
                    $('.ui-dialog-titlebar').hide();
                    $('#systemTemplatesModalTimer').css({'overflow': 'hidden'});
                }
            });
        },

        /**
         *Reloads current page with parameter message
         * @param {string} msg
         */
        reloadUrl: function (msg) {
            window.location.href = 'index.php?module=systemtemplates&action=list&msg=' + msg;
        },

        /**
         * @return {void}
         */
        readStatus: function () {
            var fileName = me.getCacheTemplateValue('filename');
            var sData = {};
            if (fileName != null) {
                sData = {'file_name': fileName};
            }
            $.ajax({
                url: 'index.php?module=systemtemplates&action=readstatus',
                data: sData,
                method: 'post',
                dataType: 'json',
                success: function (data) {
                    if (data.finished === true) {
                        var refreshId = me.getCacheTemplateValue('refreshId');
                        clearInterval(refreshId);
                        me.reloadUrl(data.message);
                        return;
                    }
                    if ($('.template-status-message').length > 0) {
                        if ($('.template-status-message').hasClass('hide')) {
                            $('.template-status-message').removeClass('hide');
                        }
                        if (data.finished === false && $('#live-status').length > 0) {
                            $('#live-status').html($.trim(data.message) + ' ...');
                        }
                    } else {
                        $('#systemTemplatesModalTimer div.loading-back').after(
                            '<div class="template-status-message hide"><p id="live-status"></p></div>');
                    }
                    //console.log(data);
                },
                error: function ($xhr, textStatus, errorThrown) {
                    var interValId = me.getCacheTemplateValue('refresh');
                    var genericErrorMsg = me.getCacheTemplateValue('generic_error');
                    if (interValId) {
                        clearInterval(interValId);
                    }
                    me.reloadUrl(genericErrorMsg);
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
        getCacheTemplateValue: function (data) {
            return typeof $('#system-templates-dialog').data(data) !== 'undefined' ? $('#system-templates-dialog').data(
                data) : null;
        },

        /**
         * @param {number} id
         */
        confirmWrittenUserForReset: function(id){
            $.ajax({
                url: 'index.php?module=systemtemplates&action=load&cmd=confirm-username',
                data: {
                    id: id,
                    username: $('#username-confirmation').val()
                },
                method: 'post',
                dataType: 'json',
                beforeSend: function() {
                    App.loading.open();
                },
                success: function(data) {
                    if(data.status){
                        App.loading.close();
                        me.resetToFactorySettings(id);
                    }else{
                        alert(data.message);
                    }
                }
            });
        },

        /**
         *
         * @param {number} id
         */
        resetToFactorySettings: function(id){
            $.ajax({
                url: 'index.php?module=systemtemplates&action=load&cmd=reset-to-factory-settings',
                data: {id: id},
                method: 'post',
                dataType: 'json',
                success: function (data) {
                    if (data.status) {
                        alert(data.message);
                        me.runLoadTemplate(id);
                        clearInterval(refreshId);
                        me.reloadUrl(data.message);
                    }else{
                        alert(data.message);
                    }
                    me.setTemplateCache('generic_error', data.generic_error);
                },
                error: function ($xhr, textStatus, errorThrown) {
                    alert('Template konnte nicht geladen werden');
                }
            });
        }

    };

    return {
        init: me.init
    };

}(jQuery);

$(function () {
    if ($('#system-templates-dialog').length > 0) {
        SystemTemplatesModule.init();
    }
});
