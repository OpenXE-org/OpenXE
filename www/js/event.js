/**
 * Tab-/Fenster-übergreifendes Event-System
 *
 * @example
 *   EventListener attachen (für Events aus eigenem Fenster/Tab):
 *   EventManager.on('notification.send', function () {
 *       alert('huhu self');
 *   });
 *
 * @example
 *   EventListener attachen (für Events aus anderen Fenstern/Tabs):
 *   EventManager.onReceive('notification.send', function () {
 *       alert('huhu received');
 *   });
 *
 * @example
 *   Event auslösen:
 *   EventManager.fire('notification.send');
 */
var EventManager = function() {
    'use strict';

    var me = {

        /** LocalStorage-Key */
        storageKey: 'xentral_event_marker',

        /** Eindeutige ID pro Tab/Fenster */
        windowIdentifier: null,

        /** Event-Warteschlange */
        ownQueue: {},
        receiveQueue: {},

        /** LocalStorage-Cache */
        storageCache: {
            name: null,
            data: {},
            index: 0,
            origin: null,
            isOwnEvent: true
        },

        init: function() {
            me.windowIdentifier = 'window_' + Math.floor(Math.random() * Math.floor(9999999999));
            me.storageCache.origin = me.windowIdentifier;

            // storageCache aus LocalStorage wiederherstellen
            var value = localStorage.getItem(me.storageKey);
            if (typeof value !== 'undefined' && value !== null) {
                me.storageCache = JSON.parse(value);
            }

            // Auf Änderungen im LocalStorage horchen
            window.addEventListener('storage', me.storageHandler, false);
        },

        /**
         * Event wurde ausgelöst > Event ausführen und andere Tabs/Fenster informieren
         *
         * @param {string} eventName
         * @param {object} eventData
         */
        fire: function(eventName, eventData) {
            me.execute(me.ownQueue, eventName, eventData);
            me.send(eventName, eventData);
        },

        /**
         * Event ausführen
         *
         * @param {object} eventQueue
         * @param {string} eventName
         * @param {object} eventData
         */
        execute: function(eventQueue, eventName, eventData) {
            var queue = eventQueue[eventName];

            // Event existiert nicht
            if (typeof queue === 'undefined') {
                return;
            }

            if (typeof eventData === 'undefined') {
                eventData = {};
            }

            // EventListener ausführen
            queue.forEach(function(callback) {
                callback(eventData);
            });
        },

        /**
         * Event an andere Tabs senden
         *
         * @param {string} eventName
         * @param {object} eventData
         */
        send: function(eventName, eventData) {
            me.storageCache.name = eventName;
            me.storageCache.data = eventData;

            // // Index hochzählen
            me.storageCache.index++;
            if (me.storageCache.index > 9999999) {
                me.storageCache.index = 1;
            }

            // Herkunft setzen
            me.storageCache.origin = me.windowIdentifier;

            // LocalStorage updaten > Andere Fenster informieren
            localStorage.setItem(me.storageKey, JSON.stringify(me.storageCache));
        },

        /**
         * EventListener auf Events im eigenen Tab binden
         *
         * @param {string} eventName
         * @param {function} listener
         */
        on: function(eventName, listener) {
            if (typeof me.ownQueue[eventName] === 'undefined') {
                me.ownQueue[eventName] = [];
            }

            // Listener in Warteschlange speichern
            me.ownQueue[eventName].push(listener);
        },

        /**
         * EventListener auf Events aus fremden Tabs/Fenstern binden
         *
         * @param {string} eventName
         * @param {function} listener
         */
        onReceive: function(eventName, listener) {
            if (typeof me.receiveQueue[eventName] === 'undefined') {
                me.receiveQueue[eventName] = [];
            }

            // Listener in Warteschlange speichern
            me.receiveQueue[eventName].push(listener);
        },

        /**
         * Horcht auf Änderungen im LocalStorage
         *
         * @param {StorageEvent} e
         */
        storageHandler: function(e) {

            // Nur auf bestimmten Key horchen
            if (e.key !== me.storageKey) {
                return;
            }

            // LocalStorage(-Key) wurde gelöscht
            if (e.newValue === null) {
                return;
            }

            var received = JSON.parse(e.newValue);
            if (received === null) {
                return;
            }

            // Eigene Änderungen ignorieren
            if (received.origin === me.windowIdentifier && received.index === me.storageCache.index) {
                return;
            }

            // Event wurde hier schon ausgeführt
            if (received.index <= me.storageCache.index) {
                return;
            }

            // EventListener ausführen
            me.execute(me.receiveQueue, received.name, received.data);
            me.storageCache = received;
        }
    };

    me.init();

    return {
        fire: me.fire,
        on: me.on,
        onReceive: me.onReceive
    };
}();
