


/**
 * Add/Edit/Remove columns
 */
var ReportParameterInputDialog = (function ($) {
    'use strict';

    var me = {
        isInitialized: false,

        template: {
            dialog: '<div id="{dialog_id}" style="display:none;" title="{dialog_title}">\n' +
                '\t<form method="post">\n' +
                '\t\t<div>\n' +
                '\t\t\t<fieldset>\n' +
                '\t\t\t\t<table class="input-control-pane" width="100%">\n' +
                '\t\t\t\t</table>\n' +
                '\t\t\t</fieldset>\n' +
                '\t\t</div>\n' +
                '\t</form>\n' +
                '</div>',

            inputField:'<tr>' +
                '<td><label for="{id}">{label}:</label></td>\n' +
                '<td><input type="text" name="{varname}" id="{id}" width="80%"></td>\n' +
                '</tr>',

            selectField:'<tr>' +
                '<td><label for="{id}">{label}:</label></td>\n' +
                '<td><select type="text" name="{varname}" id="{id}" width="80%">' +
                '{options}' +
                '</select></td>\n' +
                '</tr>',
            selectOption: '<option value="{optvalue}">{optdisplay}</option>'
        },

        selector: {
            dialog: '',
            inputPanel: '',
        },

        url: {
            ajaxGetInputParams: 'index.php?module=report&action=list&cmd=ajaxGetInputParameters',
            ajaxDownload: 'index.php?module=report&action=download',
        },

        storage: {
            $dialog: null,
            $fieldSet: null,
            formId: 0,
            fields: {},
            submitButtonText: ''
        },

        createDialog: function (prefix, onClose = me.onClose, title = 'Parameter', submitButtonText = 'OK') {

            var dialogId = prefix + '-parameter-input-dialog';
            me.selector.dialog = '#' + dialogId;

            if ($(me.selector.dialog).length === 0) {
                var htmldialog = (me.template.dialog + '')
                    .replace('{dialog_id}', dialogId)
                    .replace('{dialog_title}', title);
                $('body').append(htmldialog);
            }

            if (typeof onClose !== 'function') {
                throw 'function onClose required for parameter dialog';
            }
            me.onClose = onClose;

            me.selector.inputPanel = me.selector.dialog + ' .input-control-pane';
            me.storage.submitButtonText = submitButtonText;

            if ($(me.selector.dialog).length === 0) {
                throw 'Dialog markup does not exitst';
            }
            me.storage.$dialog = $(me.selector.dialog);
            me.storage.$fieldSet = $(me.selector.inputPanel);
            me.dialogInit(submitButtonText);
        },

        /**
         * @return {void}
         */
        dialogInit: function (submitButtonText) {
            me.storage.$dialog.dialog({
                modal: true,
                bgiframe: true,
                closeOnEscape: false,
                minWidth: 800,
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
                        text: submitButtonText,
                        click: function () {
                            me.dialogCommit();
                        }
                    }],
                open: function () {
                    //$(me.selector.inputKey).trigger('focus');
                },
                close: function () {
                    //me.dialogReset();
                }
            });
        },

        /**
         * @param {number} id data-id of the Column to be edited
         *
         * @param {string} prefix
         * @param {function} onClose
         * @param {string} title
         * @param {string} submitButtonText
         *
         * @return {void}
         */
        open: function (id = 0, prefix, onClose = me.onClose,  title = 'Parameter', submitButtonText = 'OK') {
            me.createDialog(prefix, onClose, title, submitButtonText);
            me.storage.formId = id;
            $.ajax({
                url: me.url.ajaxGetInputParams,
                data: {
                    id: id
                },
                method: 'post',
                dataType: 'json',
                beforeSend: function () {
                    App.loading.open();
                },
                success: function (data) {
                    me.dialogInitFields(data.params);
                    if (me.storage.fields === null || $.isEmptyObject(me.storage.fields)) {
                        me.onClose(null, 'skip');
                    } else {
                        me.storage.$dialog.dialog('open');
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

        /**
         * @param {array} params
         */
        dialogInitFields: function (params) {
            me.dialogReset();
            params.forEach(me.addInputField);
        },

        /**
         * @param {Object} item
         * @param {int}    index
         * @param {array}  array
         */
        addInputField: function (item, index, array) {
            if (item.editable !== true) {
                return;
            }
            var fieldname = item.varname;
            var guiId = 'inputParam' + item.varname;
            var label = item.displayname;
            if (label === null || label === '') {
                label = item.varname.toString().toUpperCase();
            }
            me.storage.fields[fieldname] = guiId;
            var htmlElement = '';
            if (item.options.length > 0 && item.control_type === 'combobox') {
                var htmlOptions = '';
                for ( var i = 0; i < item.options.length; i++ ) {
                    var key = Object.keys(item.options[i])[0];
                    var val = item.options[i][key];

                    var opt = (me.template.selectOption + '')
                        .replace('{optvalue}', val)
                        .replace('{optdisplay}', key);
                    htmlOptions += opt;
                }

                htmlElement = (me.template.selectField + '')
                    .replace(new RegExp('\\{id\\}', 'g'), guiId)
                    .replace('{varname}', item.varname)
                    .replace('{label}', label)
                    .replace('{options}', htmlOptions);
                 me.storage.$fieldSet.append(htmlElement);
            }

            if (item.control_type === '' || item.control_type === 'text') {
                htmlElement = (me.template.inputField + '')
                    .replace(new RegExp('\\{id\\}', 'g'), guiId)
                    .replace('{varname}', item.varname)
                    .replace('{label}', label);
                me.storage.$fieldSet.append(htmlElement);
            }

            if (item.control_type === 'date') {
                htmlElement = (me.template.inputField + '')
                    .replace(new RegExp('\\{id\\}', 'g'), guiId)
                    .replace('{varname}', item.varname)
                    .replace('{label}', label);
                me.storage.$fieldSet.append(htmlElement);
                $('#' + guiId).datepicker({
                    dateFormat: 'yy-mm-dd',
                    dayNamesMin: ['SO', 'MO', 'DI', 'MI', 'DO', 'FR', 'SA'],
                    firstDay:1,
                    showWeek: true,
                    monthNames: ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober',  'November', 'Dezember']
                });
            }

            if (item.control_type === 'autocomplete_project') {
                me.createAutoComplete(fieldname, label, guiId, "index.php?module=ajax&action=filter&filtername=projektname");
            }

            if (item.control_type === 'autocomplete_group') {
                me.createAutoComplete(fieldname, label, guiId, "index.php?module=ajax&action=filter&filtername=gruppe");
            }

            if (item.control_type === 'autocomplete_address') {
                me.createAutoComplete(fieldname, label, guiId, "index.php?module=ajax&action=filter&filtername=adresse");
            }

            if (item.control_type === 'autocomplete_article') {
                me.createAutoComplete(fieldname, label, guiId, "index.php?module=ajax&action=filter&filtername=artikelnummer");
            }
        },

        /**
         * @param {string} fieldname
         * @param {string} label
         * @param {string} guiId
         * @param {string} source
         */
        createAutoComplete: function (fieldname, label, guiId, source) {
            var htmlAuto = (me.template.inputField + '')
                .replace(new RegExp('\\{id\\}', 'g'), guiId)
                .replace('{varname}', fieldname)
                .replace('{label}', label);
            me.storage.$fieldSet.append(htmlAuto);

            var $element = $('#' + guiId);
            $element.autocomplete({
                source: source,
                select: function (event, ui) {
                    var i = $element.val() + ui.item.value;
                    var zahl = i.indexOf(",");

                    var text = i.slice(0, zahl);
                    if (zahl <= 0)
                        $element.val(ui.item.value);
                    else {
                        var j = $element.val();
                        var zahlletzte = j.lastIndexOf(",");
                        var text2 = j.substring(0, zahlletzte);

                        $element.val(text2 + "," + ui.item.value);
                    }
                    return false;
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
            me.storage.fields = {};
            me.storage.$fieldSet.empty();
        },

        /**
         * @return {void}
         */
        dialogCommit: function (command = 'commit') {
            me.onClose(me.getDialogData(), command);
            me.dialogClose();
        },

        onClose: function (data, command) {
            console.log('no action defined');
        },

        /**
         * @return {Object}
         */
        getDialogData: function() {
            var data =  {
                id: me.storage.formId,
            };
            for(var field in me.storage.fields) {
                var input = $('#' + me.storage.fields[field]).val();
                if(input !== null && input !== '') {
                    data[field] = input;
                }
            }

            return data;
        },
    };
    return {
        open: me.open,
        onClose: me.onClose
    };

})(jQuery);
