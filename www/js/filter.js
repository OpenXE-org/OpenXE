/**
 * Dieses Modul hat nur die Aufgabe den Filter-Zähler zu aktualisieren.
 *
 * D.h. es wird die Anzahl der aktiven Filter in einem Filter-Block angezeigt und bei Änderung aktualisiert.
 */
var FilterCountIndicator = (function ($) {
    "use strict";

    var me = {

        storage: {
            $reveals: null
        },

        init: function () {
            me.storage.$reveals = $('.filter-reveal');
            if (me.storage.length === 0) {
                return;
            }

            // Initial Filter-Zähler füllen
            me.storage.$reveals.each(function () {
                me.countActiveFilters($(this));
            });

            me.registerEvents();
        },

        registerEvents: function () {
            // Bei Änderung der Filter-Elemente > Zähler aktualisieren
            me.storage.$reveals.each(function () {
                var $inputs = $(this).find('.filter-item').find('input');
                $inputs.on('change keyup', function (event) {
                    var $filterBlock = $(event.target).closest('.filter-block');
                    var isReveal = $filterBlock.hasClass('filter-reveal');
                    if (isReveal) {
                        me.countActiveFilters($filterBlock)
                    }
                });
            });
        },

        /**
         * Filter-Zähler aktualisieren
         *
         * Methode durchläuft alle Input-Elemente eines Filter-Blocks,
         * zählt die aktiven Filter und aktualisiert den Filter-Zähler.
         *
         * @param {jQuery} $filterBlock Filter-Block als jQuery-Element
         */
        countActiveFilters: function ($filterBlock) {
            var activeFilterCounter = 0;

            // Alle Form-Elemente im Filter-Block durchlaufen und zählen wieviele Filter aktiv sind
            $filterBlock.find('.filter-item').find('input').each(function () {
                var $formElem = $(this);
                var isCheckbox = $formElem.attr('type') === 'checkbox';
                var value = isCheckbox ? $formElem.prop('checked') : $formElem.val();
                if (value === true || value.length > 0) {
                    activeFilterCounter++;
                }
            });

            // Counter-Label erstellen/aktualisieren
            var $title = $filterBlock.find('.filter-title');
            var $counter = $title.find('.filter-counter');
            var titleHasCounter = $counter.length > 0;
            if (!titleHasCounter) {
                $counter = $('<span>').addClass('filter-counter');
                $title.append($counter);
            }
            $counter.html(activeFilterCounter);

            if (activeFilterCounter === 0) {
                $counter.hide();
            } else {
                $counter.show();
            }
        }
    };

    return {
        init: me.init
    }

})(jQuery);

$(document).ready(function () {
    FilterCountIndicator.init();
});
