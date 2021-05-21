<div id="tabs">
  <ul>
    <li><a href="#tabs-1">{|Gutschriften|}</a></li>
    <li><a href="#tabs-2">{|nicht versendete|}</a></li>
    <li><a href="#tabs-3">{|in Bearbeitung|}</a></li>
  </ul>
	<div id="tabs-1">

		<div class="filter-box filter-usersave">
			<div class="filter-block filter-inline">
				<div class="filter-title">{|Filter|}</div>
				<ul class="filter-list">
					<li class="filter-item">
						<label for="gutschriftoffen" class="switch">
							<input type="checkbox" id="gutschriftoffen">
							<span class="slider round"></span>
						</label>
						<label for="gutschriftoffen">&nbsp;{|Alle offenen Gutschriften|}</label>
					</li>
					<li class="filter-item">
						<label for="gutschriftheute" class="switch">
							<input type="checkbox" id="gutschriftheute">
							<span class="slider round"></span>
						</label>
						<label for="gutschriftheute">&nbsp;{|Alle Gutschriften von heute|}</label>
					</li>
					<li class="filter-item">
						<label for="gutschriftnichterledigt" class="switch">
							<input type="checkbox" id="gutschriftnichterledigt">
							<span class="slider round"></span>
						</label>
						<label for="gutschriftnichterledigt">&nbsp;{|Alle nicht erledigten Gutschriften|}</label>
					</li>
				</ul>
			</div>
		</div>


		[MESSAGE]

		<form method="post" action="#">
			[TAB1]
			<fieldset>
				<legend>{|Stapelverarbeitung|}</legend>
				<input type="checkbox" id="auswahlalle" onchange="alleauswaehlen();" />&nbsp;{|alle markieren|}&nbsp;
				<select id="sel_aktion" name="sel_aktion">
					<option value="">{|bitte w&auml;hlen|} ...</option>
					<option value="erledigtam">{|als erledigt markieren|}</option>
					<option value="offen">{|erledigt Markierung entfernen|}</option>
					<option value="mail">{|per Mail versenden|}</option>
					<option value="versendet">{|als versendet markieren|}</option>
					<option value="pdf">{|Sammel-PDF|}</option>
					<option value="drucken">{|drucken|}</option>
				</select>&nbsp;
				{|Drucker|}: <select name="seldrucker">[SELDRUCKER]</select>&nbsp;
				<input type="submit" class="btnBlue" name="ausfuehren" value="{|ausf&uuml;hren|}" />
			</fieldset>
		</form>
	</div>

	<div id="tabs-2">
		[TAB2]
	</div>

	<div id="tabs-3">
		[TAB3]
	</div>

</div>

<script>
function alleauswaehlen()
{
  var wert = $('#auswahlalle').prop('checked');
  $('#gutschriften').find(':checkbox').prop('checked',wert);
}
</script>

