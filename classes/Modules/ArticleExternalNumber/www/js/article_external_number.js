var ArticleExternalNumber = function ($) {
    'use strict';

    var me = {
        storage: {
            messages: null
        },
        selector: {
            overviewTable: '#artikel_fremdnummern_list',
            overviewForm: '#article-external-number-form'

        },
        init: function () {
            me.storage.messages = JSON.parse($('#messages').html());
            $('#select-all').on('change', function () {
                $(me.selector.overviewTable).find(':checkbox').prop('checked', $(this).prop('checked'));
            });
            $(me.selector.overviewForm).on('submit',
                function (event) {
                    let action = $('#selected-action').val();
                    if (action === '' || action === null) {
                        event.preventDefault();
                        return;
                    }
                    if ($(me.selector.overviewTable).find(':checked').length === 0) {
                        event.preventDefault();
                        alert(me.storage.messages['NO_SELECTION']);
                        return;
                    }
                    switch (action) {
                        case 'activate':
                            if (!confirm(me.storage.messages['CONFIRM_ACTIVATION'])) {
                                event.preventDefault();
                                return;
                            }
                            break;
                        case 'deactivate':
                            if (!confirm(me.storage.messages['CONFIRM_DEACTIVATION'])) {
                                event.preventDefault();
                                return;
                            }
                            break;
                        case 'delete':
                            if (!confirm(me.storage.messages['CONFIRM_DELETION'])) {
                                event.preventDefault();
                                return;
                            }
                            break;
                    }
                });
        }
    };
    return {
        init: me.init
    };

}(jQuery);

$(document).ready(function () {
    ArticleExternalNumber.init();
});
