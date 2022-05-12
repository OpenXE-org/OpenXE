
/**
 * Run SQL Query and receive feedback about the success.
 */
var ReportDebugger = (function ($) {
    'use strict';

    var me = {
        isInitialized: false,

        url: {
            ajaxRunSql: 'index.php?module=report&action=edit&cmd=ajaxTryQuery'
        },

        selector: {
            resultTextBox: '#reportEditResult',
            queryTextBox: '#reportEditQuery',
            runDebugButton: '#structureRun',
            paramViewElement: '.parameter-view',
        },

        /**
         * @return void
         */
        init: function () {
            if (me.isInitialized === true) {
                return;
            }
            me.registerEvents();
            me.isInitialized = true;
        },

        /**
         * @return {void}
         */
        registerEvents: function () {
            $(me.selector.runDebugButton).on('click', function (event) {
                event.preventDefault();
                me.ajaxDebugRequest();
            });
        },

        /**
         * @param {string} message
         * @param {boolean} error
         */
        showDebugMessage: function(message, error = false) {
            var $textArea = $(me.selector.resultTextBox);
            if (error === true) {
                $textArea.attr('error', 'true');
            } else {
                $textArea.attr('error', 'false');
            }
            $textArea.text(message);
        },

        /**
         * @return {void}
         */
        ajaxDebugRequest: function() {
            var params = {};
            $(me.selector.paramViewElement).each(function (index, element) {
                params[$(element).data('key')] = $(element).data('value');
            });
            var sql = $(me.selector.queryTextBox).val();
            $.ajax({
                url: me.url.ajaxRunSql,
                data:{
                    statement: sql,
                    parameters: params
                },
                dataType: 'json',
                method: 'post',
                beforeSend: function () {
                    App.loading.open();
                },
                success: function (data) {
                    App.loading.close();
                    if (data.messagetype === 'error') {
                        me.showDebugMessage(data.message, true);
                    } else {
                        me.showDebugMessage(data.message);
                    }
                    if (data.error) {
                        throw('request error: ' + data.error);
                    }
                },
            });
        },
    };

    return {
        init: me.init,
    };

})(jQuery);

/**
 * Add/Edit/Remove columns
 */
