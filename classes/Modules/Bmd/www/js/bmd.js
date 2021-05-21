var Bmd = (function ($) {
    'use strict';

    var me = {

        isInitialized: false,

        selector: {
            ledgerNewDialog: '#ledgernewdialog',
            ledgerEditDialog: '.ledgereditdialog',
            ledgerDeleteDialog: '.ledgerdeletedialog',
            bmdledgerNewEdit: '#bmdledgernewedit',
            bmdledgerDelete: '#bmdledgerdelete',
            revenueledger: '#revenueledger',
            ledgerLabel: '#label',
            taxcode: '#taxcode',
            salestaxpercent: '#salestaxpercent',
            revenueledgerdeleteid: '#revenueledgerdeleteid',
            revenueledgereditid: '#revenueledgereditid',
            ledgermsg:'#ledgermsg',
            ledgeroverviewTable:'#bmdledgeroverview'
        },

        storage: {
            $dialog: null,
            $deleteDialog:null,
        },

        init: function () {

            if (me.isInitialized === true) {
                return;
            }

            me.storage.$dialog = $(me.selector.bmdledgerNewEdit);
            me.storage.$deleteDialog = $(me.selector.bmdledgerDelete);
            me.dialogInit();
            me.deleteDialogInit();
            me.registerEvents();

            me.isInitialized = true;
        },

        registerEvents: function () {

            $(me.selector.ledgerNewDialog).on('click', function (event) {
                event.preventDefault();
                me.dialogNewOpen();
            });

            $(me.selector.ledgeroverviewTable).on('click', me.selector.ledgerEditDialog, function (event) {
                event.preventDefault();
                me.dialogEditOpen(this.id.replace('lee-',''));
            });

            $(me.selector.ledgeroverviewTable).on('click', me.selector.ledgerDeleteDialog, function (event) {
                event.preventDefault();
                me.dialogDeleteOpen(this.id.replace('led-',''));
            });
        },

        deleteDialogInit: function () {
            me.storage.$deleteDialog.dialog({
                modal: true,
                bgiframe: true,
                closeOnEscape: false,
                minWidth: 500,
                minHeight: 200,
                maxHeight: 500,
                autoOpen: false,

                open: function () {},
                close: function () {}
            });
        },

        dialogInit: function () {
            me.storage.$dialog.dialog({
                modal: true,
                bgiframe: true,
                closeOnEscape: false,
                minWidth: 600,
                minHeight: 200,
                maxHeight: 500,
                autoOpen: false,

                open: function () {
                    $(me.selector.inputKey).trigger('focus');
                },
                close: function () {
                    me.dialogReset();
                }
            });
        },

        dialogNewOpen: function () {
            me.dialogReset();
            me.storage.$dialog.dialog('open');
        },

        dialogEditOpen: function (id) {
            me.dialogReset();

            $.ajax({
                url: 'index.php?module=bmd&action=erloes&cmd=revenueledgerdata',
                type: 'POST',
                dataType: 'json',
                data: {
                    id: id
                },
                success: function(data) {

                    if(data.error){
                        me.storage.$dialog.find(me.selector.ledgermsg).text(data.error);
                    }
                    else{
                        me.storage.$dialog.find(me.selector.revenueledgereditid).val(data.id);
                        me.storage.$dialog.find(me.selector.revenueledger).val(data.revenueledger);
                        me.storage.$dialog.find(me.selector.ledgerLabel).val(data.label);
                        me.storage.$dialog.find(me.selector.taxcode).val(data.taxcode);
                        me.storage.$dialog.find(me.selector.salestaxpercent).val(data.salestaxpercent);
                        me.storage.$dialog.dialog('open');
                    }
                },
                beforeSend: function() {}
            });
        },

        dialogDeleteOpen: function (id) {
            me.storage.$deleteDialog.find(me.selector.revenueledgerdeleteid).val(id);
            me.storage.$deleteDialog.dialog('open');
        },

        dialogClose: function () {
            me.storage.$dialog.dialog('close');
        },

        dialogReset: function () {
            me.storage.$dialog.find(me.selector.revenueledgereditid).val(null);
            me.storage.$dialog.find(me.selector.revenueledger).val(null);
            me.storage.$dialog.find(me.selector.ledgerLabel).val(null);
            me.storage.$dialog.find(me.selector.taxcode).val(null);
            me.storage.$dialog.find(me.selector.salestaxpercent).val(null);
        }
    };

    return {
        init: me.init
    };

})(jQuery);

$(document).ready(function () {
    Bmd.init();
});
