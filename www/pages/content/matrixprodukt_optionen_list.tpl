<!-- gehort zu tabview -->

<div id="tabs">
  <ul>
    <li><a href="#tabs-1">[TABTEXT]</a></li>
  </ul>
<!-- erstes tab -->
  <div id="tabs-1">

    <div class="row">
    <div class="row-height">
    <div class="col-xs-12 col-md-10 col-md-height">
    <div class="inside-white inside-full-height">
      [MESSAGE]
      [TAB1]
    </div>
    </div>
    <div class="col-xs-12 col-md-2 col-md-height">
    <div class="inside inside-full-height">
      <fieldset>
        <legend>{|Aktionen|}</legend>
        <input type="button" class="btnGreenNew" name="neueoption" value="&#10010; Neuer Eintrag" onclick="MatrixproduktOptionenEdit(0);">
        <input type="button" class="btnGreenNew" name="neueuebersetzung" value="&#10010; Neue &Uuml;bersetzung" onclick="MatrixproduktOptionenUebersetzungEdit(0);">
      </fieldset>
    </div>
    </div>
    </div>
    </div>
  
  </div>
<!-- tab view schließen -->
</div>
<!-- ende tab view schließen -->

<div id="editMatrixproduktOptionen" style="display:none;" title="Bearbeiten"> 
  <form action="" method="post" name="eprooform" >
    <input type="hidden" id="matrixprodukt_optionen_id">
    <input type="hidden" name = "matrixprodukt_eintragid" id="matrixprodukt_eintragid" value="[ID]">
    <fieldset>
      <legend>{|Einstellungen|}</legend>
      <table>
        <tr>
          <td width="100">{|Name|}:</td>
          <td><input type="text" size="40" name="matrixprodukt_optionen_name" id="matrixprodukt_optionen_name"></td>
        </tr>
        <tr>
          <td width="100">{|Anhang an Artikelnummer|}:</td>
          <td><input type="text" size="40" name="matrixprodukt_optionen_articlenumber_suffix" id="matrixprodukt_optionen_articlenumber_suffix"></td>
        </tr>
        <tr[STYLEEXT]>
          <td>{|Name Extern|}:</td>
          <td><input type="text" size="40" name="matrixprodukt_optionen_name_ext" id="matrixprodukt_optionen_name_ext"></td>
        </tr>
        <tr>
          <td>{|Sortierung|}:</td>
          <td><input type="text" size="8" name="matrixprodukt_optionen_sortierung" id="matrixprodukt_optionen_sortierung"></td>
        </tr>
        <tr>
          <td>{|Aktiv|}:</td>
          <td><input type="checkbox" name="matrixprodukt_optionen_aktiv" id="matrixprodukt_optionen_aktiv" value="1"></td>
        </tr>
      </table>
    </fieldset>
  </form>
</div>

<div id="editMatrixproduktOptionenUebersetzung" style="display:none;" title="Bearbeiten">
  <form action="" method="post" name="eprooform">
    <input type="hidden" id="matrixprodukt_optionen_uebersetzung_id">
    <input type="hidden" name="matrixprodukt_eintrag_uebersetzung_id" id="matrixprodukt_eintrag_uebersetzung_id" value="[ID]">
    <fieldset>
      <legend>{|Von|}</legend>
      <table>
        <tr>
          <td width="100">{|Sprache|}:</td>
          <td><select name="matrixprodukt_optionen_sprache_von" id="matrixprodukt_optionen_sprache_von">
              [SPRACHEN]
          </select></td>
        </tr>
        <tr>
          <td>{|Name|}:</td>
          <td><input type="text" size="40" name="matrixprodukt_optionen_name_von" id="matrixprodukt_optionen_name_von"></td>
        </tr>
        <tr>
          <td>{|Name Extern|}:</td>
          <td><input type="text" size="40" name="matrixprodukt_optionen_name_ext_von" id="matrixprodukt_optionen_name_ext_von"></td>
        </tr>
      </table>
    </fieldset>
    <fieldset>
      <legend>{|Nach|}</legend>
      <table>
        <tr>
          <td width="100">{|Sprache|}:</td>
          <td><select name="matrixprodukt_optionen_sprache_nach" id="matrixprodukt_optionen_sprache_nach">
                [SPRACHEN]
              </select></td>
        </tr>
        <tr>
          <td>{|Name|}:</td>
          <td><input type="text" size="40" name="matrixprodukt_optionen_name_nach" id="matrixprodukt_optionen_name_nach">
        </tr>
        <tr>
          <td>{|Name Extern|}:</td>
          <td><input type="text" size="40" name="matrixprodukt_optionen_name_ext_nach" id="matrixprodukt_optionen_name_ext_nach"></td>
        </tr>
      </table>
    </fieldset>
    <fieldset>
      <legend>{|Einstellungen|}</legend>
      <table>
        <tr>
          <td width="100">{|Aktiv|}:</td>
          <td><input type="checkbox" name="matrixprodukt_optionen_uebersetzung_aktiv" id="matrixprodukt_optionen_uebersetzung_aktiv">
        </tr>
      </table>
    </fieldset>
  </form>
