/**
 * Das Plugin formatiert die Tooltip-Werte
 *
 * * Komma als Dezimaltrenner
 * * Zwei Stellen nach dem Komma
 *
 * Plugin muss nach chart.js eingebunden werden.
 * Plugin wird automatisch für alle Charts angewendet.
 */
Chart.plugins.register({
    id: 'tooltip-money-format',
    beforeInit: function (chart) {
        chart.options.tooltips.callbacks.label = function (tooltipItem, data) {
            var label = data.datasets[tooltipItem.datasetIndex].label || '';
            var value = tooltipItem.yLabel;

            // yLabel ist nicht gefüllt bei Pie-/Doughnut-Charts
            if (tooltipItem.yLabel === '') {
                var dataIndex = tooltipItem.index;
                var datasetIndex = tooltipItem.datasetIndex;
                value = data.datasets[datasetIndex].data[dataIndex];

                // Falls Daten Labels haben > Label mit anhängen
                var dataLabel = data.labels[dataIndex];
                if (dataLabel) {
                    label += ' ' + dataLabel;
                }
            }

            // Wert auf zwei Stellen runden
            value = Math.round(value * 100) / 100;
            // Wert immer mit zwei Nachkommastellen und Tausendertrenner ausgeben
            value = value.toLocaleString('de-DE', {minimumFractionDigits: 2, maximumFractionDigits: 2});

            if (label) {
                label += ': ';
            }

            return label + value;
        };
    }
});

Chart.plugins.register({
    id: 'logartimic-scale',
    beforeLayout: function (chart) {
        for (var i = 0; i < chart.options.scales.yAxes.length; i++) {
            if (chart.options.scales.yAxes[i].type === 'logarithmic') {
                chart.options.scales.yAxes[i].ticks.callback = function (val) {
                    return val;
                };
            }
        }
    }
});
