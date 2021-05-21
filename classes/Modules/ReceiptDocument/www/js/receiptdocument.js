var address;
var receiptdocument_id;
var createorderdialog;
var createcreditnotedialog;
$(document).ready(function () {
    parcel_id = $('#paketannahme_id').val();
    receiptdocument_id = $('#receiptdocument_id').val();
    createcreditnotedialog = $('#createcreditnotedialog');
    createorderdialog = $('#createorderdialog');
    if (createorderdialog) {
        $('#createorderdialog').dialog(
            {
                modal: true,
                autoOpen: false,
                minWidth: 940,
                title: '',
                buttons: {
                    'Originalen Artikel übernehmen': function () {
                        var formularDatas = $('#frmcreateorderdialog').serialize();
                        $.ajax({
                            url: 'index.php?module=receiptdocument&action=list&cmd=createorder',
                            type: 'post',
                            dataType: 'json',
                            data: formularDatas,
                            success: function (data) {
                                var oTable = $('#receiptdocument_list').DataTable();
                                oTable.ajax.reload();
                                $('#createorderdialog').dialog('close');
                                if (parseInt(data) > 0) {
                                    window.location = 'index.php?module=auftrag&action=edit&id=' + data;
                                }
                            },
                            beforeSend: function () {

                            }
                        });
                    },
                    'Stücklistenartikel übernehmen': function () {
                        var formularDatas = $($('#frmcreateorderdialog')).serialize();
                        $.ajax({
                            url: 'index.php?module=receiptdocument&action=list&cmd=createorder&partlist=1',
                            type: 'POST',
                            dataType: 'json',
                            data: formularDatas,
                            success: function (data) {
                                var oTable = $('#receiptdocument_list').DataTable();
                                oTable.ajax.reload();
                                $('#createorderdialog').dialog('close');
                            },
                            beforeSend: function () {

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

    if (createcreditnotedialog) {
        $('#createcreditnotedialog').dialog(
            {
                modal: true,
                autoOpen: false,
                minWidth: 940,
                title: '',
                buttons: {
                    'Originalen Artikel übernehmen': function () {
                        var formularDatas = $('#frmcreatecreditnotedialog').serialize();
                        $.ajax({
                            url: 'index.php?module=receiptdocument&action=list&cmd=createcreditnote',
                            type: 'post',
                            dataType: 'json',
                            data: formularDatas,
                            success: function (data) {
                                var oTable = $('#receiptdocument_list').DataTable();
                                oTable.ajax.reload();
                                $('#createcreditnotedialog').dialog('close');
                                if (parseInt(data) > 0) {
                                    window.location = 'index.php?module=gutschrift&action=edit&id=' + data;
                                }
                            },
                            beforeSend: function () {

                            }
                        });
                    },
                    'Stücklistenartikel übernehmen': function () {
                        var formularDatas = $($('#frmcreatecreditnotedialog')).serialize();
                        $.ajax({
                            url: 'index.php?module=receiptdocument&action=list&cmd=createcreditnote&partlist=1',
                            type: 'POST',
                            dataType: 'json',
                            data: formularDatas,
                            success: function (data) {
                                var oTable = $('#receiptdocument_list').DataTable();
                                oTable.ajax.reload();
                                $('#createcreditnotedialog').dialog('close');
                            },
                            beforeSend: function () {

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

    $('.closereceiptdocument').on('click', function () {
        $('#tabs').loadingOverlay('show');
        $.ajax({
            url: 'index.php?module=receiptdocument&action=list&cmd=closereceiptdocument',
            type: 'POST',
            dataType: 'json',
            data: {id: receiptdocument_id},
            success: function (data) {
                $('#tabs').loadingOverlay('remove');
                if (typeof data.url != 'undefined') {
                    window.location = data.url;
                } else {
                    window.location.reload();
                }
            },
            fail: function () {
                $('#tabs').loadingOverlay('remove');
            }
        });
    });

    $('input.newreceiptdocument').on('click', function () {
        $.ajax({
            url: 'index.php?module=receiptdocument&action=list&cmd=addreceiptdocument',
            type: 'POST',
            dataType: 'json',
            data: {id: parcel_id},
            success: function (data) {
                window.location.reload();
            }
        });
    });

    $('#receiptdocument_list, #receiptdocument_listpaket, #receiptdocument_listpaketdistri')
        .on('afteropening', function () {
            $('.menuselect').off('change');
            $('.menuselect').on('change', function () {
                var selectionval = $(this).val() + '';
                if (selectionval === 'createorder') {
                    $('#element').val(this.id);
                    $.ajax({
                        url: 'index.php?module=receiptdocument&action=list&cmd=getcreateorderdata',
                        type: 'POST',
                        dataType: 'json',
                        data: {element: this.id},
                        success: function (data) {
                            $('#createorderdialogcontent').html(data.html);
                            $('#createorderdialog').dialog('open');
                            checkautocomplete();
                            addClicklupe();
                            lupeclickevent();
                            var partlists = $('.inppartlist');
                            var partlistsbutton = $('#createorderdialog')
                                .next()
                                .find('.ui-dialog-buttonset')
                                .find('button')
                                .first()
                                .next();
                            if (partlistsbutton) {
                                if (partlists.length) {
                                    $(partlistsbutton).show();
                                } else {
                                    $(partlistsbutton).hide();
                                }
                            }
                        }
                    });
                    $(this).val('');
                } else if (selectionval === 'createcreditnote') {
                    $('#celement').val(this.id);
                    $.ajax({
                        url: 'index.php?module=receiptdocument&action=list&cmd=getcreateorderdata',
                        type: 'POST',
                        dataType: 'json',
                        data: {element: this.id},
                        success: function (data) {
                            $('#createcreditnotedialogcontent').html(data.html);
                            $('#createcreditnotedialog').dialog('open');
                            checkautocomplete();
                            addClicklupe();
                            lupeclickevent();
                            var partlists = $('.inppartlist');
                            var partlistsbutton = $('#createcreditnotedialog')
                                .next()
                                .find('.ui-dialog-buttonset')
                                .find('button')
                                .first()
                                .next();
                            if (partlistsbutton) {
                                if (partlists.length) {
                                    $(partlistsbutton).show();
                                } else {
                                    $(partlistsbutton).hide();
                                }
                            }
                        }
                    });
                    $(this).val('');
                } else if (selectionval != '') {
                    $.ajax({
                        url: 'index.php?module=receiptdocument&action=list&cmd=' + $(this).val(),
                        type: 'POST',
                        dataType: 'json',
                        data: {element: this.id},
                        success: function (data) {
                            if (typeof data.url != 'undefined') {
                                window.location = data.url;
                            }
                            if (typeof data.error != 'undefined') {
                                alert(data.error);
                            }
                            if (typeof data.refreshtable != 'undefined') {
                                var oTable = $('#receiptdocument_list');
                                if(oTable.length > 0) {
                                    oTable.DataTable().ajax.reload();
                                }
                                oTable = $('#receiptdocument_listpaket');
                                if(oTable.length > 0) {
                                    oTable.DataTable().ajax.reload();
                                }
                                oTable = $('#receiptdocument_listpaketdistri');
                                if(oTable.length > 0) {
                                    oTable.DataTable().ajax.reload();
                                }
                            }

                        }
                    });
                }
            });

            $('input.amount_good').on('change', function () {
                $.ajax({
                    url: 'index.php?module=receiptdocument&action=list&cmd=changegood',
                    type: 'POST',
                    dataType: 'json',
                    data: {id: $(this).data('id'), amount: $(this).val()},
                    success: function (data) {

                    }
                });
            });
            $('input.amount_bad').on('change', function () {
                $.ajax({
                    url: 'index.php?module=receiptdocument&action=list&cmd=changebad',
                    type: 'POST',
                    dataType: 'json',
                    data: {id: $(this).data('id'), amount: $(this).val()},
                    success: function (data) {

                    }
                });
            });
        });

    $('#receiptdocument_list, #receiptdocument_listpaket, #receiptdocument_listpaketdistri')
        .on('afterreload', function () {
            $(this).find('input.check').on('click',function(){
                $(this).parents('tr').first().parents('tr').first().find('img.details').trigger('click');
            });

            $('#receiptdocument_list, #receiptdocument_listpaket, #receiptdocument_listpaketdistri')
                .find('a.close')
                .off('click');
            $('#receiptdocument_list, #receiptdocument_listpaket, #receiptdocument_listpaketdistri')
                .find('a.reopen')
                .off('click');
            $('#receiptdocument_list, #receiptdocument_listpaket, #receiptdocument_listpaketdistri')
                .find('a.close')
                .on('click', function () {

                    $.ajax({
                        url: 'index.php?module=receiptdocument&action=list&cmd=close',
                        type: 'POST',
                        dataType: 'json',
                        data: {id: $(this).data('id')},
                        success: function (data) {
                            if(typeof data.url != 'undefined' && data.url+'' !== '') {
                                window.location = data.url;
                            }
                            var oTable = $('#receiptdocument_list');
                            if(oTable.length > 0) {
                                oTable.DataTable().ajax.reload();
                            }
                            oTable = $('#receiptdocument_listpaket');
                            if(oTable.length > 0) {
                                oTable.DataTable().ajax.reload();
                            }
                            oTable = $('#receiptdocument_listpaketdistri');
                            if(oTable.length > 0) {
                                oTable.DataTable().ajax.reload();
                            }
                        }
                    });

                    $(this).val('');

                });
            $('#receiptdocument_list, #receiptdocument_listpaket, #receiptdocument_listpaketdistri')
                .find('a.reopen')
                .on('click', function () {
                    $.ajax({
                        url: 'index.php?module=receiptdocument&action=list&cmd=reopen',
                        type: 'POST',
                        dataType: 'json',
                        data: {id: $(this).data('id')},
                        success: function (data) {
                            var oTable = $('#receiptdocument_list');
                            if(oTable.length > 0) {
                                oTable.DataTable().ajax.reload();
                            }
                            oTable = $('#receiptdocument_listpaket');
                            if(oTable.length > 0) {
                                oTable.DataTable().ajax.reload();
                            }
                            oTable = $('#receiptdocument_listpaketdistri');
                            if(oTable.length > 0) {
                                oTable.DataTable().ajax.reload();
                            }
                        }
                    });
                    $(this).val('');

                });
        });
    $('#receiptdocument_list, #receiptdocument_listpaket, #receiptdocument_listpaketdistri').trigger('afterreload');
});
