<!-- gehort zu tabview -->
<div id="tabs">
  <ul>
    <li><a href="#tabs-1"></a></li>
  </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
<!--
<table height="80" width="100%"><tr><td>
<fieldset><legend>&nbsp;Filter</legend>
<center>
<table width="100%" cellspacing="5">
<tr>
  <td><input type="checkbox" id="angeboteoffen">&nbsp;Fehlende Artikel</td>
  <td><input type="checkbox" id="angeboteoffen">&nbsp;Artikel im Zulauf</td>
  <td><input type="checkbox" id="angeboteheute">&nbsp;Gersperrte Artikel</td>
  <td>Artikelgruppen: <select><option>alle</option><option>Waren 700000</option></select></td>
</tr></table>
</center>
</fieldset>
</td></tr></table>
-->
[MESSAGE]
[TAB1]
</div>


<!-- tab view schließen -->
</div>

<form method="post">
<div id="copyGeschaeftsbriefvorlage" style="display:none;" title="Eintrag kopieren">
  <fieldset>
    <legend>{|Eintrag kopieren|}</legend>
    <input type="hidden" id="editid">
    <table>
      <tr>
        <td>{|Typ|}:</td>
        <td><input type="text" name="editsubjekt" id="editsubjekt" size="45" rule="notempty" msg="Plichtfeld!"><div id="pflicht0" style="float:right;display:table"><font color="red"><span>Pflichtfeld!</span></font></div></td>
      </tr>
      <tr>
        <td>{|Sprache|}:</td>
        <td><select name="editsprache" id="editsprache" style="width:10em">
            [SELSPRACHEN]
            </select></td>
      </tr>
      <tr>
        <td>{|Projekt|}:</td>
        <td><input type="text" name="editprojekt" id="editprojekt" size="45"></td>
      </tr>
      <tr>
        <td>{|Betreff|}:</td>
        <td><input type="text" name="editbetreff" id="editbetreff" size="45"></td>
      </tr>
      <tr>
        <td>{|Text|}:</td>
        <td><textarea cols="60" rows="20" name="edittext" id="edittext"></textarea></td>
      </tr>
      <tr>
        <td>{|Dateien mitkopieren|}:</td>
        <td><input type="checkbox" id="editdateien" name="editdateien"></td>        
      </tr>
    </table>  
  </fieldset>  
</div>
</form>

<div id="editGeschaeftsbriefvorlage" style="display:none;" title="Bearbeiten">
  [DATEIENPOPUP]
