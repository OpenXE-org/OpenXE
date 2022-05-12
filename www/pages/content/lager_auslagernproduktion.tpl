<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1"><!--[TABTEXT]--></a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]
[TAB1]
[TAB1NEXT]
</div>

<!-- tab view schließen -->
</div>
<div id="chargenmhdpopup" style="display:none;">
<table>
<tr><td id="cmartikelnummer"></td><td><span id="cmmenge"></span> x entnehmen<input type="hidden" id="cmartikel" /><input type="hidden" id="cmlp" /><input type="hidden" id="cmlpid" /></td></tr>
<tr><td>Charge:</td><td><input type="text" id="cmchargemhd" /></td></tr>
</table>
</div>
<div id="chargenpopup" style="display:none;">
<table>
<tr><td id="cartikelnummer"></td><td><span id="cmenge"></span> x entnehmen<input type="hidden" id="cartikel" /><input type="hidden" id="clp" /><input type="hidden" id="clpid" /></td></tr>
<tr><td>Charge:</td><td><input type="text" id="ccharge" /></td></tr>
</table>
</div>
<div id="mhdpopup" style="display:none;">
<table>
<tr><td id="martikelnummer"></td><td><span id="mmenge"></span> x entnehmen<input type="hidden" id="martikel" /><input type="hidden" id="mlp" /><input type="hidden" id="mlpid" /></td></tr>
<tr><td>MHD:</td><td><input type="text" id="mmhd" /></td></tr>
</table>
</div>
<script>
$(document).ready(function() {
    $('#chargenpopup').dialog(
    {
      modal: true,
      autoOpen: false,
      minWidth: 940,
      title:'Charge auswählen',
      buttons: {
        AUSLAGERN: function()
        {
          window.location.href= 'index.php?module=lager&action=artikelfuerlieferungen&cmd=[CMD]&artikel=' + $('#cartikel').val() + '&menge=' + $('#cmenge').text() + '&projekt=&produktion=[PRODUKTION]&lagerplatzid=' + $('#clpid').val() + '&lager=' + $('#clp').val() + '&charge=' + $('#ccharge').val();
        },
        ABBRECHEN: function() {
          $(this).dialog('close');
        }
      },
      close: function(event, ui){
        
      }
    });
    $('#mhdpopup').dialog(
    {
      modal: true,
      autoOpen: false,
      minWidth: 940,
      title:'Mindesthaltbarkeitsdatum auswählen',
      buttons: {
        AUSLAGERN: function()
        {
          window.location.href='index.php?module=lager&action=artikelfuerlieferungen&cmd=[CMD]&artikel='+$('#martikel').val()+'&menge='+$('#mmenge').text()+'&projekt=&produktion=[PRODUKTION]&lagerplatzid='+$('#mlpid').val()+'&lager='+$('#mlp').val()+'&mhd='+$('#mmhd').val();
        },
        ABBRECHEN: function() {
          $(this).dialog('close');
        }
      },
      close: function(event, ui){
        
      }
    });
    $('#chargenmhdpopup').dialog(
    {
      modal: true,
      autoOpen: false,
      minWidth: 940,
      title:'Charge auswählen',
      buttons: {
        AUSLAGERN: function()
        {
          window.location.href='index.php?module=lager&action=artikelfuerlieferungen&cmd=[CMD]&artikel='+$('#cmartikel').val()+'&menge='+$('#cmmenge').text()+'&projekt=&produktion=[PRODUKTION]&lagerplatzid='+$('#cmlpid').val()+'&lager='+$('#cmlp').val()+'&mhdcharge='+$('#cmchargemhd').val();
        },
        ABBRECHEN: function() {
          $(this).dialog('close');
        }
      },
      close: function(event, ui){
        
      }
    });
    });
    function opencm(el, artikel, menge, lagerplatzid,lpid)
    {
      var tr = $(el).parents('tr').first();
      var nummer = $(tr).children('td').first().next().text();
      if(lagerplatzid == 0)
      {
        lagerplatzid = $(el).parents('td').first().prev().find('input').first().val();
        if(typeof lagerplatzid == 'undefined')lagerplatzid = $(el).val();
      }
      $('#cmartikel').val(artikel);
      $('#cmmenge').val(menge);
      $('#cmartikelnummer').text(nummer);
      $('#cmchargemhd').val('');
      $('#cmlp').val(lagerplatzid);
      $('#cmlpid').val(lpid);
      $('#chargenmhdpopup').dialog('open');
    }
    function openc(el, artikel, menge, lagerplatzid,lpid)
    {
      var tr = $(el).parents('tr').first();
      var nummer = $(tr).children('td').first().next().text();
      if(lagerplatzid == 0)
      {
        lagerplatzid = $(el).parents('td').first().prev().find('input').first().val();
        if(typeof lagerplatzid == 'undefined')lagerplatzid = $(el).val();
      }
      $('#cartikel').val(artikel);
      $('#cmenge').text(menge);
      $('#cartikelnummer').text(nummer);
      $('#ccharge').val('');
      $('#clp').val(lagerplatzid);
      $('#clpid').val(lpid);
      $('#chargenpopup').dialog('open');
    }
    function openm(el, artikel, menge, lagerplatzid,lpid)
    {
      var tr = $(el).parents('tr').first();
      var nummer = $(tr).children('td').first().next().text();
      if(lagerplatzid == 0)
      {
        lagerplatzid = $(el).parents('td').first().prev().find('input').first().val();
        if(typeof lagerplatzid == 'undefined')lagerplatzid = $(el).val();
      }
      $('#mmenge').text(menge);
      $('#martikel').val(artikel);
      $('#martikelnummer').text(nummer);
      $('#mmhd').val('');
      $('#mlp').val(lagerplatzid);
      $('#mlpid').val(lpid);
      $('#mhdpopup').dialog('open');
    }    
    
</script>