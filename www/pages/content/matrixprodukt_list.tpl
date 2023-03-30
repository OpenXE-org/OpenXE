<!--
SPDX-FileCopyrightText: 2023 Andreas Palm
SPDX-FileCopyrightText: 2019 Xentral (c) Xentral ERP Software GmbH, Fuggerstrasse 11, D-86150 Augsburg, Germany
SPDX-License-Identifier: LicenseRef-EGPL-3.1
-->
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
        <input type="button" class="btnGreenNew" name="neueuebersetzung" value="&#10010; Neue &Uuml;bersetzung" onclick="MatrixproduktUebersetzungEdit(0);">
      </fieldset>
    </div>
    </div>
    </div>
    </div>
  </div>
<!-- tab view schließen -->
</div>
<!-- ende tab view schließen -->

<div id="vueapp"></div>

<div id="editMatrixproduktUebersetzung" style="display:none;" title="Bearbeiten">
  <form action="" method="post" name="eprooform">
    <input type="hidden" id="matrixprodukt_uebersetzung_id">
    <fieldset>
      <legend>{|Von|}</legend>
      <table>
        <tr>
          <td width="100">{|Sprache|}:</td>
          <td><select name="matrixprodukt_sprache_von" id="matrixprodukt_sprache_von">
                [SPRACHEN]
              </select></td>
        </tr>
        <tr>
          <td>{|Name|}:</td>
          <td><input type="text" size="40" name="matrixprodukt_name_von" id="matrixprodukt_name_von"></td>
        </tr>
        <tr>
          <td>{|Name Extern|}:</td>
          <td><input type="text" size="40" name="matrixprodukt_name_ext_von" id="matrixprodukt_name_ext_von"></td>
        </tr>
      </table>
    </fieldset>
    <fieldset>
      <legend>{|Nach|}</legend>
      <table>
        <tr>
          <td width="100">{|Sprache|}:</td>
          <td><select name="matrixprodukt_sprache_nach" id="matrixprodukt_sprache_nach">
                [SPRACHEN]
              </select></td>
        </tr>
        <tr>
          <td>{|Name|}:</td>
          <td><input type="text" size="40" name="matrixprodukt_name_nach" id="matrixprodukt_name_nach"></td>
        </tr>
        <tr>
          <td>{|Name Extern|}:</td>
          <td><input type="text" size="40" name="matrixprodukt_name_ext_nach" id="matrixprodukt_name_ext_nach"></td>
        </tr>
      </table>
    </fieldset>
    <fieldset>
      <legend>{|Einstellungen|}</legend>
      <table>
        <tr>
          <td width="100">{|Projekt|}:</td>
          <td><input type="text" size="40" name="matrixprodukt_uebersetzung_projekt" id="matrixprodukt_uebersetzung_projekt"></td>
        </tr>
        <tr>
          <td>{|Aktiv|}:</td>
          <td><input type="checkbox" name="matrixprodukt_uebersetzung_aktiv" id="matrixprodukt_uebersetzung_aktiv" value="1"></td>
        </tr>
      </table>
    </fieldset>
  </form>
</div>

<script type="text/javascript">
function MatrixproduktUebersetzungReset(){
  $('#editMatrixproduktUebersetzung').find('#matrixprodukt_uebersetzung_id').val('');
  $('#editMatrixproduktUebersetzung').find('#matrixprodukt_sprache_von').val('');
  $('#editMatrixproduktUebersetzung').find('#matrixprodukt_name_von').val('');
  $('#editMatrixproduktUebersetzung').find('#matrixprodukt_name_ext_von').val('');
  $('#editMatrixproduktUebersetzung').find('#matrixprodukt_sprache_nach').val('');
  $('#editMatrixproduktUebersetzung').find('#matrixprodukt_name_nach').val('');
  $('#editMatrixproduktUebersetzung').find('#matrixprodukt_name_ext_nach').val('');
  $('#editMatrixproduktUebersetzung').find('#matrixprodukt_uebersetzung_projekt').val('');
  $('#editMatrixproduktUebersetzung').find('#matrixprodukt_uebersetzung_aktiv').prop("checked", true);

  var languageFrom = document.getElementById('matrixprodukt_sprache_von');
  languageFrom.selectedIndex = 0;

  var languageTo = document.getElementById('matrixprodukt_sprache_nach');
  languageTo.selectedIndex = 0;
}

function MatrixproduktUebersetzungEditSave(){
  $.ajax({
    url: 'index.php?module=matrixprodukt&action=list&cmd=uebersetzungsave',
    data: {
      //Alle Felder die fürs editieren vorhanden sind
      id: $('#matrixprodukt_uebersetzung_id').val(),
      sprache_von: $('#matrixprodukt_sprache_von').val(),
      name_von: $('#matrixprodukt_name_von').val(),
      name_ext_von: $('#matrixprodukt_name_ext_von').val(),
      sprache_nach: $('#matrixprodukt_sprache_nach').val(),
      name_nach: $('#matrixprodukt_name_nach').val(),
      name_ext_nach: $('#matrixprodukt_name_ext_nach').val(),
      uebersetzung_projekt: $('#matrixprodukt_uebersetzung_projekt').val(),
      aktiv: $('#matrixprodukt_uebersetzung_aktiv').prop("checked")?1:0
            
    },
    method: 'post',
    dataType: 'json',
    beforeSend: function() {
      App.loading.open();
    },
    success: function(data) {
      App.loading.close();
      if (data.status == 1) {
        MatrixproduktUebersetzungReset();
        updateLiveTable();
        $("#editMatrixproduktUebersetzung").dialog('close');
      } else {
        alert(data.statusText);
      }
    }
  });
}

function MatrixproduktUebersetzungEdit(id) {
  if(id > 0)
  { 
    $.ajax({
      url: 'index.php?module=matrixprodukt&action=list&cmd=uebersetzungedit',
      data: {
        id: id
      },
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
        App.loading.open();
      },
      success: function(data) {
        $('#editMatrixproduktUebersetzung').find('#matrixprodukt_uebersetzung_id').val(data.id);
        $('#editMatrixproduktUebersetzung').find('#matrixprodukt_sprache_von').val(data.language_from);
        $('#editMatrixproduktUebersetzung').find('#matrixprodukt_name_von').val(data.name_from);
        $('#editMatrixproduktUebersetzung').find('#matrixprodukt_name_ext_von').val(data.name_external_from);
        $('#editMatrixproduktUebersetzung').find('#matrixprodukt_sprache_nach').val(data.language_to);
        $('#editMatrixproduktUebersetzung').find('#matrixprodukt_name_nach').val(data.name_to);
        $('#editMatrixproduktUebersetzung').find('#matrixprodukt_name_ext_nach').val(data.name_external_to);
        $('#editMatrixproduktUebersetzung').find('#matrixprodukt_uebersetzung_projekt').val(data.project);
        $('#editMatrixproduktUebersetzung').find('#matrixprodukt_uebersetzung_aktiv').prop("checked",data.active==1?true:false);                  
        
        App.loading.close();
        $("#editMatrixproduktUebersetzung").dialog('open');
      }
    });
  } else {
    MatrixproduktUebersetzungReset(); 
    $("#editMatrixproduktUebersetzung").dialog('open');
  }
}

function MatrixproduktUebersetzungDelete(id) {
  var conf = confirm('Wirklich löschen?');
  if (conf) {
    $.ajax({
      url: 'index.php?module=matrixprodukt&action=list&cmd=uebersetzungdelete',
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
          updateLiveTable();
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

