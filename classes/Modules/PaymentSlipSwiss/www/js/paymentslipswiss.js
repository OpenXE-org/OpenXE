var PaymentSlipSwiss = (function ($) {

    var me = {

        storage: {},
        
        init: function () {
            me.storage.$table = $('table#paymentslip_swiss').DataTable();
            me.storage.$editFieldset = $('#address-edit-fieldset');
            me.storage.$editFieldset.hide();

            $(document).on('click', '.edit-address-button', function (e) {
                e.preventDefault();
                var $button = $(this);
                var addressId = $button.data('address');
                me.editAddress(addressId);
            });

            $(document).on('click', '#save-address-button', function (e) {
                e.preventDefault();
                me.saveAddress();
            });
        },

        editAddress: function (addressId) {
            me.storage.$editFieldset.show();
            $.ajax({
                url: 'index.php?module=paymentslip_swiss&action=list&cmd=ajax-edit',
                type: 'POST',
                dataType: 'json',
                data: {
                    address: addressId
                },
                success: function (data) {
                    $('#customer-address').val(data.address);
                    $('#customer-esr-number').val(data.customer_esr_number);
                }
            });
        },

        saveAddress: function () {
            var addressId = $('#customer-address').val();
            var customerEsrNumber = $('#customer-esr-number').val();

            $.ajax({
                url: 'index.php?module=paymentslip_swiss&action=list&cmd=ajax-save',
                type: 'POST',
                dataType: 'json',
                data: {
                    address: addressId,
                    customerEsrNumber: customerEsrNumber
                },
                success: function () {
                    me.storage.$editFieldset.hide();
                    me.reloadDataTable();
                },
                error: function (jqXHR) {
                    alert('Hoppla. Beim Speichern ist etwas schief gelaufen: ' + jqXHR.responseJSON.error);
                }
            });

        },

        reloadDataTable: function () {
            me.storage.$table.draw();
        }
    };
    
    return {
        init: me.init
    }
    
})(jQuery);

$(document).ready(PaymentSlipSwiss.init);
