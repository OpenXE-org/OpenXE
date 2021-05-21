var ReportMenuPopup = (function ($) {
    'use strict';

    var me = {
        storage: {
            menulinks: null,
            menuPopup: null
        },
        openMenuPopup: function (reportId)
        {
            $.ajax({
                url: 'index.php?module=report&action=view&cmd=getreportpopup',
                type: 'POST',
                dataType: 'json',
                data: { report_id: reportId  },
                success: function(data) {
                    if(typeof data.html != 'undefined')
                    {
                        $('#reportMenuPopupContent').html(data.html);
                        $(me.storage.menuPopup).dialog('open');
                    }
                }
            });
        },
        init: function () {
            me.storage.menuPopup = $('#reportMenuPopup');
            if(me.storage.menuPopup.length === 0) {
                return;
            }
            me.storage.menuLinks = $('ul#submenu li a.reportpopup');
            if(me.storage.menuLinks.length === 0) {
                return;
            }
            $(me.storage.menuPopup).dialog(
                {
                    modal: true,
                    autoOpen: false,
                    minWidth: 940,
                    title: '',
                    buttons: {
                        'OK': function () {
                            $(this).dialog('close');
                        }
                    }
                }
            );
            me.storage.menuLinks.on('click',function(){
                me.openMenuPopup($(this).data('reportid'));
            });
        }
    };

    return {
        init: me.init,
    };
})(jQuery);

$(document).on('ready',function(){
    ReportMenuPopup.init();
});