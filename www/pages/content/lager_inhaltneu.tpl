<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABNAME]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
<form method="post">


	<div class="row">
    <div class="row-height">
    <div class="col-xs-12 col-md-9 col-md-height">
    <div class="inside inside-full-height">

			<div class="filter-box filter-usersave">
				<div class="filter-block filter-inline">
					<div class="filter-title">{|Filter|}</div>
					<ul class="filter-list">
						<li class="filter-item">
							<label for="lager_platz_inhalt">{|Lagerplatz|}:</label>
							<input type="text" name="lager_platz_inhalt" id="lager_platz_inhalt" value="[LAGER_PLATZ_INHALT]">
						</li>
						<li class="filter-item">
							<label for="artikel_inhalt">{|Artikel|}:</label>
							<input type="text" name="artikel_inhalt" id="artikel_inhalt" value="[ARTIKEL_INHALT]" size="40">
						</li>
					</ul>
				</div>
			</div>

    </div>
    </div>
    <div class="col-xs-12 col-md-3 col-md-height">
    <div class="inside inside-full-height">
        <fieldset>
            <legend>{|Dokumente|}</legend>
        	<table><tr><td><input type="submit" style="width:5em" name="pdf" id="pdf" value="PDF"></td><td><input type="checkbox" style="vertical-align:middle" name="nachartikel" id="nachartikel" value="1">nach Artikel gruppieren</td></tr></table>
        </fieldset>
    </div>
    </div>
    </div>
    </div>

</form>


[TAB1]
</div>

<!-- tab view schließen -->
</div>

<script type="text/javascript">
function updateLiveTable(i) {
    var oTableL = $('#lager_inhaltlist').dataTable();
    oTableL.fnFilter('%');
    oTableL.fnFilter('');   
}
</script>