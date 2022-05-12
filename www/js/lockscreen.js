/**
 * Modul zum Sperren der Anwendung (bei Verbindungsabbrüchen)
 *
 * Die Prüfung auf Verbindungsabbrüche findet nicht hier statt, sonder beim Polling.
 * Dieses Modul stellt lediglich den Sperrbildschirm bereit.
 *
 * Die Sperrung wird durch ein Overlay erreicht, das über allen Elementen liegt
 * und so die Bedienung der Seite verhindert.
 */
var LockScreen = function ($) {
    'use strict';

    var me = {

        storage: {
            $overlay: null
        },

        /**
         * Show LockScreen
         */
        showOverlay: function () {
            if (me.storage.$overlay === null) {
                me.tryRestoreOverlay();
            }
            if (me.storage.$overlay === null) {
                me.createOverlay();
            }

            me.storage.$overlay.show();
        },

        /**
         * Hide LockScreen
         */
        hideOverlay: function () {
            if (me.storage.$overlay === null) {
                me.tryRestoreOverlay();
            }
            if (me.storage.$overlay === null) {
                return; // Nothing to hide; Overlay was never created
            }

            me.storage.$overlay.hide();
            me.storage.$overlay.remove();
            me.storage.$overlay = null;
        },

        /**
         * Try to restore overlay from eventually created element
         */
        tryRestoreOverlay: function () {
            var $check = $('#lockscreen-overlay');
            if ($check.length >= 1) {
                me.storage.$overlay = $check.first();
            }
        },

        /**
         * Create overlay element
         */
        createOverlay: function () {
            var $overlay = $('<div id="lockscreen-overlay"></div>');
            $overlay.css({
                zIndex: 10001,
                position: 'fixed',
                left: 0,
                top: 0,
                width: '100vw',
                height: '100vh',
                backgroundColor: 'rgba(0, 0, 0, 0.5)',
                fontSize: '10px'
            });

            var $text = $('<div id="lockscreen-text"></div>');
            $text.append(
                '<p>Die Verbindung zum Server wurde unterbrochen.'+
                'Möglicherweise ist Ihr Computer nicht mehr mit dem Internet verbunden?</p>'
            );
            $text.append(
                '<p><small>Sobald die Verbindung wieder hergestellt werden kann, wird diese Meldung entfernt.</small></p>'
            );
            $text.css({
                position: 'relative',
                top: '50%',
                left: '50%',
                display: 'inline-block',
                width: '300px',
                marginTop: '-50%',
                marginLeft: '-150px',
                fontSize: '1.2em',
                padding: '0.5em 1em',
                color: '#FFF',
                backgroundColor: 'rgba(0, 0, 0, 0.5)'
            });

            // Benutzer kann Sperrbildschirm schliessen, beim nächsten Fehlerfall erscheint die Meldung wieder
            var $buttonWrapper = $('<p></p>').css('text-align', 'center');
            var $button = $('<input type="button" class="btn" id="lockscreen-close-button" value="Fenster schliessen">');
            $button.on('click', function (e) {
                e.preventDefault();
                me.hideOverlay();
            });
            me.storage.$closeButton = $button;

            $button.appendTo($buttonWrapper);
            $buttonWrapper.appendTo($text);
            $text.appendTo($overlay);
            $overlay.appendTo('body');

            me.storage.$overlay = $overlay;
        }
    };

    return {
        show: me.showOverlay,
        hide: me.hideOverlay
    };

}(jQuery);
