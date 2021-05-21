var GoodsReceipt = function ($) {
    "use strict";

    var me = {
        init: function () {
            $('#newreturnorder').on('click',function(){
                if(confirm('Sind das alle zurückgeschickten Artikel? Diese Artikel kommen in ein Beleg')) {
                    $.ajax({
                        url: 'index.php?module=wareneingang&action=distriinhalt&cmd=createreturnorder',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            id: $(this).data('id')
                        },
                        success: function(data) {
                            if(typeof data.url != 'undefined') {
                                window.location = data.url;
                            }
                            else if(typeof data.error != 'undefined') {
                                alert(data.error);
                            }
                        }
                    });
                }
            });
            $('table#wareneingang_kunderetoure').on('afterreload',function(){
               $(this).find('input.qty').on('change',function () {
                   $.ajax({
                       url: 'index.php?module=wareneingang&action=distriinhalt&cmd=changeqty',
                       type: 'POST',
                       dataType: 'json',
                       data: {
                           dnpid: $(this).data('dnpid'),
                           serialnumber: $(this).data('serialnumber'),
                           batch: $(this).data('batch'),
                           bestbefore: $(this).data('bestbefore'),
                           value: $(this).val()
                       }
                   });
               });
            });
        }
    };

    return {
        init: me.init
    };
}(jQuery);
$(document).ready(function () {
    GoodsReceipt.init();
});
