

/**
 * Copy the generated link to clipboard
 */
var DownloadLinkToClipboar = (function ($) {
    'use strict';

    var me = {
        isInitialized: false,

        selector: {
            keyElement: '#report_transfer_form',
            linkTextBox: '#transferUrlAdress',
            buttonCopyLink: '#transferUrlClipboard',
        },

        /**
         * @return {void}
         */
        init: function () {
            if (me.isInitialized === true) {
                return;
            }
            if($(me.selector.keyElement).length === 0) {
                return;
            }
            me.registerEvents();
            me.isInitialized = true;
        },

        /**
         * @return {void}
         */
        registerEvents: function () {
            $(me.selector.buttonCopyLink).on('click', function (event) {
                event.preventDefault();
                me.copyToClipBoard($(me.selector.linkTextBox).val());
            });
        },

        /**
         * @param {string} text
         *
         * @return {void}
         */
        copyToClipBoard: function (text) {
            var textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.position="fixed";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                var successful = document.execCommand('copy');
            } catch (err) {
                console.error('Unable to copy link to clipboard.');
            }
            document.body.removeChild(textArea);
        }
    };

    return {
        init: me.init,
    };

})(jQuery);

$(document).ready(function () {
    DownloadLinkToClipboar.init();
});