var ReportColumns = (function ($) {
    'use strict';

    var me = {
        isInitialized: false,

        selector: {
            columnDialog: '#editColumnDialog',
            dialogMessageBox: '#editColMessage',
            inputFormId: '#reportEditId',
            inputId: '#editColId',
            inputKey: '#editColKey',
            inputTitle: '#editColTitle',
            inputWidth: '#editColWidth',
            inputAlign: '#editColAlignment',
            inputSort: '#editColSort',
            inputFormat: '#edit-col-format',
            inputFormatStatement: '#edit-col-format-statement',
            inputFormatRow: '#edit-col-format-statement-row',
            inputSum: '#chkColSum',
            inputSequence: '#editColSequence',
            buttonAddColumn: '#btnAddColumn',
            buttonAutoCreate: '#btnAutoCreateColumns',
            buttonDeleteAll: '#btnDeleteAllColumns',
            allColumnViews: 'a.table-button-col-edit',
            allColumnDelete: 'a.table-button-col-delete',
            dataTable: '#report_columns',
            resultTextBox: '#reportEditResult',
            queryTextBox: '#reportEditQuery',
        },

        url: {
            ajaxGetColumn: 'index.php?module=report&action=edit&cmd=ajaxGetColumn',
            ajaxSaveColumn: 'index.php?module=report&action=edit&cmd=ajaxSaveColumn',
            ajaxDeleteColumn: 'index.php?module=report&action=edit&cmd=ajaxDeleteColumn',
            ajaxDeleteAllColumns: 'index.php?module=report&action=edit&cmd=ajaxDeleteColumn',
            ajaxAutoCreateColumns: 'index.php?module=report&action=edit&cmd=ajaxAutoCreateColumns'
        },

        storage: {
            $dialog: null,
            formId: 0
        },

        /**
         * @return {void}
         */
        init: function () {
            if (me.isInitialized === true) {
                return;
            }
            me.storage.$dialog = $(me.selector.columnDialog);
            me.storage.formId = $(me.selector.inputFormId).data('form-id');
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
                minWidth: 500,
                minHeight: 320,
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
                    $(me.selector.inputKey).trigger('focus');
                },
                close: function () {
                    //me.dialogReset();
                }
            });
        },

        /**
         * @param {number} id data-id of the Column to be edited
         *
         * @return {void}
         */
        dialogOpen: function (id = 0) {
            if (me.storage.formId < 1 && id < 1) {
                alert('Bitte speichern Sie zuerst den Bericht.');
                return;
            }
            if (id < 1) {
                me.dialogReset();
                me.storage.$dialog.dialog('open');
                return;
            }
            $.ajax({
                url: me.url.ajaxGetColumn,
                data: {
                    id: id
                },
                method: 'post',
                dataType: 'json',
                beforeSend: function () {
                    App.loading.open();
                },
                success: function (data) {
                    me.dialogReset();
                    me.setDialogData(data);
                    App.loading.close();
                    me.storage.$dialog.dialog('open');
                }
            });
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
            me.dialogSetMessage('');
            me.setDialogData(null);
        },

        /**
         * @return {void}
         */
        dialogSave: function () {
            me.ajaxSaveColumn(me.getDialogData());
        },

        /**
         * @return {void}
         */
        dialogDelete: function () {
            var deleteId = me.storage.$dialog.find(me.selector.inputId).val();
            me.ajaxDeleteColumn(deleteId);
        },

        /**
         * @param {string} message
         *
         * @return {void}
         */
        dialogSetMessage: function(message) {
            me.storage.$dialog.find(me.selector.dialogMessageBox).text(message);
        },

        /**
         * @return {Object}
         */
        getDialogData: function() {
            return {
                reportId: me.storage.formId,
                id: me.storage.$dialog.find(me.selector.inputId).val(),
                key_name: me.storage.$dialog.find(me.selector.inputKey).val(),
                title: me.storage.$dialog.find(me.selector.inputTitle).val(),
                width: me.storage.$dialog.find(me.selector.inputWidth).val(),
                alignment: me.storage.$dialog.find(me.selector.inputAlign).val(),
                sorting: me.storage.$dialog.find(me.selector.inputSort).val(),
                format_type: me.storage.$dialog.find(me.selector.inputFormat).val(),
                format_statement: me.storage.$dialog.find(me.selector.inputFormatStatement).val(),
                sum: ((me.storage.$dialog.find(me.selector.inputSum).prop('checked') === true) ? 1 : 0),
                sequence: me.storage.$dialog.find(me.selector.inputSequence).val()
            };
        },

        /**
         * @param {Object} data
         *
         * @return {void}
         */
        setDialogData: function(data) {
            if (data == null) {
                data = {};
            }
            me.storage.$dialog.find(me.selector.inputId).val('id' in data ? data.id : '');
            me.storage.$dialog.find(me.selector.inputKey).val('key_name' in data ? data.key_name : '');
            me.storage.$dialog.find(me.selector.inputTitle).val('title' in data ? data.title : '');
            me.storage.$dialog.find(me.selector.inputWidth).val('width' in data ? data.width : '');
            me.storage.$dialog.find(me.selector.inputAlign).val(
                (('alignment' in data && data.alignment !== '') ? data.alignment : 'left')
            );
            me.storage.$dialog.find(me.selector.inputSort).val(
                (('sorting' in data && data.sorting !== '') ? data.sorting : 'numeric')
            );
            me.storage.$dialog.find(me.selector.inputFormat).val('format_type' in data ? data.format_type : null);
            me.storage.$dialog.find(me.selector.inputFormatStatement)
              .val('format_statement' in data ? data.format_statement : null);
            me.storage.$dialog.find(me.selector.inputSum).prop('checked', 'sum' in data ? data.sum : false);
            me.storage.$dialog.find(me.selector.inputSequence).val('sequence' in data ? data.sequence : '');
            me.toggleReportStatementField();
        },

        /**
         * @return {void}
         */
        registerEvents: function () {
            $(me.selector.buttonAddColumn).on('click', function (event) {
                event.preventDefault();
                me.dialogOpen(0)
            });
            $(me.selector.buttonAutoCreate).on('click', function (event) {
                event.preventDefault();
                me.autoCreateColumns();
            });
            $(me.selector.buttonDeleteAll).on('click', function (event) {
                event.preventDefault();
                var confirmValue = confirm('Wirklich alle Spalten Löschen?');
                if (confirmValue === false) {
                    return;
                }
                me.ajaxDeleteColumn(0, true);
            });
            $(me.selector.inputFormat).on('change', function (event) {
                me.toggleReportStatementField();
            });
            $(document).on('click', me.selector.allColumnViews, function (event) {
                var id = $(event.currentTarget).data('column-id');
                me.dialogOpen(id);
            });
            $(document).on('click', me.selector.allColumnDelete, function (event) {
                var id = $(event.currentTarget).data('column-id');
                if (id > 0) {
                    var confirmValue = confirm('Wirklich Löschen?');
                    if (confirmValue === false) {
                        return;
                    }
                    me.ajaxDeleteColumn(id);
                }
            });
        },

        tableRefresh: function () {
            if ($.fn.DataTable.isDataTable(me.selector.dataTable)) {
                $(me.selector.dataTable).DataTable().ajax.reload();
            }
        },

        /**
         * @param {Object} dialogData
         *
         * @return {void}
         */
        ajaxSaveColumn: function(dialogData) {
            $.ajax({
                url: me.url.ajaxSaveColumn,
                data: dialogData,
                dataType: 'json',
                method: 'post',
                beforeSend: function () {
                    App.loading.open();
                },
                success: function (response) {
                    me.dialogClose();
                    me.tableRefresh();
                },
                error: function (xhr, status, httpStatus) {
                    console.log(status + ' ' + httpStatus + ': ' + xhr.responseText);
                    me.dialogSetMessage(xhr.responseText);
                },
                complete: function () {
                    App.loading.close();
                }
            });
        },

        /**
         * @param {number} id
         * @param {boolean} deleteAll
         *
         * @return {void}
         */
        ajaxDeleteColumn: function(id, deleteAll = false) {
            $.ajax({
                url: me.url.ajaxDeleteColumn,
                data:{
                    id:id,
                    delete_all:deleteAll,
                    report_id: me.storage.formId
                },
                dataType: 'json',
                method: 'post',
                beforeSend: function () {
                    App.loading.open();
                },
                success: function (data) {
                    me.tableRefresh();
                },
                error: function (xhr, status, httpStatus) {
                    console.log(status + ' ' + httpStatus + ': ' + xhr.responseText);
                },
                complete: function () {
                    App.loading.close();
                }
            });
        },

        /**
         * @return {void}
         */
        toggleReportStatementField: function() {
            var formatType = me.storage.$dialog.find(me.selector.inputFormat).val();
            if (formatType === 'custom') {
                me.storage.$dialog.find(me.selector.inputFormatRow)
                  .removeClass('report-edit-inactive')
                  .addClass('report-edit-active');
            } else {
                me.storage.$dialog.find(me.selector.inputFormatRow)
                  .removeClass('report-edit-active')
                  .addClass('report-edit-inactive');
            }
        },

        /**
         * @return {void}
         */
        autoCreateColumns: function() {
            if (me.storage.formId < 1) {
                alert('Bitte speichern Sie zuerst den Bericht.')
                return;
            }
            $.ajax({
                url: me.url.ajaxAutoCreateColumns,
                data:{
                    id:me.storage.formId
                },
                dataType: 'json',
                method: 'post',
                beforeSend: function () {
                    App.loading.open();
                },
                success: function (data) {
                    console.log('response', data);
                    if (data.success === true) {
                        me.tableRefresh();
                    } else {
                        alert(data.message);
                    }
                },
                error: function (xhr, status, httpStatus) {
                    console.log(status + ' ' + httpStatus + ': ' + xhr.responseText);
                },
                complete: function () {
                    App.loading.close();
                }
            });
        },
    };

    return {
        init: me.init,
    };

})(jQuery);

