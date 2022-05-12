var PaymentTransaction = function ($) {
    'use strict';

    var me = {

        storage: {
            pdfinterval: null
        },

        settings: {},
        editReturnOrderSave: function () {
            $('input#save').val('1');
            $.ajax({
                url: 'index.php?module=zahlungsverkehr&action=editUeberweisung&cmd=savereturnorder',
                data: $('#editReturnOrderForm').serialize(),
                method: 'post',
                dataType: 'json',
                success: function (data) {
                    $('#editReturnOrder').dialog('close');
                    if(typeof data.paymentaccountid != 'undefined') {
                        $('#zahlungsverkehr_payment'+data.paymentaccountid).DataTable().ajax.reload();
                    }
                }
            });
        },
        editpaymenttransactionDialog: function (id, type) {
            if(typeof type == 'undefined') {
                type = '1';
            }

            $.ajax({
                url: 'index.php?module=zahlungsverkehr&action=editUeberweisung&cmd=get',
                data: {
                    editid: id, type: type
                },
                method: 'post',
                dataType: 'json',
                success: function (data) {
                    if (typeof data.type != 'undefined' && (data.type === 'returnorder' || data.type === 'liability')) {
                        $('#editReturnOrder div#editReturnOrderContent').html('');
                        $('input#save').val('');
                        $('#payment_transaction_address').val(data.adresse);
                        if (typeof data.html != 'undefined') {
                            $('#editReturnOrder div#editReturnOrderContent').html(data.html);
                            $('#editReturnOrder div#editReturnOrderContent input.datepicker').datepicker(
                                {
                                    dateFormat: 'dd.mm.yy',
                                    dayNamesMin: ['SO', 'MO', 'DI', 'MI', 'DO', 'FR', 'SA'],
                                    firstDay: 1,
                                    showWeek: true,
                                    monthNames: [
                                        'Januar', 'Februar', 'März', 'April', 'Mai',
                                        'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember']
                                }
                            );
                            $('#editReturnOrder div#editReturnOrderContent input.timeicker').timepicker();
                        }
                        if (typeof data.id != 'undefined') {
                            $('#payment_transaction_id').val(data.id);
                        }
                        $('#editReturnOrder').dialog('open');
                        return;
                    }
                    me.editPaymenttransactionReset();
                    // befüllen
                    $('#entryid').val(id);
                    $('#adresse').val(data.adresse);
                    $('#empfaenger').val(data.name);
                    $('#iban').val(data.konto);
                    $('#bic').val(data.blz);
                    $('#betrag').val(data.betrag);
                    $('#waehrung').val(data.waehrung);
                    $('#vz1').val(data.vz1);
                    $('#vz2').val(data.vz2);
                    $('#datumueberweisung').val(data.datum);
                    $('#editUeberweisung').dialog('open');
                }
            });
        },
        pdfpreview: function (el, element) {
            var pos = $(element).position();
            if (me.storage.pdfinterval != null) {
                clearTimeout(me.storage.pdfinterval);
            }
            $.ajax({
                url: 'index.php?module=zahlungsverkehr&action=ueberweisung&cmd=pdfvorschau&aktion=verbindlichkeit&parameter=' +
                    el,
                type: 'POST',
                dataType: 'json',
                data: {},
                success: function (data) {
                    $('#pdfiframe').prop('src', data.src);
                    $('#pdfvorschaudiv').show();
                    $('#pdfvorschaudiv').css('top', pos.top + 25);
                    $('#pdfvorschaudiv').css('left', pos.left > 900 ? pos.left - 900.0 : pos.left);
                },
                beforeSend: function () {

                }
            });
        },

        pdfpreviewreturnorder: function (el, element) {
            var pos = $(element).position();
            if (me.storage.pdfinterval != null) {
                clearTimeout(me.storage.pdfinterval);
            }
            $.ajax({
                url: 'index.php?module=zahlungsverkehr&action=ueberweisung&cmd=pdfvorschaugutschrift&aktion=gutschrift&parameter=' +
                    el,
                type: 'POST',
                dataType: 'json',
                data: {},
                success: function (data) {
                    $('#pdfiframe').prop('src', data.src);
                    $('#pdfvorschaudiv').show();
                    $('#pdfvorschaudiv').css('top', pos.top + 25);
                    $('#pdfvorschaudiv').css('left', pos.left > 900 ? pos.left - 900.0 : pos.left);
                },
                beforeSend: function () {

                }
            });
        },
        pdfleave: function () {
            if (me.storage.pdfinterval != null) {
                clearTimeout(me.storage.pdfinterval);
            }
            me.storage.pdfinterval = setInterval(function () {
                $('#pdfvorschaudiv').hide();

            }, 2000);
        },

        editPaymenttransaction: function (id) {
            $('#editUeberweisungForm').find('#entryid').val(id);
            $('#editUeberweisung').dialog('open');
        },
        editPaymenttransactionSave: function () {
            $.ajax({
                url: 'index.php?module=zahlungsverkehr&action=editUeberweisung&cmd=save',
                data: {
                    editid: $('#entryid').val(),
                    adresse: $('#adresseid').val(),
                    zahlungsempfadr: $('#adresse').val(),
                    datumueberweisung: $('#datumueberweisung').val(),
                    name: $('#empfaenger').val(),
                    konto: $('#iban').val(),
                    blz: $('#bic').val(),
                    betrag: $('#betrag').val(),
                    waehrung: $('#waehrung').val(),
                    vz1: $('#vz1').val(),
                    vz2: $('#vz2').val()
                },
                method: 'post',
                dataType: 'json',
                success: function (data) {
                    me.updateLiveTable();
                    $('#editUeberweisung').dialog('close');
                    me.editUeberweisungReset();
                }
            });
        },
        updateLiveTable: function () {
            $('#ueberweisung').DataTable().ajax.reload();
        },
        editPaymenttransactionReset: function () {
            $('#editUeberweisungForm').find('#entryid').val('');
            $('#editUeberweisungForm').find('#adresseid').val('');
            $('#editUeberweisungForm').find('#adresse').val('');
            $('#editUeberweisungForm').find('#empfaenger').val('');
            $('#editUeberweisungForm').find('#iban').val('');
            $('#editUeberweisungForm').find('#bic').val('');
            $('#editUeberweisungForm').find('#betrag').val('');
            $('#editUeberweisungForm').find('#waehrung').val('');
            $('#editUeberweisungForm').find('#vz1').val('');
            $('#editUeberweisungForm').find('#vz2').val('');
            $('#editUeberweisungForm').find('#datumueberweisung').val('');
            $('#editUeberweisungForm').find('#absender').val('');
            $('#editUeberweisungForm').find('#iban_absender').val('');
        },
        deletePaymenttransactionDialog: function (id, type) {
            type = typeof type == 'undefinded' ? '1' : type;
            if (confirm('Wollen Sie den Eintrag wirklich löschen?')) {
                $.ajax({
                    url: 'index.php?module=zahlungsverkehr&action=editUeberweisung&cmd=delete',
                    data: {
                        editid: id, type: type
                    },
                    method: 'post',
                    dataType: 'json',
                    success: function (data) {
                        me.updateLiveTable();
                        $('#editUeberweisung').dialog('close');
                        me.editPaymenttransactionReset();
                        if(typeof data.accountid != 'undefined') {
                            $('#zahlungsverkehr_payment'+data.accountid).DataTable().ajax.reload();
                        }
                    }
                });
            }
        },
        loadLiabilitiyPaid: function(liabilityId) {
            if(confirm('Wirklich auf bezahlt setzen?')) {
                $.ajax({
                    url: 'index.php?module=zahlungsverkehr&action=payment&cmd=setliabilitypaid',
                    data: {id: liabilityId},
                    method: 'post',
                    dataType: 'json',
                    success: function (data) {
                        if (typeof data.error != 'undefined') {
                            alert(data.error);
                        }
                        if (typeof data.empty) {
                            $('#negativeliabilities').remove();
                        } else {
                            $('#zahlungsverkehr_negativeliability').DataTable().ajax.reload();
                        }
                    }
                });
            }
        },
        loadLiabilities: function(){
            $.ajax({
                url: 'index.php?module=zahlungsverkehr&action=payment&cmd=loadLiabilities',
                data: {},
                method: 'post',
                dataType: 'json',
                success: function (data) {
                    if(typeof data.error != 'undefined') {
                        alert(data.error);
                    }
                    if(typeof data.status != 'undefined' && data.status == 1) {
                        window.location = 'index.php?module=zahlungsverkehr&action=payment';
                    }
                }
            });
        },
        loadReturnOrders: function(){
            $.ajax({
                url: 'index.php?module=zahlungsverkehr&action=payment&cmd=loadReturnorders',
                data: {},
                method: 'post',
                dataType: 'json',
                success: function (data) {
                    if(typeof data.error != 'undefined') {
                        alert(data.error);
                    }
                    if(typeof data.status != 'undefined' && data.status == 1) {
                        window.location = 'index.php?module=zahlungsverkehr&action=payment';
                    }
                }
            });
        },
        openLiabilities: function(){
            $.ajax({
                url: 'index.php?module=zahlungsverkehr&action=payment&cmd=openLiabilities',
                data: {},
                method: 'post',
                dataType: 'json',
                success: function () {
                    $('#loadLiabilityDiv').toggleClass('hidden',false);
                    $('#loadLiabilityDiv').dialog('open');
                    $('#zahlungsverkehr_liability').DataTable().ajax.reload();
                }
            });
        },
        openReturnOrders: function(){
            $.ajax({
                url: 'index.php?module=zahlungsverkehr&action=payment&cmd=openReturnorders',
                data: {},
                method: 'post',
                dataType: 'json',
                success: function () {
                    $('#loadReturnorderDiv').toggleClass('hidden',false);
                    $('#loadReturnorderDiv').dialog('open');
                    $('#zahlungsverkehr_returnorder').DataTable().ajax.reload();
                }
            });
        },
        initPaymentTabs: function () {
            $('input.selectall[data-paymentaccountid]').on('change', function () {
                $('#zahlungsverkehr_payment' + $(this).data('paymentaccountid') + ' :checkbox').prop('checked',
                    $(this).prop('checked'));
            });

            $('input.dopayment[data-paymentaccountid]').on('click', function () {
                var checkedstr = '';
                $('#zahlungsverkehr_payment' + $(this).data('paymentaccountid') + ' :checked').each(function () {
                    checkedstr += ';' + $(this).val();
                });
                if (checkedstr === '') {
                    alert('Bitte Zahlung(en) auswählen');
                } else {
                    $.ajax({
                        url: 'index.php?module=zahlungsverkehr&action=payment&cmd=checkpayments',
                        data: {
                            accountid: $(this).data('paymentaccountid'),
                            ids: checkedstr
                        },
                        method: 'post',
                        dataType: 'json',
                        success: function (data) {
                            if (typeof data.error != 'undefined') {
                                alert(data.error);
                            } else {
                                if (confirm('Soll die Zahlung wirklich übernommen werden?')) {
                                    window.console.log(data);
                                    $.ajax({
                                        url: 'index.php?module=zahlungsverkehr&action=payment&cmd=createpayment',
                                        data: {
                                            accountid: data.accountid,
                                            ids: data.idsstring
                                        },
                                        method: 'post',
                                        dataType: 'json',
                                        success: function (data) {
                                            $('#zahlungsverkehr_payment' + data.accountid).DataTable().ajax.reload();
                                            $('#zahlungsverkehr_payment' + data.accountid + ' :checkbox').prop(
                                                'checked', false);
                                            if (typeof data.error != 'undefined') {
                                                alert(data.error);
                                            }
                                            if (typeof data.file != 'undefined') {
                                                window.location
                                                    = 'index.php?module=zahlungsverkehr&action=payment&cmd=file&id='
                                                    + data.accountid
                                                    + '&file='
                                                    + data.file;
                                            }
                                        }
                                    });
                                }
                            }
                        }
                    });
                }
            });

            $('#tabs table').on('afterreload', function () {
                $('#'+this.id+' img[data-editpaymenttransaction]').on('click', function () {
                    me.editpaymenttransactionDialog($(this).data('editpaymenttransaction'),
                        $(this).data('editpaymenttransactiontype')
                    );
                });
                $('#'+this.id+' [data-pdfpreview]').on('mouseover', function () {
                    me.pdfpreview($(this).data('pdfpreview'), this);
                });
                $('#'+this.id+' [data-pdfpreview]').on('mouseleave', function () {
                    me.pdfleave();
                });
                $('#'+this.id+' [data-pdfpreviewreturnorder]').on('mouseover', function () {
                    me.pdfpreviewreturnorder($(this).data('pdfpreviewreturnorder'), this);
                });
                $('#'+this.id+' [data-pdfpreviewreturnorder]').on('mouseleave', function () {
                    me.pdfleave();
                });
                $('#'+this.id+' [data-deletepaymenttransactionreturnorderid]').on('click', function () {
                    me.deletePaymenttransactionDialog($(this).data('deletepaymenttransactionreturnorderid'), 2);
                });
                $('#'+this.id+' [data-deletepaymenttransactionid]').on('click', function () {
                    me.deletePaymenttransactionDialog($(this).data('deletepaymenttransactionreturnorderid'), 1);
                });
                $('#'+this.id+' img.setpayed').on('click',function(){
                   me.loadLiabilitiyPaid($(this).data('liabilityid'));
                });
            });
        },
        initPayment: function () {
            $('input#adresse').autocomplete({
                source: 'index.php?module=ajax&action=filter&filtername=adresse',
                select: function (event, ui) {

                    var adressid = ui.item.value.split(' ')[0];
                    $.ajax({
                        url: 'index.php?module=zahlungsverkehr&action=adresszahlungsdaten',
                        data: {
                            adressid: adressid
                        },
                        method: 'post',
                        dataType: 'json',
                        success: function (data) {
                            $('#editUeberweisungForm').find('#adresseid').val(data.id);
                            $('#editUeberweisungForm').find('#empfaenger').val(data.name);
                            $('#editUeberweisungForm').find('#iban').val(data.iban);
                            $('#editUeberweisungForm').find('#bic').val(data.swift);
                            $('#editUeberweisungForm').find('#waehrung').val(data.waehrung);
                        }
                    });

                }
            });

            $('input#payment_transaction_address').autocomplete({
                source: 'index.php?module=ajax&action=filter&filtername=adresse'
            });

            $('input#waehrung').autocomplete({
                source: 'index.php?module=ajax&action=filter&filtername=waehrung'
            });
            $('#auswahlalle').on('change', function () {
                var wert = $('#auswahlalle').prop('checked');
                $('#ueberweisung').find(':checkbox').prop('checked', wert);
            });
            $('*[data-editpaymenttransaction]').on('click', function () {
                me.editpaymenttransactionDialog(
                    $(this).data('editpaymenttransaction'),
                    $(this).data('editpaymenttransactiontype')
                );
            });
            $('#ueberweisung').on('afterreload', function () {
                $('#ueberweisung *[data-editpaymenttransaction]').on('click', function () {
                    me.editpaymenttransactionDialog($(this).data('editpaymenttransaction'),
                        $(this).data('editpaymenttransactiontype'));
                });
                $('[data-pdfpreview]').on('mouseover', function () {
                    me.pdfpreview($(this).data('pdfpreview'), this);
                });
                $('[data-pdfpreview]').on('mouseleave', function () {
                    me.pdfleave();
                });
                $('[data-pdfpreviewreturnorder]').on('mouseover', function () {
                    me.pdfpreviewreturnorder($(this).data('pdfpreviewreturnorder'), this);
                });
                $('[data-pdfpreviewreturnorder]').on('mouseleave', function () {
                    me.pdfleave();
                });
                $('[data-deletepaymenttransactionreturnorderid]').on('click', function () {
                    me.deletePaymenttransactionDialog($(this).data('deletepaymenttransactionreturnorderid'), 2);
                });
                $('[data-deletepaymenttransactionid]').on('click', function () {
                    me.deletePaymenttransactionDialog($(this).data('deletepaymenttransactionreturnorderid'), 1);
                });
            });
            $('#editUeberweisung').dialog({
                modal: true,
                bgiframe: true,
                closeOnEscape: false,
                minWidth: 700,
                maxHeight: 800,
                autoOpen: false,
                buttons: {
                    'ABBRECHEN': function () {
                        me.editPaymenttransactionReset();
                        $(this).dialog('close');
                    },
                    'SPEICHERN': function () {
                        me.editPaymenttransactionSave();
                    }
                },
                close: function (event, ui) {
                    me.editPaymenttransactionReset();
                    $(this).dialog('close');
                }
            });
            $('#editReturnOrder').dialog({
                modal: true,
                bgiframe: true,
                closeOnEscape: false,
                minWidth: 700,
                maxHeight: 800,
                autoOpen: false,
                buttons: {
                    'ABBRECHEN': function () {
                        $(this).dialog('close');
                    },
                    'SPEICHERN': function () {
                        me.editReturnOrderSave();
                    }
                },
                close: function (event, ui) {
                    $(this).dialog('close');
                }
            });
            $('#loadReturnorderDiv').dialog({
                modal: true,
                bgiframe: true,
                closeOnEscape: false,
                minWidth: 900,
                maxHeight: 800,
                autoOpen: false,
                buttons: {
                    'ABBRECHEN': function () {
                        $(this).dialog('close');
                    },
                    'LADEN': function () {
                        me.loadReturnOrders();
                    }
                },
                close: function (event, ui) {
                    $(this).dialog('close');
                }
            });
            $('#loadLiabilityDiv').dialog({
                modal: true,
                bgiframe: true,
                closeOnEscape: false,
                minWidth: 900,
                maxHeight: 800,
                autoOpen: false,
                buttons: {
                    'ABBRECHEN': function () {
                        $(this).dialog('close');
                    },
                    'LADEN': function () {
                        me.loadLiabilities();
                    }
                },
                close: function (event, ui) {
                    $(this).dialog('close');
                }
            });

            $('#loadReturnorderBtn').on('click' ,function(){
                me.openReturnOrders();
            });

            $('#loadLiabilityBtn').on('click' ,function(){
                me.openLiabilities();
            });

            $('#pdfclosebutton').on('click', function () {
                if (me.storage.pdfinterval != null) {
                    clearTimeout(me.storage.pdfinterval);
                }
                $('#pdfvorschaudiv').hide();
            });
            $('#pdfvorschaudiv').on('mouseover', function () {
                if (me.storage.pdfinterval != null) {
                    clearTimeout(me.storage.pdfinterval);
                }
            });
            $('#pdfvorschaudiv').on('mouseleave', function () {
                if (pdfinterval != null) {
                    clearTimeout(me.storage.pdfinterval);
                }
                me.storage.pdfinterval = setInterval(function () {
                    $('#pdfvorschaudiv').hide();

                }, 1000);
            });
            $('#zahlungsverkehr_returnorder').on('afterreload',function(){
                $(this).find('input.select').on('change',function(){
                    $.ajax({
                        url: 'index.php?module=zahlungsverkehr&action=payment&cmd=changeReturnorderSelection',
                        data: {
                            id:$(this).data('id'),
                            value: ($(this).prop('checked')?1:0)
                        },
                        method: 'post',
                        dataType: 'json',
                        success: function (data) {

                        }
                    });
                });
            });
            $('#zahlungsverkehr_liability').on('afterreload',function(){
                $(this).find('input.select').on('change',function(){
                    $.ajax({
                        url: 'index.php?module=zahlungsverkehr&action=payment&cmd=changeLiabilitySelection',
                        data: {
                            id:$(this).data('id'),
                            value: ($(this).prop('checked')?1:0)
                        },
                        method: 'post',
                        dataType: 'json',
                        success: function (data) {

                        }
                    });
                });
            });
        },
        init: function () {
            if ($('#editReturnOrder').length) {
                me.initPayment();
            }
            if ($('input.selectall[data-paymentaccountid]').length) {
                me.initPaymentTabs();
            }
        }
    };

    return {
        init: me.init
    };

}(jQuery);

$(document).ready(function () {
    PaymentTransaction.init();
});
