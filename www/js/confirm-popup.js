var ConfirmPopupDialog = (function ($) {
    "use strict";

    var me = {

        storage: {
            $confirmPopups: null
        },

        init: function () {
            me.storage.$confirmPopups = $('.confirmpopup');

            if (me.storage.length === 0) {
                return;
            }

            // Initial Filter-Zähler füllen
            me.storage.$confirmPopups.each(function () {
                me.initDialog(this);
            });

        },

        initDialog: function (element) {
            $(element).dialog(
            {
                modal: true,
                autoOpen: false,
                minWidth: 940,
                buttons: {
                    'ABBRECHEN': function () {
                        $(this).dialog('close');
                    },
                    'OK': function () {
                        $.ajax({
                            url: $(this).data('url'),
                            type: 'POST',
                            dataType: 'json',
                            data: $(this).find('form').serialize(),
                            success: function (data) {
                                if (typeof data.url != 'undefined') {
                                    window.location.href = data.url;
                                }
                                $(element).dialog('close');
                            },
                            error: function (data) {
                                if (typeof data.message != 'undefined') {
                                    alert(data.message);
                                }
                            },
                            beforeSend: function () {

                            }
                        });
                    }
                },
                close: function (event, ui) {

                }
            });
            $(element).toggleClass('hide', false);
        },
    };

    return {
        init: me.init,
        initDialog: me.initDialog
    }

})(jQuery);

$(document).ready(function () {
    ConfirmPopupDialog.init();
});
