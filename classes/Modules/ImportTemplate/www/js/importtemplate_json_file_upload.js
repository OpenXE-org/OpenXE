var ImportTemplateJsonFileUpload = (function ($) {
    'use strict';

    var me = {
        isInitialized: false,

        selector: {
            uploadDialog: '#jsonUploadDialog',
            editUploadDialog: '#jsonEditUploadDialog',
            jsonfile: '#jsonfile'
        },

        storage: {
            $dialog: null
        },

        init: function () {

            if (me.isInitialized === true) {
                return;
            }

            me.storage.$dialog = $(me.selector.editUploadDialog);

            me.dialogInit();
            me.registerEvents();
            me.isInitialized = true;
        },

        registerEvents: function () {

            $(me.selector.uploadDialog).on('click', function (event) {
                event.preventDefault();
                me.dialogOpen();
            });
        },

        dialogInit: function () {
            me.storage.$dialog.dialog({
                modal: true,
                bgiframe: true,
                closeOnEscape: false,
                minWidth: 500,
                minHeight: 110,
                maxHeight: 200,
                autoOpen: false,

                open: function () {
                    $(me.selector.inputKey).trigger('focus');
                },
                close: function () {
                    me.dialogReset();
                }
            });
        },

        dialogOpen: function () {
            me.dialogReset();
            me.storage.$dialog.dialog('open');
        },

        dialogClose: function () {
            me.storage.$dialog.dialog('close');
        },

        dialogReset: function () {
            me.storage.$dialog.find(me.selector.jsonfile).val(null);
        }
    };

    return {
        init: me.init
    };

})(jQuery);

$(document).ready(function () {
    ImportTemplateJsonFileUpload.init();
});