/**
 * Modul zum Nachladen von Labeln in Datatables
 *
 * Markup:
 * `<a href="#" class="label-manager"
 *     data-label-reference-table="wiedervorlage"
 *     data-label-reference-id="6"
 *     data-label-column-number="5">
 *   <span class="label-manager-icon"></span>
 * </a>
 *
 * Vorgehen:
 * 1. Auf Event warten dass eine DataTable fertig gerendert ist
 * 2. Prüfen ob DataTable Markup zum Nachladen für Labels enthält
 * 3. HTML-Markup für LabelLoader-Modul erzeugen
 * 4. LabelLoader-Modul aufrufen (Modul sammelt benötigte Informationen; lädt Labels per AJAX nach und rendert diese)
 */
var DataTableLabelLoader = (function ($, LabelLoaderModule) {
    'use strict';

    var me = {

        init: function () {
            me.registerEvents();
        },

        registerEvents: function () {
            $(document).on('draw.dt', function (e, settings) {
                me.onDrawDataTableDraw(settings.sTableId);
            });
        },

        /**
         * EventHandler fürs Nachladen von Labeln
         *
         * Der EventHandler greift wenn eine DataTable fertig gerendert ist.
         *
         * @param {string} tableName
         */
        onDrawDataTableDraw: function (tableName) {
            // Prüfen ob LabelLoader manuell erstellt wurde, oder noch erstellt werden muss
            if (me.checkLoaderRequired(tableName) === true) {
                var $firstManager = $('#' + tableName).find('.label-manager').first();
                var columnNumber = $firstManager.data('labelColumnNumber');
                if (typeof columnNumber !== 'undefined') {
                    me.createLabelLoader(tableName, columnNumber);
                }
            }

            // Benötigte Elemente mit Data-Attributen wurden erstellt
            // Ab hier übernimmt das "normale" LabelLoader-Modul
            LabelLoaderModule.loadAll();
        },

        /**
         * @param {string} tableName
         *
         * @return {boolean}
         */
        checkLoaderRequired: function (tableName) {
            var $table = $('#' + tableName);
            var hasLabelContainer = $table.find('.label-container').length > 0;
            var hasLabelManager = $table.find('.label-manager').length > 0;

            return hasLabelManager === true && hasLabelContainer === false;
        },

        /**
         * Markup für LabelLoader-Modul erzeugen
         *
         * Markup:
         * `<span class="label-loader" data-label-reference-id="1" data-label-reference-table="adresse"></span>`
         *
         * @param {string} tableName
         * @param {number} columnNumber
         */
        createLabelLoader: function (tableName, columnNumber) {
            var $table = $('#' + tableName);
            var $rows = $table.children('tbody').children('tr');
            
            $rows.each(function (index, row) {
                var $row = $(row);
                var $labelManager = $row.find('.label-manager').first();
                if ($labelManager.length === 0) {
                    return;
                }
                var referenceId = $labelManager.data('labelReferenceId');
                var referenceTable = $labelManager.data('labelReferenceTable');
                if (typeof referenceId === 'undefined' || typeof referenceTable === 'undefined') {
                    return;
                }

                var $cell = $row.children('td').eq(columnNumber - 1);
                var $labelLoader = $('<span>').addClass('label-loader').data({
                    labelReferenceId: referenceId,
                    labelReferenceTable: referenceTable
                });
                $cell.append($labelLoader);
            });
        }
    };

    // Modul so früh wie möglich registrieren.
    // Ansonsten kriegt man nicht alle DataTable Draw-Events mit.
    me.init();

    return {
        init: me.init
    }

})(jQuery, LabelLoader);
