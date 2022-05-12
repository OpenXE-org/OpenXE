var ShopImporterAmazonAttachedOffers = function ($) {
    'use strict';

    var me = {
        storage: {
            shopId: null,
            articleInputValue: null
        },
        selector: {
            attachedOffersTable: '#shopimporter_amazon_attachedoffers',
            newOfferTable: '#newoffertable',
            popupAttached: '#popupattatch',
            popupArticle: '#popuparticle',
            popupArticleInfo: '#popuparticleinfo',
        },
        reloadAttachedOffersTable: function()
        {
            $(me.selector.attachedOffersTable).DataTable().ajax.reload();
        },
        addEmptyArticleWarning: function() {
            if($(me.selector.popupArticle).val()+'' === '') {
                $(me.selector.popupArticleInfo).html('Pflichtfeld!');
                return true;
            }
            $(me.selector.popupArticleInfo).html('');
            return false;
        },
        init: function() {
            me.storage.shopId = $(me.selector.popupAttached).data('id');
            $(me.selector.attachedOffersTable).on('afterreload',function(){
                $('#shopimporter_amazon_attachedoffers img.delete').on('click',function(){
                    if(confirm('Wiklich löschen?')) {
                        $.ajax({
                            url: 'index.php?module=shopimporter_amazon&action=attachedoffers&id='+
                                me.storage.shopId +'&cmd=delete',
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                element: $(this).data('id')
                            },
                            success: function(data) {
                                me.reloadAttachedOffersTable();
                            },
                            beforeSend: function() {

                            }
                        });
                    }
                });
            });
            $(me.selector.popupAttached).dialog(
                {
                    modal: true,
                    autoOpen: false,
                    minWidth: 1440,
                    title:'',
                    buttons: {
                        'ERSTELLEN': function()
                        {
                            if(me.addEmptyArticleWarning()) {
                                return;
                            }
                            $('#frmnewoffer').trigger('submit');
                        },
                        'ABBRECHEN': function() {
                            $(this).dialog('close');
                        }
                    },
                    close: function(event, ui){

                    }
                });

            $('#new').on('click',function(){
                $(me.selector.popupAttached).dialog('open');
            });

            $(me.selector.popupAttached).on('autocompleteclose',function(){
                $(me.selector.popupAttached).trigger('change');
            });
            $(me.selector.popupAttached).on('autocompletechange',function(){
                $(me.selector.popupAttached).trigger('change');
            });
            $(me.selector.popupArticle).on('change',function(){
                me.addEmptyArticleWarning();
                me.storage.articleInputValue = ($(this).val()+'').split(' ') [ 0 ];
                if(me.storage.articleInputValue === '') {
                    return;
                }
                $('input.popupskufba').each(function(){
                    $(this).attr('placeholder', me.storage.articleInputValue+'_'+(this.id.split('_')[ 1 ]).toUpperCase()+'_FBA');
                });
                $('input.popupskufbm').each(function(){
                    $(this).attr('placeholder', me.storage.articleInputValue+'_'+(this.id.split('_')[ 1 ]).toUpperCase());
                });
                $.ajax({
                    url: 'index.php?module=shopimporter_amazon&action=attachedoffers&id='+
                        me.storage.shopId +'&cmd=getean',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        article: $(me.selector.popupArticle).val()
                    },
                    success: function (data) {
                        if(typeof data.ean != 'undefined'
                            && (
                                $('#popupasin').val()+'' === ''
                                || data.articleid+'' !== $('#lastarticle').val()+''
                            )
                        ) {
                            $('#searchtype').val('EAN');
                            $('#popupasin').val(data.ean);
                            $('#lastean').val(data.ean);
                            $('#lastarticle').val(data.id);
                            $('#searchasin').trigger('click');
                            $('.popuptitle').val('');
                            $('.ownprice').val('');
                            $(me.selector.newOfferTable).find('.trmarketplace').find('select').val('');
                            $(me.selector.newOfferTable).find('.trmarketplace').find('select.condition').val('New');
                        }
                        else {
                            $('#lastean').val('');
                            if(data.articleid+'' !== $('#lastarticle').val()+'') {
                                $('#lastarticle').val(data.articleid);
                                $('#popupasin').val('');
                                $('.popuptitle').val('');
                                $('.ownprice').val('');
                                $(me.selector.newOfferTable).find('.trmarketplace').find('select').val('');
                                $(me.selector.newOfferTable).find('.trmarketplace').find('select.condition').val('New');
                            }
                            if($('#searchtype').val() === 'EAN'
                                && $('#popupasin').val()+'' === $('#lastean').val()+'') {
                                $('#popupasin').val('');
                            }
                        }
                        if(typeof data.prices != 'undefined') {
                            $.each(data.prices, function (key, value) {
                                if((key+'').length === 2) {
                                    $('input[name="price_'+key+'"]').attr('placeholder', value);
                                }
                            })
                        }
                        if(typeof data.articleid != 'undefined') {
                            $('#lastarticle').val(data.articleid);
                        }
                    }
                });
            });
            $('#popupasin').on('keypress',function(event){
                if (event.which == 13) {
                    $('#searchasin').trigger('click');
                }
            });
            $('#searchasin').on('click',function(){
                var asin = $('#popupasin').val();
                if(asin+'' === '') {
                    return;
                }
                $('#searchresultshead').nextAll('tr').remove();
                $('#searchresultshead').toggleClass('hide', true);
                $('#searchresultsdiv').loadingOverlay('show');
                $(me.selector.popupAttached).find('tr.trmarketplace').each(function(){
                    $.ajax({
                        url: 'index.php?module=shopimporter_amazon&action=attachedoffers&id='+
                            me.storage.shopId +'&cmd=getOffers',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            asin: $('#popupasin').val(),
                            searchtype: $('#searchtype').val(),
                            marketplace: $(this).data('marketplace')
                        },
                        success: function(data) {
                            $('#searchresultsdiv').loadingOverlay('remove');
                            var $tr = $(me.selector.popupAttached).find('tr.trmarketplace[data-marketplace="'+data.marketplace+'"]');
                            if($tr.length === 0) {
                                return;
                            }
                            if(data.status == 0) {
                                $($tr).hide();
                                $($tr).find('input').val('');
                                $($tr).find('.prices').html('');
                            }
                            else {
                                $($tr).show();
                                $($tr).find('.prices').html(data.prices);
                                $($tr).find('.popuptitle').val(data.title);
                                $($tr).find('.curreny').val(data.currency);
                            }
                            if(typeof data.tr != 'undefined') {
                                $('#searchresultshead').toggleClass('hide', false);
                                $('#searchresultshead').after(data.tr);
                            }
                            $('#searchresults span.childrenasin').off('click');
                            $('#searchresults span.parentasin').off('click');
                            $('#searchresults img.useasin').off('click');
                            $('#searchresults span.childrenasin').on('click', function(){
                                $('#popupasin').val($(this).data('asin'));
                                $('#searchtype').val('ASIN');
                                $('#searchasin').trigger('click');
                            });
                            $('#searchresults span.parentasin').on('click', function(){
                                $('#popupasin').val($(this).data('asin'));
                                $('#searchtype').val('ASIN');
                                $('#searchasin').trigger('click');
                            });
                            $('#searchresults img.useasin').on('click', function(){
                                if($($(this).parents('tr').find('td.asin')).html()+'' != '') {
                                    $('#popupasin').val($($(this).parents('tr').find('td.asin')).html()+'');
                                    $('#searchtype').val('ASIN');
                                    $('#searchasin').trigger('click');
                                    return;
                                }
                                var $tr = $('tr.trmarketplace[data-marketplace="'+$(this).data('marketplace')+'"]');
                                if($tr.length) {
                                    $($tr).show();
                                    $($tr).find('.popuptitle').val($($(this).parents('tr').find('td.title')).html());
                                    $($tr).find('.popupasin').val($($(this).parents('tr').find('td.asin')).html());
                                }
                            });
                            if($('#searchtype').val()==='ASIN') {
                                $('input.popupasin').val($('#popupasin').val());
                            }
                            if($(me.selector.popupArticle).val()+'' !== '') {
                                $(me.selector.popupArticle).trigger('change');
                            }
                        },
                        beforeSend: function() {

                        }
                    });
                });
            });
        }
    };

    return {
        init: me.init
    };

}(jQuery);

$(document).ready(function () {
    ShopImporterAmazonAttachedOffers.init();
});
