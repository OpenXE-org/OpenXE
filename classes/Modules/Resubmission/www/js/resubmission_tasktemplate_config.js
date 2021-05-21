/**
 * Für die Bedienung der Aufgaben Vorlagen-Konfigurations-Oberfläche
 */
var ResubmissionTaskTemplateConfig = (function ($) {
    'use strict';

    var me = {

        isInitialized: false,

        storage: {
            $table: null,
            $editDialog: null,
            dataTableApi: null
        },

        /**
         * @return void
         */
        init: function () {
            if (me.isInitialized === true) {
                return;
            }

            me.storage.$table = $('#resubmission_tasktemplate_datatable');
            me.storage.$editDialog = $('#resubmission_tasktemplate_edit');
            if (me.storage.$table.length === 0 || me.storage.$editDialog.length === 0) {
                throw 'Could not initialize ResubmissionTaskTemplateConfig. Required elements are missing.';
            }

            me.initDialog();
            me.registerEvents();

            me.isInitialized = true;
        },

        /**
         * @return {void}
         */
        initDialog: function () {
            me.storage.$editDialog.dialog({
                modal: true,
                bgiframe: true,
                closeOnEscape: false,
                minWidth: 1100,
                maxHeight: 700,
                autoOpen: false,
                buttons: [{
                    text: 'ABBRECHEN',
                    click: function () {
                        me.resetEditDialog();
                        me.closeEditDialog();
                    }
                }, {
                    text: 'SPEICHERN',
                    click: function () {
                        me.saveItem();
                    }
                }],
                open: function () {
                    me.initCkEditor();

                    // ANFANG Workaround Projekt-Arbeitspaket-AutoComplete
                    addClicklupe();
                    //lupeclickevent();
                    // ENDE Workaround Projekt-Arbeitspaket-AutoComplete

                    // ANFANG Workaround Projekt-Arbeitspaket-AutoComplete
                    $('input#resubmissiontasktemplate-subproject').autocomplete({
                        source: 'index.php?module=ajax&action=filter&filtername=arbeitspaket&projekt=' + 0
                    });
                    $('input#resubmissiontasktemplate-project').autocomplete({
                        source: 'index.php?module=ajax&action=filter&filtername=projektname',
                        select: function (event, ui) {
                            if (ui.item) {
                                $('input#resubmissiontasktemplate-subproject').autocomplete({
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
         * Workaround ist notwendig weil `$this->app->YUI->CkEditor('resubmissiontasktemplatedescription','belege');` nicht
         * funktioniert
         *
         * @return void
         */
        initCkEditor: function () {
            if (CKEDITOR.instances.hasOwnProperty('resubmissiontasktemplatedescription')) {
                console.log(1);
                return; // Ist bereits initialisiert
            }
            if (CKEDITOR.instances.resubmissiontasktemplatedescription) {
                console.log(2);
                return;
            }
                console.log(3);
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
            var $description = me.storage.$editDialog.find('textarea#resubmissiontasktemplatedescription');
            $description.ckeditor(ckeditorSettings);
        },

        /**
         * @return void
         */
        destroyCkEditor: function () {
            if (!CKEDITOR.instances.hasOwnProperty('resubmissiontasktemplatedescription')) {
                return; // Ist nicht initialisiert
            }
            if (!CKEDITOR.instances.resubmissiontasktemplatedescription) {
                return;
            }

            CKEDITOR.instances.resubmissiontasktemplatedescription.destroy();
        },


        /**
         * @return {void}
         */
        registerEvents: function () {

            // Eintrag bearbeiten
            $(document).on('click', '.resubmissiontasktemplate-edit-button', function (e) {
                e.preventDefault();
                var textfieldConfigId = $(this).data('tasktemplateConfigId');
                me.editItem(textfieldConfigId);
            });

            // Eintrag löschen
            $(document).on('click', '.resubmissiontasktemplate-delete-button', function (e) {
                e.preventDefault();
                var textfieldConfigId = $(this).data('tasktemplateConfigId');
                me.deleteItem(textfieldConfigId);
            });

            // Neuen Eintrag anlegen
            $('#resubmissiontasktemplate-create-button').on('click', function (e) {
                e.preventDefault();
                me.createItem();
            })
        },

        /**
         * @return {void}
         */
        createItem: function () {
            if (me.isInitialized === false) {
                me.init();
            }
            me.resetEditDialog();
            me.openEditDialog();
        },

        /**
         * @param {number} tasktemplateConfigId
         *
         * @return {void}
         */
        editItem: function (tasktemplateConfigId) {
            $.ajax({
                url: 'index.php?module=wiedervorlage&action=settings&cmd=tasktemplate-detail',
                data: {
                    id: tasktemplateConfigId
                },
                method: 'post',
                dataType: 'json',
                success: function (result) {
                    me.storage.$editDialog.find('#resubmissiontasktemplate-id').val(result.data.id);
                    me.storage.$editDialog.find('#resubmissiontasktemplate-title').val(result.data.title);
                    me.storage.$editDialog.find('#resubmissiontasktemplate-employee').val(result.data.employee);
                    me.storage.$editDialog.find('#resubmissiontasktemplate-submissiondatedays').val(result.data.submission_date_days);
                    me.storage.$editDialog.find('#resubmissiontasktemplate-submissiontime').val(result.data.submission_time);
                    me.storage.$editDialog.find('#resubmissiontasktemplate-project').val(result.data.project);
                    me.storage.$editDialog.find('#resubmissiontasktemplate-subproject').val(result.data.subproject);
                    me.storage.$editDialog.find('#resubmissiontasktemplate-requiredfromstage').val(result.data.required_from_stage_id);
                    me.storage.$editDialog.find('#resubmissiontasktemplate-addtaskatstage').val(result.data.add_task_at_stage_id);
                    me.storage.$editDialog.find('#resubmissiontasktemplate-state').val(result.data.state);
                    me.storage.$editDialog.find('#resubmissiontasktemplate-priority').val(result.data.priority);

                    if (CKEDITOR.instances.hasOwnProperty('resubmissiontasktemplatedescription')
                        && CKEDITOR.instances.resubmissiontasktemplatedescription) {
                        CKEDITOR.instances.resubmissiontasktemplatedescription.setData(result.data.description);
                    } else {
                        $('textarea#resubmissiontasktemplatedescription').val(result.data.description);
                    }

                    me.storage.$editDialog.dialog('open');
                },
                error: function (jqXhr) {
                    alert('Fehler: ' + jqXhr.responseJSON.error);
                }
            });
        },

        /**
         * @return {void}
         */
        saveItem: function () {
            $.ajax({
                url: 'index.php?module=wiedervorlage&action=settings&cmd=tasktemplate-save',
                data: {
                    id: $('#resubmissiontasktemplate-id').val(),
                    title: $('#resubmissiontasktemplate-title').val(),
                    employee: $('#resubmissiontasktemplate-employee').val(),
                    submissiondatedays: $('#resubmissiontasktemplate-submissiondatedays').val(),
                    submissiontime: $('#resubmissiontasktemplate-submissiontime').val(),
                    project: $('#resubmissiontasktemplate-project').val(),
                    subproject:$('#resubmissiontasktemplate-subproject').val(),
                    requiredfromstage:$('#resubmissiontasktemplate-requiredfromstage').val(),
                    addtaskatstage:$('#resubmissiontasktemplate-addtaskatstage').val(),
                    state:$('#resubmissiontasktemplate-state').val(),
                    priority:$('#resubmissiontasktemplate-priority').val(),
                    description:$('textarea#resubmissiontasktemplatedescription').val()
                },
                method: 'post',
                dataType: 'json',
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
                }
            });
        },

        /**
         * @param {number} tasktemplateConfigId
         *
         * @return {void}
         */
        deleteItem: function (tasktemplateConfigId) {
            var confirmValue = confirm('Möchten Sie die Aufgaben Vorlage wirklich löschen?');
            if (confirmValue === false) {
                return;
            }

            $.ajax({
                url: 'index.php?module=wiedervorlage&action=settings&cmd=tasktemplate-delete',
                data: {
                    id: tasktemplateConfigId
                },
                method: 'post',
                dataType: 'json',
                success: function (data) {
                    if (data.success === true) {
                        me.reloadDataTable();
                    }
                    if (data.success === false) {
                        alert('Unbekannter Fehler beim Löschen.');
                    }
                },
                error: function (jqXhr) {
                    alert('Fehler: ' + jqXhr.responseJSON.error);
                }
            });
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
         * @return void
         */
        resetEditDialog: function () {
            me.storage.$editDialog.find('#resubmissiontasktemplate-id').val('');
            me.storage.$editDialog.find('#resubmissiontasktemplate-title').val('');
            me.storage.$editDialog.find('#resubmissiontasktemplate-employee').val('');
            me.storage.$editDialog.find('#resubmissiontasktemplate-submissiondatedays').val('');
            me.storage.$editDialog.find('#resubmissiontasktemplate-submissiontime').val('');
            me.storage.$editDialog.find('#resubmissiontasktemplate-project').val('');
            me.storage.$editDialog.find('#resubmissiontasktemplate-subproject').val('');
            me.storage.$editDialog.find('#resubmissiontasktemplate-requiredfromstage').val('0');
            me.storage.$editDialog.find('#resubmissiontasktemplate-addtaskatstage').prop('selectedIndex', 0);
            me.storage.$editDialog.find('#resubmissiontasktemplate-state').val('open');
            me.storage.$editDialog.find('#resubmissiontasktemplate-priority').val('medium');
            me.storage.$editDialog.find('textarea#resubmissiontasktemplatedescription').val('');
        },

        /**
         * Lädt die DataTable-Inhalte neu; per AJAX
         *
         * @return {void}
         */
        reloadDataTable: function () {
            if (!$.fn.DataTable.isDataTable(me.storage.$table)) {
                return; // DataTable ist noch nicht initalisiert
            }

            var dataTableApi = $(me.storage.$table).dataTable().api();
            dataTableApi.ajax.reload();
        }
    };

    return {
        init: me.init
    };

})(jQuery);

$(document).ready(function () {
    if ($('#resubmission_tasktemplate_datatable').length > 0) {
        ResubmissionTaskTemplateConfig.init();
    }
});
