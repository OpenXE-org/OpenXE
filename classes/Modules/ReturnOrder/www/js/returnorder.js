var Returnorder = function ($) {
    'use strict';
    var me = {

        init: function () {
            var $iframe = $('#framepositionen');
            $($iframe).on('load', function () {
                var $trs = $(this).contents().find('table#tableone tbody tr');
                if ($trs.length > 1) {
                    $($trs).each(function () {
                        if (typeof this.id != 'undefined' && parseInt(this.id) > 0) {
                            var $tds = $(this).children('td');
                            $($tds[10]).toggleClass('storagelocation', true);
                        }
                    });

                    $(this).contents().find('table#tableone tbody tr td.storagelocation').on('click', function () {
                        setTimeout(function (el) {
                            $(el).find('input').autocomplete({
                                source: 'index.php?module=ajax&action=filter&filtername=lagerplatz'
                            });
                        }, 100, this);
                    });

                    $(this).contents().find('select.selgrund').on('change', function(){
                       var $locationsotragetd = $(this).parents('td').first().next();
                       var $option = $(this).find('option:selected').first();
                       if($($locationsotragetd).find('input').length > 0) {
                           $($locationsotragetd).find('input').val($($option).data('lager'));
                       }
                       else {
                           $($locationsotragetd).html($($option).data('lager'));
                       }
                    });
                }
            });


            $('.storagelocation').on('click', function () {
                $('#locationstorage').val($(this).html());
            });



            $('span.lagerplatz').on('click', function () {
                $('#locationstorage').val($(this).html());
            });
        }
    };
    return {
        init: me.init
    };

}(jQuery);

$(document).ready(function () {
    Returnorder.init();
});