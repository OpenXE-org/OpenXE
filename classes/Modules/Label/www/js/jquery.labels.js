/**
 * ## Initialisierung
 *
 * ### Initialisierung über HTML
 *
 * ```html
 * <span
 *     class="label-loader"
 *     data-label-reference-table="wiedervorlage"
 *     data-label-reference-id="123"
 * ></span>
 * ```
 *
 * ### Initialisierung über Javascript
 *
 * ```html
 * <span id="element"></span>
 * ```
 *
 * ```javascript
 * $('#element').labels({
 *     referenceTable: 'wiedervorlage',
 *     referenceId: 123
 * });
 * ```
 *
 * ### Initialisierung gemischt
 *
 * ```html
 * <span id="element" data-label-reference-table="wiedervorlage"></span>
 * ```
 *
 * ```javascript
 * $('#element').labels({
 *     referenceId: 123
 * });
 * ```
 *
 * ## Initialisierungsoptionen
 *
 * * referenceTable: (Pflichtangabe)
 * * referenceId: (Pflichtangabe)
 * * trigger: CSS-ID-Selektor zum Öffnen des LabelManager-Overlays (Optional; Default: `null`)
 * * autoload: Labels bei Initialisierung direkt vom Server laden? (Optional; Default: `true`)
 * * compact: Labels kompakt darstellen? (Optional; Default: `false`)
 *
 * ## API
 *
 * ### API-Objekt holen
 *
 * ```javascript
 * var api = $('#element').labelsApi();
 * ```
 *
 * ### API-Methoden
 *
 * * `api.showOverlay()` Label-Overlay einblenden
 * * `api.hideOverlay()` Label-Overlay ausblenden
 */


/**
 * Modul zum Rendern von Labeln
 *
 * @type {{render: *}}
 */
