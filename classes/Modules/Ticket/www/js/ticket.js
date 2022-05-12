var TicketFiles = function ($) {
    "use strict";

    var me = {
        elem: {},

        selector: {
            dialog: '#attachment-assign-dialog',
            dialogTabs: '#filetabs',
            dialogContent: '#assign-dialog-content',
            documentTableContainer: '.document-table-container',
            documentFilterCheckbox: '.document-filter-checkbox',
            documentAssignButton: '.document-assign-action',
            createLiabilityButton: '.create-liability-button'
        },

        storage: {
            fileId: null,
            previewFileId: null,
            documentType: 'verbindlichkeit',
            dataTableSettings: {}
        },
        
        init: function () {
            me.elem.$assignDialog = $(me.selector.dialog);
            me.attachEvents();
            me.initDialog();
        },

        /**
         * Benötigte Events attachen
         */
        attachEvents: function () {
            // Hinzufügen-Button wird angeklickt
            $(document).on('click', '.attachment-assign-button', function (e) {
                e.preventDefault();
                me.onClickAddButton(this);
            });

            // Im Dialog-Modal wird der Tab gewechselt
            $(document).on('tabsactivate', me.selector.dialogTabs, function(event, ui) {
                me.onChangeFileTab(ui.newTab, ui.oldTab);
            });

            // DataTable-Filter wird geändert
            $(document).on('change', me.selector.documentFilterCheckbox, function () {
                me.onChangeTableFilter(this);
            });

            // "Datei zuweisen"-Button wurde in UI-Dialog-Table angeklickt
            $(document).on('click', me.selector.documentAssignButton, function (e) {
                e.preventDefault();
                me.onClickDocumentAssignButton(this);
            });

            // Neue Verbindlichkeit anlegen
            $(document).on('click', me.selector.createLiabilityButton, function () {
                me.createNewLiability();
            });
        },

        /**
         * @param {HTMLElement} element
         */
        onClickAddButton: function (element) {
            var fileId = parseInt($(element).data('file'));
            if (isNaN(fileId) || fileId === 0) {
                return;
            }

            // UI-Dialog anzeigen
            me.storage.fileId = fileId;
            me.displayDialog();
        },

        /**
         * Im UI-Dialog wird der Tab gewechselt
         *
         * @param {HTMLElement} newTab Tab-Element das aktiviert wird
         * @param {HTMLElement} oldTab Tab-Element das vorher aktiv war
         */
        onChangeFileTab: function (newTab, oldTab) {
            var docTypeBefore = $(oldTab).data('type');
            me.destroyDataTable(docTypeBefore);

            me.storage.documentType = $(newTab).data('type');
            me.loadDataTable();
        },

        /**
         * "Datei zuweisen"-Button wurde angeklickt
         *
         * @param {HTMLElement} element Angeklickter Button
         */
        onClickDocumentAssignButton: function (element) {
            var $tableRow = $(element).parents('tr');
            if (!$.fn.DataTable.isDataTable(me.elem.$currentDataTable)) {
                alert('Unbekannter Fehler #1: DataTable konnte nicht gefunden werden.');
                return;
            }
            var $dataTable = me.elem.$currentDataTable.DataTable();
            var data = $dataTable.row($tableRow).data();

            // ID aus DataTable-Datensatz holen
            var objectId = parseInt(data.id);
            if (isNaN(objectId)) {
                alert('Unbekannter Fehler #2: ID für Zuweisung konnte nicht gefunden werden.');
                return;
            }

            // Datei-Stichwort zuweisen
            me.assignDocumentKeyword(me.storage.documentType, objectId);
        },

        /**
         * Datei-Stichwort zuweisen
         *
         * @param {string} documentType
         * @param {number} objectId
         */
        assignDocumentKeyword: function (documentType, objectId) {
            $.ajax({
                url: 'index.php?module=ticket&action=assistent&cmd=assign-file',
                data: {keyword: documentType, file: me.storage.fileId, object: objectId},
                type: 'POST',
                dataType: 'json',
                success: function(result) {
                    if (result.success === false) {
                        alert('Unbekannter Fehler #3. Datei konnte nicht zugewiesen werden.');
                    }
                    me.closeDialog();
                    window.location.reload();
                }
            });
        },

        /**
         * DataTable-Filter wurde geändert
         *
         * @param {HTMLElement} element
         */
        onChangeTableFilter: function (element) {
            var isActive = $(element).prop('checked');
            var filterColumn = $(element).data('filter-column');
            if (typeof isActive === 'undefined' || typeof filterColumn === 'undefined') {
                return;
            }

            // Prüfen ob DataTable vorhanden
            if (!$.fn.DataTable.isDataTable(me.elem.$currentDataTable)) {
                return;
            }
            var dataTable = me.elem.$currentDataTable.DataTable();

            // Filter anwenden
            if (isActive) {
                dataTable.column(filterColumn).search('true', false, false, false).draw();
            } else {
                dataTable.column(filterColumn).search('', false, false, false).draw();
            }
        },

        /**
         * UI-Dialog initialisieren
         */
        initDialog: function () {
            me.elem.$assignDialog.dialog({
                title: 'Datei-Zuordnung',
                modal: true,
                minWidth: 900,
                closeOnEscape: false,
                autoOpen: false,
                resizable: false
            });

            // Tabs initialisieren
            $(me.selector.dialogTabs).tabs();
        },

        /**
         * UI-Dialog anzeigen (vorher DataTable laden)
         */
        displayDialog: function () {
            me.loadDataTable().then(me.openDialog);
        },

        /**
         * HTML-Template und DataTable-Einstellungen laden
         *
         * @return {Deferred} jQuery Promise Object
         */
        loadDataTable: function () {
            return me.fetchDataTableHtmlTemplate()
            .then(me.fetchDataTableSettings)
            .then(me.initDataTable);
        },

        /**
         * UI-Dialog öffnen
         */
        openDialog: function () {
            window.setTimeout(function () {
                // Kurz warten bis DataTable aufgebaut ist, damit Höhe berechnet werden kann
                me.elem.$assignDialog.dialog('open');
                me.elem.$assignDialog.dialog('option', 'height', 'auto');
            }, 150);
        },

        /**
         * UI-Dialog schließen
         */
        closeDialog: function () {
            me.elem.$assignDialog.dialog('close');
        },

        /**
         * DataTable initialisieren
         */
        initDataTable: function () {
            var $tabContent = me.elem.$assignDialog.find('#' + me.storage.documentType + '-tab');
            me.elem.$currentDataTable = $tabContent.find('table.display');
            me.elem.$currentDataTable.css('width', '100%');

            // DataTable ist bereist initialisiert > Instanz zerstören
            if ($.fn.DataTable.isDataTable(me.elem.$currentDataTable)) {
                me.elem.$currentDataTable.DataTable().destroy();
            }

            // DataTable initialisieren
            var table = me.elem.$currentDataTable.DataTable(me.storage.dataTableSettings);
            table.on('init.dt', function () { $(me.selector.documentTableContainer).show(); })
        },

        /**
         * Zerstört eine DataTable-Instanz
         *
         * @param {string} docType
         */
        destroyDataTable: function (docType) {
            var $tabContent = me.elem.$assignDialog.find('#' + docType + '-tab');
            var $table = $tabContent.find('table.display');

            if ($.fn.DataTable.isDataTable($table)) {
                $table.DataTable().destroy();
                me.elem.$currentDataTable = null;
            }
        },

        /**
         * DataTable-HTML-Tabelle laden
         *
         * @return {jqXHR} jQuery jqXHR-Objekt
         */
        fetchDataTableHtmlTemplate: function () {
            return $.ajax({
                url: 'index.php?module=ticket&action=assistent&cmd=table-html',
                data: { 'type': me.storage.documentType, 'id': me.storage.fileId },
                type: 'GET',
                dataType: 'html',
                success: function(data) {
                    me.elem.$assignDialog.find('#' + me.storage.documentType + '-tab').html(data);
                    me.elem.$assignDialog.find(me.selector.documentTableContainer).hide();
                }
            });
        },

        /**
         * DataTable-Einstellungen laden
         *
         * @return {jqXHR} jQuery jqXHR-Objekt
         */
        fetchDataTableSettings: function () {
            return $.ajax({
                url: 'index.php?module=ticket&action=assistent&cmd=table-settings',
                data: { 'type': me.storage.documentType, 'id': me.storage.fileId },
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    me.storage.dataTableSettings = data;
                }
            });
        },

        /**
         * Neue Verbindlichkeit anlegen + Datei zuweisen
         *
         * Bei Erfolg wird die Verbindlichkeit geöffnet.
         */
        createNewLiability: function () {
            $.ajax({
                url: 'index.php?module=ticket&action=assistent&cmd=create-liability',
                data: { 'id': me.storage.fileId },
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (typeof data.success === 'undefined' || data.success === false) {
                        alert('Unbekannter Fehler #5. Verbindlichkeit konnte nicht angelegt werden.');
                        return;
                    }
                    /** @namespace data.liability */
                    if (typeof data.liability === 'undefined') {
                        alert('Unbekannter Fehler #6. Verbindlichkeit konnte nicht angelegt werden.');
                        return;
                    }

                    window.location.href = './index.php?module=verbindlichkeit&action=edit&id=' + data.liability;
                }
            });
        }
    };

    return {
        init: me.init
    };

}(jQuery);

