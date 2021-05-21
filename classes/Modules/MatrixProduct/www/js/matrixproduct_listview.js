var MatrixproductListview = function ($) {
    "use strict";

    var me = {
        storage:{
            selectedArticles: '',
            optionTableloaded: false,
            groupTableloaded: false,
            articleTableloaded: false,
        },
        selector: {
            divlistview: 'div.listview',
            popupGroup: '#popupgroup',
            popupOption: '#popupoption',
            popupArticleCreate: '#popuparcticlecreate',
            popupArticle: '#popuparticle',
            btnNewGroup: '#newGroup',
            btnNewOption: '#newOption',
            articleTable: '#matrixprodukt_list_view',
            groupTable: '#matrixprodukt_list_view_group',
            optionTable: '#matrixprodukt_list_view_options',
        },
        editArticle: function(id) {
            if(id > 0) {
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: 'index.php?module=matrixprodukt&action=artikel&cmd=listgetarticle',
                    data: {
                        articleid: $(me.selector.popupGroup).data('articleid'),
                        listid: id
                    },
                    success: function (data) {
                        $('div#options').html(data.optionhtml);
                        $('#articlelistid').val(data.id);
                        $(me.selector.popupArticle).dialog('open');
                    }
                });
            }
        },
        editGroup: function(id){
            $('#groupid').val(id);
            if(id > 0) {
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: 'index.php?module=matrixprodukt&action=artikel&cmd=listgetgroup',
                    data: {
                        articleid: $(me.selector.popupGroup).data('articleid'),
                        groupid: $('#groupid').val()
                    },
                    success: function (data) {
                        $('#groupname').val(data.name);
                        $(me.selector.popupGroup).dialog('open');
                    }
                });
                return;
            }

            $(me.selector.popupGroup).dialog('open');
        },
        deleteGroup: function(id) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: 'index.php?module=matrixprodukt&action=artikel&cmd=listdeletegroupcheck',
                data: {
                    articleid: $(me.selector.popupGroup).data('articleid'),
                    groupid: id
                },
                success: function (data) {
                    if(typeof data.message) {
                        if(!confirm(data.message)) {
                            return;
                        }
                        $.ajax({
                            type: 'POST',
                            dataType: 'json',
                            url: 'index.php?module=matrixprodukt&action=artikel&cmd=listdeletegroup',
                            data: {
                                articleid: $(me.selector.popupGroup).data('articleid'),
                                groupid: data.groupid
                            },
                            success: function (data) {
                                if(typeof data.url !== 'undefined') {
                                    window.location.href=data.url;
                                    return;
                                }
                                $(me.selector.optionTable).DataTable().ajax.reload();
                                $(me.selector.groupTable).DataTable().ajax.reload();
                                $(me.selector.articleTable).DataTable().ajax.reload();
                            },
                            error: function()
                            {
                                $(me.selector.optionTable).DataTable().ajax.reload();
                                $(me.selector.groupTable).DataTable().ajax.reload();
                                $(me.selector.articleTable).DataTable().ajax.reload();
                            }
                        });
                    }
                }
            });
        },
        deleteOption: function(id) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: 'index.php?module=matrixprodukt&action=artikel&cmd=listdeleteoptioncheck',
                data: {
                    articleid: $(me.selector.popupGroup).data('articleid'),
                    optionid: id
                },
                success: function (data) {
                    if(typeof data.message) {
                        if(!confirm(data.message)) {
                            return;
                        }
                        $.ajax({
                            type: 'POST',
                            dataType: 'json',
                            url: 'index.php?module=matrixprodukt&action=artikel&cmd=listdeleteoption',
                            data: {
                                articleid: $(me.selector.popupGroup).data('articleid'),
                                optionid: data.optionid
                            },
                            success: function () {
                                $(me.selector.optionTable).DataTable().ajax.reload();
                                $(me.selector.articleTable).DataTable().ajax.reload();
                            },
                            error: function() {
                                $(me.selector.optionTable).DataTable().ajax.reload();
                                $(me.selector.articleTable).DataTable().ajax.reload();
                            }
                        });
                    }
                }
            });
        },
        generateList: function()
        {
            $('#tabs-1').loadingOverlay('show');
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: 'index.php?module=matrixprodukt&action=artikel&cmd=generatelist',
                data: {
                    articleid: $(me.selector.popupGroup).data('articleid')
                },
                success: function (data) {
                    if(typeof data.url != 'undefined') {
                        window.location.href=data.url;
                        return;
                    }
                    $('#tabs-1').loadingOverlay('remove');
                },
                error:function()
                {
                    $('#tabs-1').loadingOverlay('remove');
                }
            });
        },
        massedit: function(){
            me.storage.selectedArticles= '';
            $(me.selector.articleTable).find('input:checked').each(function(){
                if($(this).data('articleid') > 0) {
                    if (me.storage.selectedArticles !== '') {
                        me.storage.selectedArticles
                            = me.storage.selectedArticles + ';';
                    }
                    me.storage.selectedArticles
                        = me.storage.selectedArticles
                        + $(this).data('articleid')
                }

            });
            if(me.storage.selectedArticles !== '') {
                matrixproduktedit_open(me.storage.selectedArticles);
            }
            else {
                alert('Kein Artikel ausgwählt');
            }
        },
        createallmissingarticles: function() {
            me.storage.selectedArticles= 'ALL';
            $('#listids').val(me.storage.selectedArticles);
            $(me.selector.popupArticleCreate).dialog('open');
        },
        createMissingArticles: function(){
            me.storage.selectedArticles= '';
            $(me.selector.articleTable).find('input:checked').each(function(){
                if(me.storage.selectedArticles !== '') {
                    me.storage.selectedArticles
                        =me.storage.selectedArticles + ';';
                }
                me.storage.selectedArticles
                    =me.storage.selectedArticles
                    +$(this).data('id')
            });
            if(me.storage.selectedArticles !== '') {
                $('#listids').val(me.storage.selectedArticles);
                $(me.selector.popupArticleCreate).dialog('open');
            }
        },
        editOption: function(id){
            $('#optionid').val(id);
            if(id > 0) {
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: 'index.php?module=matrixprodukt&action=artikel&cmd=listgetoption',
                    data: {
                        articleid: $(me.selector.popupGroup).data('articleid'),
                        optionid: $('#optionid').val()
                    },
                    success: function (data) {
                        $('#optionname').val(data.name);
                        $('#optiongroup').html(data.groups);
                        $('#optiongroup').val(data.gruppe);
                        $(me.selector.popupOption).dialog('open');
                    }
                });
                return;
            }
            else {
                $('#optionname').val('');
            }

            $(me.selector.popupOption).dialog('open');

        },

        articeTableAfterReload:function(){
            $(me.selector.articleTable).find('img.editarticle').on('click',function(){
                me.editArticle($(this).data('id'));
            });
            me.storage.articleTableloaded = true;
        },
        groupTableAfterReload:function(){
            $(me.selector.groupTable).find('img.editgroup').on('click',function(){
                me.editGroup($(this).data('id'));
            });
            $(me.selector.groupTable).find('img.deletegroup').on('click',function(){
                me.deleteGroup($(this).data('id'));
            });
            me.storage.groupTableloaded = true;
        },
        optionTableAfterReload:function(){
            $(me.selector.optionTable).find('img.editoption').on('click',function(){
                me.editOption($(this).data('id'));
            });
            $(me.selector.optionTable).find('img.deleteoption').on('click',function(){
                me.deleteOption($(this).data('id'));
            });
            me.storage.optionTableloaded = true;
        },
        createMissingAriclesSave: function()
        {
            $(me.selector.popupArticleCreate).parent().loadingOverlay('show');
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: 'index.php?module=matrixprodukt&action=artikel&cmd=createarticles',
                data: {
                    listids:$('#listids').val(),
                    articleid: $(me.selector.popupGroup).data('articleid'),
                    fromcategory: $('#fromcategory').prop('checked')?1:0,
                    fromoption: $('#fromoption').prop('checked')?1:0,
                    fromsuffix: $('#fromsuffix').prop('checked')?1:0,
                    fromprefix: $('#fromprefix').prop('checked')?1:0,
                    prefixseparator: $('#prefixseparator').val(),
                    prefixcount: $('#prefixcount').val(),
                    prefixnextnumber: $('#prefixnextnumber').val(),
                    appendname: $('#prefixseparator').prop('checked')?1:0,
                    nextprefixnumber: $('#nextprefixnumber').val(),
                },
                success: function (data) {
                    $(me.selector.popupArticleCreate).parent().loadingOverlay('remove');

                    if(typeof data.continue  != 'undefined' && data.continue == 1) {
                        if(typeof data.nextprefixnumber != 'undefined') {
                            $('#nextprefixnumber').val(data.nextprefixnumber);
                        }
                        me.createMissingAriclesSave();
                        return;
                    }
                    $('#nextprefixnumber').val('');
                    $(me.selector.articleTable).DataTable().ajax.reload();
                    $(me.selector.popupArticleCreate).dialog('close');
                },
                error: function()
                {
                    $(me.selector.popupArticleCreate).parent().loadingOverlay('remove');
                    $('#nextprefixnumber').val('');
                    $(me.selector.articleTable).DataTable().ajax.reload();
                    $(me.selector.popupArticleCreate).dialog('close');
                }
            });
        },
        initListView: function(){
            $(me.selector.popupGroup).dialog(
                {
                    modal: true,
                    autoOpen: false,
                    minWidth: 940,
                    title: '',
                    buttons: {
                        'ABBRECHEN':function(){
                          $(me.selector.popupGroup).dialog('close');
                        },
                        'SPEICHERN': function () {
                            $(me.selector.popupGroup).parent().loadingOverlay('show');
                            $.ajax({
                                type: 'POST',
                                dataType: 'json',
                                url: 'index.php?module=matrixprodukt&action=artikel&cmd=listsavegroup',
                                data: {
                                    articleid: $(me.selector.popupGroup).data('articleid'),
                                    name: $('#groupname').val(),
                                    groupid: $('#groupid').val()
                                },
                                success: function (data) {
                                    if(typeof data.url != 'undefined') {
                                        window.location.href=data.url;
                                        return;
                                    }
                                    $(me.selector.groupTable).DataTable().ajax.reload();
                                    $(me.selector.articleTable).DataTable().ajax.reload();
                                    $(me.selector.popupGroup).parent().loadingOverlay('remove');
                                },
                                error: function()
                                {
                                    $(me.selector.groupTable).DataTable().ajax.reload();
                                    $(me.selector.articleTable).DataTable().ajax.reload();
                                    $(me.selector.popupGroup).parent().loadingOverlay('remove');
                                }
                            });
                        },
                    }
                }
            );
            $(me.selector.popupArticleCreate).dialog(
                {
                    modal: true,
                    autoOpen: false,
                    minWidth: 940,
                    title: '',
                    buttons: {
                        'ABBRECHEN':function(){
                            $(me.selector.popupArticleCreate).dialog('close');
                        },
                        'SPEICHERN': function () {
                            me.createMissingAriclesSave();
                        },
                    }
                }
            );
            $(me.selector.popupArticle).dialog(
                {
                    modal: true,
                    autoOpen: false,
                    minWidth: 940,
                    title: '',
                    buttons: {
                        'ABBRECHEN':function(){
                            $(me.selector.popupArticle).dialog('close');
                        },
                        'SPEICHERN': function () {
                            $(me.selector.popupArticle).parent().loadingOverlay('show');
                            $.ajax({
                                type: 'POST',
                                dataType: 'json',
                                url: 'index.php?module=matrixprodukt&action=artikel&cmd=listsavearticle',
                                data: {
                                    articleid: $(me.selector.popupGroup).data('articleid'),
                                    listid:$('#articlelistid').val(),
                                    option1: (typeof $('#option1').val() != 'undefined'?$('#option1').val():0),
                                    option2: (typeof $('#option2').val() != 'undefined'?$('#option2').val():0),
                                    option3: (typeof $('#option3').val() != 'undefined'?$('#option3').val():0),
                                    option4: (typeof $('#option4').val() != 'undefined'?$('#option4').val():0),
                                    option5: (typeof $('#option5').val() != 'undefined'?$('#option5').val():0),
                                    option6: (typeof $('#option6').val() != 'undefined'?$('#option6').val():0),
                                    option7: (typeof $('#option7').val() != 'undefined'?$('#option7').val():0),
                                    option8: (typeof $('#option8').val() != 'undefined'?$('#option8').val():0),
                                    option9: (typeof $('#option9').val() != 'undefined'?$('#option9').val():0),
                                    option10: (typeof $('#option10').val() != 'undefined'?$('#option10').val():0),
                                    option11: (typeof $('#option11').val() != 'undefined'?$('#option11').val():0),
                                    option12: (typeof $('#option12').val() != 'undefined'?$('#option12').val():0),
                                    option13: (typeof $('#option13').val() != 'undefined'?$('#option13').val():0),
                                    option14: (typeof $('#option14').val() != 'undefined'?$('#option14').val():0),
                                    option15: (typeof $('#option15').val() != 'undefined'?$('#option15').val():0),
                                    option16: (typeof $('#option16').val() != 'undefined'?$('#option16').val():0),
                                    option17: (typeof $('#option17').val() != 'undefined'?$('#option17').val():0),
                                    option18: (typeof $('#option18').val() != 'undefined'?$('#option18').val():0),
                                    option19: (typeof $('#option19').val() != 'undefined'?$('#option19').val():0),
                                    option20: (typeof $('#option20').val() != 'undefined'?$('#option20').val():0),
                                },
                                success: function () {
                                    $(me.selector.articleTable).DataTable().ajax.reload();
                                    $(me.selector.popupArticle).parent().loadingOverlay('remove');
                                    $(me.selector.popupArticle).dialog('close');
                                },
                                error: function() {
                                    $(me.selector.articleTable).DataTable().ajax.reload();
                                    $(me.selector.popupArticle).parent().loadingOverlay('remove');
                                    $(me.selector.popupArticle).dialog('close');
                                }
                            });
                        },
                    }
                }
            );
            $(me.selector.popupOption).dialog(
                {
                    modal: true,
                    autoOpen: false,
                    minWidth: 940,
                    title: '',
                    buttons: {
                        'ABBRECHEN':function(){
                            $(me.selector.popupOption).dialog('close');
                        },
                        'SPEICHERN': function () {
                            $(me.selector.popupOption).parent().loadingOverlay('show');
                            $.ajax({
                                type: 'POST',
                                dataType: 'json',
                                url: 'index.php?module=matrixprodukt&action=artikel&cmd=listsaveoption',
                                data: {
                                    articleid: $(me.selector.popupGroup).data('articleid'),
                                    name: $('#optionname').val(),
                                    groupid: $('#optiongroup').val(),
                                    optionid: $('#optionid').val()
                                },
                                success: function () {
                                    $(me.selector.optionTable).DataTable().ajax.reload();
                                    $(me.selector.articleTable).DataTable().ajax.reload();
                                    $(me.selector.popupOption).parent().loadingOverlay('remove');
                                    $(me.selector.popupOption).dialog('close');
                                },
                                error: function(){
                                    $(me.selector.optionTable).DataTable().ajax.reload();
                                    $(me.selector.articleTable).DataTable().ajax.reload();
                                    $(me.selector.popupOption).parent().loadingOverlay('remove');
                                    $(me.selector.popupOption).dialog('close');
                                }
                            });
                        },
                    }
                }
            );

            $(me.selector.popupGroup).toggleClass('hidden', false);
            $(me.selector.popupOption).toggleClass('hidden', false);
            $(me.selector.popupArticle).toggleClass('hidden', false);
            $(me.selector.popupArticleCreate).toggleClass('hidden', false);

            $(me.selector.articleTable).on('afterreload', function(){
               me.articeTableAfterReload();
            });
            $(me.selector.optionTable).on('afterreload', function(){
               me.optionTableAfterReload();
            });
            $(me.selector.groupTable).on('afterreload', function(){
               me.groupTableAfterReload();
            });
            $(me.selector.btnNewGroup).on('click',function(){
                me.editGroup(0);
            });
            $(me.selector.btnNewOption).on('click',function(){
                me.editOption(0);
            });
            $('#changeall').on('change',function(){
                $(me.selector.articleTable).find('input.select').prop('checked', $('#changeall').prop('checked'));
            });
            $('#createmissingarticles').on('click',function () {
                me.createMissingArticles();
            });
            $('#createallmissingarticles').on('click',function () {
                me.createallmissingarticles();
            });
            $('#massedit').on('click',function () {
                me.massedit();
            });
            $('#generatelist').on('click',function () {
                me.generateList();
            });
            if(!me.storage.optionTableloaded) {
                $(me.selector.optionTable).DataTable().ajax.reload();
            }
            if(!me.storage.groupTableloaded) {
                $(me.selector.groupTable).DataTable().ajax.reload();
            }
            if(!me.storage.articleTableloaded) {
                $(me.selector.articleTable).DataTable().ajax.reload();
            }
        },
        init: function () {
            if($(me.selector.divlistview).length) {
                me.initListView();
            }
        }
    };


    return {
        init: me.init,
    }

}(jQuery);

$(document).ready(function () {
    MatrixproductListview.init();
});
