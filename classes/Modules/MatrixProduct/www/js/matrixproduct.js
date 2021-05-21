$(document).ready(function() {
    $('a.groupheadline').on('click',function(){
        if(parseInt($(this).data('id')) > 0) {
            $('#GroupheadlineDialogGroupId').val($(this).data('id'));
            $('#GroupheadlineDialogArticleId').val($(this).data('article'));
            $('#GroupheadlineDialogGroupName').val($(this).data('name'));
            $('#GroupheadlineDialog').dialog('open');
        }
    });

});

$('#GroupheadlineDialog').dialog(
    {
        modal: true,
        autoOpen: false,
        minWidth: 940,
        title:'',
        buttons: {
            'ABBRECHEN': function() {
                $(this).dialog('close');
            },
            'ÄNDERN': function()
            {
                if($('#GroupheadlineDialogGroupName').val()+'' !== '') {
                    $.ajax({
                        url: 'index.php?module=matrixprodukt&action=artikel&cmd=changegroupname',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            groupId: $('#GroupheadlineDialogGroupId').val(),
                            groupName: $('#GroupheadlineDialogGroupName').val()
                        },
                        success: function (data) {
                            if(typeof data.status != 'undefined' && data.status == 1) {
                                window.location.href = window.location.href.split('#')[0];
                            }
                        },
                        beforeSend: function () {

                        }
                    });
                }else{
                  alert('Bitte eine Bezeichnung angeben');
                }
            },
            'LÖSCHEN': function() {
                $.ajax({
                    url: 'index.php?module=matrixprodukt&action=artikel&cmd=deletegroup',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        groupId: $('#GroupheadlineDialogGroupId').val()
                    },
                    success: function(data) {
                        if(typeof data.status != 'undefined' && data.status == 1) {
                            if (typeof data.confirm != 'undefined' && data.confirm == 1) {
                                if(confirm('Die Gruppe enthält Option, sollen die Gruppe mit diesen Optionen wirklich gelöscht werden?'))
                                {
                                    $.ajax({
                                        url: 'index.php?module=matrixprodukt&action=artikel&cmd=deletegroup',
                                        type: 'POST',
                                        dataType: 'json',
                                        data: {
                                            force:1,groupId: $('#GroupheadlineDialogGroupId').val()
                                        },success: function(data) {
                                            window.location.href = window.location.href.split('#')[0];
                                        }
                                    });
                                }
                            } else {
                                window.location.href = window.location.href.split('#')[0];
                            }
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