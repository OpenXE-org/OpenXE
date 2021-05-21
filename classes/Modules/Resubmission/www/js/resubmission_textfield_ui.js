/**
 * Für die Bedienung der Freifelder innerhalb des Wiedervorlagen-Popups
 */
var ResubmissionTextFieldUi = (function ($) {
    'use strict';

    var me = {

        storage: {
            $content: null,
            $errors: null
        },

        /**
         * @param {Array} textfields
         *
         * @return void
         */
        init: function (textfields) {
            var $content = $('#resubmission-textfields-content');
            if ($content.length !== 1) {
                console.error('ResubmissionTextFieldUi: Benötigtes Element #resubmission-textfields-content fehlt');
                return;
            }
            me.storage.$content = $content;

            var $errors = $('#resubmission-textfields-errors');
            if ($errors.length !== 1) {
                console.error('ResubmissionTextFieldUi: Benötigtes Element #resubmission-textfields-errors fehlt');
                return;
            }
            me.storage.$errors = $errors;

            me.buildTextFieldForm(textfields);
        },

        /**
         * @param {Array} textfields
         */
        buildTextFieldForm: function (textfields) {
            me.storage.$content.html('');
            me.storage.$errors.html('');

            if (textfields.length === 0) {
                var content = '<div class="info">Es sind keine Freifelder für diese Stage konfiguriert. ';
                content += '<a href="index.php?module=wiedervorlage&action=settings#tabs-4">Zu den Einstellungen</a>';
                content += '</div>';
                me.storage.$errors.html(content);
            }

            if (textfields.length > 0) {
                var $table = $('<table class="mkTableFormular textfield-table" width="100%">');
                $.each(textfields, function (index, textfield) {
                    var $row = me.generateTextFieldRow(textfield);
                    $row.appendTo($table);
                });
                me.storage.$content.html($table);
            }
        },

        /**
         * @param {Object} data
         *
         * @return {jQuery} jQuery-Element
         */
        generateTextFieldRow: function (data) {
            if (!data.hasOwnProperty('config_id') || !data.hasOwnProperty('label')) {
                throw 'ResubmissionTextFieldUi: Can not generate textfield html. Data has wrong format.';
            }

            var template =
                '<tr><td width="34%">' +
                '<label class="resubmission-textfield-label" for="resubmission-textfield-{{configId}}"></label>' +
                '</td><td width="66%">' +
                '<input type="text" name="textfield[{{configId}}]" class="resubmission-textfield-content" ' +
                'id="resubmission-textfield-{{configId}}">' +
                '</td></tr>';
            template = template.replace('{{configId}}', data.config_id);
            template = template.replace('{{configId}}', data.config_id);
            template = template.replace('{{configId}}', data.content_id);

            var $template = $(template);
            $template.find('label.resubmission-textfield-label').text(data.label);
            $template
                .find('input.resubmission-textfield-content')
                .data('resubmissionTextfieldConfigId', data.config_id)
                .data('resubmissionTextfieldContentId', data.content_id)
                .val(data.content);

            return $template;
        },

        /**
         * @param {Array} errors
         */
        renderErrorMessages: function (errors) {
            var errorMsg = '';

            $.each(errors, function (index, textfieldError) {
                errorMsg += '<p>' + textfieldError.message + '</p>';
            });

            me.storage.$errors.html('<div class="error">' + errorMsg + '</div>');
        }
    };

    return {
        init: me.init,
        renderErrorMessages: me.renderErrorMessages
    };

})(jQuery);
