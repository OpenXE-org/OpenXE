
var project_filter = (function ($) {

    var me = {
        isInitialized: false,

        url: {
            delete: 'index.php?module=taxdoo&action=einstellungen&cmd=deleteproject'
        },

        /**
         * @return void
         */
        init: function () {
            if (me.isInitialized === true) {
                return;
            }
            me.registerTableEvent();
            me.registerEvents();
            me.isInitialized = true;
        },

        registerTableEvent: function() {
          $('#project_filter').on('afterreload', function() {
              me.registerEvents();
          });
        },

        /**
         * @return {void}
         */
        registerEvents: function () {
            $('.project-filter-delete').off('click');
            $('.project-filter-delete').on('click', function (e) {
                e.preventDefault();
                var fieldId = $(this).data('id');
                me.deleteItem(fieldId);
            });
        },

        deleteItem: function (fieldId) {
            var confirmValue = confirm('Wirklich löschen?');
            if (confirmValue === false) {
                return;
            }

            $.ajax({
                url: me.url.delete,
                data: {
                    id: fieldId
                },
                method: 'post',
                dataType: 'json',
                beforeSend: function () {
                    App.loading.open();
                },
                success: function (data) {
                    if (data.success === true) {
                        me.reloadDataTable();
                    }
                    if (data.success === false) {
                        alert('Unbekannter Fehler beim Löschen.');
                    }
                    App.loading.close();
                }
            });
        },

        reloadDataTable: function () {
            $('#project_filter').dataTable().api().ajax.reload();
        }
    };

    return {
        init: me.init
    }

})(jQuery);


$(document).ready(function () {
    project_filter.init();
});

