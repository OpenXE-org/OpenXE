var ArticleMatrixSelection = function ($) {
    "use strict";

    var me = {

        selector: {
            dataTable: '#kundeartikelpreise',
            dataTableForm: '#kundeartikelpreiseform',

            articleSelectionCheckboxes: '.articlematrix-checkbox',
            articleQuantityInputs: '.articlematrix-quantity',
            articleQuickAddButtons: '.articlematrix-quickadd',

            selectAllCheckbox: '#articlematrixselection-selectall-checkbox',
            insertButton: '#articlematrixselection-insert-button',
            resetButton: '#articlematrixselection-reset-button',
            backButton: '#profisuche-back-button'
        },

        /** @property Vorgangs-ID */
        processId: null,

        /** @property Vorgangstyp: Auftrag, Angebot, Rechnung, usw. */
        processType: null,

        /** @property URL für alle Ajax-Anfragen; es fehlt lediglich der "cmd" Parameter
        ajaxBaseUrl: null,

        /**
         * Modul initialisieren
         */
        init: function () {

            // Bei Bestellungen und (Preis-)Anfragen gibt es andere Selektoren.
            // Hier werden auch Einkaufspreise angezeigt; ansonten Verkaufspreise.
            if ($(me.selector.dataTable).length === 0) {
                me.selector.dataTable = '#lieferantartikelpreise';
                me.selector.dataTableForm = '#lieferantartikelpreiseform';
            }

            var $form = $(me.selector.dataTableForm);
            me.processId = parseInt($form.data('process-id'));
            me.processType = $form.data('process-type');

            if (me.processId <= 0) {
                console.warn('Vorgangs-ID ist ungültig: ' + me.processType + '(' + me.processId + ')');
                return;
            }
            if (me.processType === '') {
                console.warn('Vorgangstyp ist nicht gesetzt.');
            }

            me.ajaxBaseUrl =
                'index.php?module=ajax&action=articlematrixselection' +
                '&typ=' + me.processType + '&id=' + me.processId;

            me.attachEvents();
        },

        /**
         * Registriert alle benötigten Events
         */
        attachEvents: function () {

            // Einzelnen Artikel über QuickAdd-Button zu Positionen hinzufügen
            $(document).on('click', me.selector.articleQuickAddButtons, function (e) {
                e.preventDefault();
                me.quickAddArticle($(this));
            });

            // Bei Auswahl bzw. Mengeänderungen in Matrix > Auswahl in Userkonfiguration speichern
            $(document).on('change', me.selector.articleSelectionCheckboxes, me.saveSelection);
            $(document).on('change', me.selector.articleQuantityInputs, me.saveSelection);

            // "Alle auswählen" Checkbox
            $(document).on('change', me.selector.selectAllCheckbox, function () {
                var checked = $(this).prop('checked');
                me.selectAll(checked);
            });

            // Ausgewählte Artikel aus Matrix in Auftrag übernehmen
            $(document).on('click', me.selector.insertButton, function (e) {
                e.preventDefault();
                me.insertSelection();
            });

            // Matrix-Auswahl zurücksetzen
            $(document).on('click', me.selector.resetButton, function (e) {
                e.preventDefault();
                me.resetSelection();
            });

            // Bei Änderungen der DataTable (Sortierung, Seitenwechsel) > Matrix aus Userkonfiguration laden
            $(me.selector.dataTable).on('draw.dt', function () {
                me.loadSelection();
            });
        },

        /**
         * Artikelauswahl und -mengen laden + Felder füllen
         */
        loadSelection: function () {
            jQuery.ajax({
                type: 'POST',
                url: me.ajaxBaseUrl + '&cmd=get',
                success: function (data) {
                    me.fillMatrix(data);
                }
            });
        },

        /**
         * Checkboxen und Mengefelder füllen
         *
         * @param {Object} data
         */
        fillMatrix: function (data) {
            var verkaufpreisId;
            var selectAllChecked = true;

            // Erstmal alle Mengen- und Auswahlfelder zurücksetzen
            $(me.selector.articleSelectionCheckboxes).prop('checked', false);
            $(me.selector.articleQuantityInputs).val('');

            // Auswahlfelder setzen
            if (typeof data.auswahl !== 'undefined') {
                for (verkaufpreisId in data.auswahl) {
                    if (data.auswahl.hasOwnProperty(verkaufpreisId)) {
                        $('#articlematrix-checkbox-' + verkaufpreisId).prop('checked', true);
                    }
                }
            }

            // Mengenfelder ausfüllen
            if (typeof data.menge !== 'undefined') {
                for (verkaufpreisId in data.menge) {
                    if (data.menge.hasOwnProperty(verkaufpreisId)) {
                        var menge = parseInt(data.menge[verkaufpreisId]);
                        $('#articlematrix-quantity-' + verkaufpreisId).val(menge);
                    }
                }
            }

            // "Alle auswählen"-Checkbox setzen
            $(me.selector.articleSelectionCheckboxes).each(function (index, checkbox) {
                if ($(checkbox).prop('checked') === false) {
                    selectAllChecked = false;
                }
                $(me.selector.selectAllCheckbox).prop('checked', selectAllChecked);
            });
        },

        /**
         * Aktuelle Auswahl in Userkonfiguration speichern
         *
         * @param event jQuery-EventObject
         */
        saveSelection: function (event) {
            var elementType = event.target.type;
            var $element = $(event.target);

            // Checkbox wurde aktiviert bzw. deaktiviert
            if (elementType === 'checkbox') {
                var rowId = $element.data('id');
                var isChecked = $element.prop('checked');

                // Mengeneingabefeld selektieren
                var $inputElement = $('#articlematrix-quantity-' + rowId);
                var defaultQuantity = $inputElement.data('default-quantity');
                var currentQuantity = $inputElement.val();

                // Bei Aktivierung > Menge-Eingabefeld mit "Ab Menge" füllen
                if (isChecked === true && currentQuantity === '') {
                    $inputElement.val(defaultQuantity);
                }

                // Bei Deaktivierung > Menge-Eingabefeld leeren
                if (isChecked === false) {
                    $inputElement.val('');
                }
            }

            // Aktuelle Auswahl (aktive Checkboxen und Mengen) in Userkonfiguration speichern
            $.ajax({
                type: 'POST',
                url: me.ajaxBaseUrl + '&cmd=set',
                data: $(me.selector.dataTableForm).serializeArray()
            });
        },

        /**
         * Aktuelle Auswahl in Positionen übernehmen
         */
        insertSelection: function () {
            jQuery.ajax({
                type: 'POST',
                url: me.ajaxBaseUrl + '&cmd=get',
                success: function (data) {

                    if (typeof data.auswahl === 'undefined' || typeof data.menge === 'undefined') {
                        return;
                    }

                    var auswahlCount = Object.keys(data.auswahl).length;
                    if (auswahlCount === 0) {
                        alert('Kann Einträge nicht hinzufügen. Es sind keine Einträge ausgewählt.');
                        return;
                    }

                    var confirmMsg = 'Möchten Sie die ' + auswahlCount + ' Einträge wirklich einfügen?';
                    var confirmResult = confirm(confirmMsg);
                    if (confirmResult !== true) {
                        return;
                    }

                    // Loading-Overlay einblenden
                    if ($.fn.loadingOverlay !== undefined) {
                        $('body').loadingOverlay();
                    }

                    // Pro ausgewähltem Artikel einen AJAX-Request abschicken
                    for (var verkaufpreisId in data.auswahl) {
                        if (data.auswahl.hasOwnProperty(verkaufpreisId)) {
                            var quantity = parseInt(data.menge[verkaufpreisId]);
                            me.insertSingleArticle(verkaufpreisId, quantity);
                        }
                    }

                    // Loading-Overlay ausblenden
                    if ($.fn.loadingOverlay !== undefined) {
                        $('body').loadingOverlay('remove');
                    }

                    // Auswahl zurücksetzen
                    me.resetSelection();

                    // Zurück zu den Positionen
                    window.setTimeout(function () {
                        window.location.href = $(me.selector.backButton).attr('href');
                    }, 250);
                }
            });
        },

        /**
         * Einzelnen Artikel in Positionen übernehmen
         *
         * @param verkaufpreisId
         * @param quantity
         */
        insertSingleArticle: function (verkaufpreisId, quantity) {
            if (parseInt(verkaufpreisId) <= 0) {
                return;
            }
            if (isNaN(quantity) || parseInt(quantity) <= 0) {
                quantity = null;
            }

            jQuery.ajax({
                type: 'POST',
                async: false,
                url: 'index.php?module=artikel&action=profisuche&insert=true&batch=true' +
                    '&cmd=' + me.processType +
                    '&sid=' + me.processId +
                    '&id=' + verkaufpreisId,
                data: {'menge': quantity}
            });
        },

        /**
         * Alle Artikel-Checkboxen aktivieren/deaktivieren
         *
         * @param {boolean} isChecked
         */
        selectAll: function (isChecked) {
            $(me.selector.articleSelectionCheckboxes).each(function (index, checkbox) {
                var $checkbox = $(checkbox);
                var rowId = $checkbox.data('id');

                // Checkbox aktivieren/deaktivieren
                $checkbox.prop('checked', isChecked);

                // Mengeneingabefeld selektieren
                var $inputElement = $('#articlematrix-quantity-' + rowId);
                var defaultQuantity = $inputElement.data('default-quantity');
                var currentQuantity = $inputElement.val();

                // Bei Aktivierung > Menge-Eingabefeld mit "Ab Menge" füllen
                if (isChecked === true && currentQuantity === '') {
                    $inputElement.val(defaultQuantity);
                }

                // Bei Deaktivierung > Menge-Eingabefeld leeren
                if (isChecked === false) {
                    $inputElement.val('');
                }
            });

            // Aktuelle Auswahl (aktive Checkboxen und Mengen) in Userkonfiguration speichern
            $.ajax({
                type: 'POST',
                url: me.ajaxBaseUrl + '&cmd=set',
                data: $(me.selector.dataTableForm).serializeArray()
            });
        },

        /**
         * Aktuelle Auswahl zurücksetzen/löschen
         */
        resetSelection: function () {
            $.ajax({
                type: 'POST',
                url: me.ajaxBaseUrl + '&cmd=reset',
                success: function () {
                    me.loadSelection();
                }
            });
        },

        /**
         * EventHandler für QuickAdd-Button
         *
         * @param $element Angeklickter QuickAdd-Button als jQuery-Element
         */
        quickAddArticle: function ($element) {
            var quantity, confirmResult;
            var id = $element.data('id');
            var insertUrl = $element.data('insert-url');
            var $quantityInput = $('#articlematrix-quantity-' + id);

            // Menge aus Eingabefeld an Einfügen-URL hängen
            if ($quantityInput.length > 0) {
                quantity = $quantityInput.val();
                if (quantity !== '') {
                    insertUrl += '&menge=' + quantity;
                }
            }

            confirmResult = confirm('Soll der Eintrag wirklich eingefügt werden?');
            if (confirmResult === true) {
                window.location.href = insertUrl;
            }
        }
    };

    return {
        init: me.init
    };

}(jQuery);

$(document).ready(function () {
    if ($('#kundeartikelpreise').length === 0 &&
        $('#lieferantartikelpreise').length === 0) {
        return;
    }

    ArticleMatrixSelection.init();
});
