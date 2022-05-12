
/**
 * Für die Bedienung der Modul-Oberfläche
 */
var PrinterGoogleCloudPrint = (function ($) {
    'use strict';

    var me = {

        isInitialized: false,
        $apiSelect: null,
        $printerSelect: null,
        initApiValue: null,
        initPrinterValue: null,
        printercache: {},

        /**
         * @return void
         */
        init: function () {
            if (me.isInitialized === true) {
                return;
            }
            me.$apiSelect = $('select[name="google_api"]');
            me.$printerSelect = $('select[name="google_printer"]');
            me.initApiValue = me.$apiSelect.val();
            me.initPrinterValue = me.$printerSelect.val();

            var options = {};
            $('select[name="google_printer"] option').each( function () {
                options[$(this).attr('value')] = $(this).text();
            });
            me.printercache[me.initApiValue] = options;

            me.registerEvents();
            me.isInitialized = true;
        },

        setPrinterSelectOptions: function(options, selected) {
            me.$printerSelect.empty(); // remove old options
            $.each(options, function(value, display) {
                    me.$printerSelect.append($('<option></option>').attr('value', value).text(display));
                });
            if (selected in options) {
                me.$printerSelect.val(selected);
            }
        },

        /**
         * @return {void}
         */
        registerEvents: function () {
            me.$apiSelect.change(function () {
                me.ajaxLoadPrinterOptions(this.value);
            });
        },

        /**
         * @param {number} fieldId
         *
         * @return {void}
         */
        ajaxLoadPrinterOptions: function (apiName) {
            if (apiName === '') {
                return;
            }

            if (apiName in me.printercache) {
               me.setPrinterSelectOptions(me.printercache[apiName], me.initPrinterValue);
               return;
            }

            $.ajax({
                url: 'index.php?module=googleapi&action=ajaxprinters',
                data: {
                    api_name: apiName
                },
                method: 'post',
                dataType: 'json',
                beforeSend: function () {
                    me.$printerSelect.prop('disabled', true);
                    me.$apiSelect.prop('disabled', true);
                    App.loading.open();
                },
                complete: function() {
                    me.$printerSelect.prop('disabled', false);
                    me.$apiSelect.prop('disabled', false);
                },
                error: function (error) {
                    App.loading.close();
                },
                success: function (data) {
                    me.printercache[apiName] = data;
                    me.setPrinterSelectOptions(data, me.initPrinterValue);
                    App.loading.close();
                }
            });
        },
    };

    return {
        init: me.init,
    };

})(jQuery);

$(document).ready(function () {
    PrinterGoogleCloudPrint.init();
});
