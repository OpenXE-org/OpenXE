var SendCloud = (function ($) {
    'use strict';

    var me = {

        isInitialized: false,

        storage: {
            $country: $('#land'),
            $usStatesSelect: $('#us-states'),
            $stateInput: $('#states')

        },

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

        /**
         * @return {void}
         */
        registerEvents: function () {
            $(document).ready(function() {
                if(me.storage.$country.val() === 'US'){
                    me.storage.$usStatesSelect.show();
                    me.storage.$stateInput.hide();
                }else{
                    me.storage.$stateInput.show();
                    me.storage.$usStatesSelect.hide();
                }
            });

            me.storage.$country.on('change', function () {
                if(me.storage.$country.val() === 'US'){
                    me.storage.$usStatesSelect.show();
                    me.storage.$stateInput.hide();
                }else{
                    me.storage.$usStatesSelect.hide();
                    me.storage.$stateInput.show();

                }
            });
        },
    };

    return {
        init: me.init,
    };

})(jQuery);

$(document).ready(function () {
    SendCloud.init();
});