/**
 * Add/Edit/Remove params
 */
var ReportParams = (function ($) {
    'use strict';

    var me = {
        isInitialized: false,

        storage: {
            $dialog: null,
            formId: 0
        },

        url: {
          ajaxGetParam: 'index.php?module=report&action=edit&cmd=ajaxGetParam',
          ajaxSaveParam: 'index.php?module=report&action=edit&cmd=ajaxSaveParam',
          ajaxDeleteParam: 'index.php?module=report&action=edit&cmd=ajaxDeleteParam'
        },

        selector: {
            dialog: '#editParamDialog',
            dialogMessageBox: '#editParamMessage',
            inputReportId: '#reportEditId',
            inputId: '#editParamId',
            inputVarname: '#editParamVarname',
            inputValue: '#editParamValue',
            inputLabel: '#editParamLabel',
            inputDescription: '#editParamDescription',
            inputOptions: '#editParamOptions',
            inputControl: '#editParamControl',
            inputEditable: '#chkParamEditable',
            buttonAddParameter: '#btnAddParam',
            allParamViews: '#paramListing > .parameter-view',
            paramsView: '#paramListing'
        },

        template: {
            paramView: '<div class="parameter-view" data-id="{id}" data-key="{varname}" data-value="{value}">' +
                        '{varname}={value}</div>'
        },

        /**
         * @return {void}
         */
        init: function () {
            if (me.isInitialized === true) {
                return;
            }
            me.storage.formId = $(me.selector.inputReportId).data('form-id');
            me.storage.$dialog = $(me.selector.dialog);
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
                minWidth: 500,
                minHeight: 420,
                maxHeight: 700,
                autoOpen: false,
                buttons: [
                    {
                        text: 'LÖSCHEN',
                        click: function () {
                            me.dialogDelete();
                            me.dialogClose();
                        }
                    },
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
                    $(me.selector.inputVarname).trigger('focus');
                },
                close: function () {
                    //me.dialogReset();
                }
            });
        },

        /**
         * @param {number} id
         *
         * @return {void}
         */
        dialogOpen: function (id = 0) {
            if (me.storage.formId < 1 && id < 1) {
                alert('Bitte speichern Sie zuerst den Bericht.');
                return;
            }
            if (id < 1) {
                me.dialogReset();
                me.storage.$dialog.dialog('open');
                return;
            }
            $.ajax({
                url: me.url.ajaxGetParam,
                data: {
                    id: id
                },
                method: 'post',
                dataType: 'json',
                beforeSend: function () {
                    App.loading.open();
                },
                success: function (data) {
                    me.dialogReset();
                    me.setDialogData(data)
                    App.loading.close();
                    me.storage.$dialog.dialog('open');
                }
            });
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
            me.dialogSetMessage('');
            me.setDialogData(null);
        },

        /**
         * @return {void}
         */
        dialogSave: function () {
            var varname = me.storage.$dialog.find(me.selector.inputVarname).val().toUpperCase();
            me.storage.$dialog.find(me.selector.inputVarname).val(varname);
            me.ajaxSaveParam(me.getDialogData());
        },

        /**
         * @return {void}
         */
        dialogDelete: function () {
            var confirmValue = confirm('Wirklich Löschen?');
            if (confirmValue === false) {
                return;
            }
            var deleteId = me.storage.$dialog.find('#editParamId').val();
            me.ajaxDeleteParam(deleteId);
        },

        /**
         * @param {string} message
         *
         * @return {void}
         */
        dialogSetMessage: function(message = '') {
            me.storage.$dialog.find(me.selector.dialogMessageBox).text(message);
        },

        /**
         * @return {Object}
         */
        getDialogData: function() {
            return {
                reportId: me.storage.formId,
                paramId: me.storage.$dialog.find(me.selector.inputId).val(),
                varname: me.storage.$dialog.find(me.selector.inputVarname).val(),
                value: me.storage.$dialog.find(me.selector.inputValue).val(),
                label: me.storage.$dialog.find(me.selector.inputLabel).val(),
                description: me.storage.$dialog.find(me.selector.inputDescription).val(),
                options: me.storage.$dialog.find(me.selector.inputOptions).val(),
                control_type: me.storage.$dialog.find(me.selector.inputControl).val(),
                editable: (me.storage.$dialog.find(me.selector.inputEditable).prop('checked') === true ? 1 : 0)
            };
        },

        /**
         * @param {Object} data
         *
         * @return {void}
         */
        setDialogData: function(data) {
            if (data == null) {
                data = {};
            }
            me.storage.$dialog.find(me.selector.inputId).val('id' in data ? data.id : '');
            me.storage.$dialog.find(me.selector.inputVarname).val('varname' in data ? data.varname : '');
            me.storage.$dialog.find(me.selector.inputValue).val('default_value' in data ? data.default_value : '');
            me.storage.$dialog.find(me.selector.inputLabel).val('displayname' in data ? data.displayname : '');
            me.storage.$dialog.find(me.selector.inputDescription).val('description' in data ? data.description : '');
            me.storage.$dialog.find(me.selector.inputOptions).val('options' in data ? data.options : '');
            // me.storage.$dialog.find(me.selector.inputControl).val('control_type' in data ? data.control_type : '');
            me.storage.$dialog.find(me.selector.inputControl).val(
                (('control_type' in data && data.control_type !== '') ? data.control_type : 'text')
            );
            me.storage.$dialog.find(me.selector.inputEditable).prop(
                'checked',
                ('editable' in data ? data.editable : false)
            );
        },

        /**
         * @return {void}
         */
        registerEvents: function () {
            $(me.selector.buttonAddParameter).on('click', function (event) {
                event.preventDefault();
                me.dialogOpen();
            });
            $(document).on('click', me.selector.allParamViews, function (event) {
                var id = $(event.target).data('id');
                me.dialogOpen(id);
            });
        },

        /**
         * @param {string} varname
         * @param {string} value
         * @param {number} id
         *
         * @return {void}
         */
        updateParameterView: function(varname, value, id) {
            var $view = $(me.selector.paramsView);
            var $existing = $view.find('div[data-id="'+id+'"]');

            if ($existing === null || $existing.length === 0) {
                var htmlElement = (me.template.paramView + '')
                    .replace('{id}', id)
                    .replace(new RegExp('\\{varname\\}', 'g'), varname)
                    .replace(new RegExp('\\{value\\}', 'g'), value);
                $view.append(htmlElement);
            } else {
                $existing.text(varname + '=' + value);
                $existing.attr('data-key', varname);
                $existing.attr('data-value', value);
            }
        },

        /**
         * @param {number} id
         *
         * @return {void}
         */
        removeParameterView: function(id) {
            $(me.selector.paramsView + ' > div[data-id="'+id+'"]').remove();
        },

        /**
         * @param {Object} dialogData
         *
         * @return {void}
         */
        ajaxSaveParam: function(dialogData) {
            $.ajax({
                url: me.url.ajaxSaveParam,
                data: dialogData,
                dataType: 'json',
                method: 'post',
                beforeSend: function () {
                    App.loading.open();
                },
                success: function (response) {
                    me.updateParameterView(dialogData.varname, dialogData.value, response.id);
                    me.dialogClose();
                },
                error: function (xhr, status, httpStatus) {
                    console.log(status + ' ' + httpStatus + ': ' + xhr.responseText);
                    me.dialogSetMessage(xhr.responseText);
                },
                complete: function () {
                    App.loading.close();
                }
            });
        },

        /**
         * @param {number} id
         *
         * @return {void}
         */
        ajaxDeleteParam: function(id) {
            $.ajax({
                url: me.url.ajaxDeleteParam,
                data:{
                    id:id
                },
                dataType: 'json',
                method: 'post',
                beforeSend: function () {
                    App.loading.open();
                },
                success: function (data) {
                    me.removeParameterView(id);
                },
                error: function (xhr, status, httpStatus) {
                    console.log(status + ' ' + httpStatus + ': ' + xhr.responseText);
                },
                complete: function () {
                    App.loading.close();
                }
            });
        },
    };

    return {
        init: me.init,
    };

})(jQuery);

