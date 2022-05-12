var WBase64 = {_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(e){var t="";var n,r,i,s,o,u,a;var f=0;e=WBase64._utf8_encode(e);while(f<e.length){n=e.charCodeAt(f++);r=e.charCodeAt(f++);i=e.charCodeAt(f++);s=n>>2;o=(n&3)<<4|r>>4;u=(r&15)<<2|i>>6;a=i&63;if(isNaN(r)){u=a=64}else if(isNaN(i)){a=64}t=t+this._keyStr.charAt(s)+this._keyStr.charAt(o)+this._keyStr.charAt(u)+this._keyStr.charAt(a)}return t},decode:function(e){var t="";var n,r,i;var s,o,u,a;var f=0;e=e.replace(/[^A-Za-z0-9\+\/\=]/g,"");while(f<e.length){s=this._keyStr.indexOf(e.charAt(f++));o=this._keyStr.indexOf(e.charAt(f++));u=this._keyStr.indexOf(e.charAt(f++));a=this._keyStr.indexOf(e.charAt(f++));n=s<<2|o>>4;r=(o&15)<<4|u>>2;i=(u&3)<<6|a;t=t+String.fromCharCode(n);if(u!=64){t=t+String.fromCharCode(r)}if(a!=64){t=t+String.fromCharCode(i)}}t=WBase64._utf8_decode(t);return t},_utf8_encode:function(e){e=e.replace(/\r\n/g,"\n");var t="";for(var n=0;n<e.length;n++){var r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r)}else if(r>127&&r<2048){t+=String.fromCharCode(r>>6|192);t+=String.fromCharCode(r&63|128)}else{t+=String.fromCharCode(r>>12|224);t+=String.fromCharCode(r>>6&63|128);t+=String.fromCharCode(r&63|128)}}return t},_utf8_decode:function(e){var t="";var n=0;var r=c1=c2=0;while(n<e.length){r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r);n++}else if(r>191&&r<224){c2=e.charCodeAt(n+1);t+=String.fromCharCode((r&31)<<6|c2&63);n+=2}else{c2=e.charCodeAt(n+1);c3=e.charCodeAt(n+2);t+=String.fromCharCode((r&15)<<12|(c2&63)<<6|c3&63);n+=3}}return t}};

