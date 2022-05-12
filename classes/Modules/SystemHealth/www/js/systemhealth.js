var SystemHealth = function ($) {
    "use strict";

    var me = {
        elem: {
            $window: null,
        },

        init: function () {
            me.elem.$window = $(window);
            me.attachEvents();
        },

        /**
         * Registriert alle benötigten Events und Timer
         */
        attachEvents: function () {

            // Formular-Absenden abfangen
            $('img.reset').on('click', function (event) {
                $.ajax({
                    type: 'POST',
                    url: 'index.php?module=systemhealth&action=list&cmd=reset',
                    data: {
                        id: $(this).data('id'),
                    },
                    success: function (data) {
                        if(data.status) {
                            window.location = 'index.php?module=systemhealth&action=list';
                        }
                    }
                });
            });
            $('span.systemhealthnotification').on('click', function(){
                $.ajax({
                    type: 'POST',
                    url: 'index.php?module=systemhealth&action=list&cmd=changenotification',
                    data: {
                        id: $(this).data('id'),
                        value: $(this).hasClass('active')?0:1
                    },
                    success: function (data) {
                        if(data.status) {
                            if(data.value == 1) {
                                $('span.systemhealthnotification[data-id='+data.id+']').toggleClass('active', true).toggleClass('inactive', false);
                            }
                            else{
                                $('span.systemhealthnotification[data-id='+data.id+']').toggleClass('active', false).toggleClass('inactive', true);
                            }
                        }
                    }
                });
            });
        },

    };

    return {
        init: me.init,
    };

}(jQuery);

$(document).on('ready',function(){
    SystemHealth.init();
});