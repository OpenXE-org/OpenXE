/**
 * Projektplan über alle Projekte
 */
var ProjectScheduleOverview = (function ($) {
    'use strict';

    var me = {

        storage: {
            $jsonElement: null,
            $container: null
        },

        init: function () {
            me.storage.$jsonElement = $('#project-schedule-jsondata');
            if (me.storage.$jsonElement.length === 0) {
                return;
            }
            me.storage.$container = $('#project-schedule-overview');
            if (me.storage.$container.length === 0) {
                return;
            }

            me.registerEvents();

            // Json einlesen und Gantt-Diagramm initialisieren
            var scheduleJson = me.storage.$jsonElement.html();
            var scheduleData = JSON.parse(scheduleJson);
            me.initGantt(scheduleData);
        },

        registerEvents: function () {
            $('.print-button').on('click', function (e) {
                e.preventDefault();
                window.print();
            });
        },

        /**
         * Gantt-Diagramm initialisieren
         *
         * @see http://taitems.github.io/jQuery.Gantt/
         *
         * @param {object} scheduleData
         */
        initGantt: function (scheduleData) {
            var $ganttItem = $('<div class="project-gantt">').appendTo(me.storage.$container);

            $ganttItem.gantt({
                source: scheduleData,
                navigate: 'scroll',
                scale: 'days',
                maxScale: 'months',
                minScale: 'hours',
                itemsPerPage: 20,
                useCookie: false
            });
        }
    };

    return {
        init: me.init
    };

})(jQuery);

/**
 * Projektplan für ein Projekt
 */
var ProjectScheduleDetail = (function ($) {
    'use strict';

    var me = {

        storage: {
            $jsonData: null,
            $container: null
        },

        init: function () {
            me.storage.$jsonData = $('#project-schedule-jsondata');
            if (me.storage.$jsonData.length === 0) {
                return;
            }
            me.storage.$container = $('#project-schedule-detail');
            if (me.storage.$container.length === 0) {
                return;
            }

            me.registerEvents();

            // Json einlesen und Gantt-Diagramm initialisieren
            var scheduleJson = me.storage.$jsonData.html();
            var scheduleData = JSON.parse(scheduleJson);
            me.initGantt(scheduleData);
        },

        registerEvents: function () {
            $('.print-button').on('click', function (e) {
                e.preventDefault();
                window.print();
            });
        },

        /**
         * Gantt-Diagramm initialisieren
         *
         * @see http://taitems.github.io/jQuery.Gantt/
         *
         * @param {object} scheduleData
         */
        initGantt: function (scheduleData) {
            var $ganttItem = $('<div class="project-gantt">').appendTo(me.storage.$container);

            $ganttItem.gantt({
                source: scheduleData,
                navigate: 'scroll',
                scale: 'days',
                maxScale: 'months',
                minScale: 'hours',
                itemsPerPage: 100,
                useCookie: false,
                onItemClick: function (dataObj) {
                    me.handleClick(dataObj);
                }
            });
        },

        /**
         *
         * @param {Object} dataObj
         */
        handleClick: function (dataObj) {
            if (!dataObj.hasOwnProperty('type')) {
                throw 'Kann Klick nicht verarbeiten. Typ fehlt.';
            }
            if (!dataObj.hasOwnProperty('projectId')) {
                throw 'Kann Klick nicht verarbeiten. Projekt-ID fehlt.';
            }

            var objectType = dataObj.type;
            var projectId = dataObj.projectId;

            // Meilenstein wurde angeklickt
            if (objectType === 'milestone') {
                var taskId = dataObj.milestoneId;
                $.ajax({
                    url: 'index.php?module=projekt&action=gant&cmd=getkalender&id=' + projectId,
                    type: 'POST',
                    dataType: 'json',
                    data: {uid: taskId},
                    success: function (data) {
                        $('#kalenderbeschreibung').html(data.beschreibung);
                        $('#kalenderid').val(taskId);
                        $('#kalenderbezeichnung').html(data.bezeichnung);
                        $('#kalendervon').html(data.von);
                        $('#kalenderbis').html(data.bis);
                        $('#dialogkalender').dialog('open');
                    }
                });
            }

            // Arbeitspaket wurde angeklickt
            if (objectType === 'package') {
                var packageId = dataObj.packageId;
                $.ajax({
                    url: 'index.php?module=projekt&action=gant&cmd=getteilprojekt&id=' + projectId,
                    type: 'POST',
                    dataType: 'json',
                    data: {uid: packageId},
                    success: function (data) {
                        $('#beschreibung').val(data.beschreibung);
                        $('#cke_beschreibung').find('iframe').contents().find('body').html(data.beschreibung);
                        $('#teilprojekt').val(packageId);
                        $('#aufgabe').val(data.aufgabe);
                        $('#adresse').val(data.verantwortlicher);
                        $('#farbe').val(data.farbe).change();
                        $('#status').val(data.status).change();
                        $('#startdatum').val(data.startdatum);
                        $('#abgabedatum').val(data.abgabedatum);
                        $('#dialoggantt').dialog('open');
                    }
                });
            }
        }
    };

    return {
        init: me.init
    };

})(jQuery);

$(document).ready(function () {
    // Projektplan über alle Projekte
    if ($('#project-schedule-overview').length > 0) {
        ProjectScheduleOverview.init();
    }

    // Projektplan für ein Projekt
    if ($('#project-schedule-detail').length > 0) {
        ProjectScheduleDetail.init();
    }
});
