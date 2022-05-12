
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
      
      [TAB1]
  
    </div>
    </div>
    <div class="col-xs-12 col-md-2 col-md-height">
    <div class="inside inside-full-height">
      <fieldset>
        <legend>{|Aktionen|}</legend>
        <input type="button" class="btnGreenNew" name="neueeigenschaft" value="&#10010; Neue Eigenschaft" onclick="editeigenschaft(0);">
        <input type="button" class="btnGreenNew" name="neueeigenschaftuebersetzung" value="&#10010; Neue &Uuml;bersetzung" onclick="editUebersetzung(0);">
        <br />
        <form method="post">
          <table>
            <tr>
              <td width="55">{|Vorlage|}:</td>
              <td width="200"><input type="text" id="vorlage" name="vorlage" /></td>
              <td><input type="submit" name="laden" value="Laden" /></td>
            </tr>
          </table>
        </form>
      </fieldset>
    </div>
    </div>
    </div>
    </div>

    [TAB1NEXT]
  
  </div>

<!-- tab view schließen -->
</div>
<div id="editEigenschaften" style="display:none">
  <form action="" method="post">
    <input type="hidden" id="e_id">
    <input type="hidden" id="e_werttyp" value="text">
    <input type="hidden" name = "e_artikelid" id="e_artikelid" value="[ID]">
    <fieldset>
      <legend>{|Eigenschaft|}</legend>
      <table width="" cellspacing="0" cellpadding="0">
        <tr>
          <td width="110">{|Eigenschaft|}:</td>
          <td><input type="text" id="e_name" size="40" />&nbsp;<input type="text" id="e_pseudoname" size="40" readonly="" style="display:none"/></td>
        </tr>
        <tr>    
          <td width="110">{|Wert|}:</td>
          <td id="e_selecfeld" style="display: none;"><select id="e_selectwert"></select></td>
          <td id="e_textfeld"><input type="text" id="e_textwert" size="40"></td>
        </tr>
        <tr>                
          <td width="110">{|Einheit (optional)|}:</td>
          <td><input type="text"  id="e_einheit" size="40"></td>
        </tr>
      </table>
    </fieldset>
  </form>
</div>

<div id="editUebersetzung" style="display:none;" title="Bearbeiten">
<form method="post">
  <input type="hidden" id="u_id">
  <fieldset>
    <legend>{|Eigenschaft &Uuml;bersetzung|}</legend>
    <table>
      <tr>
        <td width="90">{|Artikel|}:</td>
        <td><input type="text" id="u_article" name="u_article" size="40" readonly></td>
      </tr>
    </table>
  </fieldset>
  <fieldset>
    <legend>{|Von|}</legend>
    <table>
      <tr>
        <td width="90">{|Sprache|}:</td>
        <td><select name="u_languageFrom" id="u_languageFrom">
              [SPRACHEN]
            </select>
        </td>
      </tr>
      <tr id="propertyde">
        <td>{|Eigenschaft|}:</td>
        <td><input type="text" name="u_propertyFrom" id="u_propertyFrom" size="40"></td>
      </tr>
      <tr id="propertyelse">
        <td>{|Eigenschaft|}:</td>
        <td><input type="text" name="u_propertyFromElse" id="u_propertyFromElse" size="40"></td>
      </tr>
      <tr id="propertyvaluede">
        <td>{|Wert|}:</td>
        <td><input type="text" name="u_propertyValueFrom" id="u_propertyValueFrom" size="40"></td>
      </tr>
      <tr id="propertyvalueelse">
        <td>{|Wert|}:</td>
        <td><input type="text" name="u_propertyValueFromElse" id="u_propertyValueFromElse" size="40"></td>
      </tr>
    </table>
  </fieldset>
  <fieldset>
    <legend>{|Nach|}</legend>
    <table>
      <tr>
        <td width="90">{|Sprache|}:</td>
        <td><select name="u_languageTo" id="u_languageTo">
              [SPRACHEN]
            </select>
        </td>
      </tr>
      <tr>
        <td>{|Eigenschaft|}:</td>
        <td><input type="text" name="u_propertyTo" id="u_propertyTo" size="40"></td>
      </tr>
      <tr>
        <td>{|Wert|}:</td>
        <td><input type="text" name="u_propertyValueTo" id="u_propertyValueTo" size="40"></td>
      </tr>
    </table>
  </fieldset>
  <fieldset>
    <legend>{|Shop|}</legend>
    <table>
      <tr>
        <td width="90">{|Shop|}:</td>
        <td><input type="text" name="u_shop" id="u_shop" size="40"></td>
      </tr>
    </table>
  </fieldset>    
