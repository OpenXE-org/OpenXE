var ShippingMethodCreate = function ($) {
    'use strict';

    var me = {
        storage: {
            hideElements: null,
            showElements: null,
            vueElement: null
        },
        selector:{
            vueElementId: '#shipment-create',
        },
        search: function (el) {
            $.ajax({
                url: 'index.php?module=versandarten&action=create&cmd=suche',
                type: 'POST',
                dataType: 'json',
                data: {
                    val: $(el).val()
                }
            })
             .done(function (data) {
                 if (typeof data != 'undefined' && data != null) {
                     if (typeof data.ausblenden != 'undefined' && data.ausblenden != null) {
                         me.storage.hideElements = data.ausblenden.split(';');
                         $.each(me.storage.hideElements, function (k, v) {
                             if (v != '') {
                                 $('#' + v).hide();
                             }
                         });

                     }
                     if (typeof data.anzeigen != 'undefined' && data.anzeigen != null) {
                         me.storage.showElements = data.anzeigen.split(';');
                         $.each(me.storage.showElements, function (k, v) {
                             if (v != '') {
                                 $('#' + v).show();
                             }
                         });
                     }
                 }
             });
        },
        init: function () {
            if ($(me.selector.vueElementId).length === 0) {
                return;
            }
            me.storage.vueElement = $(me.selector.vueElementId).clone();
            $('#createSearchInput').on('keyup', function () {
                me.search(this);
            });
            $('.createbutton').on('click', function () {
                $.ajax({
                    url: 'index.php?module=versandarten&action=create&cmd=getAssistant',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        shippingmodule: $(this).data('module')
                    }
                }).done(function (data) {
                    if (typeof data.pages == 'undefined' && typeof data.location != 'undefined') {
                        window.location = data.location;
                        return;
                    }
                    if ($(me.selector.vueElementId).length === 0) {
                        $('body').append(me.storage.vueElement);
                    }
                    new Vue({
                        el: me.selector.vueElementId,
                        data: {
                            showAssistant: true,
                            pagination: true,
                            allowClose: true,
                            pages: data.pages
                        }
                    });
                });
            });
            $('.autoOpenModule').first().each(function () {
                $('.createbutton[data-module=\'' + $(this).data('module') + '\']').first().trigger('click');
                $(this).remove();
            });
        }

    };
    return {
        init: me.init
    };
}(jQuery);

$(document).ready(function () {
    ShippingMethodCreate.init();
});
