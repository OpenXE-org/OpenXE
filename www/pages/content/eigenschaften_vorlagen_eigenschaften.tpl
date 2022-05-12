<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
    <fieldset>
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
    </fieldset>
[MESSAGE]
[TAB1]

[TAB1NEXT]
</div>

<!-- tab view schließen -->
</div>
<form method="post">
<div id="editEigenschaften" style="display:none">
  <input type="hidden" id="e_id" />
  <table width="" cellspacing="0" cellpadding="0">
      <tr>
          <td width="110">Eigenschaft:</td>
          <td><input type="text" id="e_name" name="e_name" size="40" /></td>
      </tr>
      <tr>    
          <td width="110">Wert:</td>
          <td><input type="text" id="e_wert" name="e_wert" size="40"></td>
      </tr>
      <tr>                
          <td width="110">Einheit (optional):</td>
          <td><input type="text"  id="e_einheit" name="e_einheit" size="40"></td>
      </tr>
  </table>
</div>
</form>
<script>
$(document).ready(function() {
    $('#e_name').focus();

    $("#editEigenschaften").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    minWidth:480,
    autoOpen: false,
    buttons: {
      ABBRECHEN: function() {
        $(this).dialog('close');
      },
      SPEICHERN: function() {
        EigenschaftenVorlagenNeuEditSave();
      }
    }
  });

});


function EigenschaftenVorlagenNeuEditSave() {

    $.ajax({
        url: 'index.php?module=eigenschaften_vorlagen&action=eigenschaftensave',
        data: {
            //Alle Felder die fürs editieren vorhanden sind
            editid: $('#e_id').val(),
            editname: $('#e_name').val(),
            editwert: $('#e_wert').val(),
            editeinheit: $('#e_einheit').val(),
            //editaktiv: $('#editaktiv').prop("checked")?1:0,
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {
            App.loading.close();
            if (data.status == 1) {
                $('#editEigenschaften').find('#e_id').val('');
                $('#editEigenschaften').find('#e_name').val('');
                $('#editEigenschaften').find('#e_wert').val('');
                $('#editEigenschaften').find('#e_einheit').val('');
                updateLiveTable();
                $("#editEigenschaften").dialog('close');
            } else {
                alert(data.statusText);
            }
        }
    });

}

function EigenschaftenVorlagenNeuEdit(id) {

    $.ajax({
        url: 'index.php?module=eigenschaften_vorlagen&action=eigenschaftenedit&cmd=get',
        data: {
            id: id
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {
            $('#editEigenschaften').find('#e_id').val(data.id);
            $('#editEigenschaften').find('#e_name').val(data.name);
            $('#editEigenschaften').find('#e_wert').val(data.wert);
            $('#editEigenschaften').find('#e_einheit').val(data.einheit);
            //$('#editEigenschaften').find('#e_aktiv').prop("checked",data.aktiv==1?true:false);
            App.loading.close();
            $("#editEigenschaften").dialog('open');
        }
    });

}

function updateLiveTable(i) {
    var oTableL = $('#eigenschaften_vorlagen_eigenschaften').dataTable();
    var tmp = $('.dataTables_filter input[type=search]').val();
    oTableL.fnFilter('%');
    //oTableL.fnFilter('');
    oTableL.fnFilter(tmp);     
}


function EigenschaftenVorlagenNeuDelete(id) {

    var conf = confirm('Wirklich löschen?');
    if (conf) {
        $.ajax({
            url: 'index.php?module=eigenschaften_vorlagen&action=eigenschaftendelete',
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



</script>
