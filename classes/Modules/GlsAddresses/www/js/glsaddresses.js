$(document).ready(function() {
    $('#gls_name').focus();


    $(document).on('click', '.gls-addresses-edit', function(e){
        e.preventDefault();

        var labelId = $(this).data('gls-addresses-id');
        GlsEdit(labelId);
    });

    $(document).on('click', '.gls-addresses-delete', function(e){
        e.preventDefault();

        var labelId = $(this).data('gls-addresses-id');
        GlsDelete(labelId);
    });



    $("#editGls").dialog({
        modal: true,
        bgiframe: true,
        closeOnEscape:false,
        minWidth:650,
        maxHeight:700,
        autoOpen: false,
        buttons: {
            ABBRECHEN: function() {
                GlsReset();
                $(this).dialog('close');
            },
            SPEICHERN: function() {
                GlsEditSave();
            }
        }
    });

    $("#editGls").dialog({
        close: function( event, ui ) { GlsReset();}
    });

});


function GlsReset()
{
    $('#editGls').find('#gls_id').val('');
    $('#editGls').find('#gls_vorlage').val('');
    $('#editGls').find('#gls_name').val('');
    $('#editGls').find('#gls_name2').val('');
    $('#editGls').find('#gls_name3').val('');
    $('#editGls').find('#gls_telefon').val('');
    $('#editGls').find('#gls_email').val('');
    $('#editGls').find('#gls_land').val('DE');
    $('#editGls').find('#gls_plz').val('');
    $('#editGls').find('#gls_ort').val('');
    $('#editGls').find('#gls_strasse').val('');
    $('#editGls').find('#gls_hausnr').val('');
    $('#editGls').find('#gls_adresszusatz').val('');
    $('#editGls').find('#gls_notiz').val('');
    $('#editGls').find('#gls_aktiv').prop("checked", true);
}

function GlsEditSave() {
    $.ajax({
        url: 'index.php?module=gls&action=save',
        data: {
            //Alle Felder die fürs editieren vorhanden sind
            id: $('#gls_id').val(),
            vorlage: $('#gls_vorlage').val(),
            name: $('#gls_name').val(),
            name2: $('#gls_name2').val(),
            name3: $('#gls_name3').val(),
            telefon: $('#gls_telefon').val(),
            email: $('#gls_email').val(),
            land: $('#gls_land').val(),
            plz: $('#gls_plz').val(),
            ort: $('#gls_ort').val(),
            strasse: $('#gls_strasse').val(),
            hausnr: $('#gls_hausnr').val(),
            adresszusatz: $('#gls_adresszusatz').val(),
            notiz: $('#gls_notiz').val(),
            aktiv: $('#gls_aktiv').prop("checked")?1:0
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {
            App.loading.close();
            if (data.status == 1) {
                GlsReset();
                updateLiveTable();
                $("#editGls").dialog('close');
            } else {
                alert(data.statusText);
            }
        }
    });
}

function GlsEdit(id) {
    if(id > 0)
    {
        $.ajax({
            url: 'index.php?module=gls&action=edit&cmd=get',
            data: {
                id: id
            },
            method: 'post',
            dataType: 'json',
            beforeSend: function() {
                App.loading.open();
            },
            success: function(data) {
                $('#editGls').find('#gls_id').val(data.id);
                $('#editGls').find('#gls_vorlage').val(data.vorlage);
                $('#editGls').find('#gls_name').val(data.name);
                $('#editGls').find('#gls_name2').val(data.name2);
                $('#editGls').find('#gls_name3').val(data.name3);
                $('#editGls').find('#gls_telefon').val(data.telefon);
                $('#editGls').find('#gls_email').val(data.email);
                $('#editGls').find('#gls_land').val(data.land);
                $('#editGls').find('#gls_plz').val(data.plz);
                $('#editGls').find('#gls_ort').val(data.ort);
                $('#editGls').find('#gls_strasse').val(data.strasse);
                $('#editGls').find('#gls_hausnr').val(data.hausnr);
                $('#editGls').find('#gls_adresszusatz').val(data.adresszusatz);
                $('#editGls').find('#gls_notiz').val(data.notiz);
                $('#editGls').find('#gls_aktiv').prop("checked",data.aktiv==1?true:false);

                App.loading.close();
                $("#editGls").dialog('open');
            }
        });
    } else {
        GlsReset();
        $("#editGls").dialog('open');
    }

}

function updateLiveTable(i) {
    var oTableL = $('#gls_list').dataTable();
    var tmp = $('.dataTables_filter input[type=search]').val();
    oTableL.fnFilter('%');
    //oTableL.fnFilter('');
    oTableL.fnFilter(tmp);
}

function GlsDelete(id) {
    var conf = confirm('Wirklich löschen?');
    if (conf) {
        $.ajax({
            url: 'index.php?module=gls&action=delete',
            data: {
                id: id
            },
            method: 'post',
            dataType: 'json',
            beforeSend: function() {
                App.loading.open();
            },
            success: function(data) {
                if (data.status == 1) {
                    updateLiveTable();
                } else {
                    alert(data.statusText);
                }
                App.loading.close();
            }
        });
    }

    return false;
}