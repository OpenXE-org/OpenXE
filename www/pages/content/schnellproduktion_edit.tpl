<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]
<div id="ajaxmessage"></div>
<table width="100%" align="center" style="background-color:#cfcfd1;">
<tr>
<td width="33%"></td>
<td align="center" nowrap><b style="font-size: 14pt">Schnellproduktion <font color="blue">[ARTIKELNUMMER]</font></b>&nbsp;[ARTIKELNAME]</td>
<td width="33%" align="right"></td>
</tr>
</table>
<form method="POST" id="frmsp" onkeypress="return event.keyCode != 13;">
<table style="display:none;">
  <tr>
    <td>Menge: </td><td><input type="hidden" size="8" name="menge" id="menge" value="[NOTWENDIG]" /> von ben&ouml;tigten [NOTWENDIG]</td>
    <td><input type="button" id="aendern" onclick="chmenge();" value="&auml;ndern" /></td>
  </tr>
</table>
[TAB1]
[TAB1NEXT]
<table width="100%">
  <tr>
    
    <td width="100%" style="text-align:center;">[VORENTNEHMEN][HIDDENCMD]<input type="submit" name="entnehmen" style="height:50px;font-size:200%;min-width:50%" id="entnehmen" value="Artikel entnehmen" />[NACHENTNEHMEN]</td>
  </tr>
</table>

<div style="[PRODUKTIONSDIV]">
<fieldset>
<label>Produktion abschlie&szlig;en</label>
<table>
  <tr>
    <td>Produktionsmenge: <input type="text" name="prodmenge" id="prodmenge" value="[PRODMENGE]" />[PRODMENGETEXT]</td>
    <td>Lager: </td><td><input type="input" name="lager" id="lager" value="[LAGER]" /></td>
    <td><input type="submit" id="speichern" name="speichern" value="Produktionsartikel einlagern" /></td>
  </tr>
</table>
</fieldset>
</div>
</form>
</div>

<!-- tab view schließen -->
</div>
<script>

function chmenge()
{
  var menge = $('#menge').val();
  $('input.menge').each(function(){
    var elid = this.id.split('_');
    var einzel = $('#einzelmenge_'+elid[1]).val();
    var entnommen = parseInt($('#ausgelagert_'+elid[1]).html());
    var lpi = parseInt($('#lagermenge_'+elid[1]).html());
    var neu = einzel * menge - entnommen;
    if(neu < 0)neu = 0;
    $('#fehlend_'+elid[1]).html(neu);
    if (neu > lpi)neu = lpi; 
    $(this).val(neu);
  });
}


function getlagerinhalt(ele)
{
  var elea = ele.id.split('_');
  var art = $('#artikel_'+elea[1]).val();
  var lagerplatz = $('#lagerplatz_'+elea[1]).val();
  $.ajax({
    url: 'index.php?module=schnellproduktion&action=getlagerinhalt',
    type: 'POST',
    dataType: 'json',
    data: { artikel: art, lp:lagerplatz },
    success: function(data) {
      if(data == null || typeof data.anzahl == "undefined")
      {
        $('#ajaxmessage').html('<div class="error">Fehlende Rechte: Wert konnte nicht gesetzt werden!</div>');
      }else
      {
        $('#lagermenge_'+elea[1]).html(data.anzahl);
        var auslagern = $('#auslagern_'+elea[1]).val();
        var fehlend = $('#fehlend_'+elea[1]).html();
        auslagern = fehlend;
        if(auslagern > data.anzahl)
        {
          auslagern = data.anzahl;
        }
        $('#auslagern_'+elea[1]).val(auslagern);
      }
    },
    error: function() {
      $('#ajaxmessage').html('<div class="error">Fehlende Rechte: Wert konnte nicht gesetzt werden!</div>');
      }
  });
}

setInterval(function(){
  $('input.lagerplatz').each(function(){
    var ela = this.id.split('_');
    if($(this).val() != $('#lagerplatzalt_'+ela[1]).val())
    {
      getlagerinhalt(this);
      $('#lagerplatzalt_'+ela[1]).val($(this).val());
    }
  });
},500);

$('#lager').on('keyup',function(event){
  if(event.keyCode == 13)
  {
    $('#speichern').click();
  }
});

[ADDPRODJS]

$('input.lagerplatz').on('keyup',function(event){
  var el = null
  $(this).parents('tr').first().next().find('input.lagerplatz').first().each(function(){el = this});
  if(el != null)
  {
    $(el).focus();
  }else{
    $('#entnehmen').click();
  }
});

</script>