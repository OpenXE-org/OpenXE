<!-- gehort zu tabview -->
<script>

/*
$( "#popuptabs" ).tabs({
			cookie: {
				// store cookie for a day, without, it would be a session cookie
				expires: 0,
				load: 0
			}
		});*/
    
function seltyp()
{
  var value = $("input:radio:checked[name='artikelanlegentyp']").val();
  $("input:radio:not(:checked)[name='artikelanlegentyp']").each(function(){
    var val = $(this).val();
    $('#div'+val).hide();
  });
  $('#div'+value).show();
}

function addbestehendartikel(artikelid, link)
{
  if(confirm('Artikel wirklich hinzufügen?'))
  {
  
    var teilprojektid = $('#teilprojekt').val();
    $.ajax({
        url: 'index.php?module=projekt&action=dashboard&cmd=addartikel&id=[ID]',
        type: 'POST',
        dataType: 'text',
        data: { artikel: artikelid, teilprojekt: teilprojektid},
        success: function(data) {
          $('#jsmessage').html(data);
          $('#jsmessage').find('#artcmd').each(function(){$('#reloaddiv').val(1)});
        },
        beforeSend: function() {

        }
    });
  
  }
}

function selectteilprojekt()
{
  $('#iframeupload').contents().find('#uploadteilprojekt').val($('#teilprojekt').val());
}

function addstueckliste(artikelid, link)
{
  var result = 0;
  $.ajax({
      url: 'index.php?module=projekt&action=dashboard&cmd=checkartikel&id=[ID]',
      type: 'POST',
      dataType: 'json',
      data: { artikel: artikelid},
      success: function(data) {
        if(typeof data.typ != 'undefined')
        {
          if(data.typ != 'stueckliste')
          {
            if(result = window.prompt('Anzahl der Artikel?'))
            {
              result = parseInt(result);
              if(result > 0)
              {
                var teilprojektid = $('#teilprojekt').val();
                $.ajax({
                    url: 'index.php?module=projekt&action=dashboard&cmd=addstueckliste&id=[ID]',
                    type: 'POST',
                    dataType: 'text',
                    data: { artikel: artikelid, teilprojekt: teilprojektid,menge:result},
                    success: function(data) {
                      $('#jsmessage').html(data);
                      $('#jsmessage').find('#stcmd').each(function(){$('#reloaddiv').val(1)});
                    },
                    beforeSend: function() {

                    }
                });
              }
            }
          }else{
            var completest = window.confirm('Gesamte Stückliste Laden?');
            if(result = window.prompt('Anzahl der Artikel?'))
            {
              result = parseInt(result);
              if(result > 0)
              {
                var teilprojektid = $('#teilprojekt').val();
                $.ajax({
                    url: 'index.php?module=projekt&action=dashboard&cmd=addstueckliste&id=[ID]',
                    type: 'POST',
                    dataType: 'text',
                    data: { artikel: artikelid, teilprojekt: teilprojektid,menge:result,gesamtestueckliste:completest?1:0},
                    success: function(data) {
                      $('#jsmessage').html(data);
                      $('#jsmessage').find('#stcmd').each(function(){$('#reloaddiv').val(1)});
                    },
                    beforeSend: function() {

                    }
                });
              }
            }
          }
        }
      },
        beforeSend: function() {

        }
  });
}

$(document).ready(function() {
  seltyp();
  setTimeout(function(){selectteilprojekt();},500);
});
  [JAVASCRIPT]
  [DATATABLES]
  [AUTOCOMPLETE]
  [JQUERY]
</script>
<form id="frmpopupdiv" method="POST" action="">

<fieldset><legend>für Teilprojekt</legend>
<table class="mkTableFormular">
<tr><td>Artikel für Teilprojekt:</td><td><select id="teilprojekt" name="teilprojekt" onchange="selectteilprojekt()">[SELECTTEILPROJEKTE]</select></td></tr>
</table>
</fieldset>
[MESSAGE]
<div id="jsmessage"></div>

