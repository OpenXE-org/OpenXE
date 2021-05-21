var ShopImporterAmazon = function ($) {
    'use strict';

    var me = {
        storage: {
            shopId: null,
            invoiceUploadIds: null
        },
        init: function() {
            $('#resetinvoices').on('click', function() {
                me.storage.invoiceUploadIds = [];
                $('#shopimporter_amazon_invoice_upload').find(':checked').each(function(){
                    me.storage.invoiceUploadIds.push($(this).data('id'));
                });
                if(me.storage.invoiceUploadIds.length > 0) {
                    $.ajax({
                        url: 'index.php?module=onlineshops&action=edit&id='+
                            $('#resetinvoices').data('shopid') +'&cmd=resetinvoiceuploads',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            invoiceUploadIds: me.storage.invoiceUploadIds,
                        },
                        success: function() {
                            $('#shopimporter_amazon_invoice_upload').DataTable().ajax.reload();
                        }
                    });
                }
            });
            $('#sellectallinvoices').on('change', function(){
                $('#shopimporter_amazon_invoice_upload').find('input').prop('checked', $(this).prop('checked'));
            });
        }
    };

    return {
        init: me.init
    };

}(jQuery);

$(document).ready(function () {
    ShopImporterAmazon.init();
});
