<div id="tabs">
    <ul>
        <li><a href="#tabs-1">{|Bestellungen|}</a></li>
        <li><a href="#tabs-2">{|in Bearbeitung|}</a></li>
    </ul>
<div id="tabs-1">

	<div class="filter-box filter-usersave">
		<div class="filter-block filter-inline">
			<div class="filter-title">{|Filter|}</div>
			<ul class="filter-list">
				<li class="filter-item">
					<label for="bestellungenoffen" class="switch">
						<input type="checkbox" id="bestellungenoffen">
						<span class="slider round"></span>
					</label>
					<label for="bestellungenoffen">&nbsp;{|Offene|}</label>
				</li>
				<li class="filter-item">
					<label for="bestellungnichtbestaetigt" class="switch">
						<input type="checkbox" id="bestellungnichtbestaetigt">
						<span class="slider round"></span>
					</label>
					<label for="bestellungnichtbestaetigt">&nbsp;{|nicht best&auml;tigt|}</label>
				</li>
				<li class="filter-item">
					<label for="bestellungfehlt" class="switch">
						<input type="checkbox" id="bestellungfehlt">
						<span class="slider round"></span>
					</label>
					<label for="bestellungfehlt">&nbsp;{|Lieferung fehlt|}</label>
				</li>
				<li class="filter-item">
					<label for="bestellungversendet" class="switch">
						<input type="checkbox" id="bestellungversendet">
						<span class="slider round"></span>
					</label>
					<label for="bestellungversendet">&nbsp;{|versendet|}</label>
				</li>
				<li class="filter-item">
					<label for="bestellungstorniert" class="switch">
						<input type="checkbox" id="bestellungstorniert">
						<span class="slider round"></span>
					</label>
					<label for="bestellungstorniert">&nbsp;{|stornierte|}</label>
				</li>
				<li class="filter-item">
					<label for="bestellunglieferdatumueberschritten" class="switch">
						<input type="checkbox" id="bestellunglieferdatumueberschritten">
						<span class="slider round"></span>
					</label>
					<label for="bestellunglieferdatumueberschritten">&nbsp;{|Lieferdatum &uuml;berschritten|}</label>
				</li>
				<li class="filter-item">
					<label for="bestellungohneverbindlichkeit" class="switch">
						<input type="checkbox" id="bestellungohneverbindlichkeit">
						<span class="slider round"></span>
					</label>
					<label for="bestellungohneverbindlichkeit">&nbsp;{|ohne Verbindlichkeiten|}</label>
				</li>
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
			<option value="mail">{|per Mail versenden|}</option>
			<option value="freigeben">{|als freigegeben markieren|}</option>
			<option value="versendet">{|als versendet markieren|}</option>
			<option value="abgeschlossen">{|als abgeschlossen markieren|}</option>
			<option value="pdf">{|Sammel-PDF|}</option>
			<option value="drucken">{|drucken|}</option>
		</select>&nbsp;{|Drucker|}: <select name="seldrucker">[SELDRUCKER]</select>&nbsp;<input type="submit" class="btnBlue" name="ausfuehren" value="{|ausf&uuml;hren|}" />
	</fieldset>
</form>
</div>

<div id="tabs-2">
[TAB2]
</div>

</div>

<script>
    function alleauswaehlen()
    {
        var wert = $('#auswahlalle').prop('checked');
        $('#bestellungen').find(':checkbox').prop('checked',wert);
    }
</script>