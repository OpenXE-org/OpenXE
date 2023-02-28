/*
 * SPDX-FileCopyrightText: 2022 Andreas Palm
 * SPDX-FileCopyrightText: 2019 Xentral (c) Xentral ERP Software GmbH, Fuggerstrasse 11, D-86150 Augsburg, Germany
 *
 * SPDX-License-Identifier: LicenseRef-EGPL-3.1
 */

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
            let val = $(el).val().toLowerCase();
            $('.createbutton').each(function() {
                let desc = $(this).find('.tilegrid-tile-title').text();
                if (desc.toLowerCase().indexOf(val)>=0)
                    $(this).show();
                else
                    $(this).hide();
            })
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
