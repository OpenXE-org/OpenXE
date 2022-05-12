/**
 * @example Hinzufügen: $('#target').loadingOverlay();
 * @example Entfernen:  $('#target').loadingOverlay('remove');
 */
(function ($) {
    'use strict';

    $.fn.loadingOverlay = function (options) {
        var target = this;
        var action = 'show';
        var defaults = {
            overlayClass: 'loading-overlay',
            dataAttribute: 'loading-overlay',
            loadingImage: './themes/new/images/loading.gif',
            backgroundColor: '#FFFFFF',
            opacity: 0.75
        };

        // Es wurden keine Options übergeben
        if (typeof options === 'undefined') {
            options = {};
        }
        // Es wurde ein Befehl übergeben, z.B. 'remove'
        if (typeof options === 'string') {
            action = options;
            options = {};
        }

        // Übergebene Optionen und Defaults zusammenführen
        var settings = $.extend({}, defaults, options);

        /**
         * Overlay anzeigen
         */
        if (action === 'show') {
            // Icon vorladen
            var $icon = $('<img src="" alt="">').attr('src', settings.loadingImage);
            var iconLoaded = false;

            // Overlay anzeigen, wenn Icon fertig geladen
            $icon.on('load', function () {
                iconLoaded = true;
                showOverlay(target, settings);
            });

            // Fallback falls Icon nicht gefunden wird oder zu lang zum Laden braucht
            setTimeout(function() {
                if(!iconLoaded) {
                    showOverlay(target, settings);
                }
            }, 250);
        }

        /**
         * Overlay entfernen
         */
        if (action === 'remove') {
            removeOverlay(target, settings);
        }

        return this;
    };

    /**
     * @param elem
     * @param settings
     */
    var showOverlay = function (elem, settings) {
        var $target = $(elem);

        // Element auf "position: relative" stellen, wenn statisch positioniert.
        var targetPositioning = $target.css('position');
        if (targetPositioning === 'static') {
            $target.css('position', 'relative');
        }

        var overlayHtml =
            '<div class="' + settings.overlayClass + '">' +
            '<div class="loading-back"></div>' +
            '<div class="loading-icon">' +
            '<img src="' + settings.loadingImage + '" alt="Loading...">' +
            '</div>' +
            '</div>';

        if (!$target.data(settings.dataAttribute)) {
            var $overlay = $(overlayHtml);
            $overlay.find('.loading-back').css({
                'background-color': settings.backgroundColor,
                'opacity': settings.opacity
            });

            // Default-Bild nur in halber Größe anzeigen
            if (settings.loadingImage === './themes/new/images/loading.gif') {
                $overlay.find('.loading-icon img').css({'width': '50%', 'height': '50%'});
            }

            $target.data(settings.dataAttribute, true);
            $target.prepend($overlay);
        }
    };

    /**
     * @param elem
     * @param settings
     */
    var removeOverlay = function (elem, settings) {
        var $target = $(elem);
        $target.data(settings.dataAttribute, false);
        $target.find('.' + settings.overlayClass).detach();
    };

}(jQuery));
