var ChartHelper = (function ($, ChartJs) {

    var me = {

        elem: {
            $chartWrapper: null
        },

        storage: {
            charts: []
        },

        init: function () {
            me.elem.$chartWrapper = $('.chart-wrapper');

            // Keine Charts gefunden > Ende
            if (me.elem.$chartWrapper.length === 0) {
                return;
            }

            me.initCharts();
        },

        /**
         * Alle Diagramme initialisieren
         */
        initCharts: function () {
            var interval = 250;
            me.elem.$chartWrapper.each(function () {
                var $wrapper = $(this);

                // Alle 250 ms ein Chart initialisieren
                window.setTimeout(function () {
                    me.initChart($wrapper);
                }, interval);

                interval += 250;
            });
        },

        /**
         * Einzelnes Diagramm initialisieren
         *
         * @param {jQuery} $wrapper
         *
         * @return {Chart}
         */
        initChart: function ($wrapper) {
            var chartCanvas = $wrapper.find('canvas');
            var chartJson = $wrapper.find('script');
            var chartData = JSON.parse(chartJson.html());

            var graphId = chartCanvas.data('graph-id');
            var graph = new ChartJs(chartCanvas, chartData);

            if (typeof graphId === 'undefined') {
                graphId = me.generateRandomId();
                chartCanvas.data('graph-id', graphId);
            }
            me.storage.charts[graphId] = graph;

            return graph;
        },

        /**
         * Chart-Instanz abrufen
         *
         * @param {String} chartId
         *
         * @return {Chart}
         */
        getChart: function (chartId) {
            return me.storage.charts[chartId];
        },

        /**
         * Alle Chart-Instanzen abrufen
         *
         * @return {Array|Chart[]}
         */
        getCharts: function () {
            return me.storage.charts;
        },

        /**
         * Zufällige ID generieren
         *
         * @return {string}
         */
        generateRandomId: function () {
            return 'chart-' + Math.floor(Math.random() * Math.floor(9999999999));
        }
    };

    return {
        init: me.init,
        initChart: me.initChart,
        getChart: me.getChart,
        getCharts: me.getCharts
    };

})(jQuery, Chart);

$(document).ready(ChartHelper.init);
