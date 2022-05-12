<script>

function Steuer(cmd)
{

  var steuer = $('select[name=steuer] option:selected').text();
  steuer = steuer.replace('%','');
  steuer = (parseFloat(steuer) + 100)/100.0;
  var betrag = $("#betragfeld").val();

  betrag = parseFloat(betrag.replace(',','.'));

  if(cmd=='add') betrag = betrag*steuer;
  if(cmd=='sub' && steuer!=0) betrag = betrag/steuer;


  betrag = betrag.toFixed(2);
  betrag = betrag.replace('.',',');
  $("#betragfeld").val(betrag);
}

</script>


<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-8 col-md-height">
<div class="inside_white inside-full-height">

  <div>
  <div class="row-height" style="padding-top:0">
  <div class="col-xs-12 col-md-9 col-md-height" style="padding-left:0">
  <div class="inside inside-full-height">

    <fieldset>
      <legend>&nbsp;</legend>
    </fieldset>

  </div>
  </div>
  <div class="col-xs-12 col-md-3 col-md-height">
  <div class="inside inside-full-height">

    <form action="" method="post" name="vkkontierung">
      <fieldset>
        <legend>{|Aktionen|}</legend>
        <input type="button" class="btnGreenNew" name="neuebuchung" value="&#10010; Neuer Eintrag" onclick="BuchungEdit(0);">
      </fieldset>
    </form>

  </div>
  </div>
  </div>
  </div>


<br>
        
<table width="100%>">
<tr><td>Summe Verbindlichkeit</td><td>Summe Kontierung</td><td>Differenz</td></tr>                                                                                                                                      
<tr>                                                                                                                                                                                                 
  <td class="greybox" width="33%" id="summeverbindlichkeit">[SUMMEVERBINDLICHKEIT]</td>                                                                            
  <td class="greybox" width="33%" id="summekontierung">[SUMMEKONTIERUNG]</td>                                                                                
  <td class="greybox" width="33%" id="summedifferenz">[SUMMEDIFFERENZ]</td>                                                                                
</tr>                                                                                                                                                                                             
</table>  
<br>
[MESSAGE]
[TABKONTIERUNG]

</div>
</div>
<div class="col-xs-12 col-md-4 col-md-height">
<div class="inside inside-full-height">

  <fieldset>
    <legend>{|Vorschau|}</legend>
  <iframe width="100%" height="100%" style="height:calc(100vh - 100px)" class="preview" data-src="./js/production/generic/web/viewer.html?file=[FILE]&kontierung"></iframe>
  </fieldset>


</div>
</div>
</div>
</div>


<div id="editBuchung" style="display:none;" title="Bearbeiten">
  <form method="post">
    <input type="hidden" id="e_idv" value="[ID]">
    <fieldset>
      <legend>{|Buchung|}</legend>
        <table width="100%" border="0">
          <tr>
            <td>{|Sachkonto|}:</td>
            <td><input type="text" name="gegenkonto" id="gegenkonto" value="[GEGENKONTOVALUE]" size="20"></td>
          </tr>
          <tr>
            <td>{|Betrag|}:</td>
            <td><input type="text" name="betrag" id="betragfeld" value="[BETRAGVALUE]"><img style="margin-right:5px;top:5px;left:5px; position:relative;" src="./themes/new/images/add.png" onclick="$('#betragfeld').val($('#differenz').val())">
                <input type="button" value="+" style="width:20px" onclick="Steuer('add')"><input type="button" onclick="Steuer('sub')" value="-" style="width:20px">
            </td>
          </tr>
          <tr>
            <td>{|W&auml;hrung|}:</td>
            <td><input type="text" name="vkontierung_waehrung" id="vkontierung_waehrung" value="[WAEHRUNGVALUE]" size="5">&nbsp;<i>(optional)</i></td>
            <!--<td><input type="submit" name="submitkontierung" onclick="$('#vkkontierung').attr('action','#tabs-3');"  value="Speichern" style="float:right"/></td>-->
          </tr>
          <tr>
            <td>{|Buchungstext|}:</td>
            <td><input type="text" name="buchungstext" id="buchungstext" value="[BUCHUNGSTEXTVALUE]" size="50"></td>
          </tr>
     
          <tr>
            <td>{|Belegfeld|}:</td>
            <td><input type="text" name="belegfeld" id="belegfeld" value="[BELEGFELDVALUE]" size="50"></td>
          </tr>
          <tr>
            <td>{|Steuersatz|}:</td>
            <td><select name="steuer" id="steuer">
                  <option value="[STANDARDSTEUERSATZ]" [STANDARD]>[STANDARDSTEUERSATZ]%</option>
                  <option value="[ERMAESSIGTSTEUERSATZ]" [ERMAESSIGT]>[ERMAESSIGTSTEUERSATZ]%</option>
                  <option value="0.00" [OHNEUST]>[OHNESTEUERSATZ]%</option>[SELSTEUER]</select>
            </td>
          </tr>
          <tr>
            <td>{|Kostenstelle|}:</td>
            <td><input type="text" name="kont_kostenstelle" id="kont_kostenstelle" size="20"></td>
          </tr>
        </table>
        <input type="hidden" name="vkid" id="vkid" value="[VKID]"/>
        <input type="hidden" name="differenz" id="differenz" value="[SUMMEDIFFERENZ]">
    </fieldset>    
  </form>
