<div id="splitKontoauszugszeile" style="display:none;">
  <div class="inside">
  <fieldset>
    <legend>Kontoauszugszeile Teilbetrag absplitten</legend>
  <input type="hidden" id="splitid">
  <input type="hidden" id="splitdoctype">
  <input type="hidden" id="splitdoctypeid">
  <input type="hidden" id="splitsollorg">
  <input type="hidden" id="splithabenorg">


  <table width="100%">
    <tr valign="top"><td width="60%">

  <table class="mkTableFormular">
    <tr valign="top">
      <td>{|Vorgang|}:</td>
      <td><textarea id="splitvorgang" rows="5" cols="50"></textarea></td>
    </tr>
    <tr>
      <td width="100">{|Buchungstext|}:</td>
      <td><input type="text" id="splitbuchungstext" size="50" /></td>
    </tr>
     <tr>
      <td width="100">{|Belegfeld1|}:</td>
      <td><input type="text" id="splitbelegfeld1" size="50" /></td>
    </tr>
     <tr>
      <td width="100">{|Gegenkonto|}:</td>
      <td><input type="text" id="splitgegenkonto" size="50" /></td>
    </tr>
    <tr>
      <td width="100">{|Kl&auml;rfall|}:</td>
      <td><input type="checkbox" value="1" id="splitklaerfall" name="splitklaerfall" />&nbsp;<span class="klaerfall"><input type="text" size="45" id="splitklaergrund" placeholder="{|Bitte Grund angeben|}" /></span></td>
    </tr>
  </table>

    </td><td>


  <table>
    <tr>
      <td width="100">{|Datum|}:</td>
      <td><input type="text" id="splitdatum" size="20" readonly /></td>
    </tr>

    <tr>
      <td>{|Soll|}:</td>
      <td><input type="text" id="splitsoll" size="20" /></td>
    </tr>
    <tr>
      <td>{|Haben|}:</td>
      <td><input type="text" id="splithaben" size="20" /></td>
    </tr>
    <tr>
      <td>{|Gebühren|}:</td>
      <td><input type="text" id="splitgebuehr" size="20" /></td>
    </tr>
    <tr>
      <td>{|Währung|}:</td>
      <td><input type="text" id="splitwaehrung" size="20" readonly /></td>
    </tr>
  <tr>
      <td>{|Kostenstelle|}:</td>
      <td><input type="text" id="splitkostenstelle" size="20" /></td>
    </tr>

    <tr>
      <td>{|Konto Soll|}:</td>
      <td align="right"><span id="kontosoll"></span>&nbsp;</td>
    </tr>
    <tr>
      <td>{|Konto Haben|}:</td>
      <td align="right"><span id="kontohaben"></span>&nbsp;</td>
    </tr>

    


<!--
  <tr>
      <td>Importfehler:</td>
      <td><input type="checkbox" id="splitimportfehler"></td>
    </tr>
    <tr>
      <td>Abgeschlossen:</td>
      <td><input type="checkbox" id="splitabgeschlossen"></td>
    </tr>
 --> 

    </table>

    </td></tr>
  </table>
</fieldset>
  </div>
<br><br>

<div id="tabsdialog1">
  <ul>
    <li><a href="#tabs1-5">{|Offene Auftr&auml;ge|}</a></li>
    <li><a href="#tabs1-1">{|Offene Rechnungen|}</a></li>
    <li><a href="#tabs1-2">{|Offene Gutschriften|}</a></li>
    <li><a href="#tabs1-3">{|Verbindlichkeiten|}</a></li>
    <li><a href="#tabs1-4">{|Bestellungen|}</a></li>
  </ul>
  <div id="tabs1-5">
  [TAB5DIALOG1]
  </div>
  <div id="tabs1-1">
  [TAB1DIALOG1]
  </div>
  <div id="tabs1-2">
  [TAB2DIALOG1]
  </div>
  <div id="tabs1-3">
  [TAB3DIALOG1]
  </div>
  <div id="tabs1-4">
  [TAB4DIALOG1]
  </div>
</div>

</div>



