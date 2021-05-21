var SettingBetaProgram = function ($) {
    'use strict';

    var me = {

        selector: {
            betaPopup: '#beta-program-thanks-popup',
            closeLink: '#beta-program-close-btn'
        },

        init: function () {
            if ($(me.selector.betaPopup).length === 0) {
                return;
            }
            if ($(me.selector.betaPopup).data('open')) {
                $(me.selector.betaPopup).toggleClass('hide', false);
                $(me.selector.betaPopup).dialog(
                    {
                        modal: true,
                        autoOpen: true,
                        title: 'Super!'
                    }
                );
            }
            $(me.selector.closeLink).on('click', function () {
                $(me.selector.betaPopup).dialog('close');
                window.location.href = 'index.php';
            });
        }
    };

    return {
        init: me.init
    };
}(jQuery);

$(document).ready(function () {
    SettingBetaProgram.init();
});
