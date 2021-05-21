
<script type="text/javascript">

document.onkeydown = function(evt) {
    evt = evt || window.event;
    if (evt.keyCode == 27) {
        //alert("Escape");
    }
};
</script>

<div id="tabs">
<ul>
        <li><a href="#tabs-1">{|Auftr&auml;ge|}</a></li>
        <li><a href="#tabs-5">{|in Bearbeitung|}</a></li>
 </ul>
<div id="tabs-1">

	<div class="filter-box filter-usersave">

		<div class="filter-block filter-reveal">
			<div class="filter-title">{|Filter|}<span class="filter-icon"></span></div>
			<ul class="filter-list">
				<li class="filter-item"><input type="checkbox" name="artikellager" id="artikellager" value="A"><label for="artikellager">{|Artikel fehlt|}</label></li>
				<li class="filter-item"><input type="checkbox" name="teillieferung" id="teillieferung"><label for="teillieferung">{|Teillieferung m&ouml;gl.|}</label></li>
				<li class="filter-item"><input type="checkbox" id="ustpruefung"><label for="ustpruefung">{|UST-Pr&uuml;f. fehlt|}</label></li>
				<li class="filter-item"><input type="checkbox" id="zahlungseingang"><label for="zahlungseingang">{|Zahlung ok|}</label></li>
				<li class="filter-item"><input type="checkbox" id="zahlungseingangfehlt"><label for="zahlungseingangfehlt">{|Zahlung fehlt|}</label></li>
				<li class="filter-item"><input type="checkbox" id="portofehlt"><label for="portofehlt">{|Porto fehlt|}</label></li>
				<li class="filter-item"><input type="checkbox" id="manuellepruefung"><label for="manuellepruefung">{|Manuelle Pr&uuml;f.|}</label></li>
				<li class="filter-item"><input type="checkbox" id="teilzahlung"><label for="teilzahlung">{|Teilzahlung|}</label></li>
				<li class="filter-item"><input type="checkbox" id="ohnerechnung"><label for="ohnerechnung">{|ohne Rechnung|}</label></li>
				<li class="filter-item"><input type="checkbox" id="auftragheute"><label for="auftragheute">{|Heute|}</label></li>
				<li class="filter-item"><input type="checkbox" id="autoversandfehlt"><label for="autoversandfehlt">{|Auto-Versand fehlt|}</label></li>
				<li class="filter-item"><input type="checkbox" id="autoversandok"><label for="autoversandok">{|Auto-Versand OK|}</label></li>
				<li class="filter-item"><input type="checkbox" id="fastlanea"><label for="fastlanea">{|Fast-Lane|}</label></li>
				<li class="filter-item"><input type="checkbox" id="tolate"><label for="tolate">{|Lieferdatum &uuml;berf&auml;llig|}</label></li>
				[HOOK_FILTER_1]
			</ul>
		</div>

		<div class="filter-block filter-inline">
			<div class="filter-title">{|Status|}</div>
			<ul class="filter-list">
				<li class="filter-item">
					<label for="auftragoffene" class="switch">
						<input type="checkbox" id="auftragoffene">
						<span class="slider round"></span>
					</label>
					<label for="auftragoffene">{|offen|}</label>
				</li>
				<li class="filter-item">
					<label for="auftragstornierte" class="switch">
						<input type="checkbox" id="auftragstornierte">
						<span class="slider round"></span>
					</label>
					<label for="auftragstornierte">{|storniert|}</label>
				</li>
				<li class="filter-item">
					<label for="auftragabgeschlossene" class="switch">
						<input type="checkbox" id="auftragabgeschlossene">
						<span class="slider round"></span>
					</label>
					<label for="auftragabgeschlossene">{|abgeschlossen|}</label>
				</li>
			</ul>
		</div>

		[EXTRAFILTER]

	</div>



[MESSAGE]
[AUTOVERSANDBERECHNEN]
<form method="post" action="#" id="frmorder">
[TAB1]
<i style="float:right; font-size:10px;color:#6d6d6f;"><span style="color:blue">*</span> {|Freitext vorhanden|} <span style="color:red">*</span> {|Interne Bemerkung vorhanden|} (F) {|Lieferung der Zentrale an Fililale|} (FL) {|&quot;Fast-Lane&quot; aktiviert|}</i>

	  <div class="clear"></div>	
<fieldset><legend>{|Stapelverarbeitung|}</legend>
<input type="checkbox" id="auswahlalle" onchange="alleauswaehlen();" />&nbsp;{|alle markieren|}
&nbsp;<select id="sel_aktion" name="sel_aktion">
	<option value="">{|bitte w&auml;hlen|} ...</option>
	<option value="freigeben">{|als freigegeben markieren|}</option>
	<option value="storniert">{|als storniert markieren|}</option>
	<option value="versandfreigeben">{|Option &quot;F&uuml;r den Versand&quot; freigeben|}</option>
	<option value="versandentfernen">{|Option &quot;F&uuml;r den Versand&quot; entfernen|}</option>
	<option value="fastlane">{|Option "Fast-Lane" setzen|}</option>
	<option value="fastlaneentfernen">{|Option &quot;Fast-Lane&quot; entfernen|}</option>
	<option value="mail">{|per Mail versenden|}</option>
	<option value="versendet">{|als versendet markieren|}</option>
	<option value="stapelproduktionweiter">{|als Produktion weiterf&uuml;hren|}</option>
	<option value="pdf">{|Sammel-PDF|}</option>
	<option value="drucken">{|drucken|}</option>
	[HOOK_SEL_ACTION]
</select>&nbsp;{|Drucker|}: <select name="seldrucker">[SELDRUCKER]</select>&nbsp;<input type="submit" class="btnBlue" name="ausfuehren" id="ausfuehren" value="ausf&uuml;hren" />
</fieldset>
</form>
</div>
<!--
<div id="tabs-3">
<table height="80" width="100%"><tr><td></td></tr></table>
[TAB33]
<table width="100%"><tr><td><input type="submit" value="Auto-Versand starten" name="submit">
</td><td align="right"><input type="submit" value="andere Option w&auml;hlen" name="submit"></td></tr></table>
</div>
-->
<div id="tabs-4">
[TAB4]
</div>
<div id="tabs-5">
[TAB5]
</div>




</div>
<div id="lagermehrpopup" style="display:none;">
<div id="lagermehrpopupinhalt">
</div>
</div>
<script>
  $(document).ready(function() {
    $('#autoalle').on('change',function(){
      var wert = $(this).prop('checked');
      $('#auftraegeoffeneauto').find('input[type="checkbox"]').prop('checked',wert);
      $('#auftraegeoffeneauto').find('input[type="checkbox"]').first().trigger('change');
    });
    
  $("#lagermehrpopup").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    minWidth:500,
    minHeight:550,
    autoOpen: false,
    buttons: {
      "OK": function() {
        $(this).dialog('close');
      }
    }
  });

  });
function alleauswaehlen()
{
  var wert = $('#auswahlalle').prop('checked');
  $('#auftraege').find(':checkbox').prop('checked',wert);
}
  
function lagermehr(artikel)
{
    $.ajax({
        url: 'index.php?module=auftrag&action=minidetail&cmd=lager&id='+artikel,
        data: {},
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {
          $('#lagermehrpopupinhalt').html(data.inhalt);
          $("#lagermehrpopup").dialog('open');
        }
    });
}
</script>