</div>
</form>





<script>
  var copy = false;
$(document).ready(function() {


  $("#editEigenschaften").dialog({
    modal: true,
    minWidth: 640,
    bgiframe: true,
    closeOnEscape:false,
    autoOpen: false,
    buttons: [
    {
      id: "babbrechen",
      text: "ABBRECHEN",
      click: function () {
        $(this).dialog('close');
        ArtikelEigenschaftenReset();
      }
    },
    {
      id: "bspeichern",
      text: "SPEICHERN",
      click: function () {
        if(copy){
          SaveCopyEigenschaft();
        }else{
          SaveEdit();
        }   
      }
    }




    ]
  });

  $("#editEigenschaften").dialog({
    close: function( event, ui ) {ArtikelEigenschaftenReset();}
  });


  $("#editUebersetzung").dialog({
    modal: true,
    minWidth: 640,
    bgiframe: true,
    closeOnEscape:false,
    autoOpen: false,
    buttons: {
      ABBRECHEN: function() {
        $(this).dialog('close');
        resetUebersetzung();
      },
      SPEICHERN: function() {
        editSaveUebersetzung();
      }
    }
  });

  $("#editUebersetzung").dialog({
    close: function( event, ui ) {resetUebersetzung();}
  });

  languageFrom = document.getElementById('u_languageFrom');
  propertyde = document.getElementById('propertyde');
  propertyelse = document.getElementById('propertyelse');
  propertyvaluede = document.getElementById('propertyvaluede');
  propertyvalueelse = document.getElementById('propertyvalueelse');
  if(languageFrom){

    if(typeof languageFrom.options[languageFrom.selectedIndex] != 'undefined' && languageFrom.options[languageFrom.selectedIndex].value =='DE'){
      propertyde.style.display='';
      propertyelse.style.display='none';
      propertyvaluede.style.display='';
      propertyvalueelse.style.display='none';
    }else{
      propertyde.style.display='none';
      propertyelse.style.display='';
      propertyvaluede.style.display='none';
      propertyvalueelse.style.display='';
    }

    languageFrom.onchange=function(){
      if(typeof languageFrom.options[languageFrom.selectedIndex] != 'undefined' && languageFrom.options[languageFrom.selectedIndex].value == 'DE'){             
        propertyde.style.display='';
        propertyelse.style.display='none';
        propertyvaluede.style.display='';
        propertyvalueelse.style.display='none';
      }else{
        propertyde.style.display='none';
        propertyelse.style.display='';
        propertyvaluede.style.display='none';
        propertyvalueelse.style.display='';
      }
    }
  }



});

function ArtikelEigenschaftenReset(){
  document.getElementById("e_name").style.display="";
  document.getElementById("e_pseudoname").style.display="none";
  $('#editEigenschaften').find('#e_werttyp').val('text');
  document.getElementById("e_selecfeld").style.display="none";
  document.getElementById("e_textfeld").style.display="";
  $('#editEigenschaften').find('#e_id').val('');
  $('#editEigenschaften').find('#e_name').val('');
  $('#editEigenschaften').find('#e_textwert').val('');
  $('#editEigenschaften').find('#e_einheit').val(''); 
}

function SaveEdit() {
  var wert = '';
  if($('#e_werttyp').val() === 'select') {
    wert = $('#e_selectwert').val()
  }else{
    wert = $('#e_textwert').val()
  }

  $.ajax({
      url: 'index.php?module=artikel&action=eigenschaften&cmd=update&id=[ID]',
      data: {
        e_name: $('#e_name').val(), 
        e_wert : wert,
        e_einheit: $('#e_einheit').val(),
        eigenschaftid:$('#e_id').val(),
        artikelid:$('#e_artikelid').val()
      },
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
        App.loading.open();
      },
      success: function(data) {
        App.loading.close();
        if (data.status == 1) {
          ArtikelEigenschaftenReset();
          updateLiveTable();
          $("#editEigenschaften").dialog('close');
        } else {
          alert(data.statusText);
        }       
        
      }
  });
  return false;
}


