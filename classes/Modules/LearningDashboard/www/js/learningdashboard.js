var LearningDashboard = function ($) {
    'use strict';

    var me = {
        settings: {
            frontendTechnology: 'vue'
        },

        storage: {
            dashboardData: null
        },

        elem: {
            container: '#learning-dashboard-container'
        },

        init: function () {
            me.getDashboardData().then(me.runDashboard);
        },

        getDashboardData: function () {
            return $.ajax({
                url: 'index.php?module=learningdashboard&action=ajax&cmd=get_content',
                method: 'get',
                success: function (data) {
                    me.storage.dashboardData = data;
                },
                error: function (xhr, status, httpStatus) {
                    $(me.elem.container).html('Fehler beim Abrufen der Daten: ' + httpStatus);
                }
            });
        },

        runDashboard: function () {
            if (me.storage.dashboardData === null) {
                $(me.elem.container).html('No data - dashboard could not be loaded');
                return;
            }

            if (me.settings.frontendTechnology === 'vue') {
                var vueConstructor = {
                    el: me.elem.container,
                    data: me.storage.dashboardData,
                    created: function () {
                        if (this.wording === undefined) {
                            return;
                        }

                        Vue.prototype.$wording = this.wording;
                    }
                };

                new Vue(vueConstructor);
            }
        }
    };

    return {
        init: me.init
    };

}(jQuery);

$(document).ready(function () {
    LearningDashboard.init();
});