<div id="editKontoauszugszeile" style="display:none;">
  <input type="hidden" id="editid">
  <input type="hidden" id="editdoctype">
  <input type="hidden" id="editdoctypeid">

  <div class="inside">
  <fieldset>
    <legend>Kontoauszugszeile verbuchen</legend>

  <table class="mkTableFormular" width="100%">
    <tr valign="top"><td width="60%">
          <table>
            <tr valign="top">
              <td width="100">{|Vorgang|}:</td>
              <td><textarea id="editvorgang" rows="5" cols="50"></textarea></td>
            </tr>
            <tr>
              <td width="100">{|Buchungstext|}:</td>
              <td><input type="text" id="editbuchungstext" size="50" /></td>
            </tr>
             <tr>
              <td width="100">{|Belegfeld1|}:</td>
              <td><input type="text" id="editbelegfeld1" size="50" /></td>
            </tr>
             <tr>
              <td width="100">{|Gegenkonto|}:</td>
              <td><input type="text" id="editgegenkonto" size="50" /></td>
            </tr>
             <tr>
              <td width="100">{|Kl&auml;rfall|}:</td>
              <td><input type="checkbox" value="1" id="editklaerfall" name="editklaerfall" />&nbsp;<span class="editklaerfall"><input type="text" size="45" id="editklaergrund" placeholder="{|Bitte Grund angeben|}" /></span></td>
            </tr>
          </table>

        </td>
        <td>

          <table>
            <tr>
              <td>{|Datum|}:</td>
              <td><input type="text" id="editdatum" size="12" readonly /></td>
            </tr>

            <tr>
              <td>{|Soll|}:</td>
              <td><input type="text" id="editsoll" size="20" readonly /></td>
            </tr>
            <tr>
              <td>{|Haben|}:</td>
              <td><input type="text" id="edithaben" size="20" readonly /></td>
            </tr>
            <tr>
              <td>{|Gebühren|}:</td>
              <td><input type="text" id="editgebuehr" size="20" readonly /></td>
            </tr>
            <tr>
              <td>{|Währung|}:</td>
              <td><input type="text" id="editwaehrung" size="20" readonly /></td>
            </tr>
            <tr>
              <td>{|Kostenstelle|}:</td>
              <td><input type="text" id="editkostenstelle" size="20"/></td>
            </tr>


            <tr class="hideedit" style="display:none">
              <td>{|Importfehler|}:</td>
              <td><div style="width:340px;"><input type="checkbox" id="editimportfehler" /></div></td>
            </tr>
            <tr class="hideedit" style="display:none">
              <td>{|Abgeschlossen|}:</td>
              <td><div style="width:340px;"><input type="checkbox" id="editabgeschlossen" /></div></td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </fieldset>
  </div>
<br>
<br>

<div id="tabsdialog2">
  <ul>
    <li><a href="#tabs2-1">{|Offene Rechnungen|}</a></li>
    <li><a href="#tabs2-2">{|Offene Gutschriften|}</a></li>
    <li><a href="#tabs2-3">{|Verbindlichkeiten|}</a></li>
    <li><a href="#tabs2-4">{|Bestellungen|}</a></li>
  </ul>
  <div id="tabs2-1">
  [TAB1DIALOG2]
  </div>
  <div id="tabs2-2">
  [TAB2DIALOG2]
  </div>
  <div id="tabs2-3">
  [TAB3DIALOG2]
  </div>
  <div id="tabs2-4">
  [TAB4DIALOG2]
  </div>

</div>
 

</div>


<script type="text/javascript">


$(document).ready(function() {
    $( "#tabsdialog1" ).tabs();
    $( "#tabsdialog2" ).tabs();
    
    $("#editKontoauszugszeile").dialog({
      modal: true,    
      bgiframe: true,    
      closeOnEscape:false,
      minWidth:1000,    
      autoOpen: false,    
      buttons: {
      '{|ABBRECHEN|}': function() {
        $(this).dialog('close');
      },
      '{|SPEICHERN|}': function() {
        KontoauszugszeileEditSave();
      }
    }
  });

  $("#splitKontoauszugszeile").dialog({
      modal: true,    
      bgiframe: true,    
      closeOnEscape:false,
      minWidth:1000,    
      autoOpen: false,    
      buttons: {
      '{|ABBRECHEN|}': function() {
        $(this).dialog('close');
      },
      '{|SPEICHERN|}': function() {
        KontoauszugszeileSplitSave();
      }
    }
  });

});


