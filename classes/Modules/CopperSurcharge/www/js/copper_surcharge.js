var CopperSurcharge = (function ($) {
    'use strict';

    var me = {
        isInitialized: false,

        selector: {
            optional: '.surcharge-optional',
            maintenanceType: 'surcharge-maintenance-type'

        }

        , init: function (){
            me.checkOptional();
            me.registerEvents();
        }

        , registerEvents: function () {
            $('input[name=\''+me.selector.maintenanceType+'\']').on('click', me.selector.clickClass,
                function (event) {
                    let maintenanceType = $(this).val();
                    me.showHideOptional(maintenanceType);
                });
        }

        , checkOptional: function (){
            let maintenanceType = $('input[name=\''+me.selector.maintenanceType+'\']:checked').val();
            me.showHideOptional(maintenanceType);

        }

        ,showHideOptional: function(maintenanceType){
            if(maintenanceType === '1'){
                $(me.selector.optional).show();
            }
            else {
                $(me.selector.optional).hide();
            }
        }


    };

    return {
        init: me.init
    };

})(jQuery);

$(document).ready(function () {
    CopperSurcharge.init();
});
