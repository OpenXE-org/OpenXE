/**
 * Script erweitert das "Artikel bearbeiten"-Formular um einen "EAN laden"-Button
 *
 * - "EAN laden"-Button wird neben dem EAN-Nummer-Eingabefeld platziert
 * - Beim Klick auf den Button wird die nächste frei EAN-Nummer aus dem EAN-Pool abgerufen und in
 *   das EAN-Nummer-Feld übernommen.
 * - Beim Abrufen der EAN-Nummer wird diese automatisch als "vergeben" markiert.
 * - Sollte das Abrufen der EAN-Nummer fehlschlagen wird eine Alert-Box angezeigt.
 */
var EangeneratorNumberFetcher = (function ($) {
    'use strict';

    var me = {

        storage: {
            $table: null,
            dataTable: null,
            $editDialog: null
        },

        /**
         * @return void
         */
        init: function () {
            var $eanInput = $('input#ean');
            if ($eanInput.length !== 1) {
                return;
            }

            // "EAN laden" Button an EAN-Eingabefeld hängen
            var $eanWrapper = $eanInput.first().parent();
            var $eanButton = me.createEanFetcherButton();
            $eanButton.appendTo($eanWrapper);
        },

        /**
         * @return {jQuery}
         */
        createEanFetcherButton: function () {
            var $eanButton = $('<button type="button">EAN laden</button>');
            $eanButton.on('click', function (e) {
                e.preventDefault();
                var $eanInput = $('input#ean');

                $.ajax({
                    url: 'index.php?module=eangenerator&action=edit&cmd=fetch-ean-number',
                    data: {},
                    method: 'post',
                    dataType: 'json',
                    success: function (data) {
                        if (data.success === true) {
                            $eanInput.val(data.data.ean_number);
                            var msg =
                                'Die EAN-Nummer "' + data.data.ean_number + '" wurde aus dem EAN-Pool gezogen '+
                                'und als vergeben markiert. Bitte speichern Sie den Artikel noch.';
                            alert(msg);
                        }
                        if (data.success === false) {
                            alert('Fehler: ' + data.error);
                        }
                    }
                });
            });

            return $eanButton;
        }
    };

    return {
        init: me.init
    };

})(jQuery);


$(document).ready(function () {
    EangeneratorNumberFetcher.init();
});