function KontoauszugszeileSet(typ,id)
{
  $.ajax({
        url: 'index.php?module=zahlungseingang&action=split&cmd=set',
        data: {
            //Alle Felder die fürs editieren vorhanden sind
            tid: id,
            ttyp: typ
        },
        method: 'post',
        dataType: 'json',
        success: function(data) {
            if (data.status == 1) {
              $('#splitKontoauszugszeile').find('#splitbelegfeld1').val(data.belegfeld1);
              $('#splitKontoauszugszeile').find('#splitbuchungstext').val(data.buchungstext);

              if(typ=="gutschrift" || typ=="verbindlichkeit" || typ=="bestellung")
              {
                if(data.soll < 0) {
                  data.haben = Math.abs(data.soll);
                  data.soll = 0;
                }
                if(data.haben < 0) {
                  data.soll = Math.abs(data.haben);
                  data.haben = 0;
                }
                $('#splitKontoauszugszeile').find('#splithaben').val(data.haben);
                $('#splitKontoauszugszeile').find('#splitsoll').val(data.soll);
              }
        
              if(typ=="rechnung" || typ=="auftrag")
              {
                
                if(data.differenz < 0)
                {
                  $('#splitKontoauszugszeile').find('#splithaben').val(Math.abs(data.differenz));
                  $('#splitKontoauszugszeile').find('#splitsoll').val(0);
                } else {
                  $('#splitKontoauszugszeile').find('#splitsoll').val(data.differenz);
                  $('#splitKontoauszugszeile').find('#splithaben').val(0);
                }
              }
/*

              if(parseFloat($('#splitKontoauszugszeile').find('#splithabenorg').val()) >= parseFloat(data.haben))
                $('#splitKontoauszugszeile').find('#splithaben').val(Math.abs(data.haben));
              else
                $('#splitKontoauszugszeile').find('#splithaben').val(parseFloat($('#splitKontoauszugszeile').find('#splithabenorg').val()));

              if(parseFloat($('#splitKontoauszugszeile').find('#splitsollorg').val()) >= parseFloat(data.soll))
              {
                if( parseFloat(data.soll) < 0 ) {
                  $('#splitKontoauszugszeile').find('#splithaben').val(Math.abs(data.soll));
                } else {
                  $('#splitKontoauszugszeile').find('#splitsoll').val(data.soll);
                }
              }
              else
                $('#splitKontoauszugszeile').find('#splitsoll').val(parseFloat($('#splitKontoauszugszeile').find('#splitsollorg').val()));

*/

              $('#splitKontoauszugszeile').find('#splitgegenkonto').val(data.gegenkonto);
              $('#splitKontoauszugszeile').find('#splitkostenstelle').val(data.kostenstelle);
              $('#splitKontoauszugszeile').find('#splitabgeschlossen').prop("checked",data.abgeschlossen==1?true:false);
              $('#splitKontoauszugszeile').find('#splitimportfehler').prop("checked",data.importfehler==1?true:false);
              $('#splitKontoauszugszeile').find('#splitdoctype').val(data.doctype);
              $('#splitKontoauszugszeile').find('#splitdoctypeid').val(data.doctypeid);

              $('#editKontoauszugszeile').find('#editbelegfeld1').val(data.belegfeld1);
              $('#editKontoauszugszeile').find('#editbuchungstext').val(data.buchungstext);

              if(parseFloat($('#editKontoauszugszeile').find('#edithaben').val()) >= parseFloat(data.haben))
                $('#editKontoauszugszeile').find('#edithaben').val(Math.abs(data.haben));

              if(parseFloat($('#editKontoauszugszeile').find('#editsoll').val()) >= parseFloat(data.soll))
                $('#editKontoauszugszeile').find('#editsoll').val(Math.abs(data.soll));

              $('#editKontoauszugszeile').find('#editgegenkonto').val(data.gegenkonto);
              $('#editKontoauszugszeile').find('#editkostenstelle').val(data.kostenstelle);
              $('#editKontoauszugszeile').find('#editabgeschlossen').prop("checked",data.abgeschlossen==1?true:false);
              $('#editKontoauszugszeile').find('#editimportfehler').prop("checked",data.importfehler==1?true:false);
              $('#editKontoauszugszeile').find('#editdoctype').val(data.doctype);
              $('#editKontoauszugszeile').find('#editdoctypeid').val(data.doctypeid);
              $('#editKontoauszugszeile').find('#editklaerfall').prop("checked",data.klaerfall==1?true:false);
              $('#editKontoauszugszeile').find('#editklaergrund').val(data.klaergrund);
 
            } else {
              alert(data.statusText);
            }
        }
    });

}

