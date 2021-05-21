var ShopSettings = function ($) {
    'use strict';

    var me = {
        storage: {
            exampleTemplate: null,
            treeApi: null,
            lastTreeSearch: ''
        },
        openTransformationPopup: function () {
            //$('#smartyinput').dialog('open');
            $('#textareasmartyincomming').val($('#transform_cart').val());
            $('#textareasmartyincomminginput').val($('#textareasmartyincomminginput').val());
        },
        loadCart: function (shopId) {
            if ($('#cart').val() + '' === '') {
                return;
            }
            $.ajax({
                type: 'POST',
                url: 'index.php?module=onlineshops&action=edit&cmd=loadCart',
                data: {
                    shopid: shopId,
                    extid: $('#cart').val(),
                    format: $('#smartyinputtype').val(),
                    replacecart: $('#replacecart').prop('checked') ? 1 : 0,
                    content: $('#textareasmartyincomming').val()
                },
                success: function (data) {
                    $('#textareasmartyincomminginput').val(data.input);
                    $('#dataincommingobject').html(data.object);
                    $('#dataincommingpreview').val(data.preview);
                }
            });
        },
        initCategoryTree: function () {
            $('#mlmTree').aciTree({
                autoInit: false,
                checkboxChain: false,
                ajax: {
                    url: 'index.php?module=onlineshops&action=edit&cmd=loadTree&id=' + $('#mlmTree').data('id')
                },
                checkbox: true,
                multiSelectable: true,
                itemHook: function (parent, item, itemData, level) {
                    //console.log(itemData);
                },
                filterHook: function (item, search, regexp) {

                    if (search.length) {
                        var parent = this.parent(item);

                        if (parent.length) {
                            var label = this.getLabel(parent);
                            if (regexp.test(String(label))) {
                                this.setVisible(item);
                                return true;
                            }
                            this.setVisible(item);
                        }

                        if (regexp.test(String(this.getLabel(item)))) {
                            item.addClass('searched');
                            return true;
                        } else {
                            return false;
                        }

                        //return regexp.test(String(this.getLabel(item)));
                    } else {
                        return true;
                    }
                }
            });

            me.storage.treeApi = $('#mlmTree').aciTree('api');


            $('#search').val('');
            me.storage.lastTreeSearch = '';

            $('#search').on('keyup', function () {
                if ($(this).val() === me.storage.lastTreeSearch) {
                    return;
                }

                $('.aciTreeLi').removeClass('searched');

                me.storage.lastTreeSearch = $(this).val();
                api.filter(null, {
                    search: $(this).val(),
                    callback: function () {

                    },
                    success: function (item, options) {

                        if (!options.first) {
                            //alert('No results found!');
                        }
                    }
                });
            });


            $('#mlmTree').on('acitree', function (event, api, item, eventName, options) {
                switch (eventName) {
                    case 'checked':
                        var ajaxData = {
                            id: api.getId(item),
                            shopId: $('#mlmTree').data('id'),
                            name: api.getLabel(item),
                            checked: true,
                            todo: 'check'
                        };
                        var dataid = api.getId(item);
                        var allChildren = api.children(null, true, true);
                        $(allChildren).each(function () {
                            if (api.getId($(this)) != dataid) {
                                api.uncheck($(this));
                            }
                        });
                        $('#category_root_id').val(dataid);

                        $.ajax({
                            url: 'index.php?module=onlineshops&action=edit&cmd=checkTreeNode',
                            data: ajaxData,
                            success: function (data) {

                            }
                        });
                        break;
                    case 'unchecked':
                        var ajaxData = {
                            id: api.getId(item),
                            shopId: $('#mlmTree').data('id'),
                            name: api.getLabel(item),
                            checked: false,
                            todo: 'uncheck'
                        };
                        $('#category_root_id').val(0);

                        $.ajax({
                            url: 'index.php?module=onlineshops&action=edit&cmd=uncheckTreeNode',
                            data: ajaxData,
                            success: function (data) {

                            }
                        });
                        break;
                }
            });

            $('#mlmTree').aciTree('init');
            $(window).on('scroll', function () {
                checkContainerPos();
            });
        },
        initSmarty: function () {
            $('#editcarttransformation').on('click', function () {
                me.openTransformationPopup();
            });
            $('#runincomming').on('click', function () {
                $('#transform_cart').val($('#textareasmartyincomming').val());
                $('#transform_cart_data').val($('#textareasmartyincomminginput').val());
                $.ajax({
                    url: 'index.php?module=onlineshops&action=edit&cmd=runincomming',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        content: $('#textareasmartyincomming').val(),
                        input: $('#textareasmartyincomminginput').val(),
                        shopid: $('#loadCart').data('shopid'),
                        format: $('#smartyinputtype').val(),
                        replacecart: $('#replacecart').prop('checked') ? 1 : 0
                    },
                    success: function (data) {
                        $('#dataincommingobject').html(data.object);
                        $('#dataincommingpreview').val(data.preview);
                    }
                });
            });
            $('#saveincomming').on('click', function () {
                $.ajax({
                    url: 'index.php?module=onlineshops&action=edit&cmd=savesmartyincomming',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        content: $('#textareasmartyincomming').val(),
                        input: $('#textareasmartyincomminginput').val(),
                        shopid: $('#loadCart').data('shopid'),
                        format: $('#smartyinputtype').val(),
                        replacecart: $('#replacecart').prop('checked') ? 1 : 0,
                        active: $('#transferactive').prop('checked') ? '1' : '0'
                    },
                    success: function () {
                        $('#transform_cart_format').val($('#smartyinputtype').val());
                        $('#transform_cart_replace').val($('#replacecart').prop('checked') ? 1 : 0);
                        $('#transform_cart').val($('#textareasmartyincomming').val());
                        $('#transform_cart_data').val($('#textareasmartyincomminginput').val());
                        $('#transform_cart_active').val($('#transferactive').prop('checked') ? '1' : '0');
                    }
                });
            });
            $('#transferactive').on('change', function () {
                $('#transform_cart_active').val($('#transferactive').prop('checked') ? '1' : '0');
            });
            $('#smartyinputtype').on('change', function () {
                me.loadDefaultTemplate();
            });

            me.loadDefaultTemplate();
            $('#loadDefaultTemplate').on('click', function () {
                if (me.storage.loadDefaultTemplate + '' === '') {
                    return;
                }
                if (($('#textareasmartyincomming').val() + '').trim() !== '' && !confirm('Das bisherige Template wird ersetzt. Fortfahren?')) {
                    return;
                }
                $('#textareasmartyincomming').val(me.storage.loadDefaultTemplate);
            });
            $('#loadCart').on('click', function () {
                var cart = $('#cart').val() + '';
                if (cart === '') {
                    return;
                }
                me.loadCart($(this).data('shopid'));
            });
        },
        loadDefaultTemplate: function () {
            me.storage.loadDefaultTemplate = '';
            $.ajax({
                url: 'index.php?module=onlineshops&action=edit&cmd=loadDefaultTemplate',
                type: 'POST',
                dataType: 'json',
                data: {
                    content: $('#textareasmartyincomming').val(),
                    input: $('#textareasmartyincomminginput').val(),
                    shopid: $('#loadCart').data('shopid'),
                    format: $('#smartyinputtype').val(),
                    replacecart: $('#replacecart').prop('checked') ? 1 : 0,
                    active: $('#transferactive').prop('checked') ? '1' : '0'
                },
                success: function (data) {
                    if (typeof data.template != 'undefined' && data.template !== '') {
                        me.storage.loadDefaultTemplate = data.template;
                        $('#loadDefaultTemplate').toggleClass('hidden', false);
                        $('#loadDefaultTemplate').show();
                    } else {
                        me.storage.loadDefaultTemplate = '';
                        $('#loadDefaultTemplate').hide();
                    }
                }
            });
        },
        init: function () {
            if ($('#smartyinput').length) {
                me.initSmarty();
            }
            if ($('#mlmTree').length) {
                me.initCategoryTree();
            }
        }
    };


    return {
        init: me.init
    };
}(jQuery);

$(document).ready(function () {
    ShopSettings.init();
});
