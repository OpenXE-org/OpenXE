var PaymentAccountCreate = function ($) {
    'use strict';

    var me = {
        storage: {
            hideElements: null,
            showElements: null,
            vueElement: null,
            specificEventCall: null,
            triggerSubmit: null,
        },
        selector:{
            vueElementId: '#paymentaccount-create',
            specificSettingsTable: '#specific-settings',
            editFormular: '#eprooform'
        },
        search: function (el) {
            $.ajax({
                url: 'index.php?module=konten&action=create&cmd=suche',
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
        updateSpecificFields: function () {
            me.storage.specificEventCall = true;
            $('#liveimport-table').loadingOverlay('show');
            $.ajax({
                url: 'index.php?module=konten&action=edit&cmd=getspecificfields',
                type: 'POST',
                dataType: 'text',
                data: {
                    type: $('#type').val(),
                    liveimport: $('#liveimport').val()
                }
            }).done(function (data) {
                    $(me.selector.specificSettingsTable).html(data);
                    me.registerSpecificEvents();
                    $('#liveimport-table').loadingOverlay('remove');
                    me.storage.specificEventCall = null;
                    if(me.storage.triggerSubmit === true) {
                        $(me.selector.editFormular).submit();
                    }
                }
            ).error(function () {
                me.storage.specificEventCall = null;
                if(me.storage.triggerSubmit === true) {
                    $(me.selector.editFormular).submit();
                }
                $('#liveimport-table').loadingOverlay('remove');
            });
        },
        updateLiveImportField: function (element) {
            me.storage.specificEventCall = true;
            $(me.selector.specificSettingsTable).loadingOverlay('show');
            $.ajax({
                url: 'index.php?module=konten&action=edit&cmd=updatespecificfields',
                type: 'POST',
                dataType: 'json',
                data: {
                    type: $('#type').val(),
                    liveimport: $('#liveimport').val(),
                    elementname: element.id,
                    elementvalue: $(element).is(':checkbox') ? $(element).prop('checked') : $(element).val()
                }
            }).done(function (data) {
                    $(me.selector.specificSettingsTable).html(data.html);
                    $('#liveimport').val(data.liveimport);
                    me.registerSpecificEvents();
                    $(me.selector.specificSettingsTable).loadingOverlay('remove');
                    me.storage.specificEventCall = null;
                    if(me.storage.triggerSubmit === true) {
                        $(me.selector.editFormular).submit();
                    }
                }
            ).error(function () {
                me.storage.specificEventCall = null;
                if(me.storage.triggerSubmit === true) {
                    $(me.selector.editFormular).submit();
                }
                $(me.selector.specificSettingsTable).loadingOverlay('remove');
            });
        },
        registerSpecificEvents: function () {
            $(me.selector.specificSettingsTable).find('select').off('change');
            $(me.selector.specificSettingsTable).find('input').off('change');
            $(me.selector.specificSettingsTable).find('textarea').off('change');
            $(me.selector.specificSettingsTable).find('select').on('change', function () {
                me.updateLiveImportField(this);
            });
            $(me.selector.specificSettingsTable).find('input').on('change', function () {
                me.updateLiveImportField(this);
            });
            $(me.selector.specificSettingsTable).find('textarea').on('change', function () {
                me.updateLiveImportField(this);
            });
        },
        initEdit: function () {
            $('#liveimport').on('change', function () {
                me.updateSpecificFields();
            });
            $('#type').on('change', function () {
                me.updateSpecificFields();
            });
            me.registerSpecificEvents();
            $(me.selector.editFormular).on('submit', function(event){
                if(me.storage.specificEventCall !== null) {
                    event.preventDefault();
                    me.storage.triggerSubmit = true;
                    return false;
                }
            });
        },
        init: function() {
            if ($(me.selector.specificSettingsTable).length > 0) {
                me.initEdit();
            }
            if ($(me.selector.vueElementId).length === 0) {
                return;
            }
            me.storage.vueElement = $(me.selector.vueElementId).clone();
            $('#createSearchInput').on('keyup', function () {
                me.search(this);
            });
            $('.createbutton').on('click', function () {
                $.ajax({
                    url: 'index.php?module=konten&action=create&cmd=getAssistant',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        paymentaccountmodule: $(this).data('module')
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
    PaymentAccountCreate.init();
});
