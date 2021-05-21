
/**
 *
 */
var DataTableColumnFilter = (function ($) {
    "use strict";

    var me = {

        storage: {
            /** @property {object} Assoziative Identity-Map der bereits initialisierten DataTables */
            tables: {}
        },

        /**
         * Initialize column filter feature
         *
         * @param {String} tableName
         * @param {Array} settings
         */
        init: function (tableName, settings) {
            var $table = $('#' + tableName);
            var table = $table.DataTable();

            if ($table.length === 0) {
                console.warn('DataTableColumnFilter: Table not found.', '#' + tableName);
                return;
            }
            if (typeof settings !== 'object' || !Array.isArray(settings)) {
                console.warn('DataTableColumnFilter Filter not active.', '#' + tableName);
                return;
            }

            if (settings.length === 0) {
                console.warn('DataTableColumnFilter: Filter not defined.', '#' + tableName);
                return;
            }

            me.storage.tables[tableName] = table;

            // Create filter elements
            me.createFilterRow(tableName, settings);

            // Show/hide filter cells on responsive resize
            table.on('responsive-resize', me.onResponsiveResize);

            // Move filter cells on column reorder
            //table.on('column-reorder', me.onColumnReorder)
        },

        /**
         * @param {string} columName
         * @param {array}  filterSettings
         *
         * @return {object} Filter settings for given column
         */
        getFilterSettingsByName: function (columName, filterSettings) {
            return filterSettings.find(function (setting) {
                if (setting.hasOwnProperty('name') && setting.name === columName) {
                    return setting;
                }
            });
        },

        /**
         * Create header row with filter elements
         *
         * @param {string} tableName
         * @param {array}  filterTypes Types [none|text|number_range]
         */
        createFilterRow: function (tableName, filterTypes) {
            var $table = me.storage.tables[tableName];
            var $thead = $($table.table().header());
            var $filterRow = $('<tr role="row" class="column-filter-row"></tr>');

            $table.columns().every(function (index) {
                var that = this;
                var node = $(that.header());
                var columnName = node.data('name');
                var setting = me.getFilterSettingsByName(columnName, filterTypes);
                if (typeof setting === 'undefined') {
                    setting = {type: 'none', name: columnName}; // Keine Filter-Einstellung vorhanden
                }

                var filterType = setting.type;
                var $inputWrapper;
                var searchValue = that.search();
                var title = $(that.header()).html();
                var $filterHead = $('<th colspan="1" rowspan="1" aria-controls="' + tableName + '"></th>');
                $filterHead.toggle(that.responsiveHidden());

                if (filterType === 'none') {
                    $inputWrapper = $('<div class="column-filter column-filter-none"></div>');
                }

                if (filterType === 'text') {
                    $inputWrapper = $('<div class="column-filter column-filter-text"></div>');
                    var $inputField = $('<input type="text" class="column-filter-input">');
                    $inputField
                        .attr('placeholder', title)
                        .val(searchValue !== '' ? searchValue : '')
                        .data('title', title)
                        .data('index', index)
                        .on('keyup', function () {
                            var $me = $(this);
                            var value = $me.val();
                            if (that.search() !== value) {
                                me.debounce(function () { that.search(value).draw(); }, 350, that);
                            }
                        });
                    $inputField.appendTo($inputWrapper);
                }

                if (filterType === 'number_range') {
                    $inputWrapper = $('<div class="column-filter column-filter-numberrange"></div>');
                    var $inputField1 = $('<input type="text" class="column-filter-input" placeholder="von">');
                    var $inputField2 = $('<input type="text" class="column-filter-input" placeholder="bis">');

                    // Split search value: example "number_range:42|123" or "number_range:null|null"
                    var searchParts = searchValue
                        .replace('number_range:', '')
                        .split('|', 2);
                    if (typeof searchParts[0] !== 'undefined' && searchParts[0] === 'null') {
                        searchParts[0] = '';
                    }
                    if (typeof searchParts[1] !== 'undefined' && searchParts[1] === 'null') {
                        searchParts[1] = '';
                    }
                    if (typeof searchParts[1] === 'undefined') {
                        searchParts[1] = '';
                    }

                    $inputField1
                        .val(searchParts[0])
                        .data('index', index);
                    $inputField2
                        .val(searchParts[1])
                        .data('index', index);

                    $inputField2
                        .on('keyup', function () {
                            var valueFrom = $inputField1.val();
                            var valueTo = $inputField2.val();
                            var searchValue = 'number_range:' + valueFrom + '|' + valueTo;
                            me.debounce(function () {
                                that.search(searchValue).draw();
                            }, 350, that);
                        });
                    $inputField1
                        .on('keyup', function () {
                            var valueFrom = $inputField1.val() || null;
                            var valueTo = $inputField2.val() || null;
                            var searchValue = 'number_range:' + valueFrom + '|' + valueTo;
                            me.debounce(function () {
                                that.search(searchValue).draw();
                            }, 350, that);
                        });
                    $inputField1.appendTo($inputWrapper);
                    $inputField2.appendTo($inputWrapper);
                }

                $inputWrapper.appendTo($filterHead);
                $filterHead.appendTo($filterRow);
            });
            $filterRow.appendTo($thead);
        },

        /**
         * Show/hide filter cells on responsive resize
         *
         * @see https://datatables.net/reference/event/responsive-resize
         *
         * @param {Event}  e          jQuery event object
         * @param {object} datatable  DataTable API instance for the table in question
         * @param {Array}  colVisible An array of boolean values that represent the visibility of the columns
         */
        onResponsiveResize: function (e, datatable, colVisible) {
            var $filterCells = $(datatable.header()).find('tr:nth-child(2) th');
            $filterCells.each(function (index) {
                $(this).toggle(colVisible[index]);
            });
        },

        /**
         * Move filter cells on column reorder
         *
         * @param {Event}  e
         * @param {object} settings
         * @param {object} details
         */
        onColumnReorder: function (e, settings, details) {
            // @todo Filter-Spalten verschieben bei ColReorder
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


var DataTableExporter = (function ($) {
    "use strict";

    var me = {

        /**
         * @param {Event} e
         * @param {DataTable} dt
         * @param {jQuery} node
         * @param {object} buttonConfig
         */
        onClickExportButton: function (e, dt, node, buttonConfig) {
            var exportFormat = null;
            var exportResult = null;
            var ajaxUrl = dt.ajax.url();
            var ajaxParams = me.cloneObject(dt.ajax.params());

            var buttonName = buttonConfig.hasOwnProperty('name') ? buttonConfig.name : null;
            switch (buttonName) {
                case 'export-csv-all':
                    exportFormat = 'csv';
                    exportResult = 'all';
                    break;
                case 'export-csv-page':
                    exportFormat = 'csv';
                    exportResult = 'page';
                    break;
            }

            if (exportFormat === null || exportResult === null) {
                alert('Export nicht möglich. Fehlkonfiguration');
                throw 'Invalid export settings. Format or Result setting is empty.';
            }

            //
            ajaxParams.draw = 1;
            ajaxParams.export = {
                format: exportFormat,
                result: exportResult
            };
            var paramsString = $.param(ajaxParams);
            window.location = ajaxUrl + '&' + paramsString;
        },

        /**
         * @param {object} options
         */
        prepareButtonOptions: function (options) {
            if (options.hasOwnProperty('buttons')) {
                options.buttons.forEach(function (button) {

                    if (button.hasOwnProperty('action')) {
                        if (button.action === 'export-csv-all') {
                            button.name = 'export-csv-all';
                            button.action = DataTableExporter.handleExportButtonClick;
                        }
                        if (button.action === 'export-csv-page') {
                            button.name = 'export-csv-page';
                            button.action = DataTableExporter.handleExportButtonClick;
                        }
                    }

                    if (button.hasOwnProperty('extend') && button.extend === 'collection') {
                        me.prepareButtonOptions(button);
                    }
                });
            }
        },

        /**
         * @param {object} source
         *
         * @return {object}
         */
        cloneObject: function (source) {
            return JSON.parse(JSON.stringify(source));
        }
    };

    return {
        handleExportButtonClick: me.onClickExportButton,
        prepareButtonOptions: me.prepareButtonOptions
    }

})(jQuery);


var DataTableHelper = function ($, ColumnFilter, Exporter) {
    "use strict";

    var me = {

        storage: {
            debounceBuffer: null,
            filterParams: {},
            filterElements: [],

            /** @property {Array} filterStorage Filter-Informationen die im LocalStorage abgelegt werden */
            filterStorage: [],

            /** @property {Object} Assoziative Identity-Map der bereits initialisierten DataTables */
            tables: {},

            /** @property {Array} tablesConfig Init-Options wegspeichern */
            tablesConfig: {},

            /** @property {Object} Assoziative Map der zusätzlichen AJAX-Parameter aus der Initialisierung */
            ajaxParams: {}
        },

        init: function () {

            // Filter vor den DataTables initialisieren
            me.initFilter();

            // DataTables autom. initialisieren; außer bei <table data-autoinit="false">
            me.autoInitDataTables();

            // Filter-Events erst nach der Initialisierung registrieren!
            me.registerFilterEvents();
        },

        /**
         * DataTable-Filter initialisieren
         */
        initFilter: function () {
            $('input[data-datatable-filter]').each(function () {
                var that = this;
                var filterType = $(this).attr('type');

                if (filterType === 'checkbox') {
                    me.initCheckboxFilter(that);
                }
                if (filterType === 'text') {
                    me.initTextFilter(that);
                }
            });
            $('select[data-datatable-filter]').each(function () {
                var that = this;
                me.initSelectFilter(that);
            });
        },

        /**
         * Benötigte Events für Filter registrieren
         */
        registerFilterEvents: function () {
            $(document).on('change', 'select.datatable-filter', function () {
                me.onChangeFilterValueListener(this);
            });
            $(document).on('change', 'input[type="checkbox"].datatable-filter', function () {
                me.onChangeFilterValueListener(this);
            });
            $(document).on('keyup', 'input[type="text"].datatable-filter', function () {
                var that = this;
                me.debounce(function () {
                    me.onChangeFilterValueListener(that);
                }, 100);
            });
        },

        /**
         * @param {HTMLElement} element
         */
        onChangeFilterValueListener: function (element) {
            var $element = $(element);
            var filterId = $element.attr('id');
            var filterName = $element.data('datatable-filter');
            var targetTable = $element.data('datatable-target');
            var filterType = null;
            var filterValue = null;

            if (typeof filterId === 'undefined' || filterId === null) {
                console.warn('Filter element is unusable. Required property "id" is empty.', $element.get(0));
                return;
            }
            if (typeof filterName === 'undefined' || filterName === null) {
                console.warn('Filter element is unusable. Required property "data-datatable-filter" is empty.',
                    $element.get(0));
                return;
            }
            if (typeof targetTable === 'undefined' || targetTable === null) {
                console.warn('Filter element is unusable. Required property "data-datatable-target" is empty.',
                    $element.get(0));
                return;
            }

            if ($element.is('select')) {
                filterType = 'select';
            }
            if ($element.is('input')) {
                filterType = $element.attr('type');
            }
            if (filterType === 'checkbox') {
                filterValue = $element.prop('checked');
            }
            if (filterType === 'select') {
                filterValue = $element.val();
            }
            if (filterType === 'text') {
                filterValue = $element.val();
            }

            // Filter-Eigenschaften merken; für Speicherung in LocalStorage
            me.storage.filterParams[filterName] = filterValue;
            me.addFilterToLocalStorage(filterType, filterId, filterName, filterValue);

            // DataTable-Ergenisse neu laden
            me.reloadDataTable(targetTable);
        },

        /**
         *
         * @param {HTMLElement} element
         */
        initCheckboxFilter: function (element) {
            var $checkbox = $(element);
            var filterId = $checkbox.attr('id');
            var filterName = $checkbox.data('datatable-filter');
            var filterValue = $checkbox.prop('checked');

            if (filterId === '') {
                console.warn('Required attribute "id" is empty.', element);
            }
            if (filterName === '') {
                console.warn('Required attribute "datatable-filter" is empty.', element);
            }

            me.storage.filterParams[filterName] = filterValue;
            me.addFilterToLocalStorage('checkbox', filterId, filterName, filterValue);
        },

        /**
         *
         * @param {HTMLElement} element
         */
        initTextFilter: function (element) {
            var $textInput = $(element);
            var filterId = $textInput.attr('id');
            var filterName = $textInput.data('datatable-filter');
            var filterValue = $textInput.val();

            if (filterId === '') {
                console.warn('Required attribute "id" is empty.', element);
            }
            if (filterName === '') {
                console.warn('Required attribute "datatable-filter" is empty.', element);
            }

            me.storage.filterParams[filterName] = filterValue;
            me.addFilterToLocalStorage('text', filterId, filterName, filterValue);
        },

        /**
         *
         * @param {HTMLElement} element
         */
        initSelectFilter: function (element) {
            var $select = $(element);
            var filterId = $select.attr('id');
            var filterName = $select.data('datatable-filter');
            var filterValue = $select.val();

            if (filterId === '') {
                console.warn('Required attribute "id" is empty.', element);
            }
            if (filterName === '') {
                console.warn('Required attribute "datatable-filter" is empty.', element);
            }

            me.storage.filterParams[filterName] = filterValue;
            me.addFilterToLocalStorage('select', filterId, filterName, filterValue);
        },

        /**
         * DataTable automatisch initialisieren
         *
         * Struktur:
         * <div class="datatable-container">
         *   <table id="example" class="dataTable"></table>
         *   <script type="application/json"></script>
         * </div>
         */
        autoInitDataTables: function () {
            $('.datatable-container').each(function () {
                var $container = $(this);
                var $table = $container.children('table').first();
                var tableId = $table.attr('id');

                // data-autoinit="false"
                var autoInit = $table.data('autoinit');
                if (typeof autoInit !== 'undefined' && autoInit === false) {
                    console.info('Auto init for table "' + tableId + '" disabled.');
                    return;
                }

                if (typeof tableId === 'undefined' || tableId === null || tableId === '') {
                    console.error('DataTable can not be initialized. Required attribute "id" is empty.', $table.get(0));
                    return;
                }

                // DataTable initialisieren
                me.initDataTable(tableId);
            });
        },

        /**
         * DataTable initialisieren (wenn nicht autoInit)
         *
         * @param {string} tableName
         *
         * @throws
         */
        initDataTable: function (tableName) {
            if (me.isInitialized(tableName)) {
                console.warn('Can not reinitialize DataTable #' + tableName + '. DataTable is already initialized.');
                return;
            }

            var $table = $('#' + tableName);
            var $wrapper = $table.parent();
            var $config = $wrapper.children('script');

            try {
                var configJson = JSON.parse($config.html());
                if (typeof configJson !== 'object') {
                    throw 'JSON settings are invalid.';
                }

                var tableNameRequested = null;
                if (configJson.hasOwnProperty('ajax') &&
                    configJson.ajax.hasOwnProperty('data') &&
                    configJson.ajax.data.hasOwnProperty('tablename')) {
                    tableNameRequested = configJson.ajax.data.tablename;
                }

                if (tableNameRequested !== tableName) {
                    throw 'Table names does not match.';
                }

                // Save config before modifing (for debugging only)
                me.storage.tablesConfig[tableName] = me.cloneObject(configJson);

                // Save additional ajax parameter
                me.storage.ajaxParams[tableName] = me.cloneObject(configJson.ajax.data);

                // Attach additional filter params
                configJson.ajax.data = me.fetchFilterParams;

                // Register state saving and loading callbacks
                configJson.stateLoadParams = me.onStateLoadParams;
                configJson.stateSaveParams = me.onStateSaveParams;

                Exporter.prepareButtonOptions(configJson.buttons);

                // Initialize DataTable
                me.storage.tables[tableName] = $table.DataTable(configJson);

                // Add Column filters
                if (configJson.hasOwnProperty('columnFilter')) {
                    var colFilterSettings = configJson.columnFilter;
                    ColumnFilter.init(tableName, colFilterSettings);
                }
            }
            catch (err) {
                console.error('Can not init datatable "' + tableName + '". Error: ' + err);
            }
        },

        /**
         * DataTable-Inhalte neu laden (per AJAX)
         *
         * @param {string}  tableName
         * @param {boolean} resetPaging Auf die erste Seite springen? (Default: true)
         */
        refreshDataTable: function (tableName, resetPaging) {
            if (!me.isInitialized(tableName)) {
                console.warn('Can not refresh DataTable #' + tableName + '. DataTable is not initialized.');
                return;
            }

            resetPaging = (typeof resetPaging === 'boolean') ? resetPaging : true;
            me.storage.tables[tableName].ajax.reload(null, resetPaging);
        },

        /**
         * DataTable-Instanz zerstören
         *
         * @param {string} tableName
         */
        destroyDataTable: function (tableName) {
            if (!me.isInitialized(tableName)) {
                console.warn('Can not destroy DataTable #' + tableName + '. DataTable is not initialized.');
                return;
            }

            me.storage.tables[tableName].destroy();

            delete me.storage.tables[tableName];
            delete me.storage.tablesConfig[tableName];
        },

        addFilterToLocalStorage: function (filterType, elementId, filterName, filterValue) {
            var foundInStorage = false;

            // Versuche vorhandenen Eintrag zu aktualisieren
            me.storage.filterStorage.forEach(function (storage) {
                if (storage.elementId === elementId) {
                    storage.value = filterValue;
                    foundInStorage = true;
                }
            });

            // Eintrag fehlt > Anlegen
            if (!foundInStorage) {
                me.storage.filterStorage.push({
                    elementId: elementId,
                    filterType: filterType,
                    filterParam: filterName,
                    value: filterValue
                });
            }
        },

        /**
         * Filterwerte aus LocalStorage wiederherstellen
         *
         * @param {Object} settings
         * @param {Object} data
         */
        onStateLoadParams: function (settings, data) {
            if (typeof data === 'undefined' || typeof settings === 'undefined') {
                return;
            }
            if (typeof settings.sTableId === 'undefined') {
                return;
            }
            if (typeof data.filter === 'undefined') {
                return;
            }

            // console.log('onStateLoadParams');
            // var newRevision = null;
            // var lastRevision = null;
            // if (data.hasOwnProperty('revision')) {
            //     lastRevision = data.revision;
            // }
            // if (settings.hasOwnProperty('oInit') && settings.oInit.hasOwnProperty('revision')) {
            //     newRevision = settings.oInit.revision;
            // }
            // console.log({lastRevision:lastRevision,newRevision:newRevision});
            // if (lastRevision !== newRevision) {
            //     // alert('Einstellungen werden zurückgesetzt.');
            //     // return false;
            // }

            // Filterwerte merken
            me.storage.filterStorage = data.filter;

            // Filterwerte in HTML-Elemente schreiben
            me.restoreFilterElements(data.filter); // @todo evtl in onStateLoaded
        },

        /**
         * Filter-Informationen im LocalStorage speichern
         *
         * @param {Object} settings
         * @param {Object} data
         */
        onStateSaveParams: function (settings, data) {
            if (typeof data === 'undefined') {
                return;
            }
            if (typeof data.filter === 'undefined') {
                data.filter = {};
            }

            data.filter = me.storage.filterStorage;

            //var revision = null;
            // Revision speichern, zum Zurücksetzen der Einstellungen
            //if (settings.hasOwnProperty('oInit') && settings.oInit.hasOwnProperty('revision')) {
            //    data.revision = settings.oInit.revision;
            //}
            //data.revision = revision;
        },

        /**
         * @param {Object} data
         * @param {Object} settings DataTables.Settings object
         */
        fetchFilterParams: function (data, settings) {
            if (typeof data !== 'object' || typeof settings !== 'object') {
                return;
            }

            // Add additional ajax parameter from initialisation object
            var tableName = settings.sTableId;
            var ajaxParams = me.storage.ajaxParams[tableName];
            Object.keys(ajaxParams).forEach(function (key) {
                data[key] = ajaxParams[key];
            });

            // Add filter params and values to ajax data
            if (typeof data.filter === 'undefined') {
                data.filter = {};
            }
            data.filter = me.storage.filterParams;
        },

        /**
         * Filter-Elemente (HTML) aus LocalStorage wiederherstellen
         *
         * @param {Object} data
         */
        restoreFilterElements: function (data) {
            data.forEach(function (item) {
                var $element = $('#' + item.elementId);
                if ($element.length !== 1) {
                    return;
                }
                switch (item.filterType) {
                    case 'checkbox':
                        $element.prop('checked', item.value);
                        break;
                    case 'text':
                        $element.val(item.value);
                        break;
                    case 'select':
                        $element.val(item.value);
                        break;
                    default:
                        return;
                }
                me.storage.filterParams[item.filterParam] = item.value;
            });
        },

        /**
         * @param {string} tableName
         *
         * @return {object|null}
         */
        getInitConfig: function (tableName) {
            if (!me.storage.tablesConfig.hasOwnProperty(tableName)) {
                return null;
            }

            return me.storage.tablesConfig[tableName];
        },

        /**
         * DataTable neu laden
         *
         * @param {string} tableName
         */
        reloadDataTable: function (tableName) {
            if (!me.isInitialized(tableName)) {
                return;
            }

            me.storage.tables[tableName].draw();
        },

        /**
         * @param {string}  tableName
         *
         * @return {boolean}
         */
        isInitialized: function (tableName) {
            return me.storage.tables.hasOwnProperty(tableName) && me.storage.tables[tableName] !== null;
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

            window.clearTimeout(me.storage.debounceBuffer);
            me.storage.debounceBuffer = window.setTimeout(function () {
                callback.apply(context, args);
            }, delay || 250);
        },

        /**
         * @param {object} source
         *
         * @return {object}
         */
        cloneObject: function (source) {
            return JSON.parse(JSON.stringify(source));
        }
    };

    return {
        init: me.init,
        isInitialized: me.isInitialized,
        initDataTable: me.initDataTable,
        destroyDataTable: me.destroyDataTable,
        refreshDataTable: me.refreshDataTable,
        getInitConfig: me.getInitConfig
    };

}(jQuery, DataTableColumnFilter, DataTableExporter);


var DataTableDebugger = (function ($, Helper) {
    "use strict";

    var me = {

        storage: {
            /** @property {object} Assoziative Identity-Map der bereits initialisierten DataTables */
            tables: {}
        },

        init: function () {
            $(document).on('init.dt', function (e, settings) {
                var api = new $.fn.dataTable.Api(settings);
                var $table = $(api.table().node());
                if ($table.hasClass('datatable-debug')) {
                    me.registerTable($table);
                }
            });
        },

        /**
         * @param {jQuery} $table jQuery element
         */
        registerTable: function ($table) {
            var tableName = $table.attr('id');
            me.storage.tables[tableName] = $table;

            me.appendDebugElements($table);
            me.registerEventListener($table);
        },

        /**
         * @param {jQuery} $table jQuery element
         */
        appendDebugElements: function ($table) {
            var $wrapper = $table.closest('.dataTables_wrapper');
            if ($wrapper.length === 0) {
                return;
            }

            var api = $table.dataTable().api();
            var stateLoadedString = JSON.stringify(api.state.loaded(), undefined, 4);

            var $debugSql = $('<pre class="datatable-debug-sql">');
            var $row1Left = $('<div class="debug-col-8">')
                .append('<h3>Statement <small>(sql query)</small></h3>', $debugSql);
            var $debugBind = $('<pre class="datatable-debug-bind">');
            var $debugDuration = $('<pre class="datatable-debug-profiler">');
            var $row1Right = $('<div class="debug-col-4">')
                .append('<h3>Bind values <small>(sql query)</small></h3>', $debugBind)
                .append('<h3>Profiler <small>(sql query)</small></h3>', $debugDuration);
            var $row1 = $('<div class="debug-row">').append($row1Left).append($row1Right);

            var $debugConfig = $('<pre class="datatable-debug-config">');
            var $row2Left = $('<div class="debug-col-8">')
                .append('<h3>Config options <small>(onInit)</small></h3>', $debugConfig);
            var $debugStorage = $('<pre class="datatable-debug-storage">').html(stateLoadedString);
            var $row2Right = $('<div class="debug-col-4">')
                .append('<h3>State loaded <small>(onInit)</small></h3>', $debugStorage);
            var $row2 = $('<div class="debug-row">').append($row2Left).append($row2Right);

            $('<div class="debug-container">').append($row1, $row2).appendTo($wrapper);

            var ajaxResult = api.ajax.json();
            me.fillDebugElements($table, ajaxResult);
        },

        /**
         * @param {jQuery} $table jQuery element
         */
        registerEventListener: function ($table) {
            $table.on('xhr.dt', function (e, settings, data) {
                me.fillDebugElements($table, data);
            });
            $table.on('preXhr.dt', function (e, settings, data) {
                me.fillDebugElements($table, data);
            });
        },

        /**
         * @param {jQuery} $table jQuery element
         * @param {object} ajaxResult
         */
        fillDebugElements: function ($table, ajaxResult) {
            if (!ajaxResult.hasOwnProperty('debug')) {
                return;
            }

            var $wrapper = $table.parents('.dataTables_wrapper').first();
            var tableName = $table.attr('id');
            var initConfigOptions = Helper.getInitConfig(tableName);

            if (initConfigOptions !== null) {
                var initConfigString = JSON.stringify(initConfigOptions, undefined, 4);
                $wrapper.find('.datatable-debug-config').html(initConfigString);
            }
            if (ajaxResult.debug.hasOwnProperty('query')) {
                $wrapper.find('.datatable-debug-sql').html(ajaxResult.debug.query.statement);
                $wrapper.find('.datatable-debug-bind').html(ajaxResult.debug.query.bindings);
            }
            if (ajaxResult.debug.hasOwnProperty('profiler')) {
                var profilerString = JSON.stringify(ajaxResult.debug.profiler, undefined, 4);
                $wrapper.find('.datatable-debug-profiler').html(profilerString);
            }
        }
    };

    return {
        init: me.init
    };

})(jQuery, DataTableHelper);


var DataTableRowDetails = (function ($) {
    "use strict";

    var me = {

        storage: {
            urls: {},
            methods: {}
        },

        init: function () {
            me.registerEvents();
        },

        registerEvents: function () {
            $(document).on('init.dt', function (e, settings) {
                var tableName = settings.sTableId;
                var initOptions = settings.oInit;
                if (!initOptions.hasOwnProperty('rowDetails')) {
                    return;
                }
                if (!initOptions.rowDetails.hasOwnProperty('ajax')) {
                    return;
                }
                if (!initOptions.rowDetails.ajax.hasOwnProperty('url')) {
                    return;
                }

                // Ajax-Url pro DataTable speichern
                me.storage.urls[tableName] = initOptions.rowDetails.ajax.url;
                me.storage.methods[tableName] = initOptions.rowDetails.ajax.method || 'POST';
                me.registerTableEvents(tableName);
            });
        },

        /**
         * @param {string} tableName
         */
        registerTableEvents: function (tableName) {
            var $table = $('#' + tableName);
            if ($table.length === 0) {
                return;
            }
            var ajaxMethod = me.storage.methods[tableName];
            var ajaxUrl = me.storage.urls[tableName];
            if (typeof ajaxUrl === 'undefined') {
                return;
            }

            $table.on('click', '.dt-details .details', function (e) {
                e.preventDefault();
                var $opener = $(this);
                var $parentRow = $opener.closest('tr');
                var $table = $opener.closest('table.dataTable');
                var api = $table.dataTable().api();
                var row = api.row($parentRow);
                var child = row.child;

                if (child.isShown()) {
                    // Details ausblenden
                    row.child(false);
                    $parentRow.removeClass('parent');
                    $opener.removeClass('open');

                } else {
                    // Details laden und einblenden
                    var ajaxData = $opener.data();
                    me.fetchChildRow(ajaxMethod, ajaxUrl, ajaxData).then(function (htmlContent) {
                        row.child(htmlContent, 'child').show();
                        $parentRow.addClass('parent');
                        $opener.addClass('open');
                    });
                }
            });
        },

        /**
         * @param {string} ajaxMethod
         * @param {string} ajaxUrl
         * @param {Object} ajaxParams
         *
         * @return {Deferred}
         */
        fetchChildRow: function (ajaxMethod, ajaxUrl, ajaxParams) {
            return $.ajax({
                method: ajaxMethod,
                url: ajaxUrl,
                data: ajaxParams,
                dataType: 'html'
            });
        }
    };

    return {
        init: me.init
    };

})(jQuery);


/**
 * Client-side formatter
 */
// var DataTableFormatter = (function ($) {
//     "use strict";
//
//     var me = {
//
//         formatBytes: function (data, type, rowData, meta) {
//             console.log('formatBytes', data, type);
//             switch (type) {
//                 case 'display':
//                     return me.formatBytesForDisplay(data);
//
//                 case 'sort':
//                     return me.formatBytesForSorting(data);
//
//                 case 'type':
//                     return 'num';
//
//                 default:
//                     return data;
//             }
//         },
//
//         /**
//          * @param {string} value
//          *
//          * @return {string}
//          */
//         formatBytesForDisplay: function(value) {
//             var bytes = parseInt(value);
//             if (bytes === 0) {
//                 return '0 Bytes';
//             }
//
//             var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
//             var exponent = Math.floor(Math.log(bytes) / Math.log(1024));
//             var decimalString = parseFloat((bytes / Math.pow(1024, exponent)).toFixed(1)) + '';
//
//             return decimalString.replace('.', ',') + '&nbsp;' + sizes[exponent];
//         },
//
//         /**
//          * @param {string} value
//          *
//          * @return {number}
//          */
//         formatBytesForSorting: function (value) {
//             return parseInt(value);
//         }
//
//     };
//
//     return {
//         formatBytes: me.formatBytes
//     };
//
// })(jQuery);


$(document).ready(function () {
    DataTableHelper.init();
    DataTableRowDetails.init();
    DataTableDebugger.init();
});