var LabelRenderer = (function ($) {
    'use strict';

    var me = {

        /**
         * @param {Object} collection
         */
        render: function (collection) {

            Object.keys(collection).forEach(function (key) {
                var labels = collection[key];
                if (!Array.isArray(labels)) {
                    console.error('Can not render label data. Wrong data type.', labels);
                    return;
                }

                // Alle Label-Container mit der gleichen Referenz finden
                // Bsp. key = 'labels-wiedervorlage-123'
                var targetSelector = '.label-container.' + key;
                var $target = $(targetSelector);

                // Container-Element für Labels vorhanden?
                if ($target.length === 0) {
                    console.error('Can not find label target for "' + key + '".');
                    return;
                }

                $target.html('');

                // Label-Elemente erzeugen und in Label-Container stecken
                labels.forEach(function (item) {
                    if (!me.hasTypeLabel($target, item.type)) {
                        var $label = me.createLabelElement(item.type, item.title, item.bgcolor);
                        $label.appendTo($target);
                    }
                });

                $target.addClass(key);
            });
        },

        /**
         * @param {string} baseColor Hex color value
         *
         * @return {object}
         */
        calculateLabelColors: function (baseColor) {
            var altColor, colors;
            var brightness = me.calculateColorBrightness(baseColor);
            if (brightness === 'dark') {
                altColor = me.calculateColorVariant(baseColor, 35);
                colors = {
                    'background-color': altColor,
                    'border-color': baseColor,
                    'color': baseColor
                };
            } else {
                altColor = me.calculateColorVariant(baseColor, -35);
                colors = {
                    'background-color': baseColor,
                    'border-color': altColor,
                    'color': altColor
                };
            }

            return colors;
        },

        /**
         * @param {string} type
         * @param {string} title
         * @param {string} color Hex-Color (Format: #336699)
         *
         * @return {jQuery}
         */
        createLabelElement: function (type, title, color) {
            var altColor, bgColor, borderColor, textColor;
            var brightness = me.calculateColorBrightness(color);
            if (brightness === 'dark') {
                altColor = me.calculateColorVariant(color, 35);
                bgColor = altColor;
                textColor = color;
                borderColor = color;
            } else {
                altColor = me.calculateColorVariant(color, -35);
                bgColor = color;
                textColor = altColor;
                borderColor = altColor;
            }

            var $label = $('<span class="label"></span>');
            $label.addClass('label-manager-type-' + type);
            $label.css('background-color', bgColor);
            $label.css('border-color', borderColor);
            $label.css('color', textColor);
            $label.data('labelType', type);
            $label.attr('title', title);

            var $labelTextNormal = $('<span>').addClass('label-text-normal');
            $labelTextNormal.html(title).appendTo($label);

            var $labelTextCompact = $('<span>').addClass('label-text-compact');
            $labelTextCompact.html('&nbsp;').appendTo($label);

            return $label;
        },

        /**
         * @param {jQuery} $element
         * @param {string} type
         *
         * @return {boolean}
         */
        hasTypeLabel: function ($element, type) {
            var $result = $element.find('.label-manager-type-' + type);

            return $result.length > 0;
        },

        /**
         * @param {string} hexColor
         *
         * @return {string} [light|dark]
         */
        calculateColorBrightness: function(hexColor) {
            var rgbArr = me.convertHexColorToRgb(hexColor);
            var r = parseInt(rgbArr[0], 10);
            var g = parseInt(rgbArr[1], 10);
            var b = parseInt(rgbArr[2], 10);
            var yiq = (r * 299 + g * 587 + b * 114) / 1000;

            return (yiq >= 128) ? 'light' : 'dark';
        },

        /**
         * @param {string} hexColor
         * @param {number} percent
         *
         * @return {string}
         */
        calculateColorVariant: function(hexColor, percent) {
            var rgbArr = me.convertHexColorToRgb(hexColor);
            var difference = parseInt(percent * 2.55, 10);
            var r = parseInt(rgbArr[0] + difference, 10);
            var g = parseInt(rgbArr[1] + difference, 10);
            var b = parseInt(rgbArr[2] + difference, 10);

            // Sicherstellen dass RGB-Werte zwischen 0 und 255 liegen
            r = Math.min(Math.max(r, 0), 255);
            g = Math.min(Math.max(g, 0), 255);
            b = Math.min(Math.max(b, 0), 255);

            var rHex = r.toString(16);
            if (rHex.length === 1) {
                rHex = '0' + rHex;
            }
            var gHex = g.toString(16);
            if (gHex.length === 1) {
                gHex = '0' + gHex;
            }
            var bHex = b.toString(16);
            if (bHex.length === 1) {
                bHex = '0' + bHex;
            }

            return '#' + rHex + gHex + bHex;
        },

        /**
         * @param {string} hexColor
         *
         * @return {Array|null}
         */
        convertHexColorToRgb: function(hexColor) {
            var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
            hexColor = hexColor.replace(shorthandRegex, function(m, r, g, b) {
                return '#' + r + r + g + g + b + b;
            });

            var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hexColor);
            return result ? [
                parseInt(result[1], 16),
                parseInt(result[2], 16),
                parseInt(result[3], 16)
            ] : null;
        }
    };

    return {
        render: me.render,
        calculateLabelColors: me.calculateLabelColors
    };

})(jQuery);


/**
 * LabelManager-Modul
 *
 * Der LabelManager erzeugt das Overlay zum Hinzufügen/Löschen von Labels.
 *
 * @type {{init: *, showOverlay: *, hideOverlay: *, destroy: *}}
 */