function editeigenschaft(id)
{
  if(id > 0){
    $.ajax({
      url: 'index.php?module=artikel&action=eigenschaften&cmd=get&id=[ID]',
      data: {
          eigenschaftid: id
      },
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
          App.loading.open();
      },
      success: function(data) {
        if(data != null)
        {
          $('#editEigenschaften').find('#e_id').val(data.id);
          $('#editEigenschaften').find('#e_name').val(data.name);
          $('#editEigenschaften').find('#e_pseudoname').val(data.name);
          if(data.typ === 'select'){
            $('#editEigenschaften').find('#e_werttyp').val('select');
            document.getElementById("e_selecfeld").style.display="";
            document.getElementById("e_textfeld").style.display="none";
            document.getElementById("e_name").style.display="none";
            document.getElementById("e_pseudoname").style.display="";
            $("#e_selectwert").empty();
            var selected = 1;
            for (var i = 0; i < data.erlaubtewerte.length; i++) {
              $('#e_selectwert').append('<option value="'+data.erlaubtewerte[i]+'">'+data.erlaubtewerte[i]+'</option>');

              if(data.erlaubtewerte[i] === data.wert){
                selected = data.wert;
              }
            }
            $("#e_selectwert").val(selected);
          }else{
            $('#editEigenschaften').find('#e_werttyp').val('text');
            document.getElementById("e_selecfeld").style.display="none";
            document.getElementById("e_textfeld").style.display="";
            document.getElementById("e_name").style.display="";
            document.getElementById("e_pseudoname").style.display="none";
            $('#editEigenschaften').find('#e_textwert').val(data.wert);
          }
          $('#editEigenschaften').find('#e_einheit').val(data.einheit);
          $('#editEigenschaften').find('#e_artikelid').val([ID]);
          App.loading.close();
          $("#editEigenschaften").dialog('open');
        }
      }
    });
  }else{
    ArtikelEigenschaftenReset();
    $("#editEigenschaften").dialog('open');
  }
}


function updateLiveTable() {
    var oTableL = $('#artikel_eigenschaften_neu').DataTable();
    oTableL.ajax.reload();
}

function SaveCopyEigenschaft(){
  $.ajax({
    url: 'index.php?module=artikel&action=eigenschaften&cmd=copy&id=[ID]',
    data: {
      e_name: $('#e_name').val(), 
      e_wert : $('#e_textwert').val(),
      e_einheit: $('#e_einheit').val(),
      eigenschaftid:0,
      artikelid:$('#e_artikelid').val()
    },
    method: 'post',
    dataType: 'json',
    beforeSend: function() {
      App.loading.open();
    },
    success: function(data) {
      copy = false;
      $('#bspeichern').button('option', 'label', 'SPEICHERN');
      App.loading.close();
      if (data.status == 1) {
        ArtikelEigenschaftenReset();
        updateLiveTable();
        $("#editEigenschaften").dialog('close');
      } else {
        alert(data.statusText);
      }       
        
    }
  });
  return false;
}


function copyeigenschaft(id)
{
  copy = true;
  $('#bspeichern').button('option', 'label', 'KOPIEREN');
  editeigenschaft(id);
}

