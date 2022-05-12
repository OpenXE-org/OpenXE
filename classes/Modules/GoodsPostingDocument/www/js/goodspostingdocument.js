var Goodspostingdocument = function ($) {
    'use strict';
    var me = {

        init: function () {
            $('select#actionmenu').on('change', function () {
                var auswahl = $(this).val();
                switch (auswahl) {
                    case 'delete':
                        if (confirm('Beleg wirklich Stornieren?')) {
                            $.ajax({
                                url: 'index.php?module=goodspostingdocument&action=edit&cmd=delete&id=' +
                                    $(this).data('id'),
                                type: 'POST',
                                dataType: 'json',
                                data: {id: $(this).data('id')},
                                success: function (data) {
                                    if (typeof data.url != 'undefined') {
                                        window.location.href = data.url;
                                    }
                                },
                                beforeSend: function () {

                                }
                            });
                        } else {
                            $('select#actionmenu').val('');
                        }
                        break;
                    case 'authorise':
                        if (confirm('Beleg freigeben?')) {
                            $.ajax({
                                url: 'index.php?module=goodspostingdocument&action=edit&cmd=authorise&id=' +
                                    $(this).data('id'),
                                type: 'POST',
                                dataType: 'json',
                                data: {id: $(this).data('id')},
                                success: function (data) {
                                    if (typeof data.url != 'undefined') {
                                        window.location.href = data.url;
                                    } else {
                                        window.location.href = window.location.href.split('#')[0];
                                    }
                                },
                                beforeSend: function () {

                                }
                            });
                        } else {
                            $('select#actionmenu').val('');
                        }
                        break;
                    default:
                        if (auswahl != '') {
                            $.ajax({
                                url: 'index.php?module=goodspostingdocument&action=edit&cmd=' + auswahl + '&id=' +
                                    $(this).data('id'),
                                type: 'POST',
                                dataType: 'json',
                                data: {id: $(this).data('id')},
                                success: function (data) {
                                    if (typeof data.url != 'undefined') {
                                        window.location.href = data.url;
                                    }
                                },
                                beforeSend: function () {

                                }
                            });
                        }
                        break;
                }
            });

            var $iframe = $('#framepositionen');
            $($iframe).on('load', function () {
                var $trs = $(this).contents().find('table#tableone tbody tr');
                if ($trs.length > 1) {
                    $($trs).each(function () {
                        if (typeof this.id != 'undefined' && parseInt(this.id) > 0) {
                            var $tds = $(this).children('td');
                            $($tds[5]).toggleClass('storagelocation', true);
                            if ($tds.length > 7) {
                                $($tds[6]).toggleClass('storagelocation', true);
                            }
                        }
                    });
                    $(this).contents().find('table#tableone tbody tr td.storage').on('click', function () {
                        setTimeout(function (el) {
                            $(el).find('input').autocomplete({
                                source: 'index.php?module=ajax&action=filter&filtername=lager'
                            });
                        }, 100, this);
                    });
                    $(this).contents().find('table#tableone tbody tr td.storagelocation').on('click', function () {
                        setTimeout(function (el) {
                            $(el).find('input').autocomplete({
                                source: 'index.php?module=ajax&action=filter&filtername=lagerplatz'
                            });
                        }, 100, this);
                    });
                }
            });


            $('.storagelocation').on('click', function () {
                $('#locationstorage').val($(this).html());
            });

            $('.batch').on('click', function () {
                $('#batch').val($(this).html());
                var batchamount = parseFloat($(this).parents('td').first().next().html());
                var amounthidden = parseFloat($('#amounthidden').val());
                if (!isNaN(batchamount)) {
                    $('#batchamount').val(batchamount);
                    if (batchamount > amounthidden) {
                        $('#batchamount').val(amounthidden);
                    }
                }

            });
            $('.bestbefore').on('click', function () {
                $('#bestbefore').val($(this).data('bestbefore'));
                var bestbeforemount = parseFloat($(this).data('amount'));
                var amounthidden = parseFloat($('#amounthidden').val());
                if (!isNaN(bestbeforemount)) {
                    $('#bestbeforeamount').val(bestbeforemount);
                    if (bestbeforemount > amounthidden) {
                        $('#bestbeforeamount').val(amounthidden);
                    }
                }
            });

            $('span.lagerplatz').on('click', function () {
                $('#locationstorage').val($(this).html());
            });

            if ($('#step').length && $('#step').val() == '1' && $('#frmstore table.mkTable tr').find('td').length) {
                $($('#frmstore table.mkTable tr').find('td:first-child')).on('click', function () {
                    if($(this).next().length === 0) {
                        return;
                    }
                    if (!$(this).parent().hasClass('complete')) {
                        $('#locationstoragefrom2').val(($(this).next().next().next().html()+'').split(' ')[ 0 ]);
                        $('#article').val($(this).html());
                        $('#article').focus();
                        $('#nextstep').trigger('click');
                    }
                });
            }

            if ($('#step').length && $('#step').val() == '2' && $('#frmstore table.mkTable tr').find('td').length) {
                $($('#frmstore table.mkTable tr').find('td:first-child')).on('click', function () {
                    $('#locationstoragefrom').val(($(this).html()+'').split(' ')[ 0 ]);
                    $('#locationstoragefrom').focus();
                    $('#nextstep').trigger('click');
                });
            }

            $('#nextstep').on('click', function () {
                $('#stepto').val('next');
                $('#frmstore').submit();
            });
            $('#stepbefore').on('click', function () {
                $('#stepto').val('before');
                $('#frmstore').submit();
            });

            $('a.positiontab').on('click', function () {
                callCursor();
            });

            $(document).on('click', 'table#goodspostingdocument_list img.delete', function (e) {
                e.stopPropagation();
                if (confirm('Warenbuchungsbeleg wirklich stornieren?')) {
                    var documentId = $(this).data('id');
                    if (typeof documentId === 'undefined') {
                        alert('Warenbuchungsbeleg kann nicht gelöscht werden. ID fehlt.');
                        return;
                    }
                    $.ajax({
                        url: 'index.php?module=goodspostingdocument&action=edit&cmd=delete&id=' + documentId,
                        type: 'POST',
                        dataType: 'json',
                        data: {id: $(this).data('id')},
                        success: function (data) {
                            $('#goodspostingdocument_list').DataTable().ajax.reload();
                        },
                        beforeSend: function () {

                        }
                    });
                }
            });
        }
    };
    return {
        init: me.init
    };

}(jQuery);

$(document).ready(function () {
    Goodspostingdocument.init();
});