var Wiki = function ($) {
    "use strict";

    var me = {
        storage: {
            actlanguage: null,
            actworkspace: null,
            workspaceid: 0,
        },

        editworkspace: function (id) {
            $.ajax({
                url: 'index.php?module=wiki&action=settings&command=openworkspace',
                type: 'POST',
                dataType: 'json',
                data: {
                    id: id
                },
                success: function(data) {
                    $('#workspace_active').prop('checked', data.active== 1?true:false);
                    $('#workspace_name').val(data.name);
                    $('#workspace_savein').val(data.savein);
                    me.storage.workspaceid = data.id;
                    $('#popupworkspace').dialog('open');
                },
                beforeSend: function() {

                }
            });
        },
        listworkspace: function(id){
            $.ajax({
                url: 'index.php?module=wiki&action=settings&command=openworkspace',
                type: 'POST',
                dataType: 'json',
                data: {
                    id: id
                },
                success: function(data) {
                    $('#workspace_active').prop('checked', data.active== 1?true:false);
                    $('#workspace_name').val(data.name);
                    $('#workspace_savein').val(data.savein);
                    me.storage.workspaceid = data.id;
                    $('#popupsites').dialog('open');
                },
                beforeSend: function() {

                }
            });
        },
        deleteworkspace:function(id) {
            $.ajax({
                url: 'index.php?module=wiki&action=settings&command=deleteworkspace',
                type: 'POST',
                dataType: 'json',
                data: {
                    id: id
                },
                success: function(data) {
                    $('#wiki_workspaces').DataTable().ajax.reload();
                },
                beforeSend: function() {

                }
            });
        },

        init: function () {
            if($('#wikiinstall').length) {
                $('#wikiinstall').remove();
                $.ajax({
                    url: 'index.php?module=wiki&action=list&command=install',
                    type: 'POST',
                    dataType: 'json',
                    data: {

                    },
                    success: function() {

                    },
                });
            }
            if($('#popupsites').length){
                $('#popupsites').dialog(
                    {
                        modal: true,
                        autoOpen: false,
                        minWidth: 940,
                        title: '',
                        buttons: {
                            'OK': function () {
                                $(this).dialog('close');
                            }
                        },
                        close: function (event, ui) {

                        }
                    });
            }

            if($('#popupworkspace').length)
            {
                $('#popupworkspace').dialog(
                    {
                        modal: true,
                        autoOpen: false,
                        minWidth: 940,
                        title: '',
                        buttons: {
                            'OK': function () {
                                $.ajax({
                                    url: 'index.php?module=wiki&action=settings&command=saveworkspace',
                                    type: 'POST',
                                    dataType: 'json',
                                    data: {
                                        id: me.storage.workspaceid,
                                        name: $('#workspace_name').val(),
                                        savein: $('#workspace_savein').val(),
                                        active: $('#workspace_active').prop('checked')?1:0
                                    },
                                    success: function(data) {
                                        $('#popupworkspace').dialog('close');
                                        $('#wiki_workspaces').DataTable().ajax.reload();
                                    },
                                    beforeSend: function() {

                                    }
                                });

                            },
                            'ABBRECHEN': function () {
                                $(this).dialog('close');
                            }
                        },
                        close: function (event, ui) {

                        }
                    });
                $('a.neubuttonlink').on('click',function(){
                   me.editworkspace(0);
                });

                $('#wiki_workspaces').on('afterreload',function(){
                    $('img.workspaceedit').on('click',function(){
                        me.editworkspace($(this).data('id'));
                    });
                    $('img.workspacesites').on('click',function(){
                        me.listworkspace($(this).data('id'));
                    });
                    $('img.workspacedelete').on('click',function(){
                        if(confirm('Wirklich löschen?')) {
                            me.deleteworkspace($(this).data('id'));
                        }
                    });
                });
            }
            if($('#changepopup').length) {
                $('#changepopup').dialog(
                {
                    modal: true,
                    autoOpen: false,
                    minWidth: 940,
                    title: '',
                    buttons: {
                        'OK': function () {
                            if(typeof CkEditor5Helper != 'undefined') {
                                CkEditor5Helper.update('content');
                            }
                            /*
                            if(typeof editorcontent != 'undefined') {
                                editorcontent.updateSourceElement();
                            }*/
                            $.ajax({
                                url: 'index.php?module=wiki&action=list&command=savearticle',
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    workspace: me.storage.actworkspace,
                                    language: me.storage.actlanguage,
                                    content: $('textarea#content').length?$('textarea#content').val():$('#wikicontent').html(),
                                    comment: $('#comment').val(),
                                    notify: $('#notify').prop('checked')?1:0,
                                    site: $('#wikicontent').data('site')
                                },
                                success: function(data) {
                                    $('#changepopup').dialog('close');
                                    if($('#wikieditsubmit').length) {
                                        window.location.href=data.url;
                                    }
                                },
                                beforeSend: function() {

                                }
                            });

                        },
                        'ABBRECHEN': function () {
                            $(this).dialog('close');
                        }
                    },
                    close: function (event, ui) {

                    }
                });
            }
            if($('#wikieditsubmit').length){
                me.storage.actworkspace = $('td[data-workspace]').data('workspace');
                me.storage.actlanguage = $('td[data-language]').data('language');
                $('#wikieditsubmit').on('click', function(){
                    $('#changepopup').dialog('open');
                });
            }
            if($('#tabwikiedit').length) {
                $('#tabwikiedit').trigger('draw.dt', [{sTableId:"tabwikiedit"}]);
            }
            if($('#tabwikilist').length) {
                $('#tabwikilist').trigger('draw.dt', [{sTableId:"tabwikilist"}]);
            }
            if($('#language').length) {


                $('#save').on('click', function () {
                    $('#changepopup').dialog('open');
                });
                me.storage.actlanguage = $('#language').val();
                me.storage.actworkspace = $('#workspace').val();
                $('#language').trigger('change');
                $('#workspace').trigger('change');
            }
            if($('#alleworkspace').length) {
                me.storage.actworkspace = $('#alleworkspace').val();
                $('#alleworkspace').on('change',function(){
                    $.ajax({
                        url: 'index.php?module=wiki&action=alle&command=gethtml',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            workspace: $(this).val()
                        },
                        success: function(data) {
                            $('#wikicontent').html(data.html);
                        },
                        beforeSend: function() {

                        }
                    });
                });
            }
            if($('#popupfaq').length) {
                me.initfaq();
            }
            if($('#tabwikilist').length) {
                me.initlist();
            }
            if($('#wiki_changelog').length) {
                me.initchangelog();
            }
            if($('#frmwikiedit').length) {
                me.initedit();
            }
            if($('#wikitabnew').length) {
                me.initnew();
            }

            //WAWIIF VERSION=DEV
            if($('#reloadlinkbutton').length) {
                $('#reloadlinkbutton').on('click', function(){
                    $('#wikipopupurl').val('');
                    $('#reloadwikipopup').dialog('open');
                });
            }
            if($('#reloadwikipopup').length){
                $('#reloadwikipopup').dialog(
                    {
                        modal: true,
                        autoOpen: false,
                        minWidth: 940,
                        title: '',
                        buttons: {
                            'OK': function () {
                                $.ajax({
                                    url: 'index.php?module=wiki&action=edit&command=reloadfromurl',
                                    type: 'POST',
                                    dataType: 'json',
                                    data: {
                                        id:$('#wikipopupid').val(),
                                        url: $('#wikipopupurl').val()
                                    },
                                    success: function(data) {
                                        if(typeof data.url != 'undefined') {
                                            window.location.href = data.url;
                                        }
                                        else {
                                            window.location.href = 'index.php?module=wiki&action=edit&id=' +
                                                $('#wikipopupid').val();
                                        }
                                    },
                                    beforeSend: function() {

                                    }
                                });

                            },
                            'ABBRECHEN': function () {
                                $(this).dialog('close');
                            }
                        },
                        close: function (event, ui) {

                        }
                    });
            }

            //WAWIEND
        },
        initnew:function(){
            $('#workspace').on('change',function(){
                $.ajax({
                    url: 'index.php?module=wiki&action=new&command=changeworkspace',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        workspace: $('#workspace').val()
                    },
                    success: function(data) {

                    },
                    beforeSend: function() {

                    }
                });
            });
            $('#languagenew').on('change',function(){
                $.ajax({
                    url: 'index.php?module=wiki&action=new&command=changelanguage',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        language: $('#languagenew').val()
                    },
                    success: function(data) {

                    },
                    beforeSend: function() {

                    }
                });
            });
        },
        initedit:function(){

        },
        initchangelog: function(){

        },
        initlist: function() {
            $('#workspace').on('change', function () {
                me.storage.actworkspace = $('#workspace').val();
                $.ajax({
                    url: 'index.php?module=wiki&action=list&command=loadarticle',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        workspace: me.storage.actworkspace,
                        language: me.storage.actlanguage,
                        site: $('#wikicontent').data('site')
                    },
                    success: function (data) {
                        $('#wikicontent').html(data.html);
                    },
                    beforeSend: function () {

                    }
                });
            });
            $('#language').on('change', function () {
                me.storage.actlanguage = $('#language').val();
                $.ajax({
                    url: 'index.php?module=wiki&action=list&command=loadarticle',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        workspace: me.storage.actworkspace,
                        language: me.storage.actlanguage,
                        site: $('#wikicontent').data('site')
                    },
                    success: function (data) {
                        $('#wikicontent').html(data.html);
                    },
                    beforeSend: function () {

                    }
                });
            });
        },
        initfaq: function () {

            $('#popupfaq').dialog(
                {
                    modal: true,
                    autoOpen: false,
                    minWidth: 940,
                    title: '',
                    buttons: {
                        'OK': function () {
                            if(typeof CkEditor5Helper != 'undefined') {
                                CkEditor5Helper.update('popupanswer');
                            }
                            /*
                            if(typeof editorpopupanswer != 'undefined') {
                                editorpopupanswer.updateSourceElement();
                            }*/
                            $.ajax({
                                url: 'index.php?module=wiki&action=faq&command=savefaq',
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    workspace: me.storage.actworkspace,
                                    language: me.storage.actlanguage,
                                    id: $('#popupid').val(),
                                    wikifaqid: $('#popupwikifaqid').val(),
                                    question: $('#popupquestion').val(),
                                    answer: $('#popupanswer').val()
                                },
                                success: function(data) {
                                    $('#popupfaq').dialog('close');
                                    $('#wiki_faq').DataTable( ).ajax.reload();
                                },
                                beforeSend: function() {

                                }
                            });

                        },
                        'ABBRECHEN': function () {
                            $(this).dialog('close');
                        }
                    },
                    close: function (event, ui) {

                    }
                });
            $('#wiki_faq').on('afterreload', function(){
               $('#wiki_faq').find('img.wikifaqedit').on('click', function(){
                   me.getFaq($(this).data('id'));
               });
               $('#wiki_faq').find('img.wikifaqdelete').on('click', function(){
                   me.deleteFaq($(this).data('id'));
               });
            });
            $('#newfaq').on('click',function(){
                me.getFaq(0);
            });
        },
        getFaq: function (wikiFaqId) {
            $('#popupwikifaqid').val(wikiFaqId);
            $.ajax({
                url: 'index.php?module=wiki&action=faq&command=getfaq',
                type: 'POST',
                dataType: 'json',
                data: {
                    wikifaqid: wikiFaqId
                },
                success: function(data) {
                    $('#popupanswer').val(data.answer);
                    $('#popupquestion').val(data.question);
                    if(typeof CkEditor5Helper != 'undefined') {
                        CkEditor5Helper.setData('popupquestion',data.question);
                    }
                    $('#popupfaq').dialog('open');
                },
                beforeSend: function() {

                }
            });
        },
        deleteFaq: function(wikiFaqId) {
            if(wikiFaqId > 0 && confirm('FAQ wirklich löschen?')) {
                $.ajax({
                    url: 'index.php?module=wiki&action=faq&command=deletefaq',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        wikifaqid: wikiFaqId
                    },
                    success: function(data) {
                        $('#wiki_faq').DataTable( ).ajax.reload();
                    },
                    beforeSend: function() {

                    }
                });
            }
        }
    };

    return {
        init: me.init
    };
}(jQuery);

$(document).ready(function () {
    Wiki.init();

});