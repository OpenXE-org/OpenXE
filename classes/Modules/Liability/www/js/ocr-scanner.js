var OcrScanner = function ($) {
    "use strict";

    var me = {

        elem: { },

        /** @property {Integer} Verbindlichkeit-ID */
        liabilityId: null,

        init: function () {
            // Benötigte Elemente selektieren
            me.elem.$overlayContainer = $('#tabs');
            me.elem.$dataContainer = $('#ocr-scanner');
            me.elem.$startScanButton = $('#ocr-start-button');
            me.elem.$scanResultDialog = $('#ocr-result-dialog');
            me.elem.$settingsButton = $('#ocr-settings-button');
            me.elem.$settingsDialog = $('#ocr-settings-dialog');
            me.elem.$apiRegistrationDialog = $('#ocr-api-registration-dialog');
            me.elem.$invoiceNumberTerm = $('input#setting-invoice-number-term');
            me.elem.$invoiceNumberDirection = $('select#setting-invoice-number-direction');
            me.elem.$invoiceDateTerm = $('input#setting-invoice-date-term');
            me.elem.$invoiceDateDirection = $('select#setting-invoice-date-direction');
            me.elem.$totalGrossTerm = $('input#setting-total-gross-term');
            me.elem.$totalGrossDirection = $('select#setting-total-gross-direction');
            me.elem.$registrationMailInput = $('input#scanbot-registration-mail');
            me.elem.$registrationNameInput = $('input#scanbot-registration-name');
            me.elem.$registrationErrorText = $('#ocr-api-registration-error');

            // ID der Verbindlichkeit einlesen
            me.liabilityId = parseInt(me.elem.$dataContainer.data('liability'));
            if (isNaN(me.liabilityId) || me.liabilityId === 0) {
                console.error('OcrScanner: Verbindlichkeit-ID #' + me.liabilityId + ' ungültig.');
                return;
            }

            // EventListener registrieren
            me.initEventListener();

            // Dialog einrichten
            me.initSettingsDialog();
            me.initResultDialog();
            me.initRegistrationDialog();
        },

        /**
         * Registiert benötigte EventListener
         */
        initEventListener: function () {

            // OCR-Einstellungen-Dialog öffnen
            me.elem.$settingsButton.on('click', function (e) {
                e.preventDefault();
                me.getSettings();
            });

            // OCR-Erkennungs-Scan starten
            me.elem.$startScanButton.on('click', function (e) {
                e.preventDefault();
                me.scanFile();
            });
        },

        /**
         * OCR-Einstellungen-Dialog einrichten
         */
        initSettingsDialog: function () {
            me.elem.$settingsDialog.dialog({
                modal: true,
                minWidth: 500,
                closeOnEscape: false,
                autoOpen: false,
                buttons: [
                    {
                        text: 'Abbrechen',
                        click: function () {
                            $(this).dialog('close');
                        }
                    },
                    {
                        text: 'Daten übernehmen',
                        click: function () {
                            me.saveSettings();
                        }
                    }
                ],
                open: function () {
                    // Modal-Hintergrund etwas durchsichtiger machen
                    me.setLighterModalBackground();
                }
            });
        },

        /**
         * Scan-Ergebnis-Dialog einrichten
         */
        initResultDialog: function () {
            me.elem.$scanResultDialog.dialog({
                modal: true,
                minWidth: 400,
                closeOnEscape: false,
                autoOpen: false,
                title: 'Erkennungsergebnisse',
                buttons: [
                    {
                        text: 'Abbrechen',
                        click: function () {
                            $(this).dialog('close');
                        }
                    },
                    {
                        text: 'Daten übernehmen',
                        click: function () {
                            me.confirmResults();
                            $(this).dialog('close');
                        }
                    }
                ],
                open: function () {
                    // Modal-Hintergrund etwas durchsichtiger machen
                    me.setLighterModalBackground();
                }
            });
        },

        /**
         * Scan-Ergebnisse in Verbindlichkeit übernehmen + Ergebnisse zurückmelden
         *
         * Nur Ergebnisse zurückmelden die auch verändert wurden
         */
        confirmResults: function () {
            var confirmResult = {
                resultHandle: null,
                invoiceDate: null,
                invoiceNumber: null,
                totalAmount: null,
                totalTax: null
            };
            var ocrTax = $('#ocr-val-steuer').val();
            var ocrDate = $('#ocr-val-datum').val();
            var ocrTotal = $('#ocr-val-gesamt').val();
            var ocrNumber = $('#ocr-val-nummer').val();
            var ocrTaxUnchanged = $('#ocr-unchanged-steuer').val();
            var ocrDateUnchanged = $('#ocr-unchanged-datum').val();
            var ocrTotalUnchanged = $('#ocr-unchanged-gesamt').val();
            var ocrNumberUnchanged = $('#ocr-unchanged-nummer').val();
            var ocrResultHandle = $('#ocr-val-result-handle').val();

            if (typeof ocrDate !== 'undefined' && ocrDate !== '') {
                $('#rechnungsdatum').val(ocrDate);
            }
            if (typeof ocrTotal !== 'undefined' && ocrTax !== '') {
                $('#summenormal').val(ocrTax);
            }
            if (typeof ocrTotal !== 'undefined' && ocrTotal !== '') {
                $('#betrag').val(ocrTotal);
            }
            if (typeof ocrNumber !== 'undefined' && ocrNumber !== '') {
                $('#rechnung').val(ocrNumber);
            }
            if (typeof ocrCurrency !== 'undefined' && ocrCurrency !== '') {
                $('#waehrung').val(ocrCurrency);
            }

            // Nur geänderte Daten zurückmelden
            if (ocrDate !== ocrDateUnchanged) {
                confirmResult.invoiceDate = ocrDate;
            }
            if (ocrTax !== ocrTaxUnchanged) {
                confirmResult.totalTax = ocrTax;
            }
            if (ocrTotal !== ocrTotalUnchanged) {
                confirmResult.totalAmount = ocrTotal;
            }
            if (ocrNumber !== ocrNumberUnchanged) {
                confirmResult.invoiceNumber = ocrNumber;
            }
            if (typeof ocrResultHandle !== 'undefined' && ocrResultHandle !== '') {
                confirmResult.resultHandle = ocrResultHandle;
            }

            $.ajax({
                url: 'index.php?module=verbindlichkeit&action=edit&cmd=ajax-confirm-ocr-results&id=' + me.liabilityId,
                type: 'POST',
                data: confirmResult,
                dataType: 'json',
                success: function () {}
            });
        },

        /**
         * Zustimmung-zur-API-Nutzung-Dialog einrichten
         */
        initRegistrationDialog: function () {
            me.elem.$registrationErrorText.hide();
            me.elem.$apiRegistrationDialog.dialog({
                modal: true,
                minWidth: 400,
                closeOnEscape: false,
                autoOpen: false,
                title: 'Registrierung',
                buttons: [
                    {
                        text: 'Abbrechen',
                        click: function () {
                            $(this).dialog('close');
                        }
                    },
                    {
                        text: 'Zustimmen',
                        click: function () {
                            me.registerApiAccount();
                        }
                    }
                ]
            });
        },

        /**
         * Datei scannen und Ergebnis-Dialog füllen
         */
        scanFile: function () {
            me.elem.$overlayContainer.loadingOverlay();

            $.ajax({
                url: 'index.php?module=verbindlichkeit&action=edit&cmd=ajax-ocr-scan-file&id=' + me.liabilityId,
                type: 'POST',
                success: function (result) {

                    if (typeof result.data === 'undefined' || result.success === false) {
                        me.elem.$overlayContainer.loadingOverlay('remove');
                        return;
                    }

                    var valueFound = 0;
                    me.elem.$scanResultDialog.find('.ocr-row').hide();

                    if (typeof result.data.invoice_number !== 'undefined' && result.data.invoice_number !== null) {
                        me.elem.$scanResultDialog.find('#ocr-row-nummer').show();
                        me.elem.$scanResultDialog.find('#ocr-val-nummer').val(result.data.invoice_number);
                        me.elem.$scanResultDialog.find('#ocr-unchanged-nummer').val(result.data.invoice_number);
                        valueFound++;
                    }
                    if (typeof result.data.invoice_date !== 'undefined' && result.data.invoice_date !== null) {
                        me.elem.$scanResultDialog.find('#ocr-row-datum').show();
                        me.elem.$scanResultDialog.find('#ocr-val-datum').val(result.data.invoice_date);
                        me.elem.$scanResultDialog.find('#ocr-unchanged-datum').val(result.data.invoice_date);
                        valueFound++;
                    }
                    if (typeof result.data.total_amount !== 'undefined' && result.data.total_amount !== null) {
                        var moneyValue = me.formatMoneyValue(result.data.total_amount);
                        me.elem.$scanResultDialog.find('#ocr-row-gesamt').show();
                        me.elem.$scanResultDialog.find('#ocr-val-gesamt').val(moneyValue);
                        me.elem.$scanResultDialog.find('#ocr-unchanged-gesamt').val(moneyValue);
                        valueFound++;
                    }
                    if (typeof result.data.total_tax !== 'undefined' && result.data.total_tax !== null) {
                        var taxValue = me.formatMoneyValue(result.data.total_tax);
                        me.elem.$scanResultDialog.find('#ocr-row-steuer').show();
                        me.elem.$scanResultDialog.find('#ocr-val-steuer').val(taxValue);
                        me.elem.$scanResultDialog.find('#ocr-unchanged-steuer').val(taxValue);
                        valueFound++;
                    }
                    if (typeof result.data.currency !== 'undefined' && result.data.currency !== null) {
                        me.elem.$scanResultDialog.find('#ocr-row-waehrung').show();
                        me.elem.$scanResultDialog.find('#ocr-val-waehrung').val(result.data.currency);
                        valueFound++;
                    }

                    me.elem.$scanResultDialog.find('#ocr-val-result-handle').val(result.data.result_handle);

                    if (valueFound > 0) {
                        me.elem.$scanResultDialog.find('#ocr-row-empty-result').hide();
                    }
                    if (valueFound === 0) {
                        me.elem.$scanResultDialog.find('#ocr-row-empty-result').show();
                        me.elem.$scanResultDialog.dialog("option", "buttons", {
                            'Abbrechen': function () {
                                $(this).dialog('close');
                            }
                        })
                    }

                    me.elem.$scanResultDialog.dialog('open');
                    me.elem.$overlayContainer.loadingOverlay('remove');
                },
                error: function (jqXhr) {
                    var data;
                    if (typeof jqXhr.responseJSON !== 'undefined') {
                        data = jqXhr.responseJSON;
                    }
                    if (typeof data.apikey_available !== 'undefined' && data.apikey_available === false) {
                        me.elem.$apiRegistrationDialog.dialog('open');
                    }
                    if (typeof data.error !== 'undefined') {
                        alert(data.error);
                    }

                    window.setTimeout(function () {
                        me.elem.$overlayContainer.loadingOverlay('remove');
                    }, 10);
                }
            });
        },

        /**
         * Vorabprüfung ob OCR bereit zum Scannen
         *
         * "Scan"-Button wird entsprechend dem Ergebnis disabled oder enabled.
         */
        checkPreConditions: function () {
            $.ajax({
                url: 'index.php?module=verbindlichkeit&action=edit&cmd=ajax-check-ocr-conditions&id=' + me.liabilityId,
                type: 'POST',
                success: function (data) {
                    if (data.success === true) {
                        me.elem.$startScanButton.prop('disabled', '');
                    } else {
                        me.elem.$startScanButton.prop('disabled', 'disabled');
                    }
                },
                error: function () {
                    me.elem.$startScanButton.prop('disabled', 'disabled');
                }
            });
        },

        /**
         * Einstellungs-Dialog befüllen
         */
        getSettings: function () {
            $.ajax({
                url: 'index.php?module=verbindlichkeit&action=edit&cmd=ajax-get-ocr-settings&id=' + me.liabilityId,
                type: 'POST',
                success: function (data) {
                    if (typeof data.success === 'undefined' || data.success !== true) {
                        alert('Unbekannter Fehler beim Abrufen der Einstellungen. #1');
                        return;
                    }

                    if (typeof data.invoice_number !== 'undefined') {
                        me.elem.$invoiceNumberTerm.val(data.invoice_number.term);
                        me.elem.$invoiceNumberDirection.val(data.invoice_number.direction);
                    }
                    if (typeof data.invoice_date !== 'undefined') {
                        me.elem.$invoiceDateTerm.val(data.invoice_date.term);
                        me.elem.$invoiceDateDirection.val(data.invoice_date.direction);
                    }
                    if (typeof data.total_gross !== 'undefined') {
                        me.elem.$totalGrossTerm.val(data.total_gross.term);
                        me.elem.$totalGrossDirection.val(data.total_gross.direction);
                    }
                    me.elem.$settingsDialog.dialog('open');
                },
                error: function (jqXHR) {
                    if (typeof jqXHR.responseJSON.error !== 'undefined') {
                        alert('Fehler: ' + jqXHR.responseJSON.error);
                    } else {
                        alert('Unbekannter Fehler beim Abrufen der Einstellungen. #2');
                    }
                }
            });
        },

        /**
         * OCR-Einstellungen speichern
         */
        saveSettings: function () {
            var data = { };
            data.invoice_number = {
                term: me.elem.$invoiceNumberTerm.val(),
                direction: me.elem.$invoiceNumberDirection.val()
            };
            data.invoice_date = {
                term: me.elem.$invoiceDateTerm.val(),
                direction: me.elem.$invoiceDateDirection.val()
            };
            data.total_gross = {
                term: me.elem.$totalGrossTerm.val(),
                direction: me.elem.$totalGrossDirection.val()
            };

            $.ajax({
                url: 'index.php?module=verbindlichkeit&action=edit&cmd=ajax-save-ocr-settings&id=' + me.liabilityId,
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function () {
                    me.elem.$settingsDialog.dialog('close');
                    me.checkPreConditions();
                }
            });
        },

        /**
         * API-Registrierung durchführen
         */
        registerApiAccount: function () {
            var data = { };
            data.mail = me.elem.$registrationMailInput.val();
            data.name = me.elem.$registrationNameInput.val();

            me.elem.$apiRegistrationDialog.parent().loadingOverlay();

            $.ajax({
                url: 'index.php?module=verbindlichkeit&action=edit&cmd=ajax-register-api-account&id=' + me.liabilityId,
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function (result) {
                    if (typeof result.success === 'undefined') {
                        alert('Unbekannter Fehler bei der Registrierung.');
                    }

                    // Im Fehlerfall abbrechen
                    if (result.success === false) {
                        me.elem.$registrationErrorText.html(result.error).show();
                        me.elem.$apiRegistrationDialog.parent().loadingOverlay('remove');
                        return;
                    }

                    // Im Erfolgsfall: Seite neuladen; da direkter Aufruf der API zu einem Fehler führt.
                    // Account benötigt ein paar Sekunden bevor er benutzt werden kann... zzz
                    window.location.href = 'index.php?module=verbindlichkeit&action=edit&id=' + me.liabilityId;
                }
            });
        },

        /**
         * Modal-Hintergrund etwas durchsichtiger machen
         */
        setLighterModalBackground: function () {
            $('div.ui-widget-overlay.ui-front').css({
                'opacity': '.35',
                'filter': 'Alpha(Opacity=35)'
            });
        },

        /**
         * Formatiert einen Geldbetrag
         *
         * @param {String} value
         *
         * @return {String}
         */
        formatMoneyValue: function (value) {
            var result = value;
            if (typeof value === 'undefined') {
                console.warn('value is undefined');
                return '0,00';
            }

            var decimalSep = value.charAt(value.length - 3);
            if (decimalSep === '.' || decimalSep === ',') {
                // Erstmal alle Dezimal- und Tausendertrenner entfernen
                result = value.replace('.', '').replace(',', '');
                // Komma als Dezimaltrenner einfügen
                result = result.slice(0, -2) + ',' + result.slice(-2);
            }

            return result;
        }
    };

    return {
        init: me.init,
        checkPreConditions: me.checkPreConditions
    };

}(jQuery);

$(document).ready(function () {
    if ($('#ocr-scanner').length === 0) {
        return;
    }

    OcrScanner.init();
    OcrScanner.checkPreConditions();
});
