var SalesFigure = function ($) {
    "use strict";

    var me = {

        init: function () {

            $('.getdetails').each(function () {
               var elid = this.id;
                $.ajax({
                    url: 'index.php?module=verkaufszahlen&action=details&cmd=getdetails',
                    type: 'POST',
                    dataType: 'json',
                    data: {element: elid},
                    success: function (data) {
                        if(typeof data.html != 'undefined' && typeof data.element != 'undefined'
                            && data.html+'' !== '') {
                            $('#'+data.element).html(data.html);
                        }
                    }
                });
            });
            $('.geteasytablelist').each(function () {
                var elid = this.id;
                $.ajax({
                    url: 'index.php?module=verkaufszahlen&action=list&cmd=geteasytablelist',
                    type: 'POST',
                    dataType: 'json',
                    data: {element: elid},
                    success: function (data) {
                        if(typeof data.html != 'undefined' && typeof data.element != 'undefined'
                            && data.html+'' !== '') {
                            $('#'+data.element).html(data.html);
                        }
                    }
                });
            });
        }
    };

    return {
        init: me.init,
    };

}(jQuery);

$(document).ready(function () {
    SalesFigure.init();
});