var LabelManager = (function ($, LabelRendererModule) {
    'use strict';

    var me = {

        selector: {
            overlay: '#label-manager-overlay'
        },

        storage: {
            $overlay: null
        },

        init: function () {
            me.registerEvents();
        },

        destroy: function () {
            me.unregisterEvents();
        },

        registerEvents: function () {

            $(document).off('click.labelManager', '.label-manager');
            $(document).on('click.labelManager', '.label-manager', function (event) {
                event.preventDefault();
                var $trigger = $(this);
                me.showOverlayForElement($trigger);
            });

            $(document).off('change.labelManager', 'input.label-manager-type-checkbox');
            $(document).on('change.labelManager', 'input.label-manager-type-checkbox', function (event) {
                event.preventDefault();
                var $checkbox = $(this);
                var referenceTable = $checkbox.data('labelReferenceTable');
                var referenceId = $checkbox.data('labelReferenceId');
                var labelType = $checkbox.data('labelType');

                if ($checkbox.prop('checked')) {
                    me.assignLabel(labelType, referenceTable, referenceId)
                } else {
                    me.unassignLabel(labelType, referenceTable, referenceId);
                }
            });
        },

        unregisterEvents: function () {
            $(document).off('click.labelManager', '.label-manager');
            $(document).off('change.labelManager', 'input.label-manager-type-checkbox');
        },

        /**
         * @param {string} labelType
         * @param {string} referenceTable
         * @param {number} referenceId
         */
        assignLabel: function (labelType, referenceTable, referenceId) {
            if (typeof labelType !== 'string' || labelType === '') {
                throw 'LabelManager: InvalidArgument "labelType".';
            }
            if (typeof referenceTable !== 'string' || referenceTable === '') {
                throw 'LabelManager: InvalidArgument "referenceTable".';
            }
            if (typeof referenceId !== 'number' || referenceId === '' || referenceId === 0) {
                throw 'LabelManager: InvalidArgument "referenceId".';
            }

            $.ajax({
                url: 'index.php?module=ajax&action=labels&cmd=assign',
                method: 'post',
                dataType: 'json',
                data: {
                    type: labelType,
                    reference_id: referenceId,
                    reference_table: referenceTable
                },
                success: function (result) {
                    LabelRendererModule.render(result.data);
                },
                error: function (jqXhr) {
                    alert('Fehler: ' + jqXhr.responseJSON.error);
                }
            });
        },

        /**
         * @param {string} labelType
         * @param {string} referenceTable
         * @param {number} referenceId
         */
        unassignLabel: function (labelType, referenceTable, referenceId) {
            if (typeof labelType !== 'string' || labelType === '') {
                throw 'LabelManager: InvalidArgument "labelType".';
            }
            if (typeof referenceTable !== 'string' || referenceTable === '') {
                throw 'LabelManager: InvalidArgument "referenceTable".';
            }
            if (typeof referenceId !== 'number' || referenceId === '' || referenceId === 0) {
                throw 'LabelManager: InvalidArgument "referenceId".';
            }

            $.ajax({
                url: 'index.php?module=ajax&action=labels&cmd=unassign',
                method: 'post',
                dataType: 'json',
                data: {
                    type: labelType,
                    reference_id: referenceId,
                    reference_table: referenceTable
                },
                success: function (result) {
                    LabelRendererModule.render(result.data);
                },
                error: function (jqXhr) {
                    alert('Fehler: ' + jqXhr.responseJSON.error);
                }
            });
        },

        /**
         * @return {jQuery}
         */
        getOverlay: function () {
            if (typeof me.storage.$overlay === 'undefined' || me.storage.$overlay === null) {
                me.storage.$overlay = me.createOverlay();
            }

            return me.storage.$overlay;
        },

        /**
         * Label-Manager-Overlay einblenden
         *
         * @param {jQuery} $trigger
         */
        showOverlayForElement: function ($trigger) {
            if (!$trigger instanceof jQuery) {
                throw 'LabelManager: InvalidArgument "$trigger".';
            }

            var $overlay = me.getOverlay();
            var referenceTable = $trigger.data('labelReferenceTable');
            var referenceId = parseInt($trigger.data('labelReferenceId'));

            // Label-Typen laden und rendern
            me.fetchLabelTypesList(referenceTable, referenceId).then(me.renderLabelTypesList);

            // Position des Overlays setzen + Overlay einblenden
            var offset = me.calculateOverlayPosition($trigger, $overlay);
            $overlay.css({
                position: 'fixed',
                top: offset.top,
                left: offset.left,
                right: null,
                bottom: null
            });
            $overlay.show();
        },

        /**
         * @param {string} referenceTable
         * @param {number} referenceId
         * @param {number} offsetTop
         * @param {number} offsetLeft
         */
        showOverlay: function (referenceTable, referenceId, offsetTop, offsetLeft) {
            var $overlay = me.getOverlay();
            offsetTop += 0;
            offsetLeft += 0;

            // Label-Typen laden und rendern
            me.fetchLabelTypesList(referenceTable, referenceId).then(me.renderLabelTypesList);

            // Position des Overlays setzen + Overlay einblenden
            $overlay.css({
                position: 'fixed',
                top: offsetTop,
                left: offsetLeft,
                right: null,
                bottom: null
            });
            $overlay.show();
        },

        /**
         * Label-Manager-Overlay ausblenden
         */
        hideOverlay: function () {
            if (me.storage.$overlay === null) {
                $(me.selector.overlay).hide();
                return;
            }

            me.getOverlay().hide();
        },

        /**
         * Label-Manager-Overlay erzeugen
         *
         * @return {jQuery}
         */
        createOverlay: function () {
            var overlaySelector = me.selector.overlay;
            if ($(overlaySelector).length > 0) {
                return $(overlaySelector);
            }

            var overlayTemplate =
                '<header><h1>Labels<span id="label-manager-close-icon" class="icon icon-close"></span></h1></header>' +
                '<section class="content-list"></section>' +
                '<footer><a href="index.php?module=datatablelabels&action=list" ' +
                'class="button button-neutral button-block" target="_blank">' +
                'Labels erstellen/bearbeiten</a></footer>';

            var overlayIdAttr = overlaySelector.substr(1);
            var $overlay = $('<div>').attr('id', overlayIdAttr).html(overlayTemplate);

            $overlay.off('click.labelManager', '#label-manager-close-icon');
            $overlay.on('click.labelManager', '#label-manager-close-icon', function (event) {
                event.preventDefault();
                $overlay.hide();
            });

            $overlay.hide();
            $overlay.appendTo('body');
            $overlay.draggable({
                handle: 'header'
            });

            return $overlay;
        },

        /**
         * Position für Label-Manager-Overlay berechnen
         *
         * @param {jQuery} $trigger
         * @param {jQuery} $overlay
         *
         * @return {{top: number, left: number}}
         */
        calculateOverlayPosition: function ($trigger, $overlay) {
            var triggerOffset = $trigger.offset();
            var overlayWidth = $overlay.width();
            var overlayHeight = $overlay.height();
            var windowWidth = $(window).width();
            var windowHeight = $(window).height();
            var windowScrollLeft = window.pageXOffset || 0;
            var windowScrollTop = window.pageYOffset || 0;
            var triggerTop = triggerOffset.top - windowScrollTop;
            var triggerLeft = triggerOffset.left - windowScrollLeft;

            // Overlay relativ zum "Öffner"-Link platzieren
            var overlayLeft = triggerLeft + 20;
            var overlayTop = triggerTop;

            // Korrektur, falls Overlay rechts abgeschnitten werden würde
            var overlayRightBoundary = overlayLeft + overlayWidth + 20;
            if (overlayRightBoundary > windowWidth) {
                overlayLeft = windowWidth - (overlayWidth + 20);
                overlayTop += 20;
            }

            // Korrektur, falls Overlay unten abgeschnitten werden würde
            var overlayBottomBoundary = overlayTop + overlayHeight;
            if (overlayBottomBoundary > windowHeight) {
                overlayTop = windowHeight - (overlayHeight + 20);
            }

            return {top: overlayTop, left: overlayLeft};
        },

        /**
         * Liste mit Label-Typen abrufen
         *
         * @param {string} referenceTable
         * @param {number} referenceId
         *
         * @return {jqXHR}
         */
        fetchLabelTypesList: function (referenceTable, referenceId) {
            if (typeof referenceTable !== 'string' || referenceTable === '') {
                throw 'LabelManager: InvalidArgument "referenceTable".';
            }
            if (typeof referenceId !== 'number' || referenceId === '' || referenceId === 0) {
                throw 'LabelManager: InvalidArgument "referenceId".';
            }

            return $.ajax({
                url: 'index.php?module=ajax&action=labels&cmd=list',
                method: 'post',
                dataType: 'json',
                data: {
                    reference_id: referenceId,
                    reference_table: referenceTable
                }
            });
        },

        /**
         * @param {Array|object[]} labelList
         */
        renderLabelTypesList: function (labelList) {
            var $overlay = me.getOverlay();
            var $contentList = $overlay.find('section.content-list').html('').show();

            if (labelList.length === 0) {
                $contentList.html('Keine Label gefunden');
                return;
            }

            var itemHtml = '';
            var itemTemplate =
                '<div class="line">' +
                '<input type="checkbox" id="label-manager-type-{{type}}" class="label-manager-type-checkbox" {{checkedAttr}}>&nbsp;' +
                '<label for="label-manager-type-{{type}}"><span class="label-title">{{title}}</span></label>' +
                '</div>';

            labelList.forEach(function (labelItem) {
                var checkedAttr = labelItem.selected === true ? 'checked="checked"' : '';
                itemHtml = itemTemplate;
                itemHtml = itemHtml.replace('{{checkedAttr}}', checkedAttr);
                itemHtml = itemHtml.replace('{{type}}', labelItem.type);
                itemHtml = itemHtml.replace('{{type}}', labelItem.type);
                itemHtml = itemHtml.replace('{{title}}', labelItem.title);

                var $line = $(itemHtml);
                var labelColors = LabelRendererModule.calculateLabelColors(labelItem.bgcolor);
                $line.find('.label-title').css(labelColors);

                var $checkbox = $line.find('.label-manager-type-checkbox');
                $checkbox.data('labelType', labelItem.type);
                $checkbox.data('labelTitle', labelItem.title);
                $checkbox.data('labelColor', labelItem.bgcolor);
                $checkbox.data('labelReferenceTable', labelItem.referenceTable);
                $checkbox.data('labelReferenceId', labelItem.referenceId);

                $contentList.append($line);
            });

            // Höhe zurücksetzen (wird durch Draggable gesetzt)
            $overlay.css('height', 'auto');
        }
    };

    return {
        init: me.init,
        destroy: me.destroy,
        showOverlay: me.showOverlay,
        hideOverlay: me.hideOverlay
    };

})(jQuery, LabelRenderer);