<input type="hidden" name="projektid" id="projektid" value="[ID]" /><input type="hidden" name="reloaddiv" id="reloaddiv" value="" />
<div style="width:100%;padding:10px">
<table width="100%">
<tr>
<td><input type="radio" onchange="seltyp()" name="artikelanlegentyp" value="new" [NEWCHECKED] />neuen Artikel / St&uuml;ckliste anlegen</td>
<td><input type="radio" onchange="seltyp()" name="artikelanlegentyp" value="stueckliste" [BESTEHENDCHECKED] />bestehenden Artikel / St&uuml;ckliste auswählen</td>
<!--<td><input type="radio" onchange="seltyp()" name="artikelanlegentyp" value="stueckliste" [STUECKLISTECHECKED] />neue Stückliste anlegen</td>-->
<td><input type="radio" onchange="seltyp()" name="artikelanlegentyp" value="artikelliste" [ARTIKELLISTECHECKED] />Artikelliste heraufladen</td>
</tr></table>
</div>
<div id="divnew" class="artikeldivs" style="display:none;">
<fieldset><legend>{|neuen Artikel anlegen|}</legend>

<table class="mkTableFormular">
  <tr>
    <td>Artikel (Deutsch):</td>
    <td colspan="4"><input type="text" id="name_de"  name="name_de"  value="[NAME_DE]"  size="70" maxlength="50"    maxlength=""  ></td>
  </tr>
  <tr>
    <td>Artikelgruppe</td>
    <td><select name="arttyp" size="1" id="arttyp"  onchange="">[ARTIKELGRUPPE]</select> </td>
    <td></td><td>Standardlieferant:</td>
    <td>[LIEFERANTSTART]<input name="adresse" value="[ADRESSE]" type="text" id="adresse" size="20">[LIEFERANTENDE]</td>
  </tr>
  <tr>
    <td>St&uuml;ckliste:</td><td><input type="checkbox" value="1" name="stueckliste" id="stueckliste" [STUECKLISTE] /></td>
  </tr>
  <tr>
    <td>Menge:</td>
    <td width="180"><input type="text" id="menge" name="menge" value="[MENGE]"  size="20" maxlength=""  ></td>
    <td width="20">&nbsp;</td><td width="150">Projekt</td>
    <td width="170">[PROJEKTSTART]<input name="projekt" id="projekt" value="[PROJEKT]" type="text" size="20">[PROJEKTENDE]</td>
  </tr>
[LIEFERSCHEINIF]
[LIEFERSCHEINELSE]
  <tr>
    <td>EK Preis (netto):</td><td width="180"><input type="text" id="preis"  class="0"  name="preis"  value="[PREIS]"  size="20"  maxlength=""  ></td>
    
    <td width="20">&nbsp;</td>
    <td width="150">Erm&auml;&szlig;igte Umsatzsteuer:</td>
    <td width="170"><input type="checkbox" name="umsatzsteuerklasse" value="1" [UMSATZSTEUERKLASSE]></td>
  </tr>
  <tr><td>VK Preis (netto):</td><td width="180"><input type="text" id="vkpreis"  class="0"  name="vkpreis"  value="[VKPREIS]"  size="20"  maxlength=""  ></td>
  <td>
  </tr>
[LIEFERSCHEINENDIF]
  <tr>
    <td>Lagerartikel:</td><td width="180"><input type="checkbox" name="lagerartikel" value="1" [LAGERARTIKEL]></td>
    <td width="20">&nbsp;</td><td width="150"></td><td width="170"></td>
  </tr>
  <tr>
    <td>Artikelbeschreibung (DE):</td><td colspan="4"><textarea rows="2" id="kurztext_de" class="" name="kurztext_de" cols="70">[KURZTEXT_DE]</textarea></td>
  </tr>
  <tr>
    <td>Interner Kommentar:</td><td colspan="4"><textarea rows="2" id="internerkommentar" class="" name="internerkommentar" cols="70">[INTERNERKOMMENTAR]</textarea></td>
  <tr>
  <tr>
    <td></td><td colspan="4"></td>
  <tr>
</table>
</fieldset>
</div>
<div id="divbestehend" class="artikeldivs" style="display:none;">

[BESTEHENDLISTE]
</div>
<div id="divstueckliste" class="artikeldivs" style="display:none;">
[STUECKLISTELISTE]

</div>
<div id="divartikelliste" class="artikeldivs" style="display:none;">
<iframe id="iframeupload" src="index.php?module=projekt&action=dashboard&id=[ID]&cmd=upload" width="100%" height="600px" frameborder="0">

</iframe>
</div>

[TAB1]
[TAB1NEXT]
</form>
