var HubSpotModule = function ($) {
    'use strict';

    var me = {

        isInitialized: false,

        storage: {},

        /**
         * @return void
         */
        init: function () {
            if (me.isInitialized === true) {
                return;
            }


            me.registerEvents();

            me.isInitialized = true;
        },

        registerEvents: function () {
            $('#sync_hs_xt').on('click', function (event) {
                me.syncAccount();
                event.preventDefault();
            });

            $('#hs-add-more-field ').on('click', function () {
                me.addMoreCustomField();;
            });

            $('#no-matching').on('click', function (event) {
                $(this).prop('checked', true);
                $('#do-matching').prop('checked', false);
                me.hideMatchingSetting();
            });

            $('#do-matching').on('click', function (event) {
                $(this).prop('checked', true);
                $('#no-matching').prop('checked', false);
                me.showMatchingSetting();
            });
        },

        showMatchingSetting: function () {
            $('.deal-system').removeClass('hs-invisible').find('select').prop('disabled', false);
        },

        hideMatchingSetting: function () {
            $('.deal-system').addClass('hs-invisible').find('select').prop('disabled', true);
        },

        /**
         * @return {void}
         */
        addApiKey: function () {
            if (me.isInitialized === false) {
                me.init();
            }

            me.resetAdd();
            me.storage.$createItemDialog.dialog('open');
        },

        /**
         * @return {void}
         */
        syncAccount: function () {
            var $form = $('#hs-configurator-form');
            $form.action = 'index.php?module=hubspot&action=apikey';
            $form.submit();
        },
        addMoreCustomField: function () {
            var $tableTr = $('tr[id^="field"]:last');
            var num = parseInt($tableTr.prop('id').match(/\d+/g), 10) + 1;
            var $clone = $tableTr.clone().prop('id', 'field' + num);
            $tableTr.after($clone.html(
                '<td width="150">Eigenschaft ' + num + ' :</td>' +
                '<td><input type="text" name="custom_field[]" class="fd_custom_field" placeholder=\"Feld name\" id="field' + num + '" value=""></td>'));
        }

    };

    return {
        init: me.init,
        addApiKey: me.addApiKey
    };

}(jQuery);

$(function () {
    if ($('#sync_hs_xt').length > 0) {
        HubSpotModule.init();
    }
});
