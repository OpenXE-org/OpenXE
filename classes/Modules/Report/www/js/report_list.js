
/**
 * Copy/Delete a Report
 */
var TileView = (function ($) {
    'use strict';

    var me = {
        isInitialized: false,

        selector: {
            keyElement: '#report_list_main',
            tileView: '#reportTileView',
            filterButton: '#reportListFilterApply',
            filterCategory: '#reportListFilterCategory',
            filterCategorySelect: '#reportListFilterCategory > ul.ui-autocomplete',
            filterOwn: '#report-list-filter-own',
            filterFavorites: '#report-list-filter-favorites',
            filterTerm: '#reportListFilterTerm',
        },

        url: {
            ajaxGetTiles: 'index.php?module=report&action=list&cmd=ajaxTiles',
        },

        init: function () {
            if (me.isInitialized === true) {
                return;
            }
            if($(me.selector.keyElement).length === 0) {
                return;
            }
            me.registerEvents();
            me.isInitialized = true;
        },

        registerEvents: function () {
            $(me.selector.filterButton).on('click', function (event) {
                event.preventDefault();
                me.reloadTiles();
            });
            $(me.selector.filterCategory).on('blur', function (event) {
                me.reloadTiles();
            });
            $(me.selector.filterOwn).on('change', function (event) {
                me.reloadTiles();
            });
            $(me.selector.filterFavorites).on('change', function (event) {
                me.reloadTiles();
            });
        },

        getFilterSettings: function () {
            return {
                filter_category: $(me.selector.filterCategory).val(),
                filter_term: $(me.selector.filterTerm).val(),
                filter_own:  $(me.selector.filterOwn).prop('checked'),
                filter_favorites: $(me.selector.filterFavorites).prop('checked'),
            }
        },

        reloadTiles: function () {
            console.log('filter: ', me.getFilterSettings());
            var $tileView = $(me.selector.tileView);
            if ($tileView.length === 0) {
                return;
            }
            var $parent = $tileView.parent();

            $.ajax({
                url: me.url.ajaxGetTiles,
                method: 'post',
                data: me.getFilterSettings(),
                dataType: 'json',
                success: function (data) {
                    $tileView.remove();
                    $parent.append(data.html);
                },
                error: function (xhr, status, httpStatus) {
                    console.log(status + ' ' + httpStatus + ': ' + xhr.responseText);
                },
            });
        },
    };
    return {
        init: me.init,
        reload: me.reloadTiles,
    };
})(jQuery);

/**
 * Input of Parameters for the report
 */
var ParameterInput = (function ($) {
    'use strict';

    var me = {
        isInitialized: false,

        selector: {
            downloadButtons: '.report .download-csv-button.active',
            tile: '.report .tile-body'
        },

        init: function () {
            if (me.isInitialized === true) {
                return;
            }
            if (typeof ReportParameterInputDialog.open !== "function") {
                throw 'ReportParameterInputDialog required';
            }
            me.registerEvents();
            me.isInitialized = true;
        },

        registerEvents: function () {
            $(document).on('click', me.selector.downloadButtons, function (event) {
                event.preventDefault();
                var id = $(event.currentTarget).data('report-id');
                var format = $(event.currentTarget).data('format');
                var onClose = function (data, command) {

                    var url = 'index.php?module=report&action=download&id='+id+'&format=' + format;
                    if(data !== null) {
                        var paramString = $.param(data);
                        url = url + '&' + paramString
                    }
                    window.location.href = url;
                };
                ReportParameterInputDialog.open(id, 'report-list', onClose, 'Parameter', 'DOWNLOAD');
            });

            //  DEACTIVATED
            //$(document).on('click', me.selector.tile, function (event) {
                // event.preventDefault();
                // var id = $(event.currentTarget).data('id');
                // var onClose = function (data, command) {
                //     var url = 'index.php?module=report&action=view&cmd=view&id='+id;
                //     if(data !== null) {
                //         var paramString = $.param(data);
                //         url = url + '&' + paramString
                //     }
                //     window.location.href = url;
                // };
                // ReportParameterInputDialog.open(id, 'report-list', onClose, 'Parameter', 'WEITER');
            //});
        },

    };
    return {
        init: me.init,
    };
})(jQuery);

/**
 * Copy/Delete a Report
 */
