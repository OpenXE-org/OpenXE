$(document).ready(function () {
    $('#editbezeichnung').focus();

    $("#editKalendergruppen").dialog({
        modal: true,
        bgiframe: true,
        closeOnEscape: false,
        minWidth: 800,
        autoOpen: false,
        buttons: {
            ABBRECHEN: function () {
                $(this).dialog('close');
            },
            SPEICHERN: function () {
                kalendergruppenEditSave();
            }
        }
    });

    $("#editKalendergruppen").dialog({
        close: function (event, ui) {
            KalenderGruppeReset();
        }
    });

    // Kalendergruppen-Filter einmal auslösen
    $('#filterlid').trigger('change');

    // Kalendergruppe bearbeiten
    $(document).on('click', '.calendar-group-edit', function (e) {
        e.preventDefault();

        var groupID = $(this).data('calendar-group');
        KalenderGruppenEdit(groupID);
    });

    // Kalendergruppe löschen
    $(document).on('click', '.calendar-group-delete', function (e) {
        e.preventDefault();

        var groupID = $(this).data('calendar-group');
        KalenderGruppenDelete(groupID);
    });

    // Kalendergruppe anlgen
    $(document).on('click', '.calendar-group-create', function (e) {
        e.preventDefault();
        KalenderGruppenEdit(0);
    });

    // Kalendergruppen-Mitgliedschaft ändern
    $(document).on('click', '#kalender_gruppen_mitglieder input[type=checkbox]', function () {
        var groupID = $(this).data('calendar-group');
        grchange(groupID);
    });
});

function grchange(lid) {
    $.ajax({
            url: "index.php?module=kalender&action=gruppenzuordnung&cmd=change",
            type: 'POST',
            dataType: 'json',
            data: {
                lid: lid,
                kalendergruppe: $('#editid').val(),
                wert: $('#kg_' + lid).prop('checked') ? 1 : 0
            }
        }
    ).done(function (data) {
        var oTableL = $('#kalender_gruppen_mitglieder').DataTable();
        oTableL.ajax.reload();
    }).fail(function (jqXHR, textStatus) {
    });
}


function KalenderGruppeReset() {
    $('#editKalendergruppen').find('#editid').val('');
    $('#editKalendergruppen').find('#editbezeichnung').val('');
    $('#editKalendergruppen').find('#editfarbe').val('#0b8092');
    $('#editfarbe').trigger('change');
    $('#editKalendergruppen').find('#editausblenden').prop("checked", false);
}


function kalendergruppenEditSave() {

    $.ajax({
        url: 'index.php?module=kalender&action=gruppensave',
        data: {
            //Alle Felder die fürs editieren vorhanden sind
            editid: $('#editid').val(),
            editbezeichnung: $('#editbezeichnung').val(),
            editfarbe: $('#editfarbe').val(),
            editausblenden: $('#editausblenden').prop("checked") ? 1 : 0,
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function () {
            App.loading.open();
        },
        success: function (data) {
            App.loading.close();
            if (data.status == 1) {
                updateLiveTable();
                $("#editKalendergruppen").dialog('close');
                KalenderGruppeReset();
            } else if (data.status == 2) {
                $('#editKalendergruppen').find('#editid').val(data.id);
                oMoreData1kalender_gruppen_mitglieder = data.id;
                document.getElementById("kalender_gruppen_mitglieder_reihe").style.display = "";
                updateLiveTable();
                updateLiveTable('#kalender_gruppen_mitglieder');
            } else {
                alert(data.statusText);
            }
        }
    });

}

function KalenderGruppenEdit(id) {
    if (id > 0) {
        oMoreData1kalender_gruppen_mitglieder = id;
        document.getElementById("kalender_gruppen_mitglieder_reihe").style.display = "";
        updateLiveTable('#kalender_gruppen_mitglieder');
        $.ajax({
            url: 'index.php?module=kalender&action=gruppenedit&cmd=get',
            data: {
                editid: id
            },
            method: 'post',
            dataType: 'json',
            beforeSend: function () {
                App.loading.open();
            },
            success: function (data) {
                $('#editKalendergruppen').find('#editid').val(data.id);
                $('#editKalendergruppen').find('#editbezeichnung').val(data.bezeichnung);
                $('#editKalendergruppen').find('#editfarbe').val(data.farbe);
                $('#editfarbe').trigger('change');
                $('#editKalendergruppen').find('#editausblenden').prop("checked", data.ausblenden == 1 ? true : false);
                App.loading.close();
                $("#editKalendergruppen").dialog('open');
            }
        });
    } else {
        document.getElementById("kalender_gruppen_mitglieder_reihe").style.display = "none";
        KalenderGruppeReset();
        $("#editKalendergruppen").dialog('open');
    }

}

function updateLiveTable(i) {
    if (typeof i == 'undefined' || i == '') {
        var oTableL = $('#kalender_gruppenlist').DataTable();
    } else {
        var oTableL = $(i).DataTable();
    }
    oTableL.ajax.reload();
}

function KalenderGruppenDelete(id) {

    var conf = confirm('Wirklich löschen?');
    if (conf) {
        $.ajax({
            url: 'index.php?module=kalender&action=gruppendelete',
            data: {
                editid: id
            },
            method: 'post',
            dataType: 'json',
            beforeSend: function () {
                App.loading.open();
            },
            success: function (data) {
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
