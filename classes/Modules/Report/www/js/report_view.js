
/**
 * Input Parameters
 */
var ParameterInput = (function ($) {
    'use strict';

    var me = {
        isInitialized: false,

        init: function () {
            if (me.isInitialized === true) {
                return;
            }
            if (typeof ReportParameterInputDialog.open !== "function") {
                throw 'ReportParameterInputDialog required';
            }
            var id = $('#tabs-1').data('id');
            var onClose = function (data, command) {
                var url = 'index.php?module=report&action=view&cmd=view&id=' + id;
                if(data !== null) {
                    var paramString = $.param(data);
                    url = url + '&' + paramString
                }
                window.location.href = url;
            };
            ReportParameterInputDialog.open(id, 'report-view', onClose, 'Parameter', 'WEITER');
            me.isInitialized = true;
        },
    };
    return {
        init: me.init,
    };
})(jQuery);


/**
 * Input Parameters
 */
var LiveParameter = (function ($) {
    'use strict';

    var me = {
        isInitialized: false,

        selector: {
            inputFields: '.report .live-filter-input',
            selectFields: '.report .live-filter-select',
            dataTable: '#report_table',
        },

        init: function () {
            if (me.isInitialized === true) {
                return;
            }

            me.registerEvents();
            me.isInitialized = true;
        },

        registerEvents: function () {
            $(document).on('blur', me.selector.inputFields, function (event) {
                console.log('some param changed');

                var varname = $(event.currentTarget).attr('name');
                var value = $(event.currentTarget).val();

                console.log('start filter for ' + varname + '='+ value);
                me.setTableFilter(varname, value);
            });

            $(document).on('xhr.dt', me.selector.dataTable, function (e, settings) {
                var api = new $.fn.dataTable.Api(settings);
                var $table = $(api.table().node());

                        console.log('xhr data', api.ajax.params());
                     console.log(settings );
                });
        },

        setTableFilter: function (varname, value) {
            if ($.fn.DataTable.isDataTable(me.selector.dataTable)) {

                var $table = $(me.selector.dataTable);
                var table = $table.DataTable();
                var url = table.ajax.url();
                console.log('old url', url);
                var urlSplit = url.split('?');
                var path = urlSplit[0];
                var urlParams = new URLSearchParams(urlSplit[1]);

                var postfixB64 = urlParams.get('postfix');
                var postfix = atob(postfixB64);
                
                var oPostfix = JSON.parse(postfix);
                oPostfix[varname.toLowerCase()] = value;

                var nPostfix = JSON.stringify(oPostfix);
                var nPostFixB64 = btoa(nPostfix);

                urlParams.set('postfix', nPostFixB64);
                var nUrl = path + '?'+ urlParams.toString();
                console.log('new url ' + nUrl);
                table.ajax.url( 'foooooo' ).load();
            }
        },

        refreshTable: function () {
            if ($.fn.DataTable.isDataTable(me.selector.dataTable)) {
                $(me.selector.dataTable).DataTable().ajax.reload();
            }
        },


    };

    return {
        init: me.init,
    };

})(jQuery);




$(document).ready(function () {
    // LiveParameter.init();  //Does not work for some reason
});