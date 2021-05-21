<!-- gehort zu tabview -->
<form method="post">
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
  [MESSAGE]
  <div class="row">
  <div class="row-height">
  <div class="col-xs-12 col-md-10 col-md-height">
  <div class="inside_white inside-full-height">

    <fieldset class="white">
      <legend>&nbsp;</legend>
        [TAB1]
    </fieldset>

  </div>
  </div>
  <div class="col-xs-12 col-md-2 col-md-height">
  <div class="inside inside-full-height">
    <fieldset>
      <legend>{|Aktionen|}</legend>
      <center><input class="btnGreenNew" type="button" name="anlegen" value="&#10010; Neuen Eintrag anlegen" onclick="Dokumentation_AdresseEdit(0);"></center>
    </fieldset>
  </div>
  </div>
  </div>
  </div>




  [TAB1NEXT]
</div>

<!-- tab view schließen -->
</div>




<div id="editEintrag" title="Bearbeiten">
  <input type="hidden" id="editid">
  <input type="hidden" name = "editadressid" id="editadressid" value="[ID]">
  <fieldset>
    <legend>{|Dokumentation|}</legend>
    <table class="mkTableFormular">
      <tr>
        <td>{|Bezeichnung|}:</td>
        <td><input type="text" id="editbezeichnung" name="editbezeichnung" size="80"></td>
      </tr>
      <tr>
        <td>{|Beschreibung|}:</td>
        <td><textarea id="editbeschreibung" name="editbeschreibung"></textarea></td>
      </tr>
      <tr>
        <td width="100">{|Datum|}:</td>
        <td><input type="text" id="editdatum" name="editdatum" size="20"></td>        
      </tr>   
    </table>
  </fieldset>      
</div>


</form>
<script type="text/javascript">

$(document).ready(function() {
    $('#editbezeichnung').focus();

    $("#editEintrag").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    minWidth:1000,
    autoOpen: false,
    buttons: {
      ABBRECHEN: function() {
        $(this).dialog('close');
      },
      SPEICHERN: function() {
        Dokumentation_AdresseEditSave();
      }
    }
  });

});

function Dokumentation_AdresseEditSave() {

    $.ajax({
        url: 'index.php?module=dokumentation&action=adressesave',
        data: {
            //Alle Felder die fürs editieren vorhanden sind
            editid: $('#editid').val(),
            editbezeichnung: $('#editbezeichnung').val(),
            editbeschreibung: $('#editbeschreibung').val(),
            editdatum: $('#editdatum').val(),
            editadressid: $('#editadressid').val()
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
        },
        success: function(data) {
            if (data.status == 1) {
                $('#editEintrag').find('#editid').val('');
                $('#editEintrag').find('#editbezeichnung').val('');
                $('#editEintrag').find('#editbeschreibung').val('');
                $('#editEintrag').find('#editdatum').val('');

                updateLiveTable();
                $("#editEintrag").dialog('close');
            } else {
                alert(data.statusText);
            }
        }
    });

}

function Dokumentation_AdresseEdit(id) {

    $.ajax({
        url: 'index.php?module=dokumentation&action=adresseedit&cmd=get',
        data: {
            id: id
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
        },
        success: function(data) {
            $('#editEintrag').find('#editid').val(data.id);
            $('#editEintrag').find('#editbezeichnung').val(data.bezeichnung);
            $('#editEintrag').find('#editbeschreibung').val(data.beschreibung);
            $('#editEintrag').find('#editdatum').val(data.datum);
            $('#editEintrag').find('#editadressid').val([ID]);

            $("#editEintrag").dialog('open');
        }
    });

}

function updateLiveTable() {
    var oTableL = $('#dokumentation_adresselist').dataTable();
    var tmp = $('.dataTables_filter input[type=search]').val();
    oTableL.fnFilter('%');
    //oTableL.fnFilter('');
    oTableL.fnFilter(tmp);    
}

function Dokumentation_AdresseDelete(id) {

    var conf = confirm('Wirklich löschen?');
    if (conf) {
        $.ajax({ 
            url: 'index.php?module=dokumentation&action=adressedelete',
            data: {
                id: id
            },
            method: 'post',
            dataType: 'json',
            beforeSend: function() {
            },
            success: function(data) {
                if (data.status == 1) {
                    updateLiveTable();
                } else {
                    alert(data.statusText);
                }
            }
        });
    }

    return false;

}


</script>