var ReportListDataTable = (function ($) {
    'use strict';

    var me = {
        isInitialized: false,

        selector: {
            allCopyButtons: 'a.table-button-copy',
            allDeleteButtons: 'a.table-button-delete',
            allChartButtons: 'a.chart-button',
            dataTable: '#report_list',
        },

        url: {
            ajaxCopyReport: 'index.php?module=report&action=create&cmd=ajaxCopyReport',
            ajaxDeleteReport: 'index.php?module=report&action=delete&cmd=ajaxDeleteReport',
            ajaxGetChart: 'index.php?module=report&action=list&cmd=getchart',
        },

        storage: {
            TileView: null,
        },

        init: function (tileView) {
            if (me.isInitialized === true) {
                return;
            }
            me.storage.TileView = tileView;
            me.registerEvents();
            me.isInitialized = true;
        },

        /**
         * @return {void}
         */
        registerEvents: function () {
            $(document).on('click', me.selector.allCopyButtons, function (event) {
                var id = $(event.currentTarget).data('report-id');
                if (id > 0) {
                    var confirmValue = confirm('Bericht kopieren?');
                    if (confirmValue === false) {
                        return;
                    }
                    me.ajaxCopyReport(id);
                }
            });
            $(document).on('click', me.selector.allDeleteButtons, function (event) {
                var id = $(event.currentTarget).data('report-id');
                if (id > 0) {
                    var confirmValue = confirm('Wirklich Löschen?');
                    if (confirmValue === false) {
                        return;
                    }
                    me.ajaxDeleteReport(id);
                }
            });
            $(document).on('click', me.selector.allChartButtons, function (event) {
                console.log('event fired');
                var id = $(event.currentTarget).data('report-id');
                if (id > 0) {
                    me.openChartReport(id);
                }
            });
        },

        /**
         * @return {void}
         */
        refreshTable: function () {
            if ($.fn.DataTable.isDataTable(me.selector.dataTable)) {
                $(me.selector.dataTable).DataTable().ajax.reload();
            }
            if ($(me.selector.tileView !== null)) {
                me.storage.TileView.reload();
            }
        },

        /**
         * @param {int} id
         */
        ajaxCopyReport: function (id = 0) {
            $.ajax({
                url: me.url.ajaxCopyReport,
                data: {
                    id: id
                },
                method: 'post',
                dataType: 'json',
                beforeSend: function () {
                    App.loading.open();
                },
                success: function (data) {
                    me.refreshTable();
                },
                error: function (xhr, status, httpStatus) {
                    if (xhr.status === 401) {
                        alert('Ihnen fehlt die Berechtigung.');
                    } else {
                        console.log(status, httpStatus, xhr.responseText);
                        alert('Fehler beim Kopieren.');
                    }
                },
                complete: function () {
                    App.loading.close();
                }
            });
        },

        /**
         * @param {int} id
         */
        ajaxDeleteReport: function (id = 0) {
            $.ajax({
                url: me.url.ajaxDeleteReport,
                data: {
                    id: id
                },
                method: 'post',
                dataType: 'json',
                beforeSend: function () {
                    App.loading.open();
                },
                success: function (data) {
                    me.refreshTable();
                },
                error: function (xhr, status, httpStatus) {
                    if (xhr.status === 403) {
                        alert('Dieser Bericht ist schreibgeschützt.');
                    } else if (xhr.status === 401) {
                       alert('Ihnen fehlt die Berechtigung.');
                    } else {
                        console.log(status + ' ' + httpStatus + ': ' + xhr.responseText);
                    }
                },
                complete: function () {
                    App.loading.close();
                }
            });
        },

        /**
         * @param {int} id
         */
        openChartReport: function (id = 0) {
            $.ajax({
                url: me.url.ajaxGetChart,
                data: {
                    id: id
                },
                method: 'post',
                dataType: 'json',
                beforeSend: function () {
                    App.loading.open();
                },
                success: function (data) {
                    if(typeof data.html != 'undefined') {
                        $('#dialogReportChartContent').html(data.html);
                        ChartHelper.initChart($('#dialogReportChartContent div.chart-wrapper').first());
                        $('#dialogReportChart').dialog(
                            {
                                modal: true,
                                autoOpen: true,
                                minWidth: 940,
                                title:'',
                                buttons: {
                                    'OK': function() {
                                        $(this).dialog('close');
                                    }
                                },
                                close: function(event, ui){

                                }
                            }
                        );
                    }
                },
                error: function (xhr, status, httpStatus) {
                    if (xhr.status === 403) {
                        alert('Dieser Bericht ist schreibgeschützt.');
                    } else {
                        console.log(status + ' ' + httpStatus + ': ' + xhr.responseText);
                    }
                },
                complete: function () {
                    App.loading.close();
                }
            });

        },
    };
    return {
        init: me.init,
        reloadTiles: me.reloadTiles,
    };

})(jQuery);


/**
 * Copy/Delete a Report
 */
var FavoriteIcon = (function ($) {
    'use strict';

    var me = {
        isInitialized: false,

        selector: {
            allFavoriteIcons: '.svg-favorite',
        },

        url: {
            ajaxSetFavorite: 'index.php?module=report&action=list&cmd=ajaxSetFavorite',
            ajaxGetFavorite: 'index.php?module=report&action=list&cmd=ajaxGetFavorite',
        },

        init: function () {
            if (me.isInitialized === true) {
                return;
            }
            me.registerEvents();
            me.isInitialized = true;
        },

        registerEvents: function () {
            $(document).on('click', me.selector.allFavoriteIcons, function (event) {
                event.preventDefault();

                var id = $(event.currentTarget).data('id');
                var favorite = parseInt($(event.currentTarget).data('favorite')) === 1;
                // if (id > 0) {
                //     var confirmValue = confirm('Bericht kopieren?');
                //     if (confirmValue === false) {
                //         return;
                //     }
                    me.switchFavoriteIcon($(event.currentTarget));
                // }
            });
        },

        switchFavoriteIcon: function ($target) {
            var id = $target.data('id');
            var favorite = parseInt($target.data('favorite')) === 1;

            $.ajax({
                url: me.url.ajaxSetFavorite,
                method: 'post',
                data: {
                    id: id,
                    set_favorite: !favorite
                },
                dataType: 'json',
                success: function (data) {
                    if (data.is_favorite === true) {
                        $target.addClass('favorite-on');
                        $target.data('favorite', 1);
                    } else {
                        $target.removeClass('favorite-on');
                        $target.data('favorite', 0);
                    }
                },
                error: function (xhr, status, httpStatus) {
                    console.log(status + ' ' + httpStatus + ': ' + xhr.responseText);
                },
            });
        }
    };
    return {
        init: me.init,
        reload: me.reloadTiles,
    };
})(jQuery);


$(document).ready(function () {
    TileView.init();
    ReportListDataTable.init(TileView);
    FavoriteIcon.init();
    ParameterInput.init();
});
