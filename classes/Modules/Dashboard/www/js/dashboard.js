var Dashboard = (function ($, ChartHelper) {

    var me = {

        elem: {
            $dashboards: null,
            $dashboardDialog: null,
            $widgetDialog: null
        },

        storage: {
            dashboards: [],
            debounceTimer: null
        },

        template: {
            dashboardControls:
                '<span class="icon-edit dashboard-edit-button"></span>' +
                '<span class="icon-add dashboard-add-button"></span>',
            widgetControls:
                '<div class="item-controls-left">' +
                '<a href="#" class="resize" data-width="1" data-height="1">1x1</a>' +
                '<a href="#" class="resize" data-width="1" data-height="2">1x2</a>' +
                '<a href="#" class="resize" data-width="2" data-height="1">2x1</a>' +
                '<a href="#" class="resize" data-width="2" data-height="2">2x2</a>' +
                '</div>' +
                '<div class="item-controls-right">' +
                '<span class="icon-settings"></span>' +
                '<span class="icon-move"></span>' +
                '</div>'
        },

        init: function () {
            if ($('#dashboard').length === 0) {
                return;
            }

            me.elem.$dashboards = $('.dashboard');
            me.initDashboards();
            me.fillDashboardControls();

            me.fillWidgetControls();

            me.setGridLayout();

            me.initDashboardDialog();
            me.initWidgetDialog();

            me.registerEvents();
        },

        registerEvents: function () {

            $(window).on('resize', function () {
                me.debounce(me.setGridLayout, 250);
            });

            $(document).on('click', '.item-controls .icon-settings', function (e) {
                e.preventDefault();
                me.elem.$widgetDialog.dialog('open');
            });

            $(document).on('click', '.item-controls .resize', function (e) {
                e.preventDefault();

                var $elem = $(this);
                var $item = $elem.parents('.item');
                var width = parseInt($elem.data('width'));
                var height = parseInt($elem.data('height'));
                var newAspectRatio = width / height;

                $item.removeClass('item-width-2').removeClass('item-height-2');

                if (width > 1) {
                    $item.addClass('item-width-' + width);
                }
                if (height > 1) {
                    $item.addClass('item-height-' + height);
                }

                var $chart = $item.find('canvas');
                var graphId = $chart.data('graph-id');
                var graph = ChartHelper.getChart(graphId);

                if (typeof graph === 'object') {
                    graph.aspectRatio = newAspectRatio;
                    graph.update();
                }

                me.refreshGridLayouts();
            });

            $(document).on('click', '.dashboard-edit-button', function () {
                me.elem.$dashboadDialog.dialog('open');
            });

            $(document).on('click', '.dashboard-add-button', function () {
                alert('TODO');
            });
        },

        initDashboards: function () {
            me.elem.$dashboards.each(function (index, container) {
                var grid = new Muuri(container, {
                    items: '.item',
                    dragEnabled: true,
                    dragStartPredicate: {
                        distance: 0,
                        delay: 0,
                        handle: '.icon-move'
                    },
                    dragSort: function () {
                        return me.storage.dashboards;
                    },
                    dragSortPredicate: {
                        action: 'move',
                        threshold: 50
                    },
                    layout: {
                        fillGaps: true, // Default: false
                        horizontal: false,
                        alignRight: false,
                        alignBottom: false,
                        rounding: true // true für relative Abmessungen; false für absolute Abmessungen besser
                    },
                    layoutOnInit: true,
                    layoutOnResize: 200
                });

                /*grid.on('layoutEnd', function () {
                    console.log('layoutEnd');
                    //me.resizeTitle();
                });*/

                me.storage.dashboards.push(grid);
            });
        },

        fillDashboardControls: function () {
            var $controls = $('.dashboard .dashboard-controls');
            $controls.each(function () {
                $(this).html(me.template.dashboardControls);
            });
        },

        fillWidgetControls: function () {
            var $controls = $('.dashboard .item-controls');
            $controls.each(function () {
                $(this).html(me.template.widgetControls);
            });
        },

        setGridLayout: function () {
            var windowWidth = $(window).width();
            var $dashboards = $('.dashboard');

            if (windowWidth < 1080) {
                $dashboards.addClass('halfsize');
            } else {
                $dashboards.removeClass('halfsize');
            }

            me.refreshGridLayouts();
        },

        refreshGridLayouts: function () {
            me.storage.dashboards.forEach(function (dashboard) {
                dashboard.refreshItems();
                dashboard.layout();
            });

            $('.xx-large').fitText(.5);
        },

        initDashboardDialog: function () {
            me.elem.$dashboadDialog = $('#dashboard-dialog').dialog({
                title: 'Dashboard-Einstellungen',
                modal: true,
                minWidth: 900,
                closeOnEscape: false,
                autoOpen: false,
                resizable: false
            });
        },

        initWidgetDialog: function () {
            me.elem.$widgetDialog = $('#widget-dialog').dialog({
                title: 'Widget-Einstellungen',
                modal: true,
                minWidth: 500,
                closeOnEscape: false,
                autoOpen: false,
                resizable: false
            });
        },

        updateChartAspectRatio: function (chart, aspectRatio) {
            chart.options.aspectRatio = aspectRatio;
            chart.update();
        },

        resizeTitle: function () {
            var $titleElements = $('.dashboard .title');
            $titleElements.each(function () {
                var $title = $(this);
                var $dashboard = $title.parent();
                var dashboardWidth = $dashboard.width();
                var columnCount = Math.floor(dashboardWidth / 210);
                var titleWidthCalc = columnCount * 210;
                $title.outerWidth(titleWidthCalc - 10);
            });
        },

        debounce: function (callback, delay) {
            var context = this;
            var args = arguments;

            clearTimeout(me.storage.debounceTimer);
            me.storage.debounceTimer = setTimeout(function () {
                callback.apply(context, args);
            }, delay || 250);
        }
    };

    return {
        init: me.init
    };

})(jQuery, ChartHelper);

$(document).ready(Dashboard.init);
