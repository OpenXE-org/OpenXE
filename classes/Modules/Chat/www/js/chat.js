var Chat = function ($, PushJS) {
    "use strict";

    var me = {

        settings: {
            // Interval nach dem neue Nachrichten abgerufen werden (in Millisekunden)
            fetchMessageInterval: 2000,

            // Interval nach dem die Userliste aktualisiert wird (in Millisekunden)
            fetchUserlistInterval: 15000,

            // Zeit die zwischen zwei LazyLoading-Requests vergehen muss (in Millisekunden)
            lazyLoadingBufferTime: 250,

            // Zeit für die eine neue Nachricht als "NEU" markiert ist (in Millisekunden)
            newTimeout: 5000
        },

        selector: {
            // Nachrichtenbereich
            messageArea: '#message-area',

            // Eingabefeld für neue Nachrichten
            inputField: '#nachricht',

            // Container in dem die Benutzerliste angezeigt wird
            userList: '#userlist',

            // Container in dem die Raumliste angezeigt wird
            roomList: '#roomlist',

            // Bezeichner für den aktuellen Raum
            roomName: '#message-header',

            // Formular-Element
            chatForm: '#chatform',

            // Checkbox um Nachricht hervorzuheben
            prioCheckbox: '#prio',

            // InfoBox für Browser-Benachrichtigungen
            notificationInfo: '#notification-info',

            // Xentral-Sidebar
            wawiSidebar: '#sidebar'
        },

        storage: {
            // User-ID des aktuellen Chat-Partners
            activeUserId: 0,

            // Eigene User-ID
            selfUserId: 0,

            // ID der neuesten empfangenen Nachricht
            newestMessageId: 0,

            // ID der ältesten empfangenen Nachricht
            oldestMessageId: null,

            // ID der neuesten Gelesen-Markierung
            newestReadmarkId: 0,

            requestBuffer: 0,

            // Gibt an ob die älteste Nachricht bereits geladen wurde
            requestEOF: false,

            // Array mit Nachrichten-IDs die als gelesen markiert werden sollen
            readMark: [],

            // Neuestes Datum; für Anzeige der Datumszeile (wenn zu neueren Nachrichten gescrollt wird)
            newestDateString: '',

            // Ältestes Datum; für Anzeige der Datumszeile (wenn zu älteren Nachrichten gescrollt wird)
            oldestDateString: '',

            // Datumswerte merken, die schonmal angezeigt wurden
            dateStrings: [],

            isStandaloneWindow: null,

            fetchMessageInterval: null,
            fetchUserlistInterval: null
        },

        elem: {
            $window: null,
            $wrapper: null,
            $sidebarWrapper: null,
            $sidebarScroller: null,
            $messageWrapper: null,
            $messageScroller: null
        },

        init: function () {
            me.storage.isStandaloneWindow = ($(me.selector.wawiSidebar).length === 0);

            me.elem.$window = $(window);
            me.elem.$wrapper = $('#chat-wrapper');
            me.elem.$messageWrapper = $('#message-wrapper');
            me.elem.$messageScroller = $('#message-scroller');
            me.elem.$sidebarWrapper = $('#sidebar-wrapper');
            me.elem.$sidebarScroller = $('#sidebar-scroller');

            me.checkNotificationPermission();

            me.resizeElements();
            me.attachEvents();

            // Chat mit "Öffentlich"
            me.switchToUser(0);

            if (me.storage.isStandaloneWindow) {
                var navigation = '<div id="submenu-wrapper" class="clearfix"><div class="back">';
                navigation += '<a href="index.php?module=welcome&action=start"><svg xmlns="http://www.w3.org/2000/svg" width="7px" height="12px" viewBox="0 0 131.99 218.57"><path fill="none" stroke="#fff" stroke-miterlimit="10" stroke-width="33" d="M120.26 11.65l-97 97.21 97 98.11"/></svg></a>';
                navigation += '</div><a href="index.php?module=welcome&action=start" style="display:block;padding: 7px 20px;">Zurück zur Startseite</a>';
                navigation += '</div>';
                $('#tabs').prepend(navigation);
            }
        },

        /**
         * Registriert alle benötigten Events und Timer
         */
        attachEvents: function () {

            // Formular-Absenden abfangen
            $(me.selector.chatForm).on('submit', function (event) {
                event.preventDefault();
                me.sendMessage($(me.selector.inputField).val(), $(me.selector.prioCheckbox).prop('checked'));
            });

            // Klick auf User
            $(document).on('click', me.selector.userList + ' li', function (e) {
                e.preventDefault();
                me.switchToUser(parseInt($(this).data('user-id')));
            });

            // Klick auf Raum
            $(document).on('click', me.selector.roomList + ' li', function (e) {
                e.preventDefault();
                me.switchToUser(parseInt($(this).data('user-id')));
            });

            // Bei Klick auf InfoBox für Benachrichtigungen > Erlaubnis einholen
            $(document).on('click', me.selector.notificationInfo, function () {
                me.requestNotificationPermission();
            });

            // Fenstergrößen-Änderungen abfangen
            // Vorallem der Nachrichtenbereich benötigt immer eine feste Breite; ansonsten
            // können überlange Nachrichten ohne Leerzeichen nicht umgebrochen werden.
            $(window).on('resize', function () {
                me.debounce(me.resizeElements, 100);
            });
        },

        /**
         * Startet die Intervale zum Abrufen der Nachrichten und Benutzerliste
         */
        start: function () {
            // Alle 30 Sekunden die Userliste aktualisieren
            me.storage.fetchUserlistInterval = setInterval(function () {
                me.updateUserList();
            }, me.settings.fetchUserlistInterval);

            // Alle 2 Sekunden die Nachrichten aktualisieren
            me.storage.fetchMessageInterval = setInterval(function () {
                me.fetchNewMessages(false);
                me.setMessagesAsRead();
            }, me.settings.fetchMessageInterval);
        },

        /**
         * Stop die Intervale zum Abrufen der Nachrichten und Benutzerliste
         */
        stop: function () {
            clearInterval(me.storage.fetchMessageInterval);
            clearInterval(me.storage.fetchUserlistInterval);
        },

        /**
         * Ist Berechtigung zum Senden von Browser-Benachrichtigungen vorhanden?
         *
         * @return {boolean}
         */
        checkNotificationPermission: function () {
            var permissionStatus = PushJS.Permission.get();

            // Falls Berechtigung nicht erteilt oder blockiert wurde > InfoBox anzeigen
            if (permissionStatus === PushJS.Permission.DEFAULT) {
                var content =
                    '<div class="info" id="notification-info">' +
                    '<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr>' +
                    '<td>Aktuell können keine Browser-Benachrichtigungen zugestellt werden. ' +
                    'Bitte Klicken Sie hier um die Nachrichten für Xentral zu erlauben.</td>' +
                    '<td align="right"><input type="button" id="notification-button" value="Benachrichtigungen erlauben"></td>'+
                    '</tr></table></div>';
                $('#tabs-1').prepend(content);
            }
        },

        /**
         * Browser-Dialog zum Erteilen der Berechtigung öffnen
         */
        requestNotificationPermission: function () {
            PushJS.Permission.request(
                me.removeNotificationInfoBox, // Berechtigung erteilt
                me.removeNotificationInfoBox // Berechtigung verweigert
            );
        },

        /**
         * InfoBox zu Browser-Benachrichtigungen entfernen
         */
        removeNotificationInfoBox: function () {
            $(me.selector.notificationInfo).remove();
            me.resizeElements();
        },

        /**
         * Schickt eine neue Nachricht per AJAX ab
         *
         * @param {string} message
         * @param {boolean} priority
         */
        sendMessage: function (message, priority) {
            if (typeof priority === 'undefined' || typeof priority !== 'boolean') {
                priority = false;
            }

            $.ajax({
                type: 'POST',
                url: 'index.php?module=chat&action=list&cmd=sendmessage',
                data: {
                    nachricht: message,
                    empfaenger: me.storage.activeUserId,
                    prio: (priority === true) ? '1' : '0'
                },
                success: function () {
                    me.emptyInput();
                    me.focusInput();
                    me.fetchNewMessages(true);
                }
            });
        },

        /**
         * Aktualisiert per AJAX die Userliste
         */
        updateUserList: function () {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: 'index.php?module=chat&action=list&cmd=userlist',
                success: function (data) {
                    me.renderUserList(data.users);
                    me.renderRoomList(data.rooms)
                }
            });
        },

        /**
         * Rendert die JSON-Userliste zu HTML
         *
         * @param {array} data
         */
        renderUserList: function (data) {
            var content = '<ul>';

            $(data).each(function (index, user) {
                user.id = parseInt(user.id);

                // Sich selbst nicht in Userliste anzeigen
                if (user.self === true) {
                    me.storage.selfUserId = user.id;
                    return;
                }

                if (user.id === me.storage.activeUserId) {
                    var roomname = '<img src="index.php?module=ajax&action=profilbild&id=' + user.id + '" alt="' + user.name + '">';
                    roomname += '<span>Mit ' + user.name + '</span>';
                    $(me.selector.roomName).html(roomname).removeClass('public');
                }

                var classes = (user.online === true) ? 'online' : '';
                classes += (user.id === me.storage.activeUserId) ? ' active' : '';

                content += '<li class="' + classes + '" data-user-id="' + user.id + '">';
                content += '<span class="name">' + user.name + '</span> ';
                if (user.unread > 0) {
                    content += '<span class="' + (user.unread > 0 ? 'unread' : '') + '">(' + user.unread + ')</span>';
                }
                content += '</li>';
            });

            content += '</ul>';

            $(me.selector.userList).html(content);
        },

        /**
         * Rendert die JSON-Raumliste zu HTML
         *
         * @param {array} data
         */
        renderRoomList: function (data) {
            var content = '<ul>';

            $(data).each(function (index, room) {
                var classes = (room.id === me.storage.activeUserId) ? ' active' : '';
                if (room.id === me.storage.activeUserId) {
                    $(me.selector.roomName).html('<span>' + room.name + '</span>').addClass('public');
                }

                content += '<li class="' + classes + '" data-user-id="' + room.id + '">';
                content += '<span class="name">' + room.name + '</span> ';
                if (room.unread > 0) {
                    content += '<span class="' + (room.unread > 0 ? 'unread' : '') + '">(' + room.unread + ')</span>';
                }
                content += '</li>';
            });

            content += '</ul>';

            $(me.selector.roomList).html(content);
        },

        /**
         * Ruft neue Nachrichten per AJAX ab
         *
         * @param {boolean} scrollDown Soll nach dem Aktualisieren der Nachrichten zur neuesten Nachricht gescrollt werden?
         */
        fetchNewMessages: function (scrollDown) {
            if (typeof scrollDown === 'undefined' || typeof scrollDown !== 'boolean') {
                scrollDown = false;
            }

            $.ajax({
                type: 'POST',
                url: 'index.php?module=chat&action=list&cmd=messages',
                data: {
                    user_id: me.storage.activeUserId,
                    after_message_id: me.storage.newestMessageId,
                    after_readmark_id: me.storage.newestReadmarkId
                },
                success: function (data) {
                    me.renderNewMessages(data.messages);
                    me.renderMessagesAsRead(data.readmark);

                    if (scrollDown === true) {
                        me.scrollToLatestMessage();
                    }
                }
            });
        },

        /**
         * Nachrichten als "Gelesen" markieren
         *
         * renderNewMessages() füllt das readMark-Array
         */
        setMessagesAsRead: function () {
            if (me.storage.readMark.length === 0) {
                return;
            }

            $.ajax({
                type: 'POST',
                url: 'index.php?module=chat&action=list&cmd=markread',
                data: {
                    messages: me.storage.readMark
                },
                success: function (data) {
                    // PHP liefert die Nachrichten-IDs der Gelesen-markierten Nachrichten zurück
                    $(data).each(function (index, messageId) {
                        messageId = parseInt(messageId);
                        var pos = me.storage.readMark.indexOf(messageId);
                        if (pos !== -1) {
                            // Bestätigt-Gelesene Nachricht aus Array entfernen
                            me.storage.readMark.splice(pos, 1);
                        }
                    });
                }
            });
        },

        /**
         * Setzt bereits gerenderte Nachrichten auf "Gelesen"
         *
         * @param {array} data
         */
        renderMessagesAsRead: function (data) {
            if (data.length === 0) {
                return;
            }

            // Im öffentlichen Chat gibt es keine Gelesen-Markierungen
            if (me.isPublicChat()) {
                return;
            }

            $(data).each(function (index, readmark) {
                if (readmark.id > me.storage.newestReadmarkId) {
                    me.storage.newestReadmarkId = readmark.id;
                }

                // Nachricht als Gelesen markieren
                $(me.selector.messageArea)
                    .find('#message-' + readmark.message)
                    .removeClass('unread')
                    .addClass('read');
            });
        },

        /**
         * Ruft ältere Nachrichten per AJAX ab; wenn man nach oben scrollt
         */
        fetchOldMessages: function () {
            // Die älteste Nachricht wurde bereits erreicht
            if (me.storage.requestEOF === true) {
                return;
            }

            // Request-Puffer ist aktiv; du musst noch warten
            if (me.storage.requestBuffer > window.performance.now()) {
                return;
            }

            // Puffer setzen; Nur ein Request alle 0,25 Sekunden
            // Notwendig weil Scroll-Event mehrmals pro Sekunde ausgelöst wird.
            me.storage.requestBuffer = window.performance.now() + me.settings.lazyLoadingBufferTime;

            $.ajax({
                type: 'POST',
                url: 'index.php?module=chat&action=list&cmd=messages',
                data: {
                    user_id: me.storage.activeUserId,
                    before_message_id: me.storage.oldestMessageId
                },
                success: function (data) {
                    if (data.messages.length === 0) {
                        // Ende wurde erreicht; es gibt keine älteren Nachrichten
                        me.renderDateString(me.storage.oldestDateString, 'DESC', true);
                        me.storage.requestEOF = true;
                        return;
                    }

                    me.renderOldMessages(data.messages);
                }
            });
        },

        /**
         * Rendert die übergebene JSON-Nachrichtenliste zu HTML
         *
         * @param {array} data
         */
        renderNewMessages: function (data) {
            var newMessages = false;

            // Abbruch wenn keine neuen Nachrichten im JSON sind
            if (data.length === 0) {
                return;
            }

            if (me.storage.oldestMessageId === null) {
                me.storage.oldestMessageId = data[0].id;
            }

            $(data).each(function (index, msg) {

                // Zeile mit Datum einfügen
                me.renderDateString(msg.date, 'ASC', false);

                // Nachricht in Nachrichtenbereich anfügen
                me.renderSingleMessage(msg, 'ASC');

                newMessages = true;

                // Ungelesene Nachrichten für Gelesen-Markierung vormerken
                if (msg.read === false) {
                    me.reserveMessageForReadMark(msg);
                }
            });

            // Nach unten scrollen, falls eine neue Nachricht dabei war
            if (newMessages === true) {
                me.scrollToLatestMessage();

                setTimeout(function () {
                    me.enableScrollSpy();
                }, 100);
            }
        },

        /**
         * Rendert die übergebene JSON-Nachrichtenliste zu HTML
         *
         * @param {array} data
         */
        renderOldMessages: function (data) {
            // Höhe des Nachrichtenbereich merken
            var oldHeight = $(me.selector.messageArea).height();

            data = data.reverse();
            $(data).each(function (index, msg) {
                me.renderDateString(msg.date, 'DESC', false);
                me.renderSingleMessage(msg, 'DESC');

                // Ungelesene Nachrichten für Gelesen-Markierung vormerken
                if (msg.read === false) {
                    me.reserveMessageForReadMark(msg);
                }
            });

            // Scroll-Position vor dem Einfügen anzeigen
            var newHeight = $(me.selector.messageArea).height();
            var scrollTop = (newHeight - oldHeight) - 20;
            me.elem.$messageScroller.scrollTop(scrollTop);
        },

        /**
         * Nachricht für Gelesen-Markierung vormerken
         *
         * @param {object} msg
         */
        reserveMessageForReadMark: function (msg) {
            if (msg.read === true) {
                return;
            }

            // Ungelesene Nachrichten für Gelesen-Markierung vormerken, wenn
            // ... Chat mit User
            if (me.isPrivateChat() && !me.isOwnMessage(msg.user)) {
                me.storage.readMark.push(msg.id);
            }
            // .. Chat mit Öffentlich
            if (me.isPublicChat()) {
                me.storage.readMark.push(msg.id);
            }
        },

        /**
         * @param {object} msg
         * @param {string} direction [ASC|DESC]
         */
        renderSingleMessage: function (msg, direction) {

            // Abbrechen, wenn Nachricht bereits vorhanden ist
            if ($('#message-' + msg.id).length > 0) {
                return;
            }

            // CSS-Klassen zusammenstellen
            var classes = 'message';
            if (me.isPrivateChat()) {
                // Bei eigenen Nachrichten anzeigen ob Nachricht gelesen wurde
                if (me.isOwnMessage(msg.user)) {
                    if (msg.read === false) {
                        classes += ' unread';
                    }
                    if (msg.read === true) {
                        classes += ' read';
                    }
                }
            }
            // Fremde ungelesene Nachrichten als NEU markieren
            if (!me.isOwnMessage(msg.user)) {
                if (msg.read === false) {
                    classes += ' new';
                }
            }
            // Prio-Nachrichten markieren
            if (msg.prio === true) {
                classes += ' prio';
            }

            // Nachrichten-HTML zusammenstellen
            var content =
                '<div data-id="{{id}}" id="{{elementId}}" class="{{classes}}">' +
                '  <div class="image">' +
                '    <img src="index.php?module=ajax&action=profilbild&id={{user}}" alt="{{alt}}">' +
                '  </div>' +
                '  <div class="head">' +
                '    <span class="sender">{{name}}</span>' +
                '    <span class="time">{{time}}</span>' +
                '  </div>' +
                '  <div class="text">{{text}}</div>' +
                '</div>';
            content = content.replace('{{id}}', msg.id);
            content = content.replace('{{user}}', msg.user);
            content = content.replace('{{alt}}', msg.name);
            content = content.replace('{{name}}', msg.name);
            content = content.replace('{{time}}', msg.time);
            content = content.replace('{{text}}', msg.text);
            content = content.replace('{{classes}}', classes);
            content = content.replace('{{elementId}}', 'message-' + msg.id);

            // Neue Nachrichten unten anhängen
            if (direction === 'ASC') {
                $(me.selector.messageArea).append(content);
            }
            // Ältere Nachrichten oben einfügen
            if (direction === 'DESC') {
                $(me.selector.messageArea).prepend(content);
            }

            // NEU-Markierung nach fünf Sekunden wieder entfernen
            setTimeout(function () {
                $('#message-' + msg.id + '.new').removeClass('new');
            }, me.settings.newTimeout);

            // ID der ältesten und neuesten Nachricht wegspeichern
            if (msg.id > me.storage.newestMessageId) {
                me.storage.newestMessageId = msg.id
            }
            if (msg.id < me.storage.oldestMessageId) {
                me.storage.oldestMessageId = msg.id
            }
        },

        /**
         * Zeile mit Datum in Nachrichtenbereich einfügen
         *
         * @param {string}  dateString
         * @param {string}  direction [ASC|DESC]
         * @param {boolean} force Datum immer ausgeben
         */
        renderDateString: function (dateString, direction, force) {
            if (typeof direction === 'undefined' || typeof direction !== 'string') {
                direction = 'ASC';
            }
            if (typeof force === 'undefined' || typeof force !== 'boolean') {
                force = false;
            }

            // Datum wurde bereits ausgegeben
            if (me.storage.dateStrings.indexOf(dateString) !== -1) {
                return;
            }
            // Merken welche Datumseinträge bereits angezeigt wurden
            me.storage.dateStrings.push(dateString);

            if (me.storage.newestDateString === '') {
                me.storage.newestDateString = dateString;
            }
            if (me.storage.oldestDateString === '') {
                me.storage.oldestDateString = dateString;
            }

            // Aufsteigend, wenn neue Nachrichten eingefügt werden
            if (direction === 'ASC') {
                if (me.storage.newestDateString !== dateString) {
                    $(me.selector.messageArea).append(
                        '<div class="date"><span>' + dateString + '</span></div>'
                    );
                    me.storage.newestDateString = dateString;
                }
            }

            // Absteigend, wenn ältere Nachrichten eingefügt werden
            if (direction === 'DESC') {
                if (me.storage.oldestDateString !== dateString || force === true) {
                    $(me.selector.messageArea).prepend(
                        '<div class="date"><span>' + me.storage.oldestDateString + '</span></div>'
                    );
                    me.storage.oldestDateString = dateString;
                }
            }
        },

        /**
         * Scrollt zur neuesten Nachricht
         */
        scrollToLatestMessage: function () {
            me.elem.$messageScroller.animate({
                scrollTop: me.elem.$messageScroller.get(0).scrollHeight
            }, 100);
        },

        /**
         * Wechselt den Chat-Partner
         *
         * @param {number} userId
         */
        switchToUser: function (userId) {
            // Storage-Variablen zurücksetzen
            me.storage.activeUserId = userId;
            me.storage.newestMessageId = 0;
            me.storage.oldestMessageId = null;
            me.storage.oldestDateString = '';
            me.storage.newestDateString = '';
            me.storage.newestReadmarkId = 0;
            me.storage.readMark = [];
            me.storage.dateStrings = [];
            me.storage.requestBuffer = 0;
            me.storage.requestEOF = false;

            // Intervalle anhalten
            me.stop();

            me.updateUserList();
            me.emptyMessageArea();
            me.fetchNewMessages(true);
            me.emptyInput();
            me.focusInput();

            // Intervalle nach zwei Sekunden starten.
            // Ansonsten kann der Nachrichtenempfang des ersten Intervalls und das
            // obige fetchNewMessages() zeitgleich passieren. Das führt zu leeren
            // Datumseinträgen.
            window.setTimeout(function () {
                me.start();
            }, 2000);
        },

        /**
         * Höhe und Breite von Elementen festlegen
         *
         * Methode wird beim Vergößern/-kleinern des Fensters aufgerufen.
         *
         * Vorallem der Nachrichtenbereich benötigt eine feste Breite, um überlangen Text
         * ohne Whitespace zuverlässig umbrechen zu können.
         */
        resizeElements: function () {
            var windowHeight = me.elem.$window.height();
            var wrapperWidth = me.elem.$wrapper.width();
            var sidebarWidth = me.elem.$sidebarWrapper.outerWidth();
            var $notificationInfo = $(me.selector.notificationInfo);
            var notificationHeight = ($notificationInfo.outerHeight() !== null) ? $notificationInfo.outerHeight() + 10 : 0;
            var newMessageWidth = 0;
            var newMessageHeight = 0;
            var newSidebarHeight = 0;

            if (me.storage.isStandaloneWindow) {
                newMessageWidth = wrapperWidth - sidebarWidth - 10;
                newMessageHeight = windowHeight - 200;
                newSidebarHeight = windowHeight - 90;

            } else {
                newMessageWidth = wrapperWidth - sidebarWidth - 10;
                newMessageHeight = windowHeight - 360;
                newSidebarHeight = windowHeight - 240;
            }

            newMessageHeight -= notificationHeight;
            newSidebarHeight -= notificationHeight;

            me.elem.$messageScroller.width(newMessageWidth);
            me.elem.$messageScroller.height(newMessageHeight);
            me.elem.$sidebarScroller.height(newSidebarHeight);
        },

        /**
         * Ältere Nachrichten beim Nach-Oben-Scrollen abrufen
         */
        enableScrollSpy: function () {
            // Ermitteln ob Nachrichtenbereich überhaupt einen Scrollbalken hat;
            // Es gibt keinen Scrollbalken bei wenigen oder keinen Nachrichten.
            var messageAreaHasScrollbar =
                (me.elem.$messageScroller.height() < $(me.selector.messageArea).height());

            // ScrollSpy wird nicht benötigt wenn kein Scrollbalken vorhanden
            if (messageAreaHasScrollbar === false) {
                return;
            }

            // Ältere Nachrichten abrufen wenn oberer Nachrichtenbereich angezeigt wird
            me.elem.$messageScroller.on('scroll', function () {
                var scrollTop = $(this).scrollTop();
                if (scrollTop < 50) {
                    me.fetchOldMessages();
                }
            });
        },

        /**
         * Scroll-Event auf Nachrichtenbereich entfernen
         */
        disableScrollSpy: function () {
            me.elem.$messageScroller.off('scroll');
        },

        /**
         * Setzt den Focus auf das Eingabefeld
         */
        focusInput: function () {
            $(me.selector.inputField).trigger('focus');
        },

        /**
         * Eingabefeld leeren
         */
        emptyInput: function () {
            $(me.selector.inputField).val('');
            //$(me.selector.prioCheckbox).prop('checked', '');
            // Prio-Checkbox nicht zurücksetzen, soll manuell durch Benutzer passieren.
        },

        /**
         * Leert den Nachrichtenbereich
         */
        emptyMessageArea: function () {
            // Eventuell vorhandenen Scroll-Event entfernen
            me.disableScrollSpy();

            // Nachrichtenbereich leeren
            $(me.selector.messageArea).empty();
        },

        /**
         * @return {bool}
         */
        isPublicChat: function () {
            return me.storage.activeUserId === 0;
        },

        /**
         * @return {bool}
         */
        isPrivateChat: function () {
            return me.storage.activeUserId > 0;
        },

        /**
         * @param {number} senderId
         * @return {bool}
         */
        isOwnMessage: function (senderId) {
            return senderId === me.storage.selfUserId;
        },

        /**
         * Puffer-Funktion um Events erst nach einer bestimmten Zeit auszuführen
         *
         * @param {function} callback
         * @param {number}   delay
         */
        debounce: function (callback, delay) {
            var context = this;
            var args = arguments;

            window.clearTimeout(me.storage.buffer);
            me.storage.buffer = window.setTimeout(function () {
                callback.apply(context, args);
            }, delay || 250);
        }
    };

    return {
        init: me.init,
        start: me.start,
        stop: me.stop
    };

}(jQuery, Push);

$(document).ready(function () {
    Chat.init();
});
