
/**
 * Für die Bedienung der Modul-Oberfläche
 */
var VoucherPosUi = (function ($) {
    'use strict';

    var me = {

        isInitialized: false,

        storage: {
            $voucherValueInput: null,
            $voucherCustomerType: null,
            $voucherCustomerInput: null,
            $voucherCode: null,
            $voucherResidualValue: null,
            $voucherRedeemValue: null
        },

        selector: {
            voucherValueTextBox: '#voucherpos_value',
            voucherCustomerRadio: 'input[name="voucherpos_customer"]:checked',
            voucherCustomerTextBox: '#voucherpos_regular_customer_input',
            voucherCodeTextBox: '#voucherpos_code',
            voucherResidualValueTextBox: '#voucherpos_residual_value',
            voucherRedeemValueTextBox: '#voucherpos_redeem_value'
        },

        /**
         * @return void
         */
        init: function () {
            if (me.isInitialized === true) {
                return;
            }

            me.storage.$voucherValueInput = $(me.selector.voucherValueTextBox);
            me.storage.$voucherCustomerType = $(me.selector.voucherCustomerRadio);
            me.storage.$voucherCustomerInput = $(me.selector.voucherCustomerTextBox);
            me.storage.$voucherCode = $(me.selector.voucherCodeTextBox);
            me.storage.$voucherResidualValue = $(me.selector.voucherResidualValueTextBox);
            me.storage.$voucherRedeemValue = $(me.selector.voucherRedeemValueTextBox);

            me.registerEvents();
            me.isInitialized = true;
            me.storage.$voucherValueInput.focus();
            me.storage.$voucherCode.focus();
        },

        /**
         * @return {void}
         */
        registerEvents: function () {
            //Erstelle Gutschein
            $(document).on('click', '.voucherpos-create', function (e) {
                e.preventDefault();
                var voucherPosValue = me.storage.$voucherValueInput.val();
                var voucherPosCustomerType = $(me.selector.voucherCustomerRadio).val();
                var voucherPosCustomerInput = me.storage.$voucherCustomerInput.val();
                me.createVoucher(voucherPosValue, voucherPosCustomerType, voucherPosCustomerInput, false);
            });

            $(document).on('click', '.voucherpos-print', function (e) {
                e.preventDefault();
                var voucherPosValue = me.storage.$voucherValueInput.val();
                var voucherPosCustomerType = $(me.selector.voucherCustomerRadio).val();
                var voucherPosCustomerInput = me.storage.$voucherCustomerInput.val();
                me.createVoucher(voucherPosValue, voucherPosCustomerType, voucherPosCustomerInput, true);
            });

            $(document).on('click', '.voucherpos-cancel', function (e) {
                e.preventDefault();
                me.resetCreateVoucherFields();
                window.location.replace("index.php?module=appstore&action=list");
            });

            $(document).on('click', '.voucherpos-book', function (e) {
               e.preventDefault();
               var voucherPosResidualValue = me.storage.$voucherResidualValue.val();
               var voucherPosRedeemValue = me.storage.$voucherRedeemValue.val();
               var voucherCode = $('#voucherpos_redeem_code').data('code');
               me.redeemVoucher(voucherPosResidualValue, voucherPosRedeemValue, voucherCode);
            });
        },


        /**
         * @param {number} voucherPosValue
         * @param {string} voucherPosCustomerType
         * @param {string} voucherPosCustomerInput
         * @param {boolean} voucherPosPrint
         *
         * @return {void}
         */
        createVoucher: function (voucherPosValue, voucherPosCustomerType, voucherPosCustomerInput, voucherPosPrint) {
            $.ajax({
                url: 'index.php?module=voucherpos&action=createvoucher',
                data: {
                    voucherPosValue: voucherPosValue,
                    voucherPosCustomerType: voucherPosCustomerType,
                    voucherPosCustomerInput: voucherPosCustomerInput,
                    voucherPosPrint: voucherPosPrint
                },
                method: 'post',
                dataType: 'json',
                beforeSend: function () {
                    App.loading.open();
                },
                success: function (data) {
                    App.loading.close();
                    if (data.success === true) {
                        $('#voucherpos_barcode').html(data.barcode);
                        me.resetCreateVoucherFields();
                    }
                    if (data.success === false) {
                        alert(data.error);
                    }
                },
                error: function (xhr, status, httpStatus){
                    alert(JSON.parse(xhr.responseText).error);
                }
            });
        },

        /**
         * @return {void}
         */
        resetCreateVoucherFields: function () {
            me.storage.$voucherValueInput.val('');
            me.storage.$voucherValueInput.focus();
        },

        /**
         * @param {number} voucherPosResidualValue
         * @param {number} voucherPosRedeemValue
         * @param {string} voucherCode
         *
         * @return {void}
         */

        redeemVoucher: function (voucherPosResidualValue, voucherPosRedeemValue, voucherCode) {
            $.ajax({
                url: 'index.php?module=voucherpos&action=redeemvoucher',
                data: {
                    voucherPosResidualValue: voucherPosResidualValue,
                    voucherPosRedeemValue: voucherPosRedeemValue,
                    voucherPosRedeemCode: voucherCode
                },
                method: 'post',
                dataType: 'json',
                beforeSend: function () {
                    App.loading.open();
                },
                success: function (data) {
                    App.loading.close();
                    if (data.success === true) {
                        $('#new_residual_value').html('<strong>Verbleibender Betrag: € ' + data.new_residual_value + '</strong>');
                        me.resetRedeemVoucherFields();
                    }
                    if (data.success === false) {
                        alert(data.error);
                    }
                },
                error: function (xhr, status, httpStatus){
                    alert(JSON.parse(xhr.responseText).error);
                }
            });
        },

        /**
         * @return {void}
         */
        resetRedeemVoucherFields: function () {
            me.storage.$voucherResidualValue.val('');
            me.storage.$voucherRedeemValue.val('');
            me.storage.$voucherCode.focus();
            $('#voucherpos_redeem_code').data('code', null);
            $('#voucherpos_redeem_code').html('<i>Gutscheincode: </i>');
        },

    };

    return {
        init: me.init,
        createItem: me.createItem
    };

})(jQuery);

$(document).ready(function () {
    VoucherPosUi.init();
});
