var ShopimportAppNew = function ($) {
    'use strict';

    var me = {
        selector: {
            vueAppNewError: '#onlineshop-appnew-error',
            vueAppNew: '#onlineshop-appnew',
            vueAppNewJson: '#onlineshop-appnewjson'
        },
        initAppNewErrorVue: function () {
            new Vue({
                el: me.selector.vueAppNewError,
                data: {
                    showAssistant: true,
                    pagination: true,
                    allowClose: true,
                    pages: [
                        {
                            type: 'defaultPage',
                            icon: 'password-icon',
                            headline: 'Request ungültig',
                            subHeadline: $(me.selector.vueAppNewError).data('errormsg'),

                            ctaButtons: [
                                {
                                    title: 'OK',
                                    action: 'close'
                                }]
                        }
                    ]
                }
            });
        },
        initAppNewVue: function () {
            new Vue({
                el: me.selector.vueAppNew,
                data: {
                    showAssistant: true,
                    pagination: true,
                    allowClose: true,
                    pages: [
                        {
                            type: 'form',
                            dataRequiredForSubmit: {
                                data: JSON.stringify($(me.selector.vueAppNew).data('appnewdata'))
                            },
                            submitType: 'submit',
                            submitUrl: 'index.php?module=onlineshops&action=appnew&cmd=createdata',
                            headline: $(me.selector.vueAppNew).data('heading'),
                            subHeadline: $(me.selector.vueAppNew).data('info'),
                            form:
                                [
                                    {
                                        id: 0,
                                        name: 'create-shop',
                                        inputs: [
                                            {
                                                type: 'select',
                                                name: 'shopId',
                                                label: 'Auswahl',
                                                validation: true,
                                                options: JSON.parse($(me.selector.vueAppNewJson).html())
                                            }]
                                    }]
                            ,
                            ctaButtons: [
                                {
                                    title: 'Weiter',
                                    type: 'submit',
                                    action: 'submit'
                                }]
                        }
                    ]
                }
            });
        },
        init: function () {
            if ($(me.selector.vueAppNewError).length) {
                me.initAppNewErrorVue();
            }
            if ($(me.selector.vueAppNew).length) {
                me.initAppNewVue();
            }
            if ($('#frmappnew').length === 0) {
                return;
            }

            $('#data').on('paste', function (e) {
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: 'index.php?module=onlineshops&action=appnew&cmd=checkdata',
                    data: {
                        data: e.originalEvent.clipboardData.getData('text')
                    },
                    success: function (data) {
                        $('#msgwrapper').html(data.html);
                    },
                    error: function (data) {
                        if (typeof data.responseJSON !== 'undefined') {
                            $('#msgwrapper').html('<div class="error">' + data.responseJSON.error + '</div>');
                        }
                    }
                });
            });
            $('#data').on('change', function () {
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: 'index.php?module=onlineshops&action=appnew&cmd=checkdata',
                    data: {
                        data: $(this).val()
                    },
                    success: function (data) {
                        $('#msgwrapper').html(data.html);
                    },
                    error: function (data) {
                        if (typeof data.responseJSON !== 'undefined') {
                            $('#msgwrapper').html('<div class="error">' + data.responseJSON.error + '</div>');
                        }
                    }
                });
            });
        }

    };
    return {
        init: me.init
    };

}(jQuery);

$(document).ready(function () {
    ShopimportAppNew.init();
});