<form action="" method="post" name="eprooform">
  [FORMHANDLEREVENT]
  <input type="hidden" id="e_id">
  <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>
      <tr valign="top" colspan="3">
        <td>
          <fieldset>
            <legend>{|Einstellung|}</legend>
            <table width="100%">
              <tr>
                <td>{|Typ|}:</td>
                <td><input type="text" name="subjekt" id="subjekt" size="40" rule="notempty" msg="Plichtfeld!"><div id="pflicht1" style="float:right;display:table"><font color="red"><span>Pflichtfeld!</span></font></div></td>
              </tr>
              <tr>
                <td>{|Sprache|}:</td>
                <td><select name="sprache" id="sprache">
                      <option value="deutsch">{|Deutsch|}</option>
                      <option value="englisch">{|Englisch|}</option>
                      [SELSPRACHEN]
                    </select>
                </td>
              </tr>
              <tr>
                <td>{|Projekt|}:</td>
                <td><input type="text" name="projekt" id="projekt"></td>
              </tr>
              <tr>
                <td>{|Betreff|}:</td>
                <td><input type="text" name="betreff" id="betreff" size="80"></td>
              </tr>
              <tr>
                <td>{|Text|}:</td>
                <td><textarea name="text" id="text" rows="20" cols="50"></textarea></td>
              </tr>              
            </table>
            <br />
            <b>{|Variablen|}</b>
              <ul>
                <li>{|Rechnung|}, {|Rechnung_Abweichend|} <i>{|Variablen|}: {NAME}, {ANSCHREIBEN}, {BELEGNR}, {DATUM}</i></li>
                <li>{|Angebot|}, {|Angebot_Abweichend|} <i>{|Variablen|}: {NAME}, {ANSCHREIBEN}, {BELEGNR}, {DATUM}</i></li>
                <li>{|Auftrag|}, {|Auftrag_Abweichend|} <i>{|Variablen|}: {NAME}, {ANSCHREIBEN}, {BELEGNR}, {DATUM}</i></li>
                <li>{|Gutschrift|}, {|Gutschrift_Abweichend|} <i>{|Variablen|}: {NAME}, {ANSCHREIBEN}, {BELEGNR}, {DATUM}</i></li>
                <li>{|Lieferschein|}, {|Lieferschein_Abweichend|} <i>{|Variablen|}: {NAME}, {ANSCHREIBEN}, {BELEGNR}, {DATUM}</i></li>
                <li>{|Bestellung|} <i>{|Variablen|}: {NAME}, {ANSCHREIBEN}, {BELEGNR}, {DATUM}</i></li>
                <li>{|Proformarechnung|} <i>{|Variablen|}: {NAME}, {ANSCHREIBEN}, {BELEGNR}, {DATUM}</i></li>
                <li>{|Arbeitsnachweis|} <i>{|Variablen|}: {NAME}, {ANSCHREIBEN}, {BELEGNR}, {DATUM}</i></li>
                <li>{|Korrespondenz|} <i>{|Variablen|}: {BETREFF}, {NAME}, {ANSCHREIBEN}</i></li>
                <li>{|ZahlungOK|} <i>{|Variablen|}: {AUFTRAG}, {DATUM}, {GESAMT}, {ANSCHREIBEN}, {INTERNET}</i></li>
                <li>{|ZahlungDiff|} <i>{|Variablen|}: {AUFTRAG}, {DATUM}, {GESAMT}, {REST}, {ANSCHREIBEN}, {INTERNET}</i></li>
                <li>{|Stornierung|} <i>{|Variablen|}: {AUFTRAG}, {DATUM}, {INTERNET}</i></li>
                <li>{|ZahlungMiss|} <i>{|Variablen|}: {AUFTRAG}, {DATUM}, {GESAMT}, {REST}, {ANSCHREIBEN}, {INTERNET}</i></li>
                <li>{|Versand|} <i>{|Variablen|}: {VERSAND}, {VERSANDTYPE}, {VERSANDBEZEICHNUNG}, {TRACKINGNUMMER}, {NAME}, {ANSCHREIBEN}, {BELEGNR}, {IHREBESTELLNUMMER}, {INTERNET}, {AUFTRAGDATUM}, {LIEFERADRESSE}, {LIEFERADRESSELANG}</i></li>
                <li>{|VersandMailDokumente|} <i>{|Variablen|}: {NAME}, {ANSCHREIBEN}, {BELEGNR}, {IHREBESTELLNUMMER}, {INTERNET}, {AUFTRAGDATUM}</i></li>
                <li>{|Erweiterte Freigabe|} <i>{|Variablen|}: {REQUESTER}, {LINK}, {LINKFREIGABEUEBERSICHT}, {DOCTYPE}, {DOCTYPE_ID}</i></li>
                <li>{|Selbstabholer|}</li>
              </ul>
          </fieldset>

        </td>
      </tr>

      <!--<tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
        <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
          <input type="submit" value="Speichern" />
      </tr>-->
  
    </tbody>
  </table>
</form>
</div>

<script type="text/javascript">

$(document).ready(function() {
    $('#editsprache').focus();

    $("#copyGeschaeftsbriefvorlage").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    minWidth:600,
    autoOpen: false,
    buttons: {
      '{|ABBRECHEN|}': function() {
        $(this).dialog('close');
      },
      '{|SPEICHERN|}': function() {
        console.log("bbb");
        Geschaeftsbrief_vorlagenCopyEditSave();
      }
    }
  });

});

function Geschaeftsbrief_vorlagenCopyEditSave() {

    $.ajax({
        url: 'index.php?module=geschaeftsbrief_vorlagen&action=copysave',
        data: {
            //Alle Felder die fürs editieren vorhanden sind
            id: $('#editid').val(),
            subjekt: $('#editsubjekt').val(),
            sprache: $('#editsprache').val(),
            projekt: $('#editprojekt').val(),
            betreff: $('#editbetreff').val(),
            text: $('#edittext').val(),
            dateien: $('#editdateien').prop("checked")?1:0
            
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            //App.loading.open();
        },
        success: function(data) {
          //alert(data);
            //App.loading.close();

            if (data.status == 1) {
            	$('#copyGeschaeftsbriefvorlage').find('#editid').val('');
              $('#copyGeschaeftsbriefvorlage').find('#editsubjekt').val('');
              $('#copyGeschaeftsbriefvorlage').find('#editsprache').val('');
              $('#copyGeschaeftsbriefvorlage').find('#editprojekt').val('');
              $('#copyGeschaeftsbriefvorlage').find('#editbetreff').val('');
              $('#copyGeschaeftsbriefvorlage').find('#edittext').val('');
              $('#copyGeschaeftsbriefvorlage').find('#editdateien').prop('checked', false);
              updateLiveTable();
              $("#copyGeschaeftsbriefvorlage").dialog('close');
            } else {
              if(data.statusText.includes("Typf")){
                $('#pflicht0').show();
              }else{
                alert(data.statusText);
              } 
            }
        }
    });

}

