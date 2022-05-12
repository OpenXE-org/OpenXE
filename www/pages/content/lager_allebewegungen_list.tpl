<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABNAME]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">

	<div class="filter-box filter-usersave">
		<div class="filter-block filter-inline">
			<div class="filter-title">{|Filter|}</div>
			<ul class="filter-list">
				<li class="filter-item">
					<label for="lager_bewegung_alle">{|Lager|}:</label>
					<input type="text" name="lager_bewegung_alle" id="lager_bewegung_alle" value="[LAGER_BEWEGUNG_ALLE]">
				</li>
				<li class="filter-item">
					<label for="lager_platz_bewegung_alle">{|Lagerplatz|}:</label>
					<input type="text" name="lager_platz_bewegung_alle" id="lager_platz_bewegung_alle" value="[LAGER_PLATZ_BEWEGUNG_ALLE]">
				</li>
				<li class="filter-item">
					<label for="artikel_bewegung_alle">{|Artikel|}:</label>
					<input type="text" name="artikel_bewegung_alle" id="artikel_bewegung_alle" value="[ARTIKEL_BEWEGUNG_ALLE]" size="40">
				</li>
			</ul>
		</div>
	</div>

[TAB1]
</div>

<!-- tab view schließen -->
</div>

<script type="text/javascript">
function updateLiveTable(i) {
    var oTableL = $('#lager_allebewegungenlist').dataTable();
    oTableL.fnFilter('%');
    oTableL.fnFilter('');   
}
</script>