</div>



<script type="text/javascript">

$(document).ready(function() {
  $('#e_name').focus();

  $("#editBuchung").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    minWidth:630,
    maxHeight:700,
    autoOpen: false,
    buttons: {
      ABBRECHEN: function() {
        BuchungReset();
        $(this).dialog('close');
      },
      SPEICHERN: function() {
        BuchungEditSave();
      }
    }
  });

  $("#editBuchung").dialog({
    close: function( event, ui ) { BuchungReset();}
  });
});


function BuchungReset()
{
  $('#editBuchung').find('#gegenkonto').val('');
  $('#editBuchung').find('#betragfeld').val('');
  $('#editBuchung').find('#vkontierung_waehrung').val('');
  $('#editBuchung').find('#belegfeld').val('');
  $('#editBuchung').find('#buchungstext').val('');
  $("#steuer")[0].selectedIndex = 0;
  $('#editBuchung').find('#kont_kostenstelle').val('');
  //$('#editBuchung').find('#differenz').val('');
  $('#editBuchung').find('#vkid').val('');
}

function BuchungEditSave() {
  $.ajax({
    url: 'index.php?module=verbindlichkeit&action=edit&cmd=buchungsave',
    data: {
      //Alle Felder die fürs editieren vorhanden sind
      id: $('#e_idv').val(),
      egegenkonto: $('#gegenkonto').val(),
      ebetragfeld: $('#betragfeld').val(),
      ewaehrung: $('#vkontierung_waehrung').val(),
      ebelegfeld: $('#belegfeld').val(),
      ebuchungstext: $('#buchungstext').val(),
      evkid: $('#vkid').val(),
      edifferenz: $('#differenz').val(),
      esteuer: $('#steuer').val(),
      ekont_kostenstelle: $('#kont_kostenstelle').val()
                      
    },
    method: 'post',
    dataType: 'json',
    beforeSend: function() {
      App.loading.open();
    },
    success: function(data) {
      App.loading.close();
      if (data.status == 1) {
        BuchungReset();
        updateLiveTable();
        document.getElementById("summeverbindlichkeit").innerHTML = data.summeverbindlichkeit;
        document.getElementById("summekontierung").innerHTML = data.summekontierung;
        document.getElementById("summedifferenz").innerHTML = data.summedifferenz;
        $("#editBuchung").dialog('close');
      } else {
        alert(data.statusText);
      }
    }
  });

}

function BuchungEdit(id) {
  if(id > 0)
  { 
    $.ajax({
      url: 'index.php?module=verbindlichkeit&action=edit&cmd=buchungget',
      data: {
        id: id
      },
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
        App.loading.open();
      },
      success: function(data) {
        $('#editBuchung').find('#gegenkonto').val(data.gegenkonto);
        $('#editBuchung').find('#betragfeld').val(data.betrag);
        $('#editBuchung').find('#vkontierung_waehrung').val(data.waehrung);
        $('#editBuchung').find('#belegfeld').val(data.belegfeld);
        $('#editBuchung').find('#buchungstext').val(data.buchungstext);
        $('#editBuchung').find('#vkid').val(data.id);
        $('#editBuchung').find('#steuer').val(data.steuersatz);
        $('#editBuchung').find('#kont_kostenstelle').val(data.kostenstelle);
                
        App.loading.close();
        $("#editBuchung").dialog('open');
      }
    });
  } else {
    BuchungReset();

    $.ajax({
      url: 'index.php?module=verbindlichkeit&action=edit&cmd=startwerte',
      data: {
        verbindlichkeitsid: $('#e_idv').val(),
      },
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
      },
      success: function(data) {
        $('#editBuchung').find('#betragfeld').val(data.restbetrag);
        $('#editBuchung').find('#vkontierung_waehrung').val(data.waehrung);
        $('#editBuchung').find('#belegfeld').val(data.belegfeld);
        $('#editBuchung').find('#buchungstext').val(data.buchungstext);
        $('#editBuchung').find('#kont_kostenstelle').val(data.kont_kostenstelle);
        $('#editBuchung').dialog('open');
      }
    });
  }

}

function updateLiveTable(i) {
  var oTableL = $('#verbindlichkeit_kontierung').dataTable();
  var tmp = $('.dataTables_filter input[type=search]').val();
  oTableL.fnFilter('%');
  //oTableL.fnFilter('');
  oTableL.fnFilter(tmp);   
}



</script>
