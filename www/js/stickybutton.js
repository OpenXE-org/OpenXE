
/**
 * Zum "sticky"-machen von Buttons
 *
 * @example <a href="#" class="button-sticky">Abschicken</a>
 */
var StickyButton = function ($) {
    'use strict';

    var me = {

        storage: {
            $button: null,
            $buttonClone: null,
            isSticky: false,
            isCloned: false,
            stickyPositionTop: 0,
            stickyPositionRight: 0,
            stickyOffsetTopTrigger: 0,
            mobileBreakpointWidth: 652,
            buffer: null
        },

        selector: {
            target: '.button-sticky',
            menuWrapper: '.menu-wrapper'
        },

        /**
         * Position initial berechnen und EventHandler attachen
         */
        init: function () {
            me.storage.$button = $(me.selector.target);
            if (me.storage.$button.length === 0) {
                return;
            }
            if (me.storage.$button.length > 1) {
                console.info('StickyButton wurde deaktiviert. Es darf nur einen StickyButton pro Seite geben.');
                return;
            }

            // Original Button klonen
            me.makeClone();

            // Initial alle Werte berechnen
            me.debounce(me.calculateButtonPosition, 150);

            // Event-Handler attachen
            $(window).on('scroll', me.onWindowScroll);
            $(window).on('resize', me.onWindowResize);
            $(document).on('click', '.ui-tabs-anchor', me.calculateButtonPosition);
        },

        /**
         * Button-Position berechnen
         */
        calculateButtonPosition: function () {
            var windowWidth = $(window).width();
            var buttonWidth = me.storage.$buttonClone.outerWidth(true);
            var buttonOffset = me.storage.$buttonClone.offset();
            var buttonMarginLeft = parseInt(me.storage.$buttonClone.css('margin-left'));

            if (typeof buttonOffset !== 'object') {
                return;
            }
            if (buttonOffset.top === 0 && buttonOffset.left === 0) {
                return;
            }

            var isMobileWindowSize = windowWidth < me.storage.mobileBreakpointWidth;
            var menuHeight = isMobileWindowSize ? 0 : $(me.selector.menuWrapper).height();

            me.storage.stickyPositionTop = parseInt(menuHeight + 9);
            me.storage.stickyPositionRight = parseInt(windowWidth - (buttonOffset.left + buttonWidth) + buttonMarginLeft);
            me.storage.stickyOffsetTopTrigger = parseInt(buttonOffset.top - menuHeight - 12);
        },

        /**
         * EventHandler wenn Größe des Fenster geändert wird
         */
        onWindowResize: function () {
            me.debounce(me.calculateButtonPosition, 250);
        },

        /**
         * EventHandler wenn Fenster gescrollt wird
         */
        onWindowScroll: function () {
            if (!me.storage.isCloned) {
                return;
            }

            var windowScrollPos = this.scrollY;
            if (windowScrollPos > me.storage.stickyOffsetTopTrigger) {
                if (me.storage.isSticky === false) {
                    me.makeSticky();
                }
            } else {
                if (me.storage.isSticky === true) {
                    me.makeUnsticky();
                }
            }
        },

        /**
         * Button sticky machen
         */
        makeSticky: function () {
            if (me.storage.stickyPositionTop === 0 &&
                me.storage.stickyPositionRight === 0) {
                return;
            }

            me.storage.isSticky = true;
            me.storage.$button.css({
                'display': 'block',
                'zIndex': 9999,
                'position': 'fixed',
                'top': me.storage.stickyPositionTop + 'px',
                'right': me.storage.stickyPositionRight + 'px'
            });
            // Beim Klon nur die Opacity anpassen, damit nichts springt
            me.storage.$buttonClone.css({
                'opacity': 0
            });
        },

        /**
         * Button unsticky machen
         */
        makeUnsticky: function () {
            me.storage.isSticky = false;
            me.storage.$button.css({
                'display': 'none',
                'zIndex': 9999,
                'position': 'fixed',
                'top': me.storage.stickyPositionTop + 'px',
                'right': me.storage.stickyPositionRight + 'px'
            });
            me.storage.$buttonClone.css({
                'opacity': 1
            });
        },

        /**
         * Erstellt einen Klon des Buttons der sticky werden soll
         *
         * Der Klon ist notwendig als Platzhalter, sonst würden die nebenstehenden Elemente springen.
         * Beim "sticky"-machen wird der originale Button eingeblendet und der Klon versteckt.
         */
        makeClone: function () {
            me.storage.$buttonClone = me.storage.$button.clone();
            me.storage.$buttonClone.insertAfter(me.storage.$button);
            me.storage.$button.hide();
            me.storage.isCloned = true;
        },

        /**
         * @return {boolean}
         */
        isVisible: function () {
            var buttonVisible = me.storage.$button.is(":visible");
            var cloneVisible = me.storage.isCloned ? me.storage.$buttonClone.is(":visible") : false;

            return buttonVisible || cloneVisible;
        },

        /**
         * Puffer-Funktion um Events erst nach einer bestimmten Zeit
         *
         * @param {function} callback
         * @param {number}   delay
         */
        debounce: function (callback, delay) {
            var context = this;
            var args = arguments;

            clearTimeout(me.storage.buffer);
            me.storage.buffer = setTimeout(function () {
                callback.apply(context, args);
            }, delay || 250);
        }
    };

    return {
        init: me.init
    };

}(jQuery);

/**
 * Bestimmte Button "sticky" machen
 */
$(document).ready(function () {
    StickyButton.init();
});
