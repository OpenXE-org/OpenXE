<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABNAME]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]

	<div class="filter-box filter-usersave">
		<div class="filter-block filter-inline">
			<div class="filter-title">{|Filter|}</div>
			<ul class="filter-list">
				<li class="filter-item">
					<label for="lager_platz_bewegung">{|Lagerplatz|}:</label>
					<input type="text" name="lager_platz_bewegung" id="lager_platz_bewegung" value="[LAGER_PLATZ_BEWEGUNG]">
				</li>
				<li class="filter-item">
					<label for="artikel_bewegung">{|Artikel|}:</label>
					<input type="text" name="artikel_bewegung" id="artikel_bewegung" value="[ARTIKEL_BEWEGUNG]" size="40">
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
    var oTableL = $('#lager_bewegunglist').dataTable();
    oTableL.fnFilter('%');
    oTableL.fnFilter('');   
}
</script>

