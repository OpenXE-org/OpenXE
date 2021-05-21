var Datanorm = (function ($) {
    'use strict';

    var me = {
        isInitialized: false,

        selector: {
            datanormIntermediate: '#datanorm_intermediate',
            datanormEdit: '#datanorm-edit',
            datanormVidHidden: '#datanorm-edit-vid',
            datanormMsg: '#datanorm-msg',
            datanormForm: '#datanorm-form',
            datanormEditDialog: '.datanorm-edit-dialog',
            supplierInput: '#datanorm-supplier'
        },

        storage: {
            $dialog: null
        },

        init: function () {
            if (me.isInitialized === true) {
                return;
            }
            $('#chunkyfile').chunkedUpload({
                upload: {
                    url: 'index.php?module=datanorm&action=list&cmd=upload',
                    view: 'sidebar'
                }
            });
            me.storage.$dialog = $(me.selector.datanormEdit);
            me.dialogInit();
            me.registerEvents();

            me.isInitialized = true;
        },

        registerEvents: function () {

            $(me.selector.datanormIntermediate).on('click', me.selector.datanormEditDialog, function (event) {
                event.preventDefault();
                me.dialogOpen(this.id.replace('dn-', ''));
            });

        },

        dialogInit: function () {
            me.storage.$dialog.dialog({
                modal: true,
                bgiframe: true,
                closeOnEscape: false,
                minWidth: 650,
                minHeight: 250,
                autoOpen: false,
                open: function () {},
                close: function () {
                    me.dialogReset();
                },
                buttons: [
                    {
                        id: 'button-ok',
                        text: 'SPEICHERN',
                        click: function () {
                            $(me.selector.datanormForm).submit();
                        }
                    }
                ]
            });
        },

        dialogOpen: function (id) {

            me.dialogReset();
            me.storage.$dialog.find(me.selector.datanormVidHidden).val(id);

            $.ajax({
                url: 'index.php?module=datanorm&action=list&cmd=settings',
                type: 'POST',
                dataType: 'json',
                data: {
                    vid: id
                },
                success: function (data) {

                    if (data.error) {
                        me.storage.$dialog.find(me.selector.datanormMsg).text(data.error);
                    } else {
                        me.storage.$dialog.find(me.selector.supplierInput).val(data.supplier_number);
                        me.storage.$dialog.dialog('open');
                    }
                },
                beforeSend: function () {}
            });
        },

        dialogClose: function () {
            me.storage.$dialog.dialog('close');
        },

        dialogReset: function () {
            me.storage.$dialog.find(me.selector.datanormVidHidden).val(null);

        }
    };

    return {
        init: me.init
    };

})(jQuery);

$(document).ready(function () {
    Datanorm.init();
});