var TicketTree = function($) {
    "use strict";

    var me = {
        elem: {},
        storage: {
          api:null,
        },
        selector: {

        },
        initTree: function() {
            $('#mlmTree').aciTree({
                autoInit: false,
                checkboxChain: false,
                ajax: {
                    url: 'index.php?module=ticket&action=antwort&cmd=gettree&id='+$('.mlmTreeContainerLeft').data('id')
                },
                checkbox: true,
                itemHook: function(parent, item, itemData, level) {
                },
                filterHook: function(item, search, regexp) {

                    if (search.length) {
                        var parent = this.parent(item);

                        if (parent.length) {
                            var label = this.getLabel(parent);
                            if (regexp.test(String(label))) {
                                this.setVisible(item);
                                return true;
                            }
                            this.setVisible(item);
                        }

                        if (regexp.test(String(this.getLabel(item)))) {
                            item.addClass('searched');
                            return true;
                        } else {
                            return false;
                        }
                    } else {
                        return true;
                    }
                }
            });
        },
        init: function () {
            me.initTree();

            me.storage.api = $('#mlmTree').aciTree('api');
            $('#search').val('');
            var last = '';

            $('#search').on('keyup', function() {
                if ($(this).val() === last) {
                    return;
                }

                $('.aciTreeLi').removeClass('searched');

                last = $(this).val();
                me.storage.api.filter(null, {
                    search: $(this).val(),
                    callback: function() {

                    },
                    success: function(item, options) {

                        if (!options.first) {
                            //alert('No results found!');
                        }
                    }
                });
            });


            $('#mlmTree').on('acitree', function(event, api, item, eventName, options){
                switch (eventName){
                    case 'checked':
                        break;
                    case 'unchecked':
                        break;
                    case 'selected':
                        var itemData = api.itemData(item);
                        if(typeof itemData.vorlage != 'undefined') {
                            einfuegenticket(itemData.vorlage);
                        }
                        break;
                    default:
                        if (api.isItem(item)){
                            //console.log('the event is: ' + eventName + ' for the item ID: ' + api.getId(item));
                        } else {
                            //console.log('the event is: ' + eventName + ' for the tree ROOT');
                        }
                }
            });

            $('#mlmTree').aciTree('init');
        }
    }

    return {
        init: me.init
    };

}(jQuery);

$(document).ready(function () {
    if ($('div.mlmTreeContainerLeft').length > 0) {
        TicketTree.init();
    }
    if ($('#ticketassistent').length === 0) {
        return;
    }
    TicketFiles.init();
});
