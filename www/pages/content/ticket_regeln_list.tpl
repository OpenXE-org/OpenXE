<!-- gehort zu tabview -->
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
    <div class="inside-white inside-full-height">

      <div class="filter-box filter-usersave">
        <div class="filter-block filter-inline">
          <div class="filter-title">{|Filter|}</div>
          <ul class="filter-list">
            <li class="filter-item">
              <label for="nuraktive" class="switch">
                <input type="checkbox" id="nuraktive">
                <span class="slider round"></span>
              </label>
              <label for="nuraktive">&nbsp;{|nur aktive Regeln|}</label>
            </li>
          </ul>
        </div>
      </div>
      
      [TAB1]
    </div>
    </div>
    <div class="col-xs-12 col-md-2 col-md-height">
    <div class="inside inside-full-height">
        <fieldset>
          <legend>{|Aktionen|}</legend>
          <input type="button" class="btnGreenNew" name="neuereintrag" value="&#10010; Neuer Eintrag" onclick="RegelnEdit(0);">
        </fieldset>
      </div>
      </div>
      </div>
      </div>
  
    [TAB1NEXT]
  </div>

<!-- tab view schließen -->
</div>

<div id="editRegeln" style="display:none;" title="Bearbeiten">
<form method="post">
  <input type="hidden" id="e_id">
  <fieldset>
  	<legend>{|Regeln Eintrag|}</legend>
  	<table>
      <tr>
        <td width="180">{|Empf&auml;nger E-Mail Adresse|}:</td>
        <td><input type="text" name="e_empf_email_adresse" id="e_empf_email_adresse" size="40"></td>
      </tr>
      <tr>
        <td>{|Sender E-Mail Adresse|}:</td>
        <td><input type="text" name="e_email_adresse" id="e_email_adresse" size="40"></td>
      </tr>
      <tr>
        <td>{|Name|}:</td>
        <td><input type="text" name="e_name" id="e_name" size="40"></td>
      </tr>
      <tr>
        <td>{|Betreff|}:</td>
        <td><input type="text" name="e_betreff" id="e_betreff" size="40"></td>
      </tr>
      <tr>
        <td>{|Aktiv|}:</td>
        <td><input type="checkbox" name="e_aktiv" id="e_aktiv" value="1"></td>
      </tr>
    </table>
  </fieldset>
  <fieldset>
    <legend>{|Aktion|}</legend>
    <table>
      <tr>
        <td width="180">{|als SPAM markieren|}:</td>
        <td><input type="checkbox" name="e_spam" id="e_spam" value="1"></td>
      </tr>
      <tr>
        <td>{|als persönlich markieren|}:</td>
        <td><input type="checkbox" name="e_persoenlich" id="e_persoenlich" value="1"></td>
      </tr>
      <tr>
        <td>{|PRIO Markierung|}:</td>
        <td><input type="checkbox" name="e_prio" id="e_prio" value="1"></td>
      </tr>
      <tr>
        <td>{|DSGVO Markierung|}:</td>
        <td><input type="checkbox" name="e_dsgvo" id="e_dsgvo" value="1"></td>
      </tr>
      <tr>
        <td>{|Warteschlange zuordnen|}:</td>
        <td>
          <input type="text" id="ticketqueue" class="" tabindex="" name="ticketqueue" value="[WARTESCHLANGE]" size="50" placeholder="" maxlength="">
        </td>
      </tr>

    </table>
  </fieldset>    
</div>
</form>


<script type="text/javascript">

$(document).ready(function() {
  $('#e_email_adresse').focus();

  $("#editRegeln").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    minWidth:650,
    maxHeight:700,
    autoOpen: false,
    buttons: {
      ABBRECHEN: function() {
        RegelnReset();
        $(this).dialog('close');
      },
      SPEICHERN: function() {
        RegelnEditSave();
      }
    }
  });

  $("#editRegeln").dialog({
    close: function( event, ui ) { RegelnReset();}
  });

});


function RegelnReset()
{
  $('#editRegeln').find('#e_id').val('');
  $('#editRegeln').find('#e_empf_email_adresse').val('');
  $('#editRegeln').find('#e_email_adresse').val('');
  $('#editRegeln').find('#e_name').val('');
  $('#editRegeln').find('#e_betreff').val('');
  $('#editRegeln').find('#e_spam').prop("checked", false);
  $('#editRegeln').find('#e_persoenlich').prop("checked", false);
  $('#editRegeln').find('#e_prio').prop("checked", false);
  $('#editRegeln').find('#e_dsgvo').prop("checked", false);
  $('#editRegeln').find('#ticketqueue').val('');
  $('#editRegeln').find('#e_aktiv').prop("checked", true);
}

function RegelnEditSave() {
	$.ajax({
    url: 'index.php?module=ticket&action=regelnsave',
    data: {
      //Alle Felder die fürs editieren vorhanden sind
      id: $('#e_id').val(),
      empfaenger_email: $('#e_empf_email_adresse').val(),
      sender_email: $('#e_email_adresse').val(),
      name: $('#e_name').val(),
      betreff: $('#e_betreff').val(),
      spam: $('#e_spam').prop("checked")?1:0,
      persoenlich: $('#e_persoenlich').prop("checked")?1:0,
      prio: $('#e_prio').prop("checked")?1:0,
      dsgvo: $('#e_dsgvo').prop("checked")?1:0,
      warteschlange: $('#ticketqueue').val(),
      aktiv: $('#e_aktiv').prop("checked")?1:0                   
    },
    method: 'post',
    dataType: 'json',
    beforeSend: function() {
      App.loading.open();
    },
    success: function(data) {
     	App.loading.close();
      if (data.status == 1) {
        RegelnReset();
        updateLiveTable();
        $("#editRegeln").dialog('close');
      } else {
        alert(data.statusText);
      }
    }
  });
}

function RegelnEdit(id) {
  if(id > 0)
  { 
    $.ajax({
      url: 'index.php?module=ticket&action=regelnedit&cmd=get',
      data: {
        id: id
      },
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
      	App.loading.open();
      },
      success: function(data) {
        $('#editRegeln').find('#e_id').val(data.id);
        $('#editRegeln').find('#e_empf_email_adresse').val(data.empfaenger_email)
        $('#editRegeln').find('#e_email_adresse').val(data.sender_email);
        $('#editRegeln').find('#e_name').val(data.name);
        $('#editRegeln').find('#e_betreff').val(data.betreff);
        $('#editRegeln').find('#e_spam').prop("checked",data.spam==1?true:false);
        $('#editRegeln').find('#e_persoenlich').prop("checked",data.persoenlich==1?true:false);
        $('#editRegeln').find('#e_prio').prop("checked",data.prio==1?true:false);
        $('#editRegeln').find('#e_dsgvo').prop("checked",data.dsgvo==1?true:false);
        $('#editRegeln').find('#ticketqueue').prop("value",data.warteschlange);
        $('#editRegeln').find('#e_aktiv').prop("checked",data.aktiv==1?true:false);
                        
        App.loading.close();
        $("#editRegeln").dialog('open');
      }
    });
  } else {
    RegelnReset(); 
    $("#editRegeln").dialog('open');
  }

}

function updateLiveTable(i) {
  var oTableL = $('#ticketregeln').dataTable();
  var tmp = $('.dataTables_filter input[type=search]').val();
  oTableL.fnFilter('%');
  //oTableL.fnFilter('');
  oTableL.fnFilter(tmp);   
}

function RegelnDelete(id) {
  var conf = confirm('Wirklich löschen?');
  if (conf) {
    $.ajax({
      url: 'index.php?module=ticket&action=regelndelete',
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


