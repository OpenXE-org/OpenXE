<div id="tabs">
    <ul>
        <li><a href="#tabs-1">{|Lieferscheine|}</a></li>
        <li><a href="#tabs-3">{|in Bearbeitung|}</a></li>
    </ul>
<div id="tabs-1">

	<div class="filter-box filter-usersave">

		<div class="filter-block filter-reveal">
			<div class="filter-title">{|Filter|}<span class="filter-icon"></span></div>
			<ul class="filter-list">
				<li class="filter-item"><input type="checkbox" id="ohne_rechnung"><label for="ohne_rechnung">{|ohne Rechnung|}</label></li>
				<li class="filter-item"><input type="checkbox" id="nichtausgelagert"><label for="nichtausgelagert">{|nicht ausgelagert|}</label></li>
				<li class="filter-item"><input type="checkbox" id="lieferscheinoffen"><label for="lieferscheinoffen">{|nur freigegebenen|}</label></li>
				<li class="filter-item"><input type="checkbox" id="lieferscheinheute"><label for="lieferscheinheute">{|von heute|}</label></li>
				<li class="filter-item"><input type="checkbox" id="anlieferanten"><label for="anlieferanten">{|an Lieferanten|}</label></li>
			</ul>
		</div>
	</div>

[MESSAGE]
<form method="post" action="#">
[TAB1]
<fieldset><legend>{|Stapelverarbeitung|}</legend>
<input type="checkbox" id="auswahlalle" onchange="alleauswaehlen();" />&nbsp;{|alle markieren|}
&nbsp;<select id="sel_aktion" name="sel_aktion">
<option value="">{|bitte w&auml;hlen|} ...</option>
<option value="offen">{|als offen markieren|}</option>
<option value="versendet">{|als versendet markieren|}</option>
<option value="storniert">{|als storniert markieren|}</option>
<option value="versanduebergabe">{|in Versand geben|}</option>
<option value="pdf">{|Sammel-PDF|}</option>
<option value="drucken">{|drucken|}</option>
</select>&nbsp;{|Drucker|}: <select name="seldrucker">[SELDRUCKER]</select>&nbsp;<input type="submit" class="btnBlue" name="ausfuehren" value="{|ausf&uuml;hren|}" />
</fieldset>
</form>
</div>


<div id="tabs-3">
[TAB3]
</div>

</div>
<script>
function alleauswaehlen()
{
  var wert = $('#auswahlalle').prop('checked');
  $('#lieferscheine').find(':checkbox').prop('checked',wert);
}

</script>

