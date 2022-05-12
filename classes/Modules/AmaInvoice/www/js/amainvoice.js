var AmaInvoice = function ($) {
    'use strict';

    var me = {
        storage: {
            tableList: []
        },
        expertChange: function () {
            if ($('#expert').prop('checked')) {
                $('.trexpert').show();
            } else {
                $('.trexpert').hide();
            }
        },
        init: function () {
            $('#import').on('click', function () {
                me.storage.tableList = [];
                $('#amainvoice_list').find(':checked').each(function () {
                    me.storage.tableList.push($(this).data('id'));
                });
                if (me.storage.tableList.length > 0) {
                    $.ajax({
                        type: 'POST',
                        url: 'index.php?module=amainvoice&action=list&cmd=importlist',
                        data: {
                            list: me.storage.tableList
                        },
                        success: function () {
                            me.storage.tableList = [];
                        }
                    });
                }
            });
            if ($('#createorder').prop('checked')) {
                $('#expert').prop('checked', true);
            }
            $('#expert').on('change', function () {
                me.expertChange();
            });
            me.expertChange();
        }
    };

    return {
        init: me.init
    };

}(jQuery);

$(document).ready(function () {
    AmaInvoice.init();
});
