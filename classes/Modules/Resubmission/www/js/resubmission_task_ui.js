/**
 * Modul zur Bedienung der Wiedervorlagen-Aufgaben
 */
var ResubmissionTasksUi = (function ($) {

    var me = {

        storage: {
            dataTableName: 'resubmission_tasks',
            resubmissionId: null,
            $editDialog: null
        },

        /**
         * @param {number} resubmissionId Wiedervorlagen-ID
         */
        init: function (resubmissionId) {
            me.storage.resubmissionId = parseInt(resubmissionId, 10);
            if (isNaN(me.storage.resubmissionId) || me.storage.resubmissionId <= 0) {
                throw 'Could not initialize ResubmissionTasksUi. Required parameter is missing: resubmissionId';
            }

            me.storage.$editDialog = $('#editResubmissionTask');
            if (me.storage.$editDialog.length === 0) {
                throw 'Could not initialize ResubmissionTasksUi. Required elements are missing: #editResubmissionTask';
            }

            me.registerEvents();
            me.initEditDialog();
            me.loadDataTable();
        },

        /**
         * @return {void}
         */
        registerEvents: function () {

            // Aufgabe anlegen
            var $taskCreateButton = $('#resubmissiontask-create');
            $taskCreateButton.off('click');
            $taskCreateButton.on('click', function (e) {
                e.preventDefault();
                me.createTask();
            });

            // Aufgabe bearbeiten
            $(document).off('click', '.resubmissiontask-edit-button');
            $(document).on('click', '.resubmissiontask-edit-button', function (e) {
                e.preventDefault();
                var taskId = $(this).data('taskId');
                me.editTask(taskId);
            });

            // Aufgabe löschen
            $(document).off('click', '.resubmissiontask-delete-button');
            $(document).on('click', '.resubmissiontask-delete-button', function (e) {
                e.preventDefault();
                var taskId = $(this).data('taskId');
                me.deleteTask(taskId);
            });

            // Aufgaben-Status ändern
            $(document).off('click', '.resubmissiontask-state-button');
            $(document).on('click', '.resubmissiontask-state-button', function (e) {
                e.preventDefault();
                var taskId = $(this).data('taskId');
                var taskState = $(this).data('taskState');
                if (taskState === 'open') {
                    me.setTaskState(taskId, 'completed');
                }
                if (taskState === 'completed') {
                    me.setTaskState(taskId, 'open');
                }
            });
        },

        /**
         * @return {void}
         */
        destroy: function () {
            me.destroyDataTable();

            if (me.storage.$editDialog !== null) {
                me.storage.$editDialog.dialog('destroy');
            }
            me.storage.$editDialog = null;
            me.storage.resubmissionId = null;
        },

        /**
         * Öffnet den Dialog zum Anlegen einer Aufgabe
         *
         * @return {void}
         */
        createTask: function () {
            me.resetEditDialog();

            $.ajax({
                url: 'index.php?module=wiedervorlage&action=edit&cmd=taskstageslist',
                data: {
                    resubmission_id: me.storage.resubmissionId
                },
                method: 'post',
                dataType: 'json',
                success: function (data) {

                    // Stages-Dropdown füllen
                    var $stageSelect = $('#resubmissiontask-requiredcompletionstage').html('');
                    if (data.hasOwnProperty('stages')) {
                        $('<option>').val(0).html('- Nie -').appendTo($stageSelect);
                        $.each(data.stages, function (index, stage) {
                            var stageName = stage.shortname !== '' ? stage.shortname : stage.longname;
                            $('<option>').val(stage.id).html(stageName).appendTo($stageSelect);
                        });
                    }
                    $stageSelect.val(0);

                    me.storage.$editDialog.find('#resubmissiontask-id').val('-1');
                    me.openEditDialog();
                },
                error: function (jqXhr) {
                    alert('Fehler: ' + jqXhr.responseJSON.error);
                }
            });

        },

        /**
         * Öffnet den Dialog zum Bearbeiten einer Aufgabe
         *
         * @param {number} taskId
         *
         * @return {void}
         */
        editTask: function (taskId) {
            $.ajax({
                url: 'index.php?module=wiedervorlage&action=edit&cmd=taskget',
                data: {
                    resubmission_id: me.storage.resubmissionId,
                    task_id: taskId
                },
                method: 'post',
                dataType: 'json',
                success: function (data) {
                    $('#resubmissiontask-id').val(data.id);
                    $('#resubmissiontask-title').val(data.title);
                    $('#resubmissiontask-state').val(data.state);
                    $('#resubmissiontask-priority').val(data.priority);
                    $('#resubmissiontask-employee').val(data.employee_name);
                    $('#resubmissiontask-customer').val(data.customer);
                    $('#resubmissiontask-submissiondate').val(data.submission_date);
                    $('#resubmissiontask-submissiontime').val(data.submission_time);
                    $('#resubmissiontask-project').val(data.project_name);
                    $('#resubmissiontask-subproject').val(data.subproject_name);

                    if (CKEDITOR.instances.hasOwnProperty('resubmissiontaskdescription')
                        && CKEDITOR.instances.resubmissiontaskdescription) {
                        CKEDITOR.instances.resubmissiontaskdescription.setData(data.description);
                    } else {
                        $('textarea#resubmissiontaskdescription').val(data.description);
                    }

                    // Stages-Dropdown füllen
                    var $stageSelect = $('#resubmissiontask-requiredcompletionstage').html('');
                    if (data.hasOwnProperty('stages')) {
                        $('<option>').val(0).html('- Nie -').appendTo($stageSelect);
                        $.each(data.stages, function (index, stage) {
                            $('<option>').val(stage.id).html(stage.shortname).appendTo($stageSelect);
                        });
                    }
                    $stageSelect.val(data.required_completion_stage_id);

                    me.openEditDialog();
                },
                error: function (jqXhr) {
                    alert('Fehler: ' + jqXhr.responseJSON.error);
                }
            });
        },

        /**
         * Speichert das geöffnete Aufgaben-Modal;
         *
         * Wird verwendet für "Aufgabe bearbeiten" und "Aufgabe anlegen"
         *
         * @return {void}
         */
        saveTask: function () {
            var $description = $('textarea#resubmissiontaskdescription');
            var descriptionText = $description.ckeditor().editor.getData();
            var formData = {
                resubmission_id: me.storage.resubmissionId,
                task_id: $('#resubmissiontask-id').val(),
                project: $('#resubmissiontask-project').val(),
                subproject: $('#resubmissiontask-subproject').val(),
                title: $('#resubmissiontask-title').val(),
                state: $('#resubmissiontask-state').val(),
                priority: $('#resubmissiontask-priority').val(),
                employee: $('#resubmissiontask-employee').val(),
                customer: $('#resubmissiontask-customer').val(),
                submission_date: $('#resubmissiontask-submissiondate').val(),
                submission_time: $('#resubmissiontask-submissiontime').val(),
                required_completion_stage_id: $('#resubmissiontask-requiredcompletionstage').val(),
                description: descriptionText
            };

            $.ajax({
                url: 'index.php?module=wiedervorlage&action=edit&cmd=tasksave',
                data: formData,
                method: 'post',
                dataType: 'json',
                beforeSend: function () {
                    App.loading.open();
                },
                success: function (data) {
                    if (data.success === true) {
                        me.resetEditDialog();
                        me.reloadDataTable();
                        me.closeEditDialog();
                    }
                    if (data.success === false) {
                        alert(data.error);
                    }
                },
                error: function (jqXhr) {
                    alert('Fehler: ' + jqXhr.responseJSON.error);
                },
                complete: function () {
                    App.loading.close();
                }
            });
        },

        /**
         * Löscht eine Aufgabe; Löschen muss vorher bestätigt werden
         *
         * @param {number} taskId
         *
         * @return {void}
         */
        deleteTask: function (taskId) {
            var confirmation = confirm('Möchten Sie die Aufgabe wirklich löschen?');
            if (!confirmation) {
                return;
            }

            $.ajax({
                url: 'index.php?module=wiedervorlage&action=edit&cmd=taskdelete',
                data: {
                    task_id: taskId,
                    resubmission_id: me.storage.resubmissionId
                },
                method: 'post',
                dataType: 'json',
                success: function () {
                    me.reloadDataTable();
                },
                error: function (jqXhr) {
                    alert('Fehler: ' + jqXhr.responseJSON.error);
                }
            });
        },

        /**
         * Status der Aufgabe ändern
         *
         * @param {number} taskId
         * @param {string} taskState [open|completed]
         */
        setTaskState: function (taskId, taskState) {
            if (taskState !== 'open' && taskState !== 'completed') {
                return;
            }

            $.ajax({
                url: 'index.php?module=wiedervorlage&action=edit&cmd=taskstatechange',
                data: {
                    task_id: taskId,
                    task_state: taskState,
                    resubmission_id: me.storage.resubmissionId
                },
                method: 'post',
                dataType: 'json',
                success: function () {
                    me.reloadDataTable();
                },
                error: function (jqXhr) {
                    alert('Fehler: ' + jqXhr.responseJSON.error);
                }
            });
        },

        /**
         * @return {void}
         */
        initEditDialog: function () {
            me.storage.$editDialog.dialog({
                modal: true,
                bgiframe: true,
                closeOnEscape: false,
                minWidth: 980,
                minHeight: 400,
                autoOpen: false,
                buttons: [{
                    text: 'Abbrechen',
                    click: function () {
                        me.resetEditDialog();
                        me.closeEditDialog();
                    }
                }, {
                    text: 'Speichern',
                    click: function () {
                        me.saveTask();
                    }
                }],
                open: function () {

                    me.initCkEditor();

                    // ANFANG Workaround Projekt-Arbeitspaket-AutoComplete
                    addClicklupe();
                    //lupeclickevent();
                    // ENDE Workaround Projekt-Arbeitspaket-AutoComplete

                    // ANFANG Workaround Projekt-Arbeitspaket-AutoComplete
                    $('input#resubmissiontask-subproject').autocomplete({
                        source: 'index.php?module=ajax&action=filter&filtername=arbeitspaket&projekt=' + 0
                    });
                    $('input#resubmissiontask-project').autocomplete({
                        source: 'index.php?module=ajax&action=filter&filtername=projektname',
                        select: function (event, ui) {
                            if (ui.item) {
                                $('input#resubmissiontask-subproject').autocomplete({
                                    source: 'index.php?module=ajax&action=filter&filtername=arbeitspaket&projekt=' +
                                        ui.item.value
                                });
                            }
                        }
                    });
                    // ENDE Workaround Projekt-Arbeitspaket-AutoComplete
                },
                close: function () {
                    me.resetEditDialog();
                    me.destroyCkEditor();
                }
            });
        },

        /**
         * CKEditor für Beschreibungsfeld initialisieren
         *
         * Workaround für CKEditor
         * Workaround ist notwendig weil `$this->app->YUI->CkEditor('resubmissiontaskdescription','belege');` nicht
         * funktioniert
         *
         * @return void
         */
        initCkEditor: function () {
            if (CKEDITOR.instances.hasOwnProperty('resubmissiontaskdescription')) {
                return; // Ist bereits initialisiert
            }
            if (CKEDITOR.instances.resubmissiontaskdescription) {
                return;
            }

            var ckeditorSettings = {
                toolbar:
                    [
                        ['Bold', 'Italic', 'Underline', 'RemoveFormat', '-', 'Undo', 'Redo'],
                        ['NumberedList', 'BulletedList'],
                        ['Font', 'FontSize', 'TextColor'],
                        ['Source']
                    ],
                allowedContent: true,
                extraPlugins: 'colorbutton,font'
            };
            var $description = me.storage.$editDialog.find('textarea#resubmissiontaskdescription');
            $description.ckeditor(ckeditorSettings);
        },

       /**
        * @return void
        */
        destroyCkEditor: function () {
            if (!CKEDITOR.instances.hasOwnProperty('resubmissiontaskdescription')) {
                return; // Ist nicht initialisiert
            }
            if (!CKEDITOR.instances.resubmissiontaskdescription) {
               return;
            }

            CKEDITOR.instances.resubmissiontaskdescription.destroy();
        },

        /**
         * @return void
         */
        openEditDialog: function () {
            me.storage.$editDialog.dialog('open');
        },

        /**
         * @return void
         */
        closeEditDialog: function () {
            me.storage.$editDialog.dialog('close');
        },

        /**
         * @return {void}
         */
        resetEditDialog: function () {
            me.storage.$editDialog.find('#resubmissiontask-id').val('0');
            me.storage.$editDialog.find('#resubmissiontask-title').val('');
            me.storage.$editDialog.find('#resubmissiontask-state').val('open');
            me.storage.$editDialog.find('#resubmissiontask-priority').val('medium');
            me.storage.$editDialog.find('#resubmissiontask-employee').val('');
            me.storage.$editDialog.find('#resubmissiontask-customer').val('');
            me.storage.$editDialog.find('#resubmissiontask-submissiondate').val('');
            me.storage.$editDialog.find('#resubmissiontask-submissiontime').val('');
            me.storage.$editDialog.find('#resubmissiontask-project').val('');
            me.storage.$editDialog.find('#resubmissiontask-subproject').val('');
            me.storage.$editDialog.find('#resubmissiontask-requiredcompletionstage').html(
                '<option value="0">- Nie -</option>'
            );

            if (CKEDITOR.instances.hasOwnProperty('resubmissiontaskdescription')) {
                CKEDITOR.instances.resubmissiontaskdescription.setData('');
            }
        },

        /**
         *
         *
         * @return {void}
         */
        loadDataTable: function () {
            me.fetchDataTableHtml()
              .then(
                  function () {
                      // DataTable-Daten laden und anzeigen
                      me.initDataTable();
                  },
                  function () {
                      // Fehler beim Abrufen der DataTable-Einstellungen
                      alert('Fehler beim Abrufen der Datatable \'resubmission_tasks\'.');
                  }
              );
        },

        /**
         * DataTable-HTML-Tabelle + Settings-JSON laden
         *
         * @return {jqXHR} jQuery jqXHR-Objekt
         */
        fetchDataTableHtml: function () {
            if (me.storage.resubmissionId === null) {
                throw 'Could not initialize ResubmissionTasksUi. Required settings are missing: resubmissionId';
            }

            return $.ajax({
                url: 'index.php?module=wiedervorlage&action=edit&cmd=tasktablehtml',
                data: { 'id': me.storage.resubmissionId },
                type: 'GET',
                dataType: 'html',
                success: function (htmlResult) {
                    $('#resubmission-tasks-datatable').html(htmlResult);
                }
            });
        },

        /**
         * @return {void}
         */
        initDataTable: function () {
            DataTableHelper.initDataTable(me.storage.dataTableName);
        },

        /**
         * @return {void}
         */
        reloadDataTable: function () {
            DataTableHelper.refreshDataTable(me.storage.dataTableName);
        },

        /**
         * @return {void}
         */
        destroyDataTable: function () {
            DataTableHelper.destroyDataTable(me.storage.dataTableName);
        }
    };

    return {
        init: me.init,
        destroy: me.destroy,
        editTask: me.editTask
    };

})(jQuery);
