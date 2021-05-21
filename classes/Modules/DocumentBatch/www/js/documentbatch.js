var DocumentBatch = function ($) {
    "use strict";

    var me = {
        init: function () {
            $('#checkall').on('change',function(){
               $('#documentbatches_orders').find('input.select').prop('checked', $(this).prop('checked'));
            });
            $('#checkallinwork').on('change',function(){
                $('#documentbatches_inwork').find('input.select').prop('checked', $(this).prop('checked'));
            });
        }
    };
    return {
        init: me.init
    }
}(jQuery);

$(document).ready(function () {
    DocumentBatch.init();
});
