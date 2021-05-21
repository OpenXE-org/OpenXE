var BackupModule = function ($) {
    'use strict';

    /**
     *
     * @type {{readStatus: readStatus, showProcessStarterMissingError: showProcessStarterMissingError, init: init,
     *     saveItem: saveItem, enableDebugMode: enableDebugMode, backupRecovery: backupRecovery, isInitialized:
     *     boolean, runRecovery: runRecovery, storage: {$backupDialog: null, $createItemDialog: null}, initDialog:
     *     initDialog, setCacheBackup: setCacheBackup, hasProcessStarterEnabled: (function(): boolean), resetAdd:
     *     resetAdd, backupImporter: {registerEvents: registerEvents, init: init}, setInterval: (function(): number),
     *     isImporter: boolean, reloadUrl: reloadUrl, createItem: createItem, addDialog: addDialog,
     *     getCacheBackupValue: (function(string): *), disableDebugMode: disableDebugMode, registerEvents:
     *     registerEvents, debugMode: boolean}}
     */
    var me = {

        isInitialized: false,
        isImporter: false,
        debugMode: false,

        storage: {
            $backupDialog: null,
            $createItemDialog: null
        },

        /**
         * @return void
         */
        init: function () {
            if (me.isInitialized === true) {
                return;
            }

            me.storage.$backupDialog = $('#backupModal');
            me.storage.$createItemDialog = $('#add-backup');

            if (me.storage.$createItemDialog.length === 0) {
                throw 'Could not initialize DataTableLabelsUi. Required elements are missing.';
            }

            me.initDialog();
            me.addDialog();

            me.registerEvents();

            me.isInitialized = true;
        },

        /**
         * @return {void}
         */
        enableDebugMode: function () {
            me.debugMode = true;
            console.log('Debug mode enabled!');
        },

        /**
         * @return {void}
         */
        disableDebugMode: function () {
            me.debugMode = false;
            console.log('Debug mode disabled!');
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
         * @return {void}
         */
        initDialog: function () {
            me.storage.$backupDialog.dialog({
                modal: true,
                bgiframe: true,
                closeOnEscape: false,
                minWidth: 700,
                maxHeight: 700,
                autoOpen: false,
                buttons: {
                    ABBRECHEN: function () {
                        $(this).dialog('close');
                    },
                    SPEICHERN: {
                        text: 'WIEDERHERSTELLEN',
                        id: 'run-recovery-btn',
                        click: function () {
                            var iBid = $(this).data('bid');
                            // check migration setting choice
                            if (!$('#recovery-migration').hasClass('invisible')) {
                                if ($('#do-migration').prop('checked') === true &&
                                    $('#old-dbname').val().replace(/\s/g, '') === '') {
                                    alert('Bitte angeben: Alter Datenbankname !');
                                    return ;
                                }
                            }
                            $(this).dialog('close');
                            var refreshId = me.setInterval();
                            me.runRecovery(iBid, refreshId);
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

            // recover backup
            $(document).on('click', '#recover-backup', function (e) {
                e.preventDefault();
                var fieldId = $(this).data('id');
                if (me.hasProcessStarterEnabled() === true) {
                    me.backupRecovery(fieldId);
                } else {
                    me.showProcessStarterMissingError();
                }
            });
            $('#no-migration').on('click', function (event) {
                $(this).prop('checked', true);
                $('#do-migration').prop('checked', false);
                me.hideMigrationDbFieldName();
            });

            $('#do-migration').on('click', function (event) {
                $(this).prop('checked', true);
                $('#no-migration').prop('checked', false);
                me.showMigrationDbFieldName();
            });

            $('.remove-backup').on('click', function (e) {
                e.preventDefault();
                var url = $(this).data('url');
                if (typeof url !== 'undefined') {
                    me.confirmDelete(url);
                }
            });

            $('#create-backup').on('click', function (e) {
                e.preventDefault();
                me.createItem();
            });
        },

        /**
         *Reloads current page with parameter message
         * @param {string} msg
         */
        reloadUrl: function (msg) {
            if (me.debugMode === false) {
                window.location.href = 'index.php?module=backup&action=list&msg=' + msg;
            } else {
                console.log('JOB done! But No Redirect. Debug mode has been enabled!');
            }
        },

        showMigrationSetting: function () {
            $('#recovery-migration').removeClass('invisible');
        },

        showMigrationDbFieldName: function () {
            $('#tr-old-dbname').removeClass('invisible');
        },

        hideMigrationDbFieldName: function () {
            $('#tr-old-dbname').addClass('invisible');
        },

        /**
         *
         * @param {number} bid
         * @returns {boolean}
         */
        backupRecovery: function (bid) {
            var bckId = parseInt(bid);
            if (isNaN(bckId) || bckId <= 0) {
                return false;
            }
            me.setCacheBackup(bckId, 'bid');
            // Check Meta
            $.ajax({
                url: 'index.php?module=backup&action=recover&cmd=check-meta',
                data: {id: bckId},
                method: 'post',
                dataType: 'json',
                beforeSend: function () {
                    App.loading.open();
                },
                success: function (data) {
                    me.storage.$backupDialog.dialog('open');
                    if (data.status === false) {
                        if (typeof data.missing_file !== 'undefined' && data.missing_file === true) {
                            me.storage.$backupDialog.dialog('close');
                            me.reloadUrl(data.message);
                            return;
                        }
                        $('#backupModalTimer').addClass('invisible').loadingOverlay('remove');
                        $('#bck-message').addClass('error').html(data.message);
                        me.showMigrationSetting();
                    } else {
                        $('#bck-message').addClass('warning').html(data.message);
                        me.showMigrationSetting();
                    }
                    if (data.ps_message.replace(/\s/g, '') !== '') {
                        $('#bck-ps-message').addClass('error').append(data.ps_message);
                    }

                },
                error: function ($xhr, textStatus, errorThrown) {
                    alert('Backup konnte nicht hergestellt werden.');
                }
            });

            return false;
        },

        /**
         *
         * @param {number} value
         * @param {string} value
         * @param {string} data
         */
        setCacheBackup: function (value, data) {
            $('#backupModal').attr('data-' + data, value);
        },

        /**
         *
         * @param {string} data
         * @return {string}|{null}
         */
        getCacheBackupValue: function (data) {
            return typeof $('#backupModal').data(data) !== 'undefined' ? $('#backupModal').data(data) : null;
        },

        /**
         *
         * @param {number} bid
         * @param {number} refreshId
         */
        runRecovery: function (bid, refreshId) {
            $('#run-recovery-btn').prop('disabled', true);
            me.setCacheBackup(refreshId, 'refresh');
            // run recovery
            $('#backupModalTimer').removeClass('invisible').loadingOverlay('show').dialog({
                modal: true, minWidth: 1200, resizable: false, closeOnEscape: false,
                dialogClass: 'no-titlebar',
                open: function (event, ui) {
                    $('.ui-dialog-titlebar').hide();
                    $('#backupModalTimer').css({'overflow': 'hidden'});
                }
            });
            var sData = {id: bid};

            if ($('#do-migration').prop('checked') === true && $('#old-dbname').val().replace(/\s/g, '') !== '') {
                sData.old_db = $('#old-dbname').val();
            }

            $.ajax({
                url: 'index.php?module=backup&action=recover',
                data: sData,
                method: 'post',
                dataType: 'json',
                success: function (data) {
                    if (data.status === false) {
                        clearInterval(refreshId);
                        me.reloadUrl(data.message);
                    } else if (typeof data.file_name !== 'undefined') {
                        me.setCacheBackup(data.generic_error, 'generic_error');
                        me.setCacheBackup(data.file_name, 'filename');
                    } else if (typeof data.created_at !== 'undefined') {
                        me.setCacheBackup(data.created_at, 'created_at');
                    }
                },
                error: function ($xhr, textStatus, errorThrown) {
                    alert('Backup konnte nicht hergestellt werden.');
                }
            });
        },

        /**
         * @return {void}
         */
        readStatus: function () {
            var fileName = me.getCacheBackupValue('filename');
            var sData = {};
            if (fileName != null) {
                sData.file_name = fileName;
            }
            var createdAt = me.getCacheBackupValue('created_at');

            if (createdAt != null) {
                sData.created_at = createdAt;
            }

            var backupFile = me.getCacheBackupValue('backup_file');

            if (backupFile != null) {
                sData.backup_file = backupFile;
            }

            $.ajax({
                url: 'index.php?module=backup&action=readstatus',
                data: sData,
                method: 'post',
                dataType: 'json',
                success: function (data) {
                    if (data.finished === true) {
                        var refreshId = me.getCacheBackupValue('refresh');
                        if (refreshId) {
                            clearInterval(refreshId);
                        }
                        me.reloadUrl(data.message);
                    }
                    if ($('.bck-status-message').length > 0) {
                        if ($('.bck-status-message').hasClass('hide')) {
                            $('.bck-status-message').removeClass('hide');
                        }
                        if (data.finished === false && $('#live-status').length > 0) {
                            $('#live-status').html($.trim(data.message) + ' ...');
                        }
                    } else {
                        $('#backupModalTimer div.loading-back').after(
                            '<div class="bck-status-message hide"><p id="live-status"></p></div>');
                    }
                },
                error: function ($xhr, textStatus, errorThrown) {
                    var interValId = me.getCacheBackupValue('refresh');
                    var genericErrorMsg = me.getCacheBackupValue('generic_error');
                    if (interValId) {
                        clearInterval(interValId);
                    }

                    if (me.debugMode === true) {
                        alert('Debug Mode::\n' + errorThrown);
                    }

                    me.reloadUrl(genericErrorMsg);
                }
            });
        },

        /**
         * @return {void}
         */
        createItem: function () {
            if (me.isInitialized === false) {
                me.init();
            }
            if (me.storage.$backupDialog.length === 0) {
                throw 'Could not initialize DataTableLabelsUi. Required elements are missing.';
            }

            if ($('.backup-success').length > 0) {
                alert('Entfernen Sie bitte zuerst das Letzte Backup !');
                return;
            }

            me.resetAdd();
            if (me.hasProcessStarterEnabled() === true) {
                me.storage.$createItemDialog.dialog('open');
            } else {
                me.showProcessStarterMissingError();
            }
        },

        /**
         * @return {boolean}
         */
        hasProcessStarterEnabled: function () {
            return true;
            //var $backupModalStorage = $('#backupModal');
            //return parseInt($backupModalStorage.data('ps')) === 1;
        },

        /**
         * @return {void}
         */
        showProcessStarterMissingError: function () {
            var message = 'Es sieht so aus, als ob der Prozessstarter Cronjob nicht regelm&auml;&szlig;ig ' +
                'ausgef&uuml;hrt wird! Bitte aktivieren Sie diesen ' +
                '(<a href="http://helpdesk.wawision.de/doku.php?id=entwickler:grundinstallation#einrichten_des_heartbeat-cronjobs_optional" target="_blank">Link zu Helpdesk</a>)!';
            me.storage.$backupDialog.dialog('open');
            $('#run-recovery-btn').hide();
            $('#bck-message').addClass('error').html(message);
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
        saveItem: function () {
            var refreshId = me.setInterval();
            me.setCacheBackup(refreshId, 'refresh');

            $('#backupModalTimer').removeClass('invisible').loadingOverlay('show').dialog({
                modal: true, minWidth: 1200, resizable: false, closeOnEscape: false,
                dialogClass: 'no-titlebar',
                open: function (event, ui) {
                    $('.ui-dialog-titlebar').hide();
                    $('#backupModalTimer').css({'overflow': 'hidden'});
                }
            });
            $.ajax({
                url: 'index.php?module=backup&action=create',
                data: {name: $('#b_name').val()},
                method: 'post',
                dataType: 'json',
                beforeSend: function () {
                    App.loading.open();
                },
                success: function (data) {
                    App.loading.close();
                    if (data.status === false) {
                        me.reloadUrl(data.message);
                    }

                    me.reloadUrl(data.success_msg);

                    me.setCacheBackup(data.generic_error, 'generic_error');
                    if (typeof data.created_at !== 'undefined') {
                        me.setCacheBackup(data.created_at, 'created_at');
                    }
                    if (typeof data.backup_file !== 'undefined') {
                        me.setCacheBackup(data.backup_file, 'backup_file');
                    }
                },
                error: function ($xhr, textStatus, errorThrown) {
                    alert('Backup konnte nicht angelegt werden. ');
                }
            });
        },

        /**
         * @return void
         */
        resetAdd: function () {
            $('#add-backup').find('#b_name').val('');
        },

        /**
         * @type {{registerEvents: registerEvents, init: init}}
         */
        backupImporter: {
            isInitialized: false,
            registerEvents: function () {
                $('#input-for-backup-importer').on('click', function (e) {
                    e.preventDefault();
                    $('#backup-importer').click();
                });
            },
            init: function () {

                me.backupImporter.registerEvents();

                if (me.backupImporter.isInitialized === true) {
                    return;
                }

                $('#backup-importer').chunkedUpload({
                    //chunkSize: 2097152, // 2097152 = 2MB
                    upload: {
                        url: 'index.php?module=backup&action=importer&cmd=upload'
                    },
                    fileComplete: function (fileInfo) {
                        if (typeof fileInfo.name === 'undefined') {
                            throw 'File name is missing!';
                        }
                        $.ajax({
                            url: 'index.php?module=backup&action=importer&cmd=completed',
                            data: {file_name: fileInfo.name},
                            method: 'post',
                            dataType: 'json',
                            beforeSend: function () {
                                App.loading.open();
                            },
                            success: function (data) {
                                App.loading.close();
                                if (data.status === false) {
                                    me.reloadUrl(data.message);
                                }
                            },
                            error: function ($xhr, textStatus, errorThrown) {
                                alert('Backup konnte nicht importiert werden');
                            }
                        });
                    }
                });
                me.backupImporter.isInitialized = true;
            }
        },

        /**
         * @param {string} value
         * @return {boolean}|{void}
         */
        confirmDelete: function (value) {

            if (!confirm('Soll der Backup Eintrag wirklich gelöscht werden?')) {
                return false;
            }
            window.location.href = value;
        }
    };

    return {
        init: me.init,
        enableDebug: me.enableDebugMode,
        disableDebug: me.disableDebugMode,
        //createItem: me.createItem,
        import: me.backupImporter.init
    };

}(jQuery);

$(function () {
    if ($('#backupModal').length > 0 || ('.backup-template').length > 0) {
        BackupModule.init();
    }

    if ($('#backup-importer').length > 0) {
        BackupModule.import();
    }
});
