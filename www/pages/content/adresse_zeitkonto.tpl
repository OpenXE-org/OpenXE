
<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1"></a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">

	<div class="filter-box filter-usersave">
		<div class="filter-block filter-inline">
			<div class="filter-title">{|Filter|}</div>
			<ul class="filter-list">
				<li class="filter-item">
					<label for="kunden" class="switch">
						<input type="checkbox" id="kunden" title="auf Kundenkonto gebucht">
						<span class="slider round"></span>
					</label>
					<label for="kunden">{|nur auf Kundenkonto gebuchte Zeiten|}</label>
				</li>
				<li class="filter-item">
					<label for="archiviert" class="switch">
						<input type="checkbox" id="archiviert" title="nur abgeschlossen">
						<span class="slider round"></span>
					</label>
					<label for="archiviert">{|nur abgeschlossen|}</label>
				</li>
			</ul>
		</div>
	</div>

	<form action="index.php?module=abrechnung&action=zeiterfassung&back=[BACK]&id=[ID]" method="post">
		[MESSAGE]
		[OFFENE]
	</form>
</div>


</div>

