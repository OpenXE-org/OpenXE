<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
    <!--<fieldset>
        <legend>{|Anlegen|}</legend>
        <form method="post">
            <table width="" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="80">Eigenschaft:&nbsp;</td>
                    <td width="200"><input type="text" id="name" name="name" /></td>
                    <td>&nbsp;</td>
                    <td width="40">Wert:&nbsp;</td>
                    <td width="180"><input type="text" name="wert" id="wert" /></td>
                    <td>&nbsp;</td>
                    <td width="110">Einheit (optional):&nbsp;</td>
                    <td width="200"><input type="text" name="einheit" id="einheit" /></td>
                    <td>&nbsp;</td>
                    <td><input type="submit" name="speichern" value="Speichern" /></td>
                </tr>
            </table>
        </form>
    </fieldset>-->
   
    <center><input type="button" name="anlegen" onclick="ArtikelArbeitsanweisungVorlagenNeuEdit(0);" value="Neuen Schritt hinzuf&uuml;gen"></center>
    
[MESSAGE]
[TAB1]

[TAB1NEXT]
</div>

<!-- tab view schließen -->
</div>

<div id="editArbeitsanweisungen" style="display:none">
<form action="index.php?module=artikelarbeitsanweisung_vorlagen&action=artikelarbeitsanweisungsave&id=[ID]" method="POST" enctype="multipart/form-data" id="artikelarbeitsanweisungvorlagenform">
  <input type="hidden" id="e_id" name="e_id"/>
  <table width="800">
    <tr>
        <td class="formline formline_1" valign="top" width="50%">
            <table width="100%">
            <!--<table width="" cellspacing="0" cellpadding="0">-->
                <tr>
                  <td width="110">{|Name|}:</td>
                  <td><input type="text" id="e_name" name="e_name" size="80" /></td>
                </tr>
                <tr>    
                  <td width="110">{|Beschreibung|}:</td>
                  <td><textarea name="e_beschreibung" id="e_beschreibung" cols="80" rows="10"></textarea></td>
                </tr>
                <tr>                
                  <td width="110">{|Einzelzeit|}:</td>
                  <td><input type="text" id="e_stunden" name="e_stunden" size="3">:<input type="text" id="e_minuten" name="e_minuten" size="3">:<input type="text" id="e_sekunden" name="e_sekunden" size="3"></td>
                </tr>
                <tr>
                  <td>{|Arbeitsplatzgruppe|}:</td>
                  <td><input type="text" name="e_arbeitsplatzgruppe" id="e_arbeitsplatzgruppe" size="80" /></td>
                </tr>
                <tr>
                  <td>{|Bild|}:</td>
                  <td><input type="file" name="bild"></td>
                </tr>
            </table>
        </td>
    </tr>
  </table>
</form>
</div>

<script>
$(document).ready(function() {
    $('#e_name').focus();

    $("#editArbeitsanweisungen").dialog({
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
        if($('#e_name').val() != '' && $('#e_name').val() != undefined) {
          $('#artikelarbeitsanweisungvorlagenform').submit();
        }else{
          alert('Bitte einen Namen eingeben!');
        }
      }
    }
  });

});


function ArtikelArbeitsanweisungVorlagenNeuEditSave() {

    $.ajax({
        url: 'index.php?module=artikelarbeitsanweisung_vorlagen&action=artikelarbeitsanweisungsave&id=[ID]',
        data: {
            //Alle Felder die fürs editieren vorhanden sind
            editid: $('#e_id').val(),
            editname: $('#e_name').val(),
            editbeschreibung: $('#e_beschreibung').val(),
            editarbeitsplatzgruppe: $('#e_arbeitsplatzgruppe').val(),
            editstunden: $('#e_stunden').val(),
            editminuten: $('#e_minuten').val(),
            editsekunden: $('#e_sekunden').val(),
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {
            App.loading.close();
            if (data.status == 1) {
                $('#editArbeitsanweisungen').find('#e_id').val('');
                $('#editArbeitsanweisungen').find('#e_name').val('');
                $('#editArbeitsanweisungen').find('#e_beschreibung').val('');
                $('#editArbeitsanweisungen').find('#e_arbeitsplatzgruppe').val('');
                $('#editArbeitsanweisungen').find('#e_stunden').val('');
                $('#editArbeitsanweisungen').find('#e_minuten').val('');
                $('#editArbeitsanweisungen').find('#e_sekunden').val('');
                updateLiveTable();
                $("#editArbeitsanweisungen").dialog('close');
            } else {
                alert(data.statusText);
            }
        }
    });

}

function ArtikelArbeitsanweisungVorlagenNeuEdit(id) {
    if(id==0){
        $("#e_id").val("");
        $("#e_name").val("");
        $("#e_beschreibung").val("");
        $("#e_arbeitsplatzgruppe").val("");
        $("#e_stunden").val('00');
        $("#e_sekunden").val('00');
        $("#e_minuten").val('00');
        $("#editArbeitsanweisungen").dialog('open');
        //console.log("huhu");
    }else{
        $.ajax({
        url: 'index.php?module=artikelarbeitsanweisung_vorlagen&action=artikelarbeitsanweisungedit&cmd=get',
        data: {
            id: id
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {
            $('#editArbeitsanweisungen').find('#e_id').val(data.id);
            $('#editArbeitsanweisungen').find('#e_name').val(data.name);
            $('#editArbeitsanweisungen').find('#e_beschreibung').val(data.beschreibung);
            $('#editArbeitsanweisungen').find('#e_arbeitsplatzgruppe').val(data.arbeitsplatzgruppe);
            $('#editArbeitsanweisungen').find('#e_stunden').val(data.stunden);
            $('#editArbeitsanweisungen').find('#e_minuten').val(data.minuten);
            $('#editArbeitsanweisungen').find('#e_sekunden').val(data.sekunden);
            //$('#editEigenschaften').find('#e_aktiv').prop("checked",data.aktiv==1?true:false);
            App.loading.close();
            $("#editArbeitsanweisungen").dialog('open');
        }
        });
    }

}

function updateLiveTable(i) {
    var oTableL = $('#artikelarbeitsanweisung_vorlagen_arbeitsanweisungen').dataTable();
    var tmp = $('.dataTables_filter input[type=search]').val();
    oTableL.fnFilter('%');
    //oTableL.fnFilter('');
    oTableL.fnFilter(tmp);   
}


function ArtikelArbeitsanweisungVorlagenNeuDelete(id) {

    var conf = confirm('Wirklich löschen?');
    if (conf) {
        $.ajax({
            url: 'index.php?module=artikelarbeitsanweisung_vorlagen&action=artikelarbeitsanweisungdelete',
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

function ArtikelArbeitsanweisungVorlagenNeuDown(elid){

    $.ajax({
        url: 'index.php?module=artikelarbeitsanweisung_vorlagen&action=artikelarbeitsanweisungdown',
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

function ArtikelArbeitsanweisungVorlagenNeuUp(elid){

    $.ajax({
        url: 'index.php?module=artikelarbeitsanweisung_vorlagen&action=artikelarbeitsanweisungup',
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