/**
 * Modul zum Laden von Labels
 *
 * @type {{loadElement: *, loadAll: *}}
 */
var LabelLoader = (function ($, LabelRendererModule) {
    'use strict';

    var me = {

        /**
         * Lädt alle (noch) nicht geladenenen Labels
         */
        loadAll: function () {

            // Referenzen für benötigte Labels sammeln
            var collection = me.collectLabels();
            var collectionKeys = Object.keys(collection);
            if (collectionKeys.length === 0) {
                return; // Keine Daten vorhanden
            }

            // Label-Daten für gesammelte Labels abrufen und rendern
            me.fetchCollection(collection).then(me.renderCollection, function (jqXhr) {
                alert('LabelLoader-Fehler: ' + jqXhr.responseJSON.error);
            });
        },

        /**
         * Lädt die Labels für ein einzelnes Element
         *
         * autoload-Einstellung wird ignoriert
         *
         * @param {jQuery} $element
         */
        loadElement: function ($element) {
            if (!$element instanceof jQuery) {
                return;
            }
            if ($element.length !== 1) {
                return;
            }

            var referenceTable = $element.data('labelReferenceTable');
            var referenceId = $element.data('labelReferenceId');
            var compactData = $element.data('labelCompact');

            var compact = typeof compactData !== 'undefined' && typeof compactData === 'boolean' ? compactData : false;

            // Pflicht-Optionen vorhanden
            if (typeof referenceTable === 'undefined' || typeof referenceId === 'undefined') {
                return;
            }
            referenceId = parseInt(referenceId);
            if (isNaN(referenceId)) {
                return;
            }

            $element.addClass('labels-' + referenceTable + '-' + referenceId);
            $element.addClass('label-container');
            $element.removeClass('label-loader');

            if (compact) {
                $element.addClass('label-compact');
            }

            me.fetchLabels(referenceTable, referenceId);
        },

        /**
         * @param {string} referenceTable
         * @param {number} referenceId
         */
        fetchLabels: function (referenceTable, referenceId) {
            if (typeof referenceTable !== 'string' || referenceTable === '') {
                throw 'LabelManager: InvalidArgument "referenceTable".';
            }
            if (typeof referenceId !== 'number' || referenceId === '' || referenceId === 0) {
                throw 'LabelManager: InvalidArgument "referenceId".';
            }

            var collection = {};
            collection[referenceTable] = [referenceId];

            $.ajax({
                url: 'index.php?module=ajax&action=labels&cmd=collect',
                method: 'post',
                dataType: 'json',
                data: {
                    collection: collection
                },
                success: function (result) {
                    LabelRendererModule.render(result.data);
                },
                error: function (jqXhr) {
                    alert('Fehler: ' + jqXhr.responseJSON.error);
                }
            });
        },

        /**
         * Sammelt alle Labels die noch nicht geladen wurden
         *
         * @return {Object} Collection von Labels die geladen werden müssen
         */
        collectLabels: function () {
            var result = {};
            var $loaders = $('.label-loader');

            $loaders.each(function () {
                var $elem = $(this);
                var referenceTable = $elem.data('labelReferenceTable');
                var referenceId = $elem.data('labelReferenceId');

                if (typeof referenceTable === 'undefined' || typeof referenceId === 'undefined') {
                    return;
                }

                var autoloadData = $elem.data('labelAutoload');
                var autoload = typeof autoloadData !== 'undefined' && typeof autoloadData === 'boolean' ? autoloadData : true;
                if (!autoload) {
                    return;
                }

                // Label-Container erstellen mit eindeutiger CSS-Klasse (aber nicht Unique)
                // CSS-Klasse kann mehrmals vorkommen, in unterschiedlichen DataTables
                // mit identischen Referenz-ID und -Tabelle.
                $elem.addClass('labels-' + referenceTable + '-' + referenceId);
                $elem.addClass('label-container');
                $elem.removeClass('label-loader');

                var compactData = $elem.data('labelCompact');
                var compactStyle = typeof compactData !== 'undefined' && typeof compactData === 'boolean' ? compactData : false;
                if (compactStyle) {
                    $elem.addClass('label-compact');
                }

                // References sammeln
                if (!result.hasOwnProperty(referenceTable)) {
                    result[referenceTable] = [];
                }
                if (!me.inArray(referenceId, result[referenceTable])) {
                    result[referenceTable].push(referenceId);
                }
            });

            return result;
        },

        /**
         * @param {object} collection
         *
         * @return {jqXHR|null}
         */
        fetchCollection: function (collection) {
            return $.ajax({
                url: 'index.php?module=ajax&action=labels&cmd=collect',
                method: 'post',
                dataType: 'json',
                data: {collection: collection}
            });
        },

        /**
         * @param {object} collectionResult Das Ergebnis von me.fetchCollection()
         */
        renderCollection: function (collectionResult) {
            if (!collectionResult.hasOwnProperty('data')) {
                return;
            }

            LabelRendererModule.render(collectionResult.data);
        },

        /**
         * Prüfen ob Wert in einem Array vorkommt
         *
         * @param {number|string} value
         * @param {Array}         array
         *
         * @return {boolean}
         */
        inArray: function (value, array) {
            return array.indexOf(value) > -1;
        }
    };

    return {
        loadAll: me.loadAll,
        loadElement: me.loadElement
    };

})(jQuery, LabelRenderer);


