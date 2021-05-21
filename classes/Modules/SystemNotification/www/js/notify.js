/**
 * @example Notify.create('Normale Nachricht');
 * @example Notify.create('Fehler Benachrichtgung', 'error', true);
 */
var Notify = function ($, PushJS) {
    'use strict';

    var me = {

        validTypes: ['default', 'notice', 'success', 'warning', 'error', 'push'],

        settings: {
            storageKeyPrefix: 'notification_',
            storageKeyProgressBar: 'notification_progressbar'
        },

        defaults: {
            layout: 'topRight',
            theme: 'notification',
            maxVisible: 5,
            timeout: 10000,
            progressBar: true,
            animation: {
                open: {height: 'toggle'},
                close: {height: 'toggle'},
                easing: 'swing',
                speed: 250
            },
            closeWith: [], // Überschreiben
            template: '<div class="noty_message noselect"><div class="noty_text"></div><div class="close-icon"></div></div>'
        },

        init: function () {
            // Notifications nicht in IFrames anzeigen
            if (me.isIframe()) {
                return;
            }

            // Eigene Default-Einstellungen in Noty-Defaults integrieren
            $.noty.defaults = $.extend({}, $.noty.defaults, me.defaults);
            $.noty.defaults.callback.onClose = function () {
                me.closeNotificationInOtherTabs(this.options.id);
            };

            // Init abbrechen, wenn in Beleg-Positionen oder Positionen-Popup
            var action = $('body').data('action');
            if (typeof action === 'undefined' || action === 'positionen' || action === 'positioneneditpopup') {
                return;
            }

            // Wenn Seite geladen wird > Geöffnete Benachrichtigungen aus LocalStorage wiederherstellen
            me.restoreFromLocalStorage();

            // Auf Änderungen im LocalStorage horchen
            window.addEventListener('storage', me.storageHandler, false);

            $(document).on('click', '.notification .close-icon', function () {
                var notiId = $(this).parents('.noty_bar').prop('id');
                me.close(notiId);
            });
        },

        /**
         * @param {string} key
         *
         * @return {boolean}
         */
        has: function (key) {
            return $.noty.get(key) !== false;
        },

        /**
         * @param {string} key
         *
         * @return {object} noty-Objekt
         */
        get: function (key) {
            return $.noty.get(key);
        },

        /**
         * @return {string[]}
         */
        keys: function () {
            return Object.keys($.noty.store);
        },

        /**
         * @param {string|null} type [default|notice|success|warning|error|push]
         * @param {string|null} title
         * @param {string|null} message
         * @param {boolean|null} hasPriority
         * @param {object|null} options
         */
        create: function (type, title, message, hasPriority, options) {
            if (typeof options !== 'object' || options === null) {
                options = {};
            }
            var data = options;

            if (typeof type === 'undefined' || type === null) {
                type = 'default';
            }
            if (me.validTypes.indexOf(type) === -1) {
                type = 'default';
            }
            if (typeof title === 'undefined' || title === null) {
                title = '';
            }
            if (typeof message === 'undefined' || message === null) {
                message = '';
            }
            if (typeof hasPriority === 'undefined' || hasPriority === null) {
                hasPriority = false;
            }
            if (title === '' && message === '') {
                return;
            }

            if (hasPriority === true) {
                me.playSound();
                data.progressBar = false;
                data.timeout = false; // Sticky machen
                data.sticky = true;
                data.force = true; // An den Anfang setzen
            }

            data.text = '';
            if (title !== '') {
                data.text += '<h6>' + title + '</h6>';
            }
            if (message !== '') {
                data.text += message;
            }

            data.type = type;

            // Buttons aufbereiten
            if (typeof data.buttons !== 'undefined' && typeof data.buttons === 'object') {
                data.buttons.forEach(function (button) {
                    if (typeof button.text === 'undefined' || typeof button.link === 'undefined') {
                        console.warn('Could not create Notify button. Required property \'text\' oder \'link\' is missing');
                        return;
                    }
                    if (typeof button.addClass === 'undefined') {
                        button.addClass = 'btn notification-button';
                    } else {
                        button.addClass += ' btn notification-button';
                    }
                });
            }

            if (data.type === 'push') {
                me.createPushNotification(title, message, hasPriority);
            } else {
                me.createFromData(data);
            }
        },

        /**
         * Benachrichtigung erzeugen
         *
         * @param {object} data
         */
        createFromData: function (data) {
            // ID, zur Wiedererkennung über alle Tabs/Fenster, generieren und zuweisen
            if (typeof data.id === 'undefined' || data.id === null) {
                data.id = me.generateRandomId();
            }
            if (typeof data.timestamp === 'undefined' || data.timestamp === null) {
                data.timestamp = Date.now();
            }
            if (typeof data.type === 'undefined') {
                data.type = 'default';
            }

            switch (data.type) {
                case 'default':
                    data.type = 'alert';
                    break;
                case 'notice':
                    data.type = 'information';
                    break;
                case 'push':
                    return;
            }

            if (me.has(data.id)) {
                // Es gibt schon eine Notification mit dieser ID > Notification aktualisieren
                me.updateNotificationInOwnTab(data.id, data);
                me.updateNotificationInOtherTabs(data.id, data);
            } else {
                // Neue Notification anlegen
                me.createNotificationInOwnTab(data.id, data);
                me.createNotificationInOtherTabs(data.id, data);
            }
        },

        /**
         * Benachrichtigung schließen
         *
         * @param {string} key
         */
        close: function (key) {
            me.closeNotificationInOwnTab(key);
            me.closeNotificationInOtherTabs(key);
        },

        /**
         * Alle Benachrichtigungen schließen
         */
        closeAll: function () {
            me.closeAllNotificationsInOwnTab();
            me.closeAllNotificationsInOtherTabs();
        },

        /* ------\/------ Private Methoden ------\/------ */


        /**
         * Geöffnete Benachrichtigungen wiederherstellen
         */
        restoreFromLocalStorage: function () {
            var restored = me.collectFromLocalStorage();

            // Zeitliche Reihenfolge wiederherstellen
            restored.sort(function (a, b) {
                return a.timestamp - b.timestamp;
            });

            // Benachrichtigungen erzeugen
            restored.forEach(function (data) {
                me.createNotificationInOwnTab(data.id, data);
            });
        },

        /**
         * Benachrichtigungen aus LocalStorage holen
         *
         * @return {Array}
         */
        collectFromLocalStorage: function () {
            var notifications = [];

            for (var key in localStorage) {
                if (key === me.settings.storageKeyProgressBar) {
                    continue;
                }
                if (key.substr(0, 13) !== me.settings.storageKeyPrefix) {
                    continue;
                }
                if (localStorage.hasOwnProperty(key)) {
                    var store = localStorage.getItem(key);
                    var data = JSON.parse(store);

                    // Push-Benachrichtigungen nicht wiederherstellen
                    if (typeof data.type !== 'undefined' && data.type === 'push') {
                        continue;
                    }

                    notifications.push(data);
                }
            }

            return notifications;
        },

        /**
         * Ton abspielen
         */
        playSound: function () {
            try {
                var bell = new Audio('./sound/pling.mp3');
                bell.play();
            } catch (e) {
                // Sound abspielen funktioniert auf neueren Chromes nicht mehr:
                // https://developers.google.com/web/updates/2017/09/autoplay-policy-changes
            }
        },

        /**
         * Benachrichtigung im eigenen Fenster/Tab erstellen
         *
         * @param {string} key
         * @param {object} data
         */
        createNotificationInOwnTab: function (key, data) {
            if (typeof key === 'string' && typeof data === 'object') {
                var item = noty(data);
                if (data.sticky === true) {
                    item.$bar.addClass('sticky');
                }

                // Events für Fortschrittsbalken
                if (item.$progressBar && item.options.progressBar) {
                    item.$bar.on('mouseenter', function () {
                        me.resetProgressBarInOtherTabs(item.options.id);
                    });
                    item.$bar.on('mouseleave', function () {
                        me.startProgressBarInOtherTabs(item.options.id);
                    });
                }

                // Buttons wiederherstellen
                if (typeof item.options.buttons !== 'undefined' && typeof item.options.buttons === 'object') {
                    item.options.buttons.forEach(function (button) {

                        // Data-Attribute wiederherstellen
                        var $button = $('#' + button.id);
                        $.each(button, function (property, value) {
                            if (property.substr(0, 5) !== 'data-') {
                                return;
                            }
                            var dataName = property.substr(5);
                            $button.data(dataName, value);
                        });

                        // onClick-Methode wiederherstellen
                        button.onClick = function ($noty) {
                            var event = jQuery.Event('notification-button:clicked');
                            $(document).trigger(event, button);

                            if (!event.isDefaultPrevented()) {
                                $noty.close();
                                window.location.href = button.link;
                            }
                        };
                    });
                }

                // Custom Event feuern
                $(document).trigger('notification:created', data);
            }
        },

        /**
         * Benachrichtigung in allen anderen Fenstern/Tabs erstellen
         *
         * @param {string} key
         * @param {object} data
         */
        createNotificationInOtherTabs: function (key, data) {
            if (typeof key === 'string' && typeof data === 'object') {
                localStorage.setItem(key, JSON.stringify(data));
            }
        },

        /**
         * Browser-Benachrichtigung erzeugen
         *
         * @param {string} title
         * @param {string|null} message
         * @param {boolean} hasPriority
         */
        createPushNotification: function (title, message, hasPriority) {
            if (typeof PushJS === 'undefined') {
                throw 'push.js wurde nicht gefunden!';
            }
            if (typeof title === 'undefined' || title === null) {
                message = '';
            }
            if (typeof message === 'undefined' || message === null) {
                message = '';
            }
            if (typeof hasPriority === 'undefined') {
                hasPriority = false;
            }
            if (title === '' && message === '') {
                return;
            }

            var data = {
                icon: './js/pushjs/icon.png',
                onClick: function () {
                    window.focus();
                    this.close();
                }
            };
            if (message !== '') {
                data.body = message;
            }
            if (hasPriority === false) {
                data.tag = 'default';
            }

            // Nicht-Prio-Nachrichten löschen
            // (Prio-Nachrichten werden gestacked)
            PushJS.close('default');

            // Push-Nachricht erzeugen
            PushJS.create(title, data);
        },

        /**
         * Vorhandene Benachrichtigung aktualisieren
         *
         * @param {string} notifyId ID der Notification
         * @param {object} notifyData
         */
        updateNotificationInOwnTab: function (notifyId, notifyData) {
            me.updateNotificationType(notifyId, notifyData.type);
            me.updateNotificationText(notifyId, notifyData.text);
            // @todo me.updateNotificationButtons(notifyId, notifyData.buttons);
        },

        /**
         * Vorhandene Benachrichtigung aktualisieren
         *
         * @param {string} key ID der Notification
         * @param {object} data
         */
        updateNotificationInOtherTabs: function (key, data) {
            if (typeof key === 'string' && typeof data === 'object') {
                localStorage.setItem(key, JSON.stringify(data));
            }
        },

        /**
         * Benachrichtigung im eigenen Fenster/Tab schließen
         *
         * @param {string} key
         */
        closeNotificationInOwnTab: function (key) {
            if (typeof key === 'undefined') {
                return;
            }
            $.noty.close(key);
        },

        /**
         * Benachrichtigung in allen anderen Fenstern/Tabs schließen
         *
         * @param {string} key
         */
        closeNotificationInOtherTabs: function (key) {
            localStorage.removeItem(key);
        },

        /**
         * Alle Benachrichtigungen im eigenen Fenster/Tab schließen
         */
        closeAllNotificationsInOwnTab: function () {
            $.noty.closeAll();
        },

        /**
         * Alle Benachrichtigungen in allen anderen Fenstern/Tabs schließen
         */
        closeAllNotificationsInOtherTabs: function () {
            for (var key in localStorage) {
                if (key.substr(0, 13) !== me.settings.storageKeyPrefix) {
                    continue;
                }
                if (localStorage.hasOwnProperty(key)) {
                    me.closeNotificationInOtherTabs(key);
                }
            }
        },

        /**
         * Text einer vorhandenen Benachrichtigung aktualisieren
         *
         * @param {string} notifyId
         * @param {string} text
         */
        updateNotificationText: function(notifyId, text) {
            var existing = me.get(notifyId);
            if (existing === false) {
                return;
            }

            existing.$message.find('.noty_text').html(text);
        },

        /**
         * Typ einer vorhandenen Benachrichtigung aktualisieren
         *
         * @param {string} notifyId
         * @param {string} type
         */
        updateNotificationType: function(notifyId, type) {
            var existing = me.get(notifyId);
            if (existing === false) {
                return;
            }

            var newOuterClassName = 'noty_container_type_' + type;
            var $outer = existing.$bar;
            if (!$outer.hasClass(newOuterClassName)) {
                var classList = $outer.attr('class').split(/\s+/);
                $.each(classList, function(index, className) {
                    if (className.substring(0, 20) === 'noty_container_type_') {
                        $outer.removeClass(className);
                    }
                });
                $outer.addClass(newOuterClassName);
            }

            var newInnerClassName = 'noty_type_' + type;
            var $inner = existing.$bar.find('.noty_bar');
            if (!$inner.hasClass(newInnerClassName)) {
                var innerClasses = $inner.attr('class').split(/\s+/);
                $.each(innerClasses, function (index, className) {
                    if (className.substring(0, 10) === 'noty_type_') {
                        $inner.removeClass(className);
                    }
                });
                $inner.addClass(newInnerClassName);
            }
        },

        /**
         * Fortschrittsbalken im eigenen Tab/Fenster zurücksetzen
         *
         * @param {string} notifyId
         */
        resetProgressBarInOwnTab: function (notifyId) {
            var $noty = $.noty.get(notifyId);
            if (typeof $noty !== 'object') {
                return;
            }

            // Nicht alle Benachrichtigungen haben einen Fortschrittsbalken
            if ($noty.options.progressBar && $noty.$progressBar) {
                $noty.dequeueClose();
            }
        },

        /**
         * Fortschrittsbalken im eigenen Tab/Fenster wieder starten
         *
         * @param {string} notifyId
         */
        startProgressBarInOwnTab: function (notifyId) {
            var $noty = $.noty.get(notifyId);
            if (typeof $noty !== 'object') {
                return;
            }

            // Nicht alle Benachrichtigungen haben einen Fortschrittsbalken
            if ($noty.options.progressBar && $noty.$progressBar) {
                $noty.queueClose($noty.options.timeout);
            }
        },

        /**
         * Fortschrittsbalken in anderen Tabs/Fenstern zurücksetzen
         *
         * @param {string} notifyId
         */
        resetProgressBarInOtherTabs: function (notifyId) {
            var data = {
                id: notifyId,
                date: Date.now(),
                action: 'reset'
            };

            localStorage.setItem(me.settings.storageKeyProgressBar, JSON.stringify(data));
        },

        /**
         * Fortschrittsbalken in anderen Tabs/Fenstern wieder starten
         *
         * @param {string} notifyId
         */
        startProgressBarInOtherTabs: function (notifyId) {
            var data = {
                id: notifyId,
                date: Date.now(),
                action: 'start'
            };

            localStorage.setItem(me.settings.storageKeyProgressBar, JSON.stringify(data));
        },

        /**
         * Horcht auf Änderungen im LocalStorage
         *
         * @param {StorageEvent} e
         */
        storageHandler: function (e) {
            // LocalStorage wurde komplett geleert
            if (typeof e === 'undefined') {
                return;
            }

            // Fortschrittsbalken zurücksetzen/neustarten
            if (e.key === me.settings.storageKeyProgressBar) {
                if (e.newValue === null) {
                    return;
                }
                var progressData = JSON.parse(e.newValue);
                if (progressData.action === 'reset') {
                    me.resetProgressBarInOwnTab(progressData.id);
                }
                if (progressData.action === 'start') {
                    me.startProgressBarInOwnTab(progressData.id);
                }

                localStorage.removeItem(me.settings.storageKeyProgressBar);
                return;
            }

            // Nur auf bestimmten Key horchen
            if (e.key.substr(0, 13) !== me.settings.storageKeyPrefix) {
                return;
            }

            // LocalStorage(-Key) wurde gelöscht > Notification schließen
            if (e.newValue === null) {
                me.close(e.key);
                return;
            }

            var received = JSON.parse(e.newValue);
            if (received === null) {
                return;
            }

            // Daten wurden empfangen
            if (typeof received === 'object' && typeof received.id === 'string') {
                if (me.has(received.id)) {
                    // Vorhandene Notification aktualisieren
                    me.updateNotificationInOwnTab(received.id, received);
                } else {
                    // Notification erstellen
                    me.createNotificationInOwnTab(received.id, received);
                }
            }
        },

        /**
         * Zufällige ID generieren
         *
         * @return {string}
         */
        generateRandomId: function () {
            return me.settings.storageKeyPrefix + Math.floor(Math.random() * Math.floor(9999999999));
        },

        /**
         * @return {boolean}
         */
        isIframe: function () {
            return window.location !== window.parent.location;
        }
    };

    return {
        has: me.has,
        get: me.get,
        keys: me.keys,
        init: me.init,
        create: me.create,
        createFromData: me.createFromData,
        close: me.close,
        closeAll: me.closeAll
    };

}(jQuery, Push);

$(document).ready(Notify.init);
