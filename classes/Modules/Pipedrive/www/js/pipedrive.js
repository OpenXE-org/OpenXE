var PipedriveModule = function ($) {
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
            $('#sync_pd_xt').on('click', function (event) {
                me.syncAccount();
                event.preventDefault();
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

            $('#addresses_interval-checkbox').add('#deals_interval-checkbox').on('change', function () {
                me.setReadOnly(this);
            })
        },

        setReadOnly: function(src) {

            var id = $(src).attr('id');
            var idExploded = id.split('-');
            $('#'+ idExploded[0]).prop('readonly',!$('#'+ idExploded[0]).prop('readonly'));
        },

        showMatchingSetting: function () {
            $('.deal-system').removeClass('hd-invisible').find('select').prop('disabled', false);
        },

        hideMatchingSetting: function () {
            $('.deal-system').addClass('hd-invisible').find('select').prop('disabled', true);
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
            var $form = $('#hd-configurator-form');
            $form.action = 'index.php?module=pipedrive&action=apikey';
            $form.submit();
        }

    };

    return {
        init: me.init,
        addApiKey: me.addApiKey
    };

}(jQuery);

$(function () {
    if ($('#sync_pd_xt').length > 0) {
        PipedriveModule.init();
    }
});
