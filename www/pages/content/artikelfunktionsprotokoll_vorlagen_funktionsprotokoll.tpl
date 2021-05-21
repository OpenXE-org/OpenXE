<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
    
    <center><input type="button" name="anlegen" onclick="ArtikelFunktionsprotokollVorlagenNeuEdit(0);" value="Neuen Schritt hinzuf&uuml;gen"></center>
    
[MESSAGE]
[TAB1]

[TAB1NEXT]
</div>

<!-- tab view schließen -->
</div>
<form method="post">
<div id="editFunktionsprotokoll" style="display:none">
  <input type="hidden" id="e_id" />
  <table width="800">
    <tr>
        <td class="formline formline_1" valign="top" width="50%">
            <table width="100%">
                <tbody>
            <!--<table width="" cellspacing="0" cellpadding="0">-->
                    <tr>
                        <td>Name:</td>
                        <td><input type="text" id="e_name" name="e_name" size="100" /></td>
                    </tr>
                    <tr>    
                        <td>Beschreibung:</td>
                        <td><textarea name="e_beschreibung" id="e_beschreibung" cols="80" rows="10"></textarea></td>
                    </tr>
                    <tr>
                        <td>Bild:</td>
                        <td><input type="file" name="bild"></td>
                    </tr>
                    <tr>
                        <td>Weiter bei Fehler:</td>
                        <td><input type="checkbox" name="e_weiter_bei_fehler" id="e_weiter_bei_fehler" value="1" /></td>
                    </tr>
                </tbody>
            </table>
        </td>
    </tr>
</table>




</div>
</form>

<script>
$(document).ready(function() {
    $('#e_name').focus();

    $("#editFunktionsprotokoll").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    minWidth:840,
    autoOpen: false,
    buttons: {
      ABBRECHEN: function() {
        $(this).dialog('close');
      },
      SPEICHERN: function() {
        ArtikelFunktionsprotokollVorlagenNeuEditSave();
      }
    }
  });

});


function ArtikelFunktionsprotokollVorlagenNeuEditSave() {

    $.ajax({
        url: 'index.php?module=artikelfunktionsprotokoll_vorlagen&action=artikelfunktionsprotokollsave&id=[ID]',
        data: {
            //Alle Felder die fürs editieren vorhanden sind
            editid: $('#e_id').val(),
            editname: $('#e_name').val(),
            editbeschreibung: $('#e_beschreibung').val(),
            editweiterbeifehler: $('#e_weiter_bei_fehler').prop("checked")?1:0,
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {
            App.loading.close();
            if (data.status == 1) {
                $('#editFunktionsprotokoll').find('#e_id').val('');
                $('#editFunktionsprotokoll').find('#e_name').val('');
                $('#editFunktionsprotokoll').find('#e_beschreibung').val('');
                $('#editFunktionsprotokoll').find('#e_weiter_bei_fehler').val('');
                updateLiveTable();
                $("#editFunktionsprotokoll").dialog('close');
            } else {
                alert(data.statusText);
            }
        }
    });

}

function ArtikelFunktionsprotokollVorlagenNeuEdit(id) {

    if(id==0){
        $("#e_name").val("");
        $("#e_beschreibung").val("");
        $("#e_weiter_bei_fehler").val("");
        $("#editFunktionsprotokoll").dialog('open');
        console.log("huhu");
    }else{
        $.ajax({
        url: 'index.php?module=artikelfunktionsprotokoll_vorlagen&action=artikelfunktionsprotokolledit&cmd=get',
        data: {
            id: id
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {
            $('#editFunktionsprotokoll').find('#e_id').val(data.id);
            $('#editFunktionsprotokoll').find('#e_name').val(data.name);
            $('#editFunktionsprotokoll').find('#e_beschreibung').val(data.beschreibung);
            $('#editFunktionsprotokoll').find('#e_weiter_bei_fehler').prop("checked",data.weiter_bei_fehler==1?true:false);
            App.loading.close();
            $("#editFunktionsprotokoll").dialog('open');
        }
        });
    }

}

function updateLiveTable(i) {
    var oTableL = $('#artikelfunktionsprotokoll_vorlagen_funktionsprotokoll').dataTable();
    oTableL.fnFilter('%');
    oTableL.fnFilter('');   
}


function ArtikelFunktionsprotokollVorlagenNeuDelete(id) {

    var conf = confirm('Wirklich löschen?');
    if (conf) {
        $.ajax({
            url: 'index.php?module=artikelfunktionsprotokoll_vorlagen&action=artikelfunktionsprotokolldelete',
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

function ArtikelFunktionsprotokollVorlagenNeuDown(elid){

    $.ajax({
        url: 'index.php?module=artikelfunktionsprotokoll_vorlagen&action=artikelfunktionsprotokolldown',
        type: 'POST',
        dataType: 'json',
        data: { id: elid },
        success: function(data) {
   
                updateLiveTable();

            App.loading.close();
        },
        beforeSend: function() {
            App.loading.open();
        }
    });

}

function ArtikelFunktionsprotokollVorlagenNeuUp(elid){

    $.ajax({
        url: 'index.php?module=artikelfunktionsprotokoll_vorlagen&action=artikelfunktionsprotokollup',
        type: 'POST',
        dataType: 'json',
        data: { id: elid },
        success: function(data) {
   
                updateLiveTable();

            App.loading.close();
        },
        beforeSend: function() {
            App.loading.open();
        }
    });


}



</script>