function Geschaeftsbrief_vorlagenCopyEdit(id) {

  $('#pflicht0').hide();

    $.ajax({
        url: 'index.php?module=geschaeftsbrief_vorlagen&action=copyedit&cmd=get',
        data: {
            id: id
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {

            if(typeof data.id != "undefined") $('#copyGeschaeftsbriefvorlage').find('#editid').val(data.id);
            $('#copyGeschaeftsbriefvorlage').find('#editprojekt').val(data.projekt);
            $('#copyGeschaeftsbriefvorlage').find('#editsubjekt').val(data.subjekt);
            $('#copyGeschaeftsbriefvorlage').find('#editbetreff').val(data.betreff);
            $('#copyGeschaeftsbriefvorlage').find('#edittext').val(data.text);
            $('#copyGeschaeftsbriefvorlage').find('#editdateien').prop('checked', true);

            if(data.sprache=="" || data.sprache <=0){
              $('#copyGeschaeftsbriefvorlage').find('#editsprache').val('DE');
            }else{
              if(data.sprache=="englisch"){
                $('#copyGeschaeftsbriefvorlage').find('#editsprache').val('EN');
              } 
              
            } 

            App.loading.close();
            $("#copyGeschaeftsbriefvorlage").dialog('open');
        }
    });

}

</script>

<script type="text/javascript">
$(document).ready(function() {
  $('#subjekt').focus();

  $("#editGeschaeftsbriefvorlage").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    minWidth:950,
    maxHeight:850,
    autoOpen: false,
    buttons: {
      [DATEIBUTTON]
      '{|ABBRECHEN|}': function() {
        GeschaeftsbriefvorlageReset();
        $(this).dialog('close');
      },
      '{|SPEICHERN|}': function() {
        GeschaeftsbriefvorlageEditSave();
      }
    }
  });

  $("#editGeschaeftsbriefvorlage").dialog({
    close: function( event, ui ) {}
  });

});


function GeschaeftsbriefvorlageReset(){
  $('#editGeschaeftsbriefvorlage').find('#e_id').val('');
  $('#editGeschaeftsbriefvorlage').find('#subjekt').val('');
  $('#editGeschaeftsbriefvorlage').find('#sprache').val('deutsch');
  $('#editGeschaeftsbriefvorlage').find('#projekt').val('');
  $('#editGeschaeftsbriefvorlage').find('#betreff').val('');
  $('#editGeschaeftsbriefvorlage').find('#text').val('');
  
}


function GeschaeftsbriefvorlageEditSave() {

  $.ajax({
    url: 'index.php?module=geschaeftsbrief_vorlagen&action=edit&cmd=popupsave',
    data: {
      //Alle Felder die fürs editieren vorhanden sind
      eid: $('#e_id').val(),
      esubjekt: $('#subjekt').val(),
      esprache: $('#sprache').val(),
      eprojekt: $('#projekt').val(),
      ebetreff: $('#betreff').val(),
      etext: $('#text').val()

    },
    method: 'post',
    dataType: 'json',
    beforeSend: function() {
      App.loading.open();
    },
    success: function(data) {
      App.loading.close();
      if (data.status == 1) {
        GeschaeftsbriefvorlageReset();
        updateLiveTable();
        $("#editGeschaeftsbriefvorlage").dialog('close');
      } else {
        if(data.statusText.includes("Typf")){
          $('#pflicht1').show();
        }else{
          alert(data.statusText);
        } 
      }
    }
  });


}

function Geschaeftsbrief_vorlagenEdit(id){
  GeschaeftsbriefvorlageReset();
  $('#pflicht1').hide();

  if(id > 0)
  { 
    $.ajax({
      url: 'index.php?module=geschaeftsbrief_vorlagen&action=edit&cmd=popupedit',
      data: {
        id: id
      },
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
        App.loading.open();
      },
      success: function(data) {
        if(data.id > 0)
        {
          $('#editGeschaeftsbriefvorlage').find('#e_id').val(data.id);
          $('#editGeschaeftsbriefvorlage').find('#subjekt').val(data.subjekt);
          $('#editGeschaeftsbriefvorlage').find('#sprache').val(data.sprache);
          $('#editGeschaeftsbriefvorlage').find('#projekt').val(data.projekt);
          $('#editGeschaeftsbriefvorlage').find('#betreff').val(data.betreff);
          $('#editGeschaeftsbriefvorlage').find('#text').val(data.text);
                         
        }

        App.loading.close();
        $("#editGeschaeftsbriefvorlage").dialog('open');
        [AFTERPOPUPOPEN]
      }
    });
  } else {
    GeschaeftsbriefvorlageReset(); 
    $("#editGeschaeftsbriefvorlage").dialog('open');
    [AFTERPOPUPOPEN]
  }

}

function updateLiveTable(i) {
    var oTableL = $('#geschaeftsbrief_vorlagenlist').dataTable();
    oTableL.fnFilter('%');
    oTableL.fnFilter('');   
}

function GeschaeftsbriefvorlageDelete(id) {

    var conf = confirm('Wirklich löschen?');
    if (conf) {
        $.ajax({
            url: 'index.php?module=geschaeftsbrief_vorlagen&action=edit&cmd=delete',
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