/**
 * Mark query field as yellow if not saved
 */
var ChangesHighlight = (function ($) {
    'use strict';

    var me = {
        isInitialized: false,

        storage: {
            initQuery: '',
            $queryBox: null,
            $hint: null
        },

        selector: {
            queryTextBox: '#reportEditQuery',
            runDebugButton: '#structureRun',
            dirtyHint: '#dirtyWarning',
        },

        /**
         * @return void
         */
        init: function () {
            if (me.isInitialized === true) {
                return;
            }
            me.storage.$queryBox = $(me.selector.queryTextBox);
            me.storage.$hint = $(me.selector.dirtyHint);
            me.storage.initQuery = me.storage.$queryBox.val();
            me.registerEvents();
            me.isInitialized = true;
        },

        /**
         * @return {void}
         */
        registerEvents: function () {
            $(me.selector.queryTextBox).on('input propertychange', function (event) {
                me.updateHighlight();
            });
        },

        updateHighlight: function () {
            if (me.storage.$queryBox.val() !== me.storage.initQuery) {
                me.storage.$queryBox.attr('dirty', 'true');
                me.storage.$hint.attr('dirty', 'true');
            } else {
                me.storage.$queryBox.attr('dirty', 'false');
                me.storage.$hint.attr('dirty', 'false');
            }
        }

    };

    return {
        init: me.init,
    };

})(jQuery);

$(document).ready(function () {
    if($('#report_edit_form').length === 0) {
        return;
    }
    ReportColumns.init();
    ReportDebugger.init();
    ReportParams.init();
    ChangesHighlight.init();
});
