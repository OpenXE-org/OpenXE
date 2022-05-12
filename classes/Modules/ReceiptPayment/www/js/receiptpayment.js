var ReceiptPayment = function ($) {
    "use strict";

    var me = {
        init: function () {
            if ($('#challengepopup').length) {
                $('#challengepopup').dialog(
                    {
                        modal: true,
                        autoOpen: true,
                        minWidth: 940,
                        title:'',
                        buttons: {
                            OK: function()
                            {
                                $('#challengepopupfrm').trigger('submit');
                            },
                            ABBRECHEN: function() {
                                $(this).dialog('close');
                            }
                        },
                        close: function(event, ui){

                        }
                    });
            }
        }
    };

    return {
        init: me.init
    };

}(jQuery);

$(document).on('ready',function() {
    ReceiptPayment.init();
});