</div>

<script type="text/javascript">

$(document).ready(function() {
  $('#matrixprodukt_optionen_name').focus();
 
  $("#editMatrixproduktOptionen").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    minWidth:500,
    maxHeight:800,
    autoOpen: false,
    buttons: {
      ABBRECHEN: function() {
        MatrixproduktOptionenReset();
        $(this).dialog('close');
      },
      SPEICHERN: function() {
        MatrixproduktOptionenEditSave();
      }
    }
  });

  $("#editMatrixproduktOptionen").dialog({
    close: function( event, ui ) {MatrixproduktOptionenReset();}
  });


  $("#editMatrixproduktOptionenUebersetzung").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    minWidth:500,
    maxHeight:800,
    autoOpen: false,
    buttons: {
      ABBRECHEN: function() {
        MatrixproduktOptionenUebersetzungReset();
        $(this).dialog('close');
      },
      SPEICHERN: function() {
        MatrixproduktOptionenUebersetzungEditSave();
      }
    }
  });

  $("#editMatrixproduktOptionenUebersetzung").dialog({
    close: function( event, ui ) {MatrixproduktOptionenUebersetzungReset();}
  });

});

function MatrixproduktOptionenUebersetzungReset(){
  $('#editMatrixproduktOptionenUebersetzung').find('#matrixprodukt_optionen_uebersetzung_id').val('');
  $('#editMatrixproduktOptionenUebersetzung').find('#matrixprodukt_optionen_sprache_von').val('');
  $('#editMatrixproduktOptionenUebersetzung').find('#matrixprodukt_optionen_name_von').val('');
  $('#editMatrixproduktOptionenUebersetzung').find('#matrixprodukt_optionen_name_ext_von').val('');
  $('#editMatrixproduktOptionenUebersetzung').find('#matrixprodukt_optionen_sprache_nach').val('');
  $('#editMatrixproduktOptionenUebersetzung').find('#matrixprodukt_optionen_name_nach').val('');
  $('#editMatrixproduktOptionenUebersetzung').find('#matrixprodukt_optionen_name_ext_nach').val('');
  $('#editMatrixproduktOptionenUebersetzung').find('#matrixprodukt_optionen_uebersetzung_aktiv').prop("checked", true);

  var languageFrom = document.getElementById('matrixprodukt_optionen_sprache_von');
  languageFrom.selectedIndex = 0;

  var languageTo = document.getElementById('matrixprodukt_optionen_sprache_nach');
  languageTo.selectedIndex = 0;
}

function MatrixproduktOptionenUebersetzungEditSave() {

  $.ajax({
    url: 'index.php?module=matrixprodukt&action=optionenlist&cmd=optionenuebersetzungsave',
    data: {
      //Alle Felder die fürs editieren vorhanden sind
      optionenid: $('#matrixprodukt_optionen_uebersetzung_id').val(),
      matrixproduktid: $('#matrixprodukt_eintrag_uebersetzung_id').val(),
      sprachevon: $('#matrixprodukt_optionen_sprache_von').val(),
      optionennamevon: $('#matrixprodukt_optionen_name_von').val(),
      optionenname_extvon: $('#matrixprodukt_optionen_name_ext_von').val(),
      sprachenach: $('#matrixprodukt_optionen_sprache_nach').val(),
      optionennamenach: $('#matrixprodukt_optionen_name_nach').val(),
      optionenname_extnach: $('#matrixprodukt_optionen_name_ext_nach').val(),
      optionenaktiv: $('#matrixprodukt_optionen_uebersetzung_aktiv').prop("checked")?1:0
            
    },
    method: 'post',
    dataType: 'json',
    beforeSend: function() {
      App.loading.open();
    },
    success: function(data) {
      App.loading.close();
      if (data.status == 1) {
        MatrixproduktOptionenUebersetzungReset();
        updateLiveTableOptionen();
        $("#editMatrixproduktOptionenUebersetzung").dialog('close');
      } else {
        alert(data.statusText);
      }
    }
  });
}

function MatrixproduktOptionenUebersetzungEdit(id) {

  if(id > 0)
  { 
    $.ajax({
      url: 'index.php?module=matrixprodukt&action=optionenlist&cmd=optionenuebersetzungedit',
      data: {
        id: id
      },
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
        App.loading.open();
      },
      success: function(data) {
        $('#editMatrixproduktOptionenUebersetzung').find('#matrixprodukt_optionen_uebersetzung_id').val(data.id);
        $('#editMatrixproduktOptionenUebersetzung').find('#matrixprodukt_optionen_sprache_von').val(data.language_from);
        $('#editMatrixproduktOptionenUebersetzung').find('#matrixprodukt_optionen_name_von').val(data.name_from);
        $('#editMatrixproduktOptionenUebersetzung').find('#matrixprodukt_optionen_name_ext_von').val(data.name_external_from);
        $('#editMatrixproduktOptionenUebersetzung').find('#matrixprodukt_optionen_sprache_nach').val(data.language_to);
        $('#editMatrixproduktOptionenUebersetzung').find('#matrixprodukt_optionen_name_nach').val(data.name_to);
        $('#editMatrixproduktOptionenUebersetzung').find('#matrixprodukt_optionen_name_ext_nach').val(data.name_external_to);
        $('#editMatrixproduktOptionenUebersetzung').find('#matrixprodukt_optionen_uebersetzung_aktiv').prop("checked",data.active==1?true:false);                  
        
        App.loading.close();
        $("#editMatrixproduktOptionenUebersetzung").dialog('open');
      }
    });
  } else {
    MatrixproduktOptionenUebersetzungReset(); 
    $("#editMatrixproduktOptionenUebersetzung").dialog('open');
  }
}

