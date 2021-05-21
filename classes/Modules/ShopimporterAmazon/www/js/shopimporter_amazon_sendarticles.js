var ShopImporterAmazonSendArticles = function ($) {
    'use strict';

    var me = {
        storage: {
            templateTarget: null
        },
        selector: {
            flatFileTable: '#shopimporter_amazon_flatfile',
            articlePopup: '#getarticlediv',
            articlePopupForm: '#getarticlefrm',
            templatePopup: '#popupTemplate',
            flatFileTemplateInfo: '#flatFileTemplateInfo',
            deleteIcon: 'img.deletearticle',
            getIcon: 'img.getarticle'
        },
        reloadFlatFileTable: function () {
            $(me.selector.flatFileTable).DataTable().ajax.reload();
        },
        updateAutoComplete: function () {
            $(me.selector.articlePopupForm + ' input').each(function () {
                if (typeof this.id != 'undefined') {
                    if (this.id === 'article') {
                        $(this).autocomplete({
                            source: 'index.php?module=ajax&action=filter&filtername=artikelnummer',
                            select: function (event, ui) {
                                var i = ui.item.value;
                                var zahl = i.indexOf(' ');
                                var text = i.slice(0, zahl);
                                $('input#article').val(text);
                                return false;
                            }
                        });
                    } else if (typeof this.id != 'undefined') {
                        $(this).autocomplete({
                            source: 'index.php?module=ajax&action=filter&filtername=amazongetarticle&flatfile='
                                + encodeURI($('#flatfile').val())
                                + '&feedproducttype='
                                + encodeURI($('#feed_product_type').val())
                                + '&elementid='
                                + this.id
                        });
                    }
                }
            });
        },
        deleteArticle: function (id) {
            if (!confirm('Wirklich löschen?')) {
                return;
            }
            $.ajax({
                url: 'index.php?module=shopimporter_amazon&action=sendarticles&cmd=deletearticle',
                type: 'POST',
                dataType: 'json',
                data: {
                    article_ids: id
                },
                success: function () {
                    me.reloadFlatFileTable();
                },
                beforeSend: function () {

                }
            });
        },
        createImportTemplate: function (template)
        {
            $.ajax({
                url: 'index.php?module=shopimporter_amazon&action=sendarticles&cmd=createImportTemplate',
                type: 'POST',
                dataType: 'json',
                data: {
                    template: template,
                },
                success: function (data) {
                    if(typeof data.id != 'undefined') {
                        $(me.selector.flatFileTemplateInfo).html(
                            'Importvorlage: <a href="index.php?module=importvorlage&action=edit&id='
                            +data.id+'" target="_blank">'+data.bezeichnung+'</a>'
                        );
                    }
                }
            });
        },
        createExportTemplate: function (template)
        {
            $.ajax({
                url: 'index.php?module=shopimporter_amazon&action=sendarticles&cmd=createExportTemplate',
                type: 'POST',
                dataType: 'json',
                data: {
                    template: template,
                },
                success: function (data) {
                    if(typeof data.id != 'undefined') {
                        $(me.selector.flatFileTemplateInfo).html(
                            'Exportvorlage: <a href="index.php?module=exportvorlage&action=edit&id='
                            +data.id+'" target="_blank">'+data.bezeichnung+'</a>'
                        );
                    }
                }
            });
        },
        getArticle: function (id) {
            $.ajax({
                url: 'index.php?module=shopimporter_amazon&action=sendarticles&cmd=getarticle',
                type: 'POST',
                dataType: 'json',
                data: {
                    article_id: id,
                    flatfile_id: $('#flatfile').val(),
                    shopid: $(me.selector.articlePopup).data('shopid')
                },
                success: function (data) {
                    $(me.selector.articlePopupForm).html(data.html);
                    me.updateAutoComplete();

                    $(me.selector.articlePopupForm + ' input[data-required]').on('change',
                        function () {
                            if (trim($(this).val() + '') === ''
                                &&
                                (typeof $(this).attr('placeholder') == 'undefined'
                                    || trim($(this).attr('placeholder') + '') === '')
                            ) {
                                $(this).next('span').remove();
                                $(this).after('<span class="red">Pflichtfeld</span>');
                            } else {
                                $(this).next('span').remove();
                            }
                        }
                    );
                    $('#feed_product_type').on('change', function () {
                        me.updateAutoComplete();
                    });
                    $('#flatarticletabs').tabs();
                    $(me.selector.articlePopupForm + ' input[data-required]').trigger('change');
                    $('img.delprevimage').on('click', function () {
                        $('#' + $(this).data('field')).val('');
                        $('#' + $(this).data('field')).parents('tr').first().find('img.amazonimageprev').attr(
                            'src',
                            './themes/new/images/keinbild_hell.png'
                        );
                    });
                    $('img.getprevimage').on('click', function () {
                        var val = ($('#amazonimgprevdiv').find(':checked').val()) + '';
                        if (val !== '') {
                            $('#' + $(this).data('field')).val(val);
                            $('#' + $(this).data('field')).parents('tr').first().find('img.amazonimageprev').attr(
                                'src',
                                'index.php?module=ajax&action=thumbnail&cmd=artikel&id=' + val
                            );
                        }
                    });
                    $(me.selector.articlePopup).dialog('open');
                },
                beforeSend: function () {

                }
            });
        },
        openTemplate: function(){
            me.storage.templateTarget = 'articlePopup';
            $(me.selector.flatFileTemplateInfo).html('');
            $(me.selector.templatePopup).dialog('open');
        },
        init: function () {
            if ($(me.selector.articlePopup).length === 0) {
                return;
            }
            $(me.selector.articlePopup).dialog(
                {
                    modal: true,
                    autoOpen: false,
                    minWidth: 1040,
                    title: '',
                    buttons: {
                        'SPEICHERN': function () {
                            $(me.selector.articlePopupForm + ' input[data-required]').trigger('change');
                            if ($(me.selector.articlePopupForm + ' span.red').length
                                && !confirm('Es sind nicht alle Pflichtfelder ausgewählt wirklich speicher?')) {
                                return;
                            }
                            $(me.selector.articlePopupForm).trigger('submit');
                        },
                        ABBRECHEN: function () {
                            $(this).dialog('close');
                        }
                    },
                    close: function (event, ui) {

                    }
                });
            $(me.selector.templatePopup).dialog({
                modal: true,
                autoOpen: false,
                minWidth: 1040,
                title: '',
                buttons: {
                    'WEITER': function () {
                        if($('#flatfile').val()+'' !== '') {
                            if(me.storage.templateTarget === 'articlePopup') {
                                $(this).dialog('close');
                                me.getArticle(0);
                            }
                            if(me.storage.templateTarget === 'exportTemplate') {
                                me.createExportTemplate($('#flatfile').val());
                            }
                            if(me.storage.templateTarget === 'importTemplate') {
                                me.createImportTemplate($('#flatfile').val());
                            }
                        }
                    },
                    'ABBRECHEN': function () {
                        $(this).dialog('close');
                    }
                },
                close: function (event, ui) {

                }
            });
            $('#new').on('click', function () {
                me.openTemplate();
            });
            $('#newExportSendArticles').on('click', function (){
                me.storage.templateTarget = 'exportTemplate';
                $(me.selector.flatFileTemplateInfo).html('');
                $(me.selector.templatePopup).dialog('open');
            });
            $('#newImportSendArticles').on('click', function (){
                me.storage.templateTarget = 'importTemplate';
                $(me.selector.flatFileTemplateInfo).html('');
                $(me.selector.templatePopup).dialog('open');
            });
            $('#send').on('click', function () {
                var data_ids = '';
                $(me.selector.flatFileTable + ' input:checked').each(function () {
                    data_ids += ',' + $(this).data('id');
                });
                if (data_ids !== '') {
                    if ($('#selaction').val() === 'send') {
                        $.ajax({
                            url: 'index.php?module=shopimporter_amazon&action=sendarticles&cmd=sendarticles',
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                article_ids: data_ids
                            },
                            success: function (data) {
                                me.reloadFlatFileTable();
                            }
                        });
                    } else if ($('#selaction').val() === 'delete') {
                        if (!confirm('Wirklich löschen?')) {
                            return;
                        }
                        $.ajax({
                            url: 'index.php?module=shopimporter_amazon&action=sendarticles&cmd=deletearticle',
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                article_ids: data_ids
                            },
                            success: function (data) {
                                me.reloadFlatFileTable();
                            }
                        });
                    }
                } else {
                    alert('Sie haben keine Artikel ausgewählt');
                }
            });
            $(me.selector.flatFileTable).on('afterreload', function () {
                $(this).find(me.selector.getIcon).off('click');
                $(this).find(me.selector.deleteIcon).off('click');
                $(this).find(me.selector.getIcon).on('click', function () {
                    me.getArticle($(this).data('id'));
                });
                $(this).find(me.selector.deleteIcon).on('click', function () {
                    me.deleteArticle($(this).data('id'));
                });
            });
            $(me.selector.flatFileTable).trigger('afterreload');
        }
    };
    return {
        init: me.init
    };

}(jQuery);

$(document).ready(function () {
    ShopImporterAmazonSendArticles.init();
});
