
<div id="seriennummerndiaglog" style="display:none;">
  <form id="frmseriennummerndiaglog" method="POST">
    <table>
      <tr><td><input type="radio" name="serientyp" value="eigen" [SERIENTYPEIGEN] />&nbsp;manuell ab angegebener Startnummer:</td><td><input type="text" name="seriennummervoneigen" value="1" />&nbsp;<i>(Startnummer)</i></td></tr>
      <tr><td><input type="radio" name="serientyp" value="produkt" [SERIENTYPARTIKEL] />&nbsp;aus Stücklistenartikel:</td><td>[SERIENNUMMERARTIKEL]&nbsp;<i>(Vorschau eventuell nicht mehr aktuell)</i></td></tr>
      <tr><td><input type="radio" name="serientyp" value="nummernkreis" [SERIENTYPNUMMERNKREIS] />&nbsp;aus globalen Nummernkreis:</td><td>[SERIENNUMMERNNUMMERKREIS]&nbsp;<i>(Vorschau eventuell nicht mehr aktuell)</i></td></tr>
      <tr><td></td><td><input type="submit" name="seriennummergeneratorsubmit" value="Generieren" /></td></tr>
    </table>
  </form>
</div>


<div id="chargendiaglog" style="display:none;">
  
  <form method="POST" id="frmchargendiaglog">
    <table>
      <tr><td><input type="radio" name="chargentyp" id="chargentypeigen" value="eigen" [CHARGENTYPEIGEN] />&nbsp;manuell Charge vergeben:</td><td><input type="text" id="chargevoneigen" name="chargevoneigen" value="[CHARGEVONEIGEN]" /></i></td></tr>
      <tr><td><input type="radio" name="chargentyp" id="chargentypprodukt" value="produkt" [CHARGENTYPARTIKEL] />&nbsp;aus Stücklistenartikel:</td><td nowrap><input type="text" name="chargevonproduktcharge" id="chargevonproduktcharge" value="[CHARGENARTIKELCHARGE]" /></td></tr>
      <tr><td><input type="radio" name="chargentyp" id="chargentypnummernkreis" value="nummernkreis" [CHARGENTYPNUMMERNKREIS] />&nbsp;aus globalen Chargen Nummernkreis:</td><td><input type="text" id="chargevonnummernkreis" name="chargevonnummernkreis" value="[CHARGENNUMMERKREIS]" />&nbsp;<i>(Startnummer)</i></td></tr>
      <tr><td></td><td><input type="submit" name="chargengeneratorsubmit" value="Generieren" /></td></tr>
    </table>
  </form>
  
</div>

<script>
$(document).ready(function() {
  $('#chargevoneigen').on('change',function(){$('#chargentypeigen').prop('checked',true);});
  $('#chargevoneigen').on('click',function(){$('#chargentypeigen').prop('checked',true);});
  $('#chargevonproduktcharge').on('change',function(){$('#chargentypprodukt').prop('checked',true);});
  $('#chargevonproduktcharge').on('click',function(){$('#chargentypprodukt').prop('checked',true);});  
  $('#chargevonnummernkreis').on('change',function(){$('#chargentypnummernkreis').prop('checked',true);});
  $('#chargevonnummernkreis').on('click',function(){$('#chargentypnummernkreis').prop('checked',true);});
  
  $('#seriennummerngenerieren').on('click',function(){
    $('#seriennummerndiaglog').dialog({
				title: 'Seriennummer für Baugruppen generieren',
				width: 600
			});
  });

  $('#chargegenerieren').on('click',function(){
    $('#chargendiaglog').dialog({
				title: 'Chargen erfassen',
				width: 800
			});
  });
});
</script>
