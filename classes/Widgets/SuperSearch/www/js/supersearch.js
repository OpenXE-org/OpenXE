var SuperSearch = (function ($) {
    'use strict';

    var me = {

        config: {
            inputBuffer: 300 // in milliseconds
        },

        storage: {
            $input: null,
            $overlay: null,
            $details: null,
            $results: null,
            $lastUpdate: null,
            debounceBuffer: null,
            hasResults: false,
            isOpen: false
        },

        init: function () {
            me.storage.$input = $('#supersearch-input');
            if (me.storage.$input.length === 0) {
                return;
            }

            me.registerEvents();
        },

        registerEvents: function () {
            me.storage.$input.on('keyup.SuperSearch', me.onKeyUpSearchInput);

            // Overlay anzeigen bei Focus in das Such-Eingabefeld; nur wenn es schon mal geöffnet war
            me.storage.$input.on('focus.SuperSearch', me.onFocusSearchInput);

            // Overlay mit ESC schließen
            $(document).bind('keydown', function(e) {
                if (me.storage.$overlay === null) {
                    return;
                }
                if (me.storage.isOpen !== true) {
                    return;
                }

                // ESC
                if (e.keyCode === 27) {
                    me.hideOverlay();
                }
            });
        },

        /**
         * @return {jQuery}
         */
        getOverlay: function () {
            if (typeof me.storage.$overlay === 'undefined' || me.storage.$overlay === null) {
                me.storage.$overlay = me.createOverlay();
                me.storage.$details = me.storage.$overlay.find('section.detail');
                me.storage.$results = me.storage.$overlay.find('section.result');
                me.storage.$lastUpdate = me.storage.$overlay.find('section.last-update');
            }

            return me.storage.$overlay;
        },

        showOverlay: function () {
            var $overlay = me.getOverlay();
            $overlay.show();
            me.storage.isOpen = true;
            me.showDetails();
        },

        hideOverlay: function () {
            me.getOverlay().hide();
            me.storage.isOpen = false;
        },

        /**
         * @return {jQuery}
         */
        createOverlay: function () {
            var overlaySelector = '#supersearch-overlay';
            if ($(overlaySelector).length > 0) {
                return $(overlaySelector);
            }

            var overlayTemplate =
                '<span id="supersearch-icon-close" class="icon icon-close"></span>' +
                '<div class="result-wrapper">' +
                '<section class="empty-message">Keine Suchergebnisse gefunden</section>' +
                '<section class="error-message"></section>' +
                '<section class="result"></section>' +
                '<section class="last-update"></section>' +
                '</div>' +
                '<div class="detail-wrapper">' +
                '<section class="detail"></section>' +
                '</div>';

            var overlayIdAttr = overlaySelector.substr(1);
            var $overlay = $('<div>').attr('id', overlayIdAttr).addClass('supersearch-overlay').html(overlayTemplate);

            $overlay.off('click.SuperSearch', '#supersearch-icon-close');
            $overlay.on('click.SuperSearch', '#supersearch-icon-close', function (event) {
                event.preventDefault();
                me.hideOverlay();
            });

            $overlay.hide();
            $overlay.appendTo('#header');
            me.storage.isOpen = false;

            return $overlay;
        },

        /**
         * @param {Event} event
         */
        onKeyUpSearchInput: function (event) {
            event.preventDefault();
            var controlKeyCodes = [
                 9, // Tab
                13, // Enter
                16, // Shift
                17, // Strg
                18, // Alt
                20, // Caps lock
                27, // ESC
                37, // Cursor Left
                38, // Cursor Up
                39, // Cursor Right
                40  // Cursor Down
            ];
            if ($.inArray(event.keyCode, controlKeyCodes) !== -1) {
                return;
            }

            var that = this;
            me.debounce(function () {
                var searchQuery = $(that).val();
                me.fetchSearchResults(searchQuery).then(me.renderSearchResults);
            }, me.config.inputBuffer);
        },

        /**
         * Overlay anzeigen bei Focus in das Such-Eingabefeld; nur wenn es schon mal geöffnet war
         *
         * @param {Event} event
         */
        onFocusSearchInput: function (event) {
            event.preventDefault();
            if (me.storage.$overlay === null) {
                return;
            }
            if (me.storage.hasResults === false) {
                return;
            }

            me.showOverlay();
        },

        /**
         * @param {string} searchQuery
         *
         * @return {jqXHR}
         */
        fetchSearchResults: function (searchQuery) {
            if (typeof searchQuery !== 'string') {
                searchQuery = '';
            }

            return $.ajax({
                url: 'index.php?module=supersearch&action=ajax&cmd=search',
                method: 'post',
                dataType: 'json',
                data: {
                    search_query: searchQuery
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    var errorMessage = 'SuperSearch - Unbekannter Fehler #31: ' + errorThrown;

                    // PHP-Skript hat Fehler geliefert (z.b. 404)
                    if (textStatus === 'error') {
                        errorMessage = 'SuperSearch - Unbekannter Server-Fehler beim Laden der Such-Ergebnisse: ';
                        errorMessage += errorThrown;
                    }

                    // PHP-Skript liefert JSON-Error-Response
                    if (jqXHR.hasOwnProperty('responseJSON') && jqXHR.responseJSON.hasOwnProperty('error')) {
                        errorMessage = 'SuperSearch - Server-Fehler beim Laden der Such-Ergebnisse: ';
                        errorMessage += jqXHR.responseJSON.error;

                        if (jqXHR.responseJSON.hasOwnProperty('data') &&
                            jqXHR.responseJSON.data === 'index-empty') {
                            me.showErrorMessage('Fehler: ' + jqXHR.responseJSON.error);
                            return;
                        }
                    }

                    alert(errorMessage);
                }
            });
        },

        /**
         * @param {Array} rawResult
         */
        renderSearchResults: function (rawResult) {
            var $overlay = me.getOverlay();
            var $resultContainer = $overlay.find('section.result');
            $resultContainer.html('');

            if (rawResult.length === 0 || !rawResult.hasOwnProperty('data')) {
                $resultContainer.html('Fehler: Suche hat fehlerhaftes Ergebnis geliefert.');
                me.storage.hasResults = false;
                me.hideResults();
                return;
            }

            // Overlay ausblenden, wenn Suchbegriff zu kurz
            if (rawResult.data === null) {
                me.storage.hasResults = false;
                me.hideOverlay();
                return;
            }

            // Anzeigen wann der Such-Index das letzte Mal aktualisiert wurde
            if (rawResult.data.hasOwnProperty('last_index_update_formatted')) {
                if (rawResult.data.last_index_update_formatted !== null) {
                    var lastIndexUpdate = rawResult.data.last_index_update_formatted;
                    me.storage.$lastUpdate.text('Such-Index vom ' + lastIndexUpdate).show();
                } else {
                    me.storage.$lastUpdate.text('').hide();
                }
            }

            var resultCount = rawResult.data.count;
            var searchResults = rawResult.data.results;
            if (resultCount === 0) {
                me.storage.hasResults = false;
                me.showEmptyResults();
                return;
            }

            me.storage.$details.html('');
            Object.keys(searchResults).forEach(function (group) {
                var groupResult = searchResults[group];
                var $groupHtml = me.buildGroupResult(groupResult.key, groupResult.title, groupResult.items);
                $resultContainer.append($groupHtml);
            });

            me.storage.hasResults = true;
            me.showResults();
            me.showOverlay();
        },

        /**
         * @param {string} groupKey
         * @param {string} groupTitle
         * @param {array}  items
         *
         * @return {jQuery}
         */
        buildGroupResult: function (groupKey, groupTitle, items) {
            if (items.length === 0) {
                return;
            }
            if (typeof groupTitle === 'undefined') {
                groupTitle = 'Ergebnis';
            }

            var $resultWrapper = $('<div class="result-group">');
            var $resultList = $('<ul class="result-list">');
            var $listHead = $('<li class="result-head">').html(groupTitle);

            $resultList.append($listHead);
            items.forEach(function (item) {
                item.group = groupKey;
                var itemType = item.type !== null ? item.type : 'default';
                var $listItem;

                switch (itemType) {
                    case 'default':
                    default:
                        $listItem = me.buildDefaultItemResult(item);
                        break;
                }

                $resultList.append($listItem);
            });
            $resultWrapper.append($resultList);

            return $resultWrapper;
        },

        /**
         * @param {object} item
         *
         * @return {jQuery}
         */
        buildDefaultItemResult: function (item) {
            var hasSubtitle = item.hasOwnProperty('subtitle') && typeof item.subtitle === 'string';
            var hasAdditionalInfos =
                item.hasOwnProperty('additionalInfos') &&
                typeof item.additionalInfos === 'object' &&
                item.additionalInfos !== null;

            var mainTitle = '<span class="title-main">' + item.title + '</span>';
            var subTitle = hasSubtitle ? '<span class="title-sub">' + item.subtitle + '</span>' : '';
            var titleString = '<span class="title">' + mainTitle + subTitle + '</span>';

            if (hasAdditionalInfos) {
                titleString += '<span class="caption">';
                $.each(item.additionalInfos, function (index, additionalInfo) {
                    titleString += '<span class="additional">' + additionalInfo + '</span>';
                });
                titleString += '</span>';
            }

            var $listItem = $('<li>').addClass('result-item');
            var $itemLink = $('<a>').attr('href', item.link).html(titleString);
            $itemLink.appendTo($listItem);

            $itemLink.on('click', function (e) {
                e.preventDefault();
                me.renderItemDetails(item);
            });

            return $listItem;
        },

        /**
         * Rendert Ergebnisdetails
         *
         * @param {object} item
         */
        renderItemDetails: function (item) {
            // Per AJAX ausführliche Inhalte nachladen
            me.fetchItemDetailsDynamicContent(item).then(
                function (data) {
                    me.renderItemDetailsDynamicContent(data, item);
                },
                function (jqXhr) {
                    var error =
                        typeof jqXhr.responseJSON !== 'undefined' &&
                        typeof jqXhr.responseJSON.error !== 'undefined'
                            ? jqXhr.responseJSON.error
                            : 'Unbekannter Fehler';
                    alert('Fehler beim Laden der Detail-Informationen: ' + error);
                }
            );
        },

        /**
         * @param {object} detailResult
         * @param {object} listItem Originales Item-Objekt aus Suchergebnis-Liste
         *
         * @return {void}
         */
        renderItemDetailsDynamicContent: function (detailResult, listItem) {
            if (!detailResult.hasOwnProperty('data') || detailResult.data === false) {
                // Es wurde kein Detail-Result gefunden
                // Link aus Suchergebnis-Item aufrufen
                me.hideDetails();
                window.location.href = listItem.link;
                return;
            }

            var detail = detailResult.data;
            var $details = me.storage.$details;

            // Überschrift rendern
            var $headline = $('<h1>').html(detail.title);
            $details.html('').append($headline);

            // Attachments (z.B. Buttons) rendern
            if (detail.hasOwnProperty('attachments')) {
                var $attachments = me.generateDetailAttachments(detail.attachments);
                $details.append($attachments);
            }

            me.showDetails();
        },

        /**
         * @param {object} item
         *
         * @return {jqXHR}
         */
        fetchItemDetailsDynamicContent: function (item) {
            return $.ajax({
                url: 'index.php?module=supersearch&action=ajax&cmd=detail',
                method: 'post',
                dataType: 'json',
                data: {
                    detail_group: item.group,
                    detail_identifier: item.identifier
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    var errorMessage = 'SuperSearch - Unbekannter Fehler #32: ' + errorThrown;

                    // PHP-Skript hat Fehler geliefert (z.b. 404)
                    if (textStatus === 'error') {
                        errorMessage = 'SuperSearch - Unbekannter Server-Fehler beim Laden des Detail-Ergebnisses: ';
                        errorMessage += errorThrown;
                    }

                    // PHP-Skript liefer JSON-Error-Response
                    if (jqXHR.hasOwnProperty('responseJSON') && jqXHR.responseJSON.hasOwnProperty('error')) {
                        errorMessage = 'SuperSearch - Server-Fehler beim Laden des Detail-Ergebnisses: ';
                        errorMessage += jqXHR.responseJSON.error;
                    }

                    alert(errorMessage);
                }
            });
        },

        /**
         * @param {Array} attachments
         *
         * @return {jQuery} jQuery-Element
         */
        generateDetailAttachments: function (attachments) {
            var $attachments = $('<div>');

            $.each(attachments, function (index, attachment) {
                if (!attachment.hasOwnProperty('type')) {
                    console.error('Attachment ungültig. "type"-Property fehlt.');
                    return;
                }
                if (!attachment.hasOwnProperty('data')) {
                    console.error('Attachment ungültig. "data"-Property fehlt.');
                    return;
                }

                if (attachment.type === 'button_block') {
                    var $buttonBlock = me.generateDetailAttachmentTypeButtonBlock(attachment.data);
                    $attachments.append($buttonBlock);
                }
                if (attachment.type === 'content_static') {
                    var $contentStatic = me.generateDetailAttachmentTypeStaticContent(attachment.data);
                    $attachments.append($contentStatic);
                }
                if (attachment.type === 'content_dynamic') {
                    var $contentDynamic = me.generateDetailAttachmentTypeDynamicContent(attachment.data);
                    $attachments.append($contentDynamic);
                }
            });

            return $attachments;
        },

        /**
         * @param {Array} items
         *
         * @return {jQuery} jQuery-Element
         */
        generateDetailAttachmentTypeButtonBlock: function (items) {
            var $buttonBlock = $('<div>');

            $.each(items, function (index, item) {
                var $button = $('<a>').text(item.title).addClass('button');
                if (item.hasOwnProperty('attributes')) {

                    // Button-Attribute verarbeiten
                    $.each(item.attributes, function (attrName, attrValue) {
                        if (attrName === 'class') {
                            $button.addClass(attrValue);
                            return;
                        }
                        if (attrName === 'data-icon') {
                            var iconUrl = '';
                            switch (attrValue) {
                                case 'help':
                                    iconUrl = './themes/new/images/help.svg';
                                    break;
                                case 'settings':
                                    iconUrl = './themes/new/images/settings.svg';
                                    break;
                            }
                            if (iconUrl !== '') {
                                $button.addClass('icon');
                                $button.addClass('icon-' + attrValue);
                                var $iconElem = $('<img alt="Handbuch">').attr('src', iconUrl);
                                var $iconWrapper = $('<span class="icon">').append($iconElem);
                                $button.prepend($iconWrapper);

                            }
                        }
                        $button.attr(attrName, attrValue);
                    });
                }
                $button.appendTo($buttonBlock);
            });

            return $buttonBlock;
        },

        /**
         * @param {Object} data
         *
         * @return {jQuery} jQuery-Element
         */
        generateDetailAttachmentTypeStaticContent: function (data) {
            return  $('<p>').html(data.content);
        },

        /**
         * @param {Object} data
         *
         * @return {jQuery} jQuery-Element
         */
        generateDetailAttachmentTypeDynamicContent: function (data) {
            var $dynamicContent = $('<div>').addClass('minidetail');

            if (data.hasOwnProperty('url') && data.url !== null) {
                me.fetchMiniDetailContent(data.url, data.params)
                  .then(
                      function (htmlContent) {
                          $dynamicContent.html(htmlContent);
                          me.storage.$details.append($dynamicContent);
                      },
                      function (jqXhr) {
                          var message = 'Fehler beim Laden der Mini-Details: ';
                          if (jqXhr.hasOwnProperty('responseJSON') && jqXhr.responseJSON.hasOwnProperty('error')) {
                              message += jqXhr.responseJSON.error;
                          } else {
                              message += jqXhr.status + ' ' + jqXhr.statusText;
                          }
                          $('<div class="error"></div>').text(message).appendTo(me.storage.$details);
                      }
                  );
            }

            return $dynamicContent;
        },

        /**
         * @param {string} miniDetailUrl
         * @param {Object} miniDetailParams Zusätzliche POST-Parameter
         *
         * @return {jqXHR}
         */
        fetchMiniDetailContent: function (miniDetailUrl, miniDetailParams) {
            if (miniDetailUrl.substr(0, 10) !== 'index.php?') {
                alert('Mini-Detail-URL ist ungültig: ' + miniDetailUrl);
                throw 'Mini-Detail-URL ist ungültig: ' + miniDetailUrl;
            }
            if (typeof miniDetailParams !== 'object') {
                miniDetailParams = {};
            }

            return $.ajax({
                url: miniDetailUrl,
                data: miniDetailParams,
                method: 'post',
                dataType: 'html'
            });
        },

        /**
         * Suchergebnisse einblenden
         */
        showResults: function () {
            me.getOverlay().addClass('has-result');
            me.getOverlay().find('section.empty-message').hide();
            me.getOverlay().find('section.error-message').hide();
        },

        /**
         * Suchergebnisse ausblenden
         */
        hideResults: function () {
            me.getOverlay().removeClass('has-result');
            me.getOverlay().find('section.empty-message').hide();
            me.getOverlay().find('section.error-message').hide();
            me.getOverlay().find('section.last-update').hide();
        },

        /**
         * Details einblenden
         */
        showDetails: function () {
            me.getOverlay().addClass('has-detail');
            me.getOverlay().find('.detail-wrapper').scrollTop(0);
        },

        /**
         * Details einblenden
         */
        hideDetails: function () {
            me.getOverlay().removeClass('has-detail');
        },

        /**
         * Hinweis anzeigen das keine Ergebnisse gefunden wurden
         */
        showEmptyResults: function () {
            me.showOverlay();
            me.hideDetails();
            me.getOverlay().removeClass('has-result');
            me.getOverlay().find('section.empty-message').show();
            me.getOverlay().find('section.error-message').hide();
        },

        /**
         * @param {string} errorMessage
         */
        showErrorMessage: function (errorMessage) {
            me.showOverlay();
            me.hideDetails();
            me.getOverlay().find('section.empty-message').hide();
            me.getOverlay().find('section.error-message').html(errorMessage).show();
        },

        /**
         * Puffer-Funktion um Events erst nach einer bestimmten Zeit auszuführen
         *
         * @param {function}    callback
         * @param {number}      delay
         * @param {object|null} contextParam
         */
        debounce: function (callback, delay, contextParam) {
            var context = typeof contextParam !== 'undefined' && contextParam !== null ? contextParam : this;
            var args = arguments;

            window.clearTimeout(me.storage.debounceBuffer);
            me.storage.debounceBuffer = window.setTimeout(function () {
                callback.apply(context, args);
            }, delay || 250);
        }
    };

    return {
        init: me.init
    };

})(jQuery);

$(function () {
    SuperSearch.init();
});