function deleteeigenschaft(id)
{
  if(confirm('Eigenschaft wirklich löschen?'))
  {
    $.ajax({
        url: 'index.php?module=artikel&action=eigenschaften&cmd=delete&id=[ID]',
        data: {eigenschaftid:id},
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
}


function resetUebersetzung()
{
  $('#editUebersetzung').find('#u_id').val('');
  $('#editUebersetzung').find('#u_article').val('');
  $('#editUebersetzung').find('#u_languageFrom').val('');
  $('#editUebersetzung').find('#u_propertyFrom').val('');
  $('#editUebersetzung').find('#u_propertyValueFrom').val('');
  $('#editUebersetzung').find('#u_languageTo').val('');
  $('#editUebersetzung').find('#u_propertyTo').val('');
  $('#editUebersetzung').find('#u_propertyValueTo').val('');
  $('#editUebersetzung').find('#u_shop').val('');
  $('#editUebersetzung').find('#u_propertyFromElse');
  $('#editUebersetzung').find('#u_propertyValueFromElse');

  var languageFrom = document.getElementById('u_languageFrom');
  languageFrom.selectedIndex = 0;

  var languageTo = document.getElementById('u_languageTo');
  languageTo.selectedIndex = 0;

  languageFrom = document.getElementById('u_languageFrom');
  propertyde = document.getElementById('propertyde');
  propertyelse = document.getElementById('propertyelse');
  propertyvaluede = document.getElementById('propertyvaluede');
  propertyvalueelse = document.getElementById('propertyvalueelse');
  if(languageFrom){
    // Hide the target field if priority isn't critical
    if(languageFrom.options[languageFrom.selectedIndex].value =='DE'){
      propertyde.style.display='';
      propertyelse.style.display='none';
      propertyvaluede.style.display='';
      propertyvalueelse.style.display='none';
    }else{
      propertyde.style.display='none';
      propertyelse.style.display='';
      propertyvaluede.style.display='none';
      propertyvalueelse.style.display='';
    }

  }
}

function editSaveUebersetzung() {
  var propertyfromfield = '';
  var propertyvaluefromfield = '';
  if($('#u_languageFrom').val() == 'DE'){
    propertyfromfield = $('#u_propertyFrom').val();
    propertyvaluefromfield = $('#u_propertyValueFrom').val();
  }else{
    propertyfromfield = $('#u_propertyFromElse').val();
    propertyvaluefromfield = $('#u_propertyValueFromElse').val();
  }

  $.ajax({
    url: 'index.php?module=artikel&action=eigenschaften&cmd=saveuebersetzung&id=[ID]',
    data: {
      //Alle Felder die fürs editieren vorhanden sind
      id: $('#u_id').val(),
      languageFrom: $('#u_languageFrom').val(),
      propertyFrom: propertyfromfield,
      propertyValueFrom: propertyvaluefromfield,
      languageTo: $('#u_languageTo').val(),
      propertyTo: $('#u_propertyTo').val(),
      propertyValueTo: $('#u_propertyValueTo').val(),
      shop: $('#u_shop').val()                      
    },
    method: 'post',
    dataType: 'json',
    beforeSend: function() {
      App.loading.open();
    },
    success: function(data) {
      App.loading.close();
      if (data.status == 1) {
        resetUebersetzung();
        updateLiveTable();
        $("#editUebersetzung").dialog('close');
      } else {
        alert(data.statusText);
      }
    }
  });
}

function editUebersetzung(id) {
  $.ajax({
    url: 'index.php?module=artikel&action=eigenschaften&cmd=getuebersetzung&id=[ID]',
    data: {
      id: id
    },
    method: 'post',
    dataType: 'json',
    beforeSend: function() {
      App.loading.open();
    },
    success: function(data) {
      $('#editUebersetzung').find('#u_id').val(data.id);
      $('#editUebersetzung').find('#u_article').val(data.article);
      if(id > 0){
        if(data.language_from == 'DE'){
          $('#editUebersetzung').find('#u_propertyFrom').val(data.property_from);
          $('#editUebersetzung').find('#u_propertyValueFrom').val(data.property_value_from);
        }else{
          $('#editUebersetzung').find('#u_propertyFromElse').val(data.property_from);
          $('#editUebersetzung').find('#u_propertyValueFromElse').val(data.property_value_from);
        }
        
        $('#editUebersetzung').find('#e_languageFrom').val(data.language_from);
        $('#editUebersetzung').find('#u_languageTo').val(data.language_to);
        $('#editUebersetzung').find('#u_propertyTo').val(data.property_to);
        $('#editUebersetzung').find('#u_propertyValueTo').val(data.property_value_to);
        $('#editUebersetzung').find('#u_shop').val(data.shop);       
      }

      languageFrom = document.getElementById('u_languageFrom');
      propertyde = document.getElementById('propertyde');
      propertyelse = document.getElementById('propertyelse');
      propertyvaluede = document.getElementById('propertyvaluede');
      propertyvalueelse = document.getElementById('propertyvalueelse');
      if(languageFrom){
        // Hide the target field if priority isn't critical
        if(typeof languageFrom.options[languageFrom.selectedIndex] != 'undefined' && languageFrom.options[languageFrom.selectedIndex].value =='DE'){
          propertyde.style.display='';
          propertyelse.style.display='none';
          propertyvaluede.style.display='';
          propertyvalueelse.style.display='none';
        }else{
          propertyde.style.display='none';
          propertyelse.style.display='';
          propertyvaluede.style.display='none';
          propertyvalueelse.style.display='';
        }
      }
      
      App.loading.close();
      $("#editUebersetzung").dialog('open');
    }
  });
}

function deleteUebersetzung(id) {
  var conf = confirm('Wirklich löschen?');
  if (conf) {
    $.ajax({
      url: 'index.php?module=artikel&action=eigenschaften&cmd=deleteuebersetzung&id=[ID]',
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