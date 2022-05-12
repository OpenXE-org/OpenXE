var CompanyData = (function ($) {
    var me = {

        storage: {},

        init: function () {
            $('#document_popup').dialog(
                {
                    modal: true,
                    autoOpen: false,
                    minWidth: 800,
                    title:'',
                    buttons: {
                        'ABBRECHEN': function() {
                            $(this).dialog('close');
                        },
                        'SPEICHERN': function()
                        {
                            $.ajax({
                                url: 'index.php?module=firmendaten&action=documentsettings&cmd=savedocument',
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    id: $('#document_id').val(),
                                    doctype: $('#document_doctype').val(),
                                    content:$('#document_content').val(),
                                    active:$('#document_active').prop('checked')?1:0,
                                    project:$('#document_project').val(),
                                    fontstyle:$('#document_fontstyle').val(),
                                    alignment:$('#document_alignment').val()
                                },
                                success: function(data) {
                                    if(typeof data.status != 'undefined' && data.status == 1) {
                                        $('#company_document_setting').DataTable( ).ajax.reload();
                                        $('#document_popup').dialog('close');
                                    }else{
                                        alert(data.statusText);
                                    }
                                },
                                beforeSend: function() {

                                }
                            });
                        }
                    },
                    close: function(event, ui){

                    }
                });

            me.registerEvents();

            $('#translation_popup').dialog(
                {
                    modal: true,
                    autoOpen: false,
                    minWidth: 800,
                    title:'',
                    buttons: {
                        'ABBRECHEN': function() {
                            $(this).dialog('close');
                        },
                        'SPEICHERN': function()
                        {
                            $.ajax({
                                url: 'index.php?module=firmendaten&action=documentsettings&cmd=savetranslation',
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    parent_id: $('#document_id').val(),
                                    language: $('#language').val(),
                                    doctype: $('#document_doctype').val(),
                                    content:$('#translationcontent').val(),
                                    active:$('#active').prop('checked')?1:0,
                                    fontstyle:$('#document_translation_fontstyle').val(),
                                    alignment:$('#document_translation_alignment').val()
                                },
                                success: function(data) {
                                    $('#translation_popup').dialog('close');
                                },
                                beforeSend: function() {

                                }
                            });
                        }
                    },
                    close: function(event, ui){

                    }
                });

            $('#language').on('change',function() {
                $.ajax({
                    url: 'index.php?module=firmendaten&action=documentsettings&cmd=loadtranslation',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        language: $(this).val(),
                        parent_id: $('#document_id').val()
                    },
                    success: function(data) {
                        if(typeof data.id != 'undefined' && data.id != '0') {
                            $('#active').prop('checked', data.active);
                            $('#translationcontent').val(data.content);
                            $('#document_translation_fontstyle').val(data.fontstyle);
                            $('#document_translation_alignment').val(data.alignment);
                        }
                    },
                    beforeSend: function() {

                    }
                });
            });

            $('button.getelements').on('click',function(){
                $($('#document_preview option:selected')).each(function(){
                    var $elements = $('#document_content');
                    if(($($elements).val()+'').indexOf('{'+$(this).val()+'}') < 0)
                    {
                        $($elements).val($($elements).val()+($($elements).val()+'' ===''?'':"\n")+$(this).text()+':|{'+$(this).val()+'}');
                    }
                });
            });

            $('button.gettranslationelements').on('click',function(){
                $($('#document_translation_preview option:selected')).each(function(){
                    var $elements = $('#translationcontent');
                    if(($($elements).val()+'').indexOf('{'+$(this).val()+'}') < 0)
                    {
                        $($elements).val($($elements).val()+($($elements).val()+'' ===''?'':"\n")+$(this).text()+':|{'+$(this).val()+'}');
                    }
                });
            });

            $('img.translate').on('click', function(){
                me.openTranslation($(this).data('document'));
            });


            $('input.edit').on('click', function(){
                me.openDocument($(this).data('id'));
            });

            $('input#selectall').on('change', function(){
               $('#company_document_setting input:checkbox').prop('checked', $(this).prop('checked'));
            });

            $('#doaction').on('click', function(){
                if($('#actionselection').val() !== '' && $('#company_document_setting :checked').length) {
                    var ids = [];
                    $('#company_document_setting :checked').each(function(){
                        ids.push($(this).val());
                    });
                    $.ajax({
                        url: 'index.php?module=firmendaten&action=documentsettings&cmd=changestatus',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            action: $('#actionselection').val(),
                            ids: ids
                        },
                        success: function(data) {
                            $('#company_document_setting').DataTable( ).ajax.reload();
                        },
                        beforeSend: function() {

                        }
                    });
                }
            });

            $('#document_doctype').on('change', function(){
                $('#document_preview option').each(function() {
                    if ($(this).hasClass('doctype-'+$('#document_doctype').val())) {
                        $(this).show();
                    }else{
                        $(this).hide();
                    }
                });
            });
        },

        registerEvents: function () {

            $(document).on('click', '.companydocument-edit', function (e) {
                e.preventDefault();
                var fieldId = $(this).data('companydocumentId');
                me.openDocument(fieldId);
            });

            $(document).on('click', '.companydocument-copy', function (e) {
                e.preventDefault();
                var fieldId = $(this).data('companydocumentId');
                me.copyInfoBlock(fieldId);
            });

            $(document).on('click', '.companydocument-delete', function (e) {
                e.preventDefault();
                var fieldId = $(this).data('companydocumentId');
                me.deleteInfoBlock(fieldId);
            });

        },

        openTranslation: function(doctype) {
            $('#doctype').val(doctype);
            $('#language').trigger('change');
            $('#translation_popup').dialog('open');
        },
        deleteInfoBlock: function(id) {
            if(id && confirm('Wirklich löschen?') )
            {
                $.ajax({
                    url: 'index.php?module=firmendaten&action=documentsettings&cmd=deleteinfoblock',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        id:id
                    },
                    success: function(data) {
                        $('#company_document_setting').DataTable( ).ajax.reload();
                    },
                    beforeSend: function() {

                    }
                });
            }
        },
        copyInfoBlock: function(id) {
            if(id && confirm('Wirklich kopieren?') )
            {
                $.ajax({
                    url: 'index.php?module=firmendaten&action=documentsettings&cmd=copyinfoblock',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        id: id
                    },
                    success: function(data) {
                        $('#company_document_setting').DataTable( ).ajax.reload();
                    },
                    beforeSend: function() {

                    }
                });
            }
        },
        openDocument: function(id) {
            $('#document_id').val(id);
            if(id) {
                $('img#opentranlation').show();
            }
            else {
                $('img#opentranlation').hide();
            }
            $.ajax({
                url: 'index.php?module=firmendaten&action=documentsettings&cmd=loaddocument',
                type: 'POST',
                dataType: 'json',
                data: {
                    id: id,
                },
                success: function(data) {
                    $('#document_active').prop('checked',data.active);
                    $('#document_doctype').val(data.doctype);
                    $('#document_content').val(data.content);
                    $('#document_project').val(data.project);
                    $('#document_fontstyle').val(data.fontstyle);
                    $('#document_alignment').val(data.alignment);

                    $('#document_preview option').each(function() {
                        if($('#document_doctype').val() === null || $('#document_doctype').val == ''){
                            $(this).show();
                        }else{
                            if ($(this).hasClass('doctype-'+$('#document_doctype').val())) {
                                $(this).show();
                            }else{
                                $(this).hide();
                            }
                        }
                    });


                    $('#document_popup').dialog('open');
                },
                beforeSend: function() {

                }
            });

        }

    };

    return {
        init: me.init
    }

})(jQuery);


$(document).ready(function(){
    CompanyData.init();
});