function KontoauszugszeileSplitSave()
{
 $.ajax({
        url: 'index.php?module=zahlungseingang&action=split&cmd=save',
        data: {
            //Alle Felder die fürs editieren vorhanden sind
            splitid: $('#splitid').val(),
            splitvorgang: $('#splitvorgang').val(),
            splitdatum: $('#splitdatum').val(),
            splitsoll: $('#splitsoll').val(),
            splithaben: $('#splithaben').val(),
            splitgebuehr: $('#splitgebuehr').val(),
            splitwaehrung: $('#splitwaehrung').val(),
            splitbuchungstext: $('#splitbuchungstext').val(),
            splitgegenkonto: $('#splitgegenkonto').val(),
            splitkostenstelle: $('#splitkostenstelle').val(),
            splitabgeschlossen: $('#splitabgeschlossen').prop("checked")?1:0,
            splitimportfehler: $('#splitimportfehler').prop("checked")?1:0,
            splitbelegfeld1: $('#splitbelegfeld1').val(),
            splitdoctype: $('#splitdoctype').val(),
            splitdoctypeid: $('#splitdoctypeid').val(),
            splitklaerfall: $('#splitklaerfall').prop("checked")?1:0,
            splitklaergrund: $('#splitklaergrund').val()
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {
            App.loading.close();
            if (data.status == 1) {
                $("#splitKontoauszugszeile").dialog('close');
                var url = window.location.href;
                window.location.href='index.php?module=zahlungseingang&action=import&id=[ID]&seitennummer=[SEITENNUMMER]&filtereinnahmen=[FILTEREINNAHMEN]';//url.replace("\#", "");
            } else {
                alert(data.statusText);
            }
        }
    });
}

function KontoauszugszeileSplit(id) {
    $.ajax({
        url: 'index.php?module=zahlungseingang&action=split&cmd=get',
        data: {
            id: id
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {

            var sollnumber = parseFloat(data.soll);
            var habennumber = parseFloat(data.haben);

            if(sollnumber > 0)
              $( "#tabsdialog1" ).tabs({ active: 3});
            else
              $( "#tabsdialog1" ).tabs({ active: 1});

            $('#splitKontoauszugszeile').find('#splitid').val(data.id);
            $('#splitKontoauszugszeile').find('#splitvorgang').val(data.vorgang);
            $('#splitKontoauszugszeile').find('#splitdatum').val(data.datum);
  
            $('#splitKontoauszugszeile').find('#splitsollorg').val(sollnumber);
            $('#splitKontoauszugszeile').find('#splithabenorg').val(habennumber);


            $('#splitKontoauszugszeile').find('#kontosoll').html(sollnumber.toLocaleString('de-DE')+' '+data.waehrung);
            $('#splitKontoauszugszeile').find('#kontohaben').html(habennumber.toLocaleString('de-DE')+' '+data.waehrung);


            $('#splitKontoauszugszeile').find('#splitsoll').val(data.soll);
            $('#splitKontoauszugszeile').find('#splithaben').val(data.haben);
            $('#splitKontoauszugszeile').find('#splitgebuehr').val(data.gebuehr);
            $('#splitKontoauszugszeile').find('#splitwaehrung').val(data.waehrung);
            $('#splitKontoauszugszeile').find('#splitbuchungstext').val(data.buchungstext);
            $('#splitKontoauszugszeile').find('#splitbelegfeld1').val(data.belegfeld1);
            $('#splitKontoauszugszeile').find('#splitgegenkonto').val(data.gegenkonto);
            $('#splitKontoauszugszeile').find('#splitkostenstelle').val(data.kostenstelle);
            $('#splitKontoauszugszeile').find('#splitabgeschlossen').prop("checked",data.abgeschlossen==1?true:false);
            $('#splitKontoauszugszeile').find('#splitimportfehler').prop("checked",data.importfehler==1?true:false);

            $('#splitKontoauszugszeile').find('#splitdoctype').val(data.doctype);
            $('#splitKontoauszugszeile').find('#splitdoctypeid').val(data.doctypeid);
            $('#splitKontoauszugszeile').find('#splitklaerfall').prop("checked",data.klaerfall==1?true:false);
            $('#splitKontoauszugszeile').find('#splitklaergrund').val(data.klaergrund);
  
            App.loading.close();
            $("#splitKontoauszugszeile").dialog('open');
            $('#splitKontoauszugszeile').find('#splitklaerfall').trigger('change');
        }
    });

}



function KontoauszugszeileEdit(id,editform) {

    if(editform==1)
    {
      $(".hideedit").css("display","");
    } else {
      $(".hideedit").css("display","none");
    }


    $.ajax({
        url: 'index.php?module=zahlungseingang&action=editzeile&cmd=get',
        data: {
            id: id
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {
            $('#editKontoauszugszeile').find('#editid').val(data.id);
            $('#editKontoauszugszeile').find('#editdatum').val(data.datum);
            $('#editKontoauszugszeile').find('#editsoll').val(data.soll);
            $('#editKontoauszugszeile').find('#edithaben').val(data.haben);
            $('#editKontoauszugszeile').find('#editgebuehr').val(data.gebuehr);
            $('#editKontoauszugszeile').find('#editwaehrung').val(data.waehrung);
            $('#editKontoauszugszeile').find('#editvorgang').val(data.vorgang);
            $('#editKontoauszugszeile').find('#editgegenkonto').val(data.gegenkonto);
            $('#editKontoauszugszeile').find('#editkostenstelle').val(data.kostenstelle);
            $('#editKontoauszugszeile').find('#editbuchungstext').val(data.buchungstext);
            $('#editKontoauszugszeile').find('#editbelegfeld1').val(data.belegfeld1);
            $('#editKontoauszugszeile').find('#editabgeschlossen').prop("checked",data.abgeschlossen==1?true:false);
            $('#editKontoauszugszeile').find('#editimportfehler').prop("checked",data.importfehler==1?true:false);
            $('#editKontoauszugszeile').find('#editdoctype').val(data.doctype);
            $('#editKontoauszugszeile').find('#editdoctypeid').val(data.doctypeid);
            $('#editKontoauszugszeile').find('#editklaerfall').prop("checked",data.klaerfall==1?true:false);
            $('#editKontoauszugszeile').find('#editklaerfall').trigger('change');
            $('#editKontoauszugszeile').find('#editklaergrund').val(data.klaergrund);
            App.loading.close();
            $("#editKontoauszugszeile").dialog('open');

            if(data.doctype == '' && data.buchungstext == '') {
              var doctype = $('select[name="aktion' + data.id + '"]');
              var doctypeid = $('input[name="verbindlichkeit_parameter' + data.id + '"]');
              if(doctypeid)doctypeid = $(doctypeid).val().split(' ');
              if(doctype && doctype.val() == 'verbindlichkeit' && doctypeid[ 0 ] != '')
              {
                KontoauszugszeileSet('verbindlichkeit',doctypeid[ 0 ]);
              }
            }
            
        }
    });
}

function KontoauszugszeileEditSave()
{
 $.ajax({
        url: 'index.php?module=zahlungseingang&action=editzeile&cmd=save',
        data: {
            //Alle Felder die fürs editieren vorhanden sind
            editid: $('#editid').val(),
            editvorgang: $('#editvorgang').val(),
            editdatum: $('#editdatum').val(),
            editsoll: $('#editsoll').val(),
            edithaben: $('#edithaben').val(),
            editgebuehr: $('#editgebuehr').val(),
            editwaehrung: $('#editwaehrung').val(),
            editbuchungstext: $('#editbuchungstext').val(),
            editgegenkonto: $('#editgegenkonto').val(),
            editkostenstelle: $('#editkostenstelle').val(),
            editabgeschlossen: $('#editabgeschlossen').prop("checked")?1:0,
            editimportfehler: $('#editimportfehler').prop("checked")?1:0,
            editbelegfeld1: $('#editbelegfeld1').val(),
            editdoctype: $('#editdoctype').val(),
            editdoctypeid: $('#editdoctypeid').val(),
            editklaerfall: $('#editklaerfall').prop("checked")?1:0,
            editklaergrund: $('#editklaergrund').val()
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {
            App.loading.close();
            if (data.status == 1) {
              $("#editKontoauszugszeile").dialog('close');
              var checkurl = window.location.href;
              if(checkurl.includes("action=import"))
                window.location.href='index.php?module=zahlungseingang&action=import&id=[ID]&seitennummer=[SEITENNUMMER]&filtereinnahmen=[FILTEREINNAHMEN]'; 
              else
                updateLiveTable();
                updateLiveTableAlleKontoauszuege();
            } else {
                alert(data.statusText);
            }
        }
    });
}

function updateLiveTable(i) {
  var oTableL = $('#kontoauszuege').dataTable();
  var tmp = $('.dataTables_filter input[type=search]').val();
  oTableL.fnFilter('%');
  //oTableL.fnFilter('');
  oTableL.fnFilter(tmp);  
}

function updateLiveTableAlleKontoauszuege(i){
  var oTableL = $('#kontoauszuege_allekontoauszuege').dataTable();
  var tmp = $('.dataTables_filter input[type=search]').val();
  oTableL.fnFilter('%');
  //oTableL.fnFilter('');
  oTableL.fnFilter(tmp);  
}



</script>