function MatrixproduktOptionenUebersetzungDelete(id) {
  var conf = confirm('Wirklich löschen?');
  if (conf) {
    $.ajax({
      url: 'index.php?module=matrixprodukt&action=optionenlist&cmd=optionenuebersetzungdelete',
      data: {
        id: id
      },
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
        App.loading.open();
      },
      success: function(data) {
        if(data.status == 1){
          updateLiveTableOptionen();
        }else{
          alert(data.statusText);
        }
        App.loading.close();
      }
    });
  }

  return false;
}


function MatrixproduktOptionenReset(){
  $('#editMatrixproduktOptionen').find('#matrixprodukt_optionen_id').val('');
  $('#editMatrixproduktOptionen').find('#matrixprodukt_optionen_name').val('');
  $('#editMatrixproduktOptionen').find('#matrixprodukt_optionen_name_ext').val('');
  $('#editMatrixproduktOptionen').find('#matrixprodukt_optionen_articlenumber_suffix').val('');
  $('#editMatrixproduktOptionen').find('#matrixprodukt_optionen_sortierung').val('');
  $('#editMatrixproduktOptionen').find('#matrixprodukt_optionen_aktiv').prop("checked", true);
}

function MatrixproduktOptionenEditSave() {

  $.ajax({
    url: 'index.php?module=matrixprodukt&action=optionenlist&cmd=optionensave',
    data: {
      //Alle Felder die fürs editieren vorhanden sind
      optionenid: $('#matrixprodukt_optionen_id').val(),
      matrixproduktid: $('#matrixprodukt_eintragid').val(),
      optionenname: $('#matrixprodukt_optionen_name').val(),
      optionenarticlenumber_suffix: $('#matrixprodukt_optionen_articlenumber_suffix').val(),
      optionenname_ext: $('#matrixprodukt_optionen_name_ext').val(),
      optionensortierung: $('#matrixprodukt_optionen_sortierung').val(),
      optionenaktiv: $('#matrixprodukt_optionen_aktiv').prop("checked")?1:0
            
    },
    method: 'post',
    dataType: 'json',
    beforeSend: function() {
      App.loading.open();
    },
    success: function(data) {
      App.loading.close();
      if (data.status == 1) {
        MatrixproduktOptionenReset();
        updateLiveTableOptionen();
        $("#editMatrixproduktOptionen").dialog('close');
      } else {
        alert(data.statusText);
      }
    }
  });
}

function MatrixproduktOptionenEdit(id) {

  if(id > 0)
  { 
    $.ajax({
      url: 'index.php?module=matrixprodukt&action=optionenlist&cmd=optionenedit',
      data: {
        id: id
      },
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
        App.loading.open();
      },
      success: function(data) {
        $('#editMatrixproduktOptionen').find('#matrixprodukt_optionen_id').val(data.opt_id);
        $('#editMatrixproduktOptionen').find('#matrixprodukt_optionen_name').val(data.opt_name);
        $('#editMatrixproduktOptionen').find('#matrixprodukt_optionen_articlenumber_suffix').val(data.opt_articlenumber_suffix);
        $('#editMatrixproduktOptionen').find('#matrixprodukt_optionen_name_ext').val(data.opt_name_ext);
        $('#editMatrixproduktOptionen').find('#matrixprodukt_optionen_sortierung').val(data.opt_sortierung);
        $('#editMatrixproduktOptionen').find('#matrixprodukt_optionen_aktiv').prop("checked",data.opt_aktiv==1?true:false);                  
        
        App.loading.close();
        $("#editMatrixproduktOptionen").dialog('open');
      }
    });
  } else {
    MatrixproduktOptionenReset(); 
    $("#editMatrixproduktOptionen").dialog('open');
  }
}

function updateLiveTableOptionen(i) {
  var oTableL = $('#matrixprodukt_eigenschaftenoptionen').dataTable();
  var tmp = $('.dataTables_filter input[type=search]').val();
  oTableL.fnFilter('%');
  //oTableL.fnFilter('');
  oTableL.fnFilter(tmp);  
}

function MatrixproduktOptionenDelete(id) {
  var conf = confirm('Wirklich löschen?');
  if (conf) {
    $.ajax({
      url: 'index.php?module=matrixprodukt&action=optionenlist&cmd=optionendelete',
      data: {
        id: id
      },
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
        App.loading.open();
      },
      success: function(data) {
        if(data.status == 1){
          updateLiveTableOptionen();
        }else{
          alert(data.statusText);
        }
        App.loading.close();
      }
    });
  }

  return false;
}


</script>

