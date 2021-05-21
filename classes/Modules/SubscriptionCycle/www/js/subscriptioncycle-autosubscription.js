var SubscriptionCycleAutoSubscription = (function ($) {
    'use strict';

    var me = {

        isInitialized: false,

        selector: {
            newDialog: '#autosubscriptionnewdialog',
            editDialog: '.autosubscriptioneditdialog',
            deleteDialog: '.autosubscriptiondeletedialog',
            newEdit: '#autosubscriptionnewedit',
            delete: '#autosubscriptiondelete',
            articleInput: '#article',
            projectInput: '#project',
            pricecycleSelect: '#pricecycle',
            documenttypeSelect: '#documenttype',
            subscriptiongroupSelect: '#subscriptiongroup',
            positionInput: '#position',
            firstdatetypeSelect: '#firstdatetype',
            preventautodispatchCheck: '#preventautodispatch',
            autoemailconfirmationCheck: '#autoemailconfirmation',
            businessletterpatternInput: '#businessletterpattern',
            businessletterrow: '.businessletter',
            addpdf: '#addpdf',
            deleteid: '#autosubscriptiondeleteid',
            editid: '#autosubscriptioneditid',
            msg: '#autosubscriptionmsg',
            overviewTable: '#rechnungslaufautoabo',
            autosubscriptionform: '#autosubscriptionform'
        },

        storage: {
            $dialog: null,
            $deleteDialog: null
        },

        init: function () {
            if (me.isInitialized === true) {
                return;
            }

            me.storage.$dialog = $(me.selector.newEdit);
            me.storage.$deleteDialog = $(me.selector.delete);
            me.dialogInit();
            me.deleteDialogInit();
            me.registerEvents();

            me.isInitialized = true;
        },

        registerEvents: function () {
            $(me.selector.newDialog).on('click', function (event) {
                event.preventDefault();
                me.dialogNewOpen();
            });

            $(me.selector.overviewTable).on('click', me.selector.editDialog,
                function (event) {
                    event.preventDefault();
                    me.dialogEditOpen(this.id.replace('aae-', ''));
                });

            $(me.selector.overviewTable).on('click', me.selector.deleteDialog,
                function (event) {
                    event.preventDefault();
                    me.dialogDeleteOpen(this.id.replace('aad-', ''));
                });

            $(me.selector.autoemailconfirmationCheck).on('click', function () {
                if (me.storage.$dialog.find(me.selector.autoemailconfirmationCheck).prop('checked')) {
                    me.storage.$dialog.find(me.selector.businessletterrow).show();
                } else {
                    me.storage.$dialog.find(me.selector.businessletterrow).hide();
                }
            });
        },

        deleteDialogInit: function () {
            me.storage.$deleteDialog.dialog({
                modal: true,
                bgiframe: true,
                closeOnEscape: false,
                minWidth: 500,
                minHeight: 110,
                maxHeight: 200,
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
                minWidth: 650,
                minHeight: 450,
                maxHeight: 500,
                autoOpen: false,
                open: function () {
                    $(me.selector.inputKey).trigger('focus');
                },
                close: function () {
                    me.dialogReset();
                },
                buttons:{
                    ABBRECHEN: function() {
                        me.dialogClose();
                    },
                    SPEICHERN: function() {
                        $(me.selector.autosubscriptionform).submit();
                    }
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
                url: 'index.php?module=rechnungslauf&action=autoabo&cmd=editdata',
                type: 'POST',
                dataType: 'json',
                data: {
                    id: id
                },
                success: function (data) {

                    if (data.error) {
                        me.storage.$dialog.find(me.selector.msg).text(data.error);
                    } else {
                        me.storage.$dialog.find(me.selector.editid).val(data.id);
                        me.storage.$dialog.find(me.selector.articleInput).val(data.article_name);
                        me.storage.$dialog.find(me.selector.projectInput).val(data.project_name);
                        me.storage.$dialog.find(me.selector.pricecycleSelect).val(data.price_cycle);
                        me.storage.$dialog.find(me.selector.documenttypeSelect).val(data.document_type);
                        me.storage.$dialog.find(me.selector.subscriptiongroupSelect).val(
                            data.subscription_group_id);
                        me.storage.$dialog.find(me.selector.positionInput).val(
                            data.position == 0 ? '' : data.position);
                        me.storage.$dialog.find(me.selector.firstdatetypeSelect).val(data.first_date_type);
                        me.storage.$dialog.find(me.selector.preventautodispatchCheck).prop('checked',
                            data.prevent_auto_dispatch);
                        me.storage.$dialog.find(me.selector.autoemailconfirmationCheck).prop('checked',
                            data.auto_email_confirmation);
                        me.storage.$dialog.find(me.selector.businessletterpatternInput).val(
                            data.business_letter_pattern_id);
                        me.storage.$dialog.find(me.selector.addpdf).prop('checked', data.add_pdf);

                        if(data.auto_email_confirmation){
                            me.storage.$dialog.find(me.selector.businessletterrow).show();
                        }

                        me.storage.$dialog.dialog('open');
                    }
                },
                beforeSend: function () {}
            });
        },

        dialogDeleteOpen: function (id) {
            me.storage.$deleteDialog.find(me.selector.deleteid).val(id);
            me.storage.$deleteDialog.dialog('open');
        },

        dialogClose: function () {
            me.storage.$dialog.dialog('close');
        },

        dialogReset: function () {
            me.storage.$dialog.find(me.selector.editid).val(null);
            me.storage.$dialog.find(me.selector.articleInput).val(null);
            me.storage.$dialog.find(me.selector.projectInput).val(null);
            me.storage.$dialog.find(me.selector.pricecycleSelect).val('monatspreis');
            me.storage.$dialog.find(me.selector.documenttypeSelect).val('auftrag');
            me.storage.$dialog.find(me.selector.subscriptiongroupSelect).val(0);
            me.storage.$dialog.find(me.selector.positionInput).val('');
            me.storage.$dialog.find(me.selector.firstdatetypeSelect).val('auftragsdatum');
            me.storage.$dialog.find(me.selector.preventautodispatchCheck).prop('checked', false);
            me.storage.$dialog.find(me.selector.autoemailconfirmationCheck).prop('checked', false);
            me.storage.$dialog.find(me.selector.businessletterpatternInput).val('');
            me.storage.$dialog.find(me.selector.addpdf).prop('checked', false);
            me.storage.$dialog.find(me.selector.businessletterrow).hide();
        }
    };

    return {
        init: me.init
    };

})(jQuery);

$(document).ready(function () {
    SubscriptionCycleAutoSubscription.init();
});
