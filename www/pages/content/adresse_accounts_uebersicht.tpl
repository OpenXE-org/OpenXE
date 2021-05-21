<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1"></a></li>
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
    <center><input class="btnGreenNew" type="button" name="anlegen" value="&#10010; Neuen Eintrag anlegen" onclick="AccountsEdit(0);"></center>
  </fieldset>

</div>
</div>
</div>
</div>

[TABNEXT]
</div>

<!-- tab view schließen -->
</div>

<div id="editAccounts" style="display:none;" title="Bearbeiten">
<form method="post">
  <input type="hidden" id="e_id">
  <input type="hidden" name ="e_adressid" id="e_adressid" value="[ID]">
  <fieldset>
  	<legend>{|Accounts|}</legend>
  	<table class="mkTableFormular">
      <tr>
        <td width="100">{|Bezeichnung|}:</td>
        <td><input type="text" name="e_bezeichnung" id="e_bezeichnung" size="40"></td>
      </tr>
      <tr>
        <td>{|Benutzername|}:</td>
        <td><input type="text" name="e_benutzername" id="e_benutzername" size="40"></td>
      </tr>
      <tr>
        <td>{|Passwort|}:</td>
        <td><input type="text" name="e_passwort" id="e_passwort" size="40"></td>
      </tr>
      <tr>
      	<td>{|Art|}:</td>
      	<td><input type="text" name="e_art" id="e_art" size="40"></td>
      </tr>   
      <tr>
        <td>{|URL|}:</td>
        <td><input type="text" name="e_url" id="e_url" size="40"></td>
      </tr>
      <tr>
      	<td>{|E-Mail|}:</td>
      	<td><input type="text" name="e_email" id="e_email" size="40"></td>
      </tr>
      <tr>
      	<td>{|Notizen|}:</td>
      	<td><textarea name="e_notiz" id="e_notiz"></textarea></td>
      </tr>
      <tr>
      	<td>{|G&uuml;ltig|}</td>
      	<td>{|ab|}&nbsp;<input type="text" name="e_gueltigab" id="e_gueltigab" size="10">&nbsp;{|bis|}&nbsp;<input type="text" name="e_gueltigbis" id="e_gueltigbis" size="10"></td>
      </tr>
      <tr>
      	<td>{|Aktiv|}:</td>
      	<td><input type="checkbox" name="e_aktiv" id="e_aktiv"></td>
      </tr>
    </table>
  </fieldset>
  
</div>


</form>
<script type="text/javascript">

$(document).ready(function() {
    $('#e_bezeichnung').focus();

    $("#editAccounts").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    minWidth:600,
    maxHeight:900,
    autoOpen: false,
    buttons: {
      ABBRECHEN: function() {
        AccountsReset();
        $(this).dialog('close');
      },
      SPEICHERN: function() {
        AccountsEditSave();
      }
    }
  });

    $("#editAccounts").dialog({

  close: function( event, ui ) { AccountsReset();}
});

});


function AccountsReset()
{
  $('#editAccounts').find('#e_id').val('');
  $('#editAccounts').find('#e_bezeichnung').val('');
  $('#editAccounts').find('#e_benutzername').val('');
  $('#editAccounts').find('#e_passwort').val('');
  $('#editAccounts').find('#e_art').val('');
  $('#editAccounts').find('#e_url').val('');
  $('#editAccounts').find('#e_email').val('');
  $('#editAccounts').find('#e_notiz').val('');
  $('#editAccounts').find('#e_gueltigab').val('');
  $('#editAccounts').find('#e_gueltigbis').val('');
  $('#editAccounts').find('#e_aktiv').prop('checked', false);
  
}

function AccountsEditSave() {
	$.ajax({
    url: 'index.php?module=adresse&action=accounts&cmd=save',
    data: {
      //Alle Felder die fürs editieren vorhanden sind
      id: $('#e_id').val(),
      bezeichnung: $('#e_bezeichnung').val(),
      benutzername: $('#e_benutzername').val(),
      passwort: $('#e_passwort').val(),
      art: $('#e_art').val(),
      url: $('#e_url').val(),
      email: $('#e_email').val(),
      notiz: $('#e_notiz').val(),
      gueltigab: $('#e_gueltigab').val(),
      gueltigbis: $('#e_gueltigbis').val(),
      aktiv: $('#e_aktiv').prop("checked")?1:0,
      adressid: $('#e_adressid').val()            
    },
    method: 'post',
    dataType: 'json',
    beforeSend: function() {
      App.loading.open();
    },
    success: function(data) {
     	App.loading.close();
      if (data.status == 1) {
        AccountsReset();
        updateLiveTable();
        $("#editAccounts").dialog('close');
      } else {
        alert(data.statusText);
      }
    }
  });


}

function AccountsEdit(id) {
  if(id > 0)
  { 
    $.ajax({
      url: 'index.php?module=adresse&action=accounts&cmd=edit',
      data: {
          id: id
      },
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
      	App.loading.open();
      },
      success: function(data) {
        if(data.id > 0){
          $('#editAccounts').find('#e_id').val(data.id);
          $('#editAccounts').find('#e_bezeichnung').val(data.bezeichnung);
          $('#editAccounts').find('#e_benutzername').val(data.benutzername);
          $('#editAccounts').find('#e_passwort').val(data.passwort);
          $('#editAccounts').find('#e_art').val(data.art);
          $('#editAccounts').find('#e_url').val(data.url);
          $('#editAccounts').find('#e_email').val(data.email);
          $('#editAccounts').find('#e_notiz').val(data.notiz);
          $('#editAccounts').find('#e_gueltigab').val(data.gueltig_ab);
          $('#editAccounts').find('#e_gueltigbis').val(data.gueltig_bis);
          $('#editAccounts').find('#e_aktiv').prop("checked", data.aktiv==1?true:false);
          $('#editAccounts').find('#e_adressid').val([ID]);
                          
        }
        App.loading.close();
        $("#editAccounts").dialog('open');
      }
    });
  } else {
    AccountsReset(); 
    $("#editAccounts").dialog('open');
  }

}

function updateLiveTable(i) {
  var oTableL = $('#adresse_accounts').dataTable();
  var tmp = $('.dataTables_filter input[type=search]').val();
  oTableL.fnFilter('%');
  //oTableL.fnFilter('');
  oTableL.fnFilter(tmp);   
}

function AccountsDelete(id) {

  var conf = confirm('Wirklich löschen?');
  if (conf) {
    $.ajax({
      url: 'index.php?module=adresse&action=accounts&cmd=delete',
      data: {
        eid: id
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