/**
 * Labels jQuery-Plugin + LabelsApi
 *
 * Dokumentation: Siehe Dateianfang
 */
(function ($, LabelLoaderModule, LabelManagerModule) {
    'use strict';

    var LabelsApi = function ($element, options) {

        var me = {

            /** @property {Object} me.defaults Default configuration */
            defaults: {
                referenceTable: null, // Pflicht-Option
                referenceId: null, // Pflicht-Option
                autoload: true,
                compact: false,
                trigger: null
            },

            storage: {
                $trigger: null,
                $element: null,
                options: {}
            },

            /**
             * @param {jQuery} $element
             * @param {Object} options
             */
            init: function ($element, options) {
                if (typeof $element === 'undefined') {
                    throw 'LabelsApi konnte nicht initialisiert werden. Element wurde nicht übergeben.';
                }

                if ($element.length !== 1) {
                    throw 'LabelsApi konnte nicht initialisiert werden. Trigger-Element wurde nicht gefunden.';
                }

                // Optionen mit Defaults mergen
                // Reihenfolge: Javascript-Optionen überschreiben Data-Attribute
                var dataAttributesAll = $element.data();
                var dataAttributesLabel = me.filterDataAttributesByPrefix(dataAttributesAll, 'label');
                var mergedOptions = $.extend({}, me.defaults, dataAttributesLabel, options);

                // Benötigte Optionen prüfen
                if (mergedOptions.referenceTable === null) {
                    throw 'LabelsApi konnte nicht initialisiert werden. Benötigte Option \'referenceTable\' fehlt.';
                }
                if (mergedOptions.referenceId === null) {
                    throw 'LabelsApi konnte nicht initialisiert werden. Benötigte Option \'referenceId\' fehlt.';
                }
                mergedOptions.referenceId = parseInt(mergedOptions.referenceId);
                if (isNaN(mergedOptions.referenceId)) {
                    throw 'LabelsApi konnte nicht initialisiert werden. Benötigte Option \'referenceId\' ist ungültig.';
                }

                // Sicherstellen dass alle Data-Attribute gesetzt sind;
                // bei Initialisierung über jQuery-Plugin mit Options-Array.
                if (mergedOptions.hasOwnProperty('referenceTable')) {
                    $element.data('labelReferenceTable', mergedOptions.referenceTable);
                }
                if (mergedOptions.hasOwnProperty('referenceId')) {
                    $element.data('labelReferenceId', mergedOptions.referenceId);
                }
                if (mergedOptions.hasOwnProperty('autoload')) {
                    $element.data('labelAutoload', mergedOptions.autoload);
                }
                if (mergedOptions.hasOwnProperty('compact')) {
                    $element.data('labelCompact', mergedOptions.compact);
                }
                if (mergedOptions.hasOwnProperty('trigger')) {
                    $element.data('labelTrigger', mergedOptions.trigger);
                }

                LabelLoaderModule.loadElement($element);

                if (typeof mergedOptions.trigger !== 'undefined' && mergedOptions.trigger !== null) {
                    me.initTriggerElement(mergedOptions.trigger, mergedOptions.referenceTable, mergedOptions.referenceId);
                }
            },

            /**
             * @param {string} triggerSelector
             * @param {string} referenceTable
             * @param {number} referenceId
             */
            initTriggerElement: function (triggerSelector, referenceTable, referenceId) {
                var $trigger = $(triggerSelector);
                if ($trigger.length !== 1) {
                    console.warn('LabelsApi: Trigger-Element "' + triggerSelector + '" wurde nicht gefunden');
                    return;
                }

                $trigger
                    .addClass('label-manager')
                    .data('labelReferenceTable', referenceTable)
                    .data('labelReferenceId', referenceId);

                me.storage.$trigger = $trigger;
            },

            /**
             * Label-Manager-Overlay einblenden
             */
            showOverlay: function () {
                if (me.storage.$trigger === null) {
                    console.warn('LabelsApi: Kein Trigger definiert');
                }

                me.storage.$trigger.trigger('click.labelManager');
            },

            /**
             * Label-Manager-Overlay ausblenden
             */
            hideOverlay: function () {
                LabelManagerModule.hideOverlay();
            },

            /**
             * @param {object} attributes
             * @param {string} prefix
             *
             * @return {object}
             */
            filterDataAttributesByPrefix: function (attributes, prefix) {
                if (typeof attributes !== 'object') {
                    throw 'LabelManagerApi: InvalidArgument "attributes".';
                }
                if (typeof prefix !== 'string' || prefix === '') {
                    throw 'LabelManagerApi: InvalidArgument "prefix".';
                }

                var filteredAttributes = {};
                Object.keys(attributes).forEach(function (key) {
                    if (key.indexOf(prefix) === 0) {
                        var value = attributes[key];
                        var keyWithoutPrefix = key.substr(prefix.length);
                        var keyFirstCharLowered = keyWithoutPrefix.charAt(0).toLowerCase() + keyWithoutPrefix.slice(1);
                        filteredAttributes[keyFirstCharLowered] = value;
                    }
                });

                return filteredAttributes;
            }
        };

        me.init($element, options);

        return {
            showOverlay: me.showOverlay,
            hideOverlay: me.hideOverlay
        }
    };

    // jQuery-Plugin registrieren
    $.fn.labels = function (options) {
        return this.each(function () {
            var $elem = $(this);

            // Neue API-Instanz erzeugen
            // Dokumentation: Siehe Dateianfang
            var api = new LabelsApi($elem, options);
            /**@return {{showOverlay: *, hideOverlay: *}} */
            $elem.init.prototype.labelsApi = function () {
                return api;
            };
            $elem.data('labelsApi', api);
        });
    };

}(jQuery, LabelLoader, LabelManager));


$(function () {

    LabelManager.init();
    LabelLoader.loadAll();

    // LabelManager-Overlay ausblenden beim Wechseln von Tabs
    $('#tabs').on('tabsactivate', function() {
        LabelManager.hideOverlay();
    });
});