var UiTranslation = (function ($, base64) {

    var me = {

        storage: {
            translationModeDisabled: true,
            translationModePaused: true,
            currentTranslationElement: null,
            currentTranslationLanguage: null
        },

        elem: {
            $translationItems: null,
            $itemOverlay: null,
            $controlOverlay: null,
            $languageSelect: null,
            $startButton: null,
            $pauseButton: null,
            $closeButton: null
        },

        init: function () {
            me.initSpecialElements();

            me.elem.$translationItems = $('.edittranslation');
            if (me.elem.$translationItems.length > 0) {
                me.storage.translationModeDisabled = false;
                me.storage.translationModePaused = false;
            }

            me.initItemOverlay();

            me.initControlOverlay();
            me.fetchLanguageList();

            if (me.storage.translationModeDisabled === false) {
                me.resumeTranslationMode();
            }
        },

        /**
         * Übersetzungs-Overlay erstellen
         */
        initControlOverlay: function () {
            me.elem.$controlOverlay = $('<div id="ui-translation-overlay"></div>');
            var content =
                '<fieldset>' +
                '<legend>Oberflächen-Übersetzung</legend>' +
                '<div class="line"><label>Sprache auswählen: ' +
                '<select id="ui-translation-language-select"></select>' +
                '</label></div>' +
                '<div class="line">';
            if (me.storage.translationModeDisabled) {
                content += '<a id="ui-translation-start-button" class="button" href="#">Übersetzung starten</a>';
            } else {
                content += '<a id="ui-translation-pause-button" class="button" href="#">Übersetzung pausieren</a>';
                content += '<a id="ui-translation-close-button" class="button" href="#">Übersetzung beenden</a>';
            }
            content += '</div></fieldset>';

            // Overlay erzeugen und einblenden
            me.elem.$controlOverlay
              .html(content)
              .appendTo('body')
              .show()
              .draggable({
                  handle: 'legend'
              });

            /*
             * Events für Overlay-Elemente registrieren
             */

            me.elem.$languageSelect = $('#ui-translation-language-select');
            me.elem.$startButton = $('#ui-translation-start-button');
            me.elem.$closeButton = $('#ui-translation-close-button');
            me.elem.$pauseButton = $('#ui-translation-pause-button');

            me.elem.$languageSelect.on('change', function () {
                if (me.storage.translationModeDisabled === true) {
                    return;
                }
                me.changeLanguage($(this).val());
            });

            me.elem.$startButton.on('click', function (e) {
                e.preventDefault();
                var selectedLanguage = me.elem.$languageSelect.val();
                me.startTranslationMode(selectedLanguage);
            });

            me.elem.$closeButton.on('click', function (e) {
                e.preventDefault();
                me.stopTranslationMode();
            });

            me.elem.$pauseButton.on('click', function (e) {
                e.preventDefault();
                if (me.storage.translationModePaused === true) {
                    me.resumeTranslationMode();
                } else {
                    me.pauseTranslationMode();
                }
            });
        },

        /**
         * Kleines Overlay zum Übersetzen erstellen
         */
        initItemOverlay: function () {
            var content =
                '<div id="ui-translation-item">' +
                '<div class="line">' +
                '<input id="ui-translation-item-input" type="text" size="30" value="">' +
                '<input id="ui-translation-item-hidden" type="hidden" value="">' +
                '</div>' +
                '<div class="line">' +
                '<a id="ui-translation-item-save" class="button btnGreen" href="#">Speichern</a>' +
                '<a id="ui-translation-item-cancel" class="button" href="#">Abbrechen</a>' +
                '</div>' +
                '</div>';

            me.elem.$itemOverlay = $(content).appendTo('body').hide();

            me.elem.$itemOverlay
              .on('keypress', '#ui-translation-item-input', function (e) {
                  if (e.which === 13) { // Enter-Taste
                      me.saveTranslation();
                  }
              })
              .on('click', '#ui-translation-item-save', function (e) {
                  e.preventDefault();
                  me.saveTranslation();
              })
              .on('click', '#ui-translation-item-cancel', function (e) {
                  e.preventDefault();
                  me.elem.$itemOverlay.hide();
              });
        },

        initSpecialElements: function () {
            $('button, input[type="button"], input[type="submit"]').each(function () {
                var val = $(this).val();
                if (val.indexOf('****') > -1) {
                    val = val.replace('****', '');
                    var content =
                        '<span>' +
                        '<input type="hidden" class="wawision_uebersetzung_text" value="' + base64.encode(val) + '">' +
                        '<input type="hidden" class="wawision_uebersetzung_type" value="">' +
                        '<input type="hidden" class="wawision_uebersetzung_elem" value="' + base64.encode(val) + '">' +
                        '</span>';

                    $(this)
                        .val(val)
                        .addClass('edittranslation')
                        .css('border', '1px dashed red')
                        .after(content);
                }
            });
        },

        /**
         * Verfügbare Sprachen abrufen
         */
        fetchLanguageList: function () {
            $.ajax({
                url: 'index.php?module=wawision_uebersetzung&action=list&cmd=fetch-languages',
                method: 'post',
                dataType: 'json',
                success: function (data) {
                    if (typeof data.languages === 'undefined') {
                        throw 'Sprachenliste konnte nicht geladen werden.';
                    }
                    if (typeof data.selected === 'undefined') {
                        throw 'Zielsprache konnte nicht geladen werden.';
                    }

                    // Sprachen-Dropdown füllen
                    data.languages.forEach(function (language) {
                        var $option = $('<option value="' + language + '">' + language + '</option>');
                        if (language === data.selected) {
                            $option.prop('selected', 'selected');
                        }
                        me.elem.$languageSelect.append($option);
                    });

                    // Aktuell eingestellte Sprache merken
                    me.storage.currentTranslationLanguage = data.selected;
                }
            });
        },

        /**
         * Übersetzungsmodus aktivieren
         */
        resumeTranslationMode: function () {

            me.elem.$translationItems
              .on('mouseover', function () {
                  me.onHoverTranslationItem(this);
              })
              // .on('mouseout', function (e) {
              //     if (e.relatedTarget.id !== 'ui-translation-item') {
              //         me.elem.$itemOverlay.hide();
              //     }
              // })
              .css('border', '1px dashed red');

            me.elem.$pauseButton.text('Übersetzung pausieren');
            me.storage.translationModePaused = false;
        },

        /**
         * Übersetzung nur pausieren; aber nicht beenden
         */
        pauseTranslationMode: function () {
            me.elem.$translationItems
            //.off('mouseout')
              .off('mouseover')
              .css('border', '1px solid transparent');

            me.elem.$itemOverlay.hide();

            me.elem.$pauseButton.text('Übersetzung fortsetzen');
            me.storage.translationModePaused = true;
        },

        /**
         * Übersetzungsmodus starten
         */
        startTranslationMode: function (selectedLanguage) {
            $.ajax({
                url: 'index.php?module=wawision_uebersetzung&action=list&cmd=start-translation',
                method: 'post',
                data: {selected: selectedLanguage},
                dataType: 'json',
                success: function (data) {
                    if (typeof data.success === 'undefined') {
                        return;
                    }
                    if (data.success === false) {
                        alert('FEHLER: ' + data.error);
                    }
                    if (data.success === true) {
                        window.location.reload(true);
                    }
                }
            });
        },

        /**
         * Übersetzungsmodus beenden
         */
        stopTranslationMode: function () {
            $.ajax({
                url: 'index.php?module=wawision_uebersetzung&action=list&cmd=stop-translation',
                method: 'post',
                dataType: 'json',
                success: function (data) {
                    if (typeof data.success !== 'undefined' && data.success === true) {
                        // Zurück zum Übersetzungs-Modul
                        window.location.href = 'index.php?module=wawision_uebersetzung&action=list';
                    }
                }
            });
        },

        /**
         * Die zu übersetzende Sprache wechseln
         *
         * @param {string} selectedLanguage
         */
        changeLanguage: function (selectedLanguage) {
            me.pauseTranslationMode();
            $.ajax({
                url: 'index.php?module=wawision_uebersetzung&action=list&cmd=change-language',
                method: 'post',
                data: {selected: selectedLanguage},
                dataType: 'json',
                success: function (data) {
                    if (typeof data.success !== 'undefined' && data.success === true) {
                        window.location.reload(true);
                    }
                }
            });
        },

        /**
         * Beim Hovern eines Übersetzungs-Elements
         *
         * @param {HTMLElement} element
         */
        onHoverTranslationItem: function (element) {
            me.storage.currentTranslationElement = element;

            // Aktuelle Übersetzung auslesen
            var $element = $(element);
            var currentItemTranslation = $element.text(); // Span-Element
            if (currentItemTranslation === '' || currentItemTranslation === null) {
                currentItemTranslation = $element.val(); // Input-Element
            }

            // Aktuelle Übersetzungen in Eingabefelder schreiben
            $('#ui-translation-item-input').val(currentItemTranslation);
            $('#ui-translation-item-hidden').val(currentItemTranslation);

            // Position für Übersetzung-Overlay berechnen
            var windowWidth = $(window).width();
            var currentItemOffset = $element.offset();
            var currentItemRightPos = currentItemOffset.left + 300;
            var currentItemNewLeftPos = currentItemRightPos > windowWidth ? windowWidth - 320 : currentItemOffset.left;
            var currentItemNewTopPos = currentItemOffset.top - 16;

            // Übersetzung-Overlay anzeigen und positionieren
            me.elem.$itemOverlay.show()
              .css('top', currentItemNewTopPos)
              .css('left', currentItemNewLeftPos);
        },

        /**
         * Übersetzung abspeichern
         */
        saveTranslation: function () {
            if (me.storage.translationModeDisabled === true) {
                return;
            }
            if (me.storage.translationModePaused === true) {
                return;
            }
            if (me.storage.currentTranslationElement === null) {
                return;
            }
            if (me.storage.currentTranslationLanguage === null) {
                return;
            }

            var translationNew = $('#ui-translation-item-input').val();
            var translationOriginal = $('#ui-translation-item-hidden').val();
            if (translationNew === translationOriginal) {
                return;
            }

            var $dataContainer = $(me.storage.currentTranslationElement).next('span');
            var dataType = $dataContainer.find('input.wawision_uebersetzung_type').first().val();
            var dataText = $dataContainer.find('input.wawision_uebersetzung_text').first().val();
            var dataElem = $dataContainer.find('input.wawision_uebersetzung_elem').first().val();

            $.ajax({
                url: 'index.php?module=wawision_uebersetzung&action=list&cmd=savetext',
                method: 'post',
                dataType: 'json',
                data: {
                    orig: translationOriginal,
                    neu: translationNew,
                    type: dataType,
                    text: dataText,
                    elem: dataElem
                },
                success: function (data) {
                    if (typeof data.success === 'undefined') {
                        throw 'Unbekannter Fehler beim Speichern';
                    }
                    if (data.success === true) {
                        if (me.storage.currentTranslationElement !== null) {
                            $(me.storage.currentTranslationElement).html(data.text);
                            $(me.storage.currentTranslationElement).val(data.text);
                        }
                        me.elem.$itemOverlay.hide();
                    }
                }
            });
        }
    };

    return {
        init: me.init,
        pauseTranslation: me.pauseTranslationMode,
        resumeTranslation: me.resumeTranslationMode,
        startTranslation: me.startTranslationMode,
        stopTranslation: me.stopTranslationMode,
        changeLanguage: me.changeLanguage
    };

})(jQuery, WBase64);

$(document).ready(UiTranslation.init);
