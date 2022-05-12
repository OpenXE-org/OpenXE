<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1"><!--[TABTEXT]--></a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]


	<div class="filter-box filter-usersave">

		<div class="filter-block filter-reveal">
			<div class="filter-title">{|Belege|}<span class="filter-icon"></span></div>
			<ul class="filter-list">
				[VORAUFTRAG]<li class="filter-item"><input type="checkbox" value="1" id="auftrag" /><label for="auftrag">{|Auftrag|}</label></li>[NACHAUFTRAG]
				[VORRECHNUNG]<li class="filter-item"><input type="checkbox" value="1" id="rechnung" /><label for="rechnung">{|Rechnung|}</label></li>[NACHRECHNUNG]
				[VORGUTSCHRIFT]<li class="filter-item"><input type="checkbox" value="1" id="gutschrift" /><label for="gutschrift">{|Gutschrift|}</label></li>[NACHGUTSCHRIFT]
				[VORANGEBOT]<li class="filter-item"><input type="checkbox" value="1" id="angebot" /><label for="angebot">{|Angebot|}</label></li>[NACHANGEBOT]
				[VORBESTELLUNG]<li class="filter-item"><input type="checkbox" value="1" id="bestellung" /><label for="bestellung">{|Bestellung|}</label></li>[NACHBESTELLUNG]
				[VORLIEFERSCHEIN]<li class="filter-item"><input type="checkbox" value="1" id="lieferschein" /><label for="lieferschein">{|Lieferschein|}</label></li>[NACHLIEFERSCHEIN]
				[VORPRODUKTION]<li class="filter-item"><input type="checkbox" value="1" id="produktion" /><label for="produktion">{|Produktion|}</label></li>[NACHPRODUKTION]
			</ul>
		</div>

		<div class="filter-block filter-inline">
			<div class="filter-title">{|Status|}</div>
			<ul class="filter-list">
				<li class="filter-item">
					<label for="statusoffen" class="switch">
						<input type="checkbox" value="1" id="statusoffen" />
						<span class="slider round"></span>
					</label>
					<label for="statusoffen">{|offen|}</label>
				</li>
				<li class="filter-item">
					<label for="statusabgeschlossen" class="switch">
						<input type="checkbox" value="1" id="statusabgeschlossen" />
						<span class="slider round"></span>
					</label>
					<label for="statusabgeschlossen">{|abgeschlossen|}</label>
				</li>
			</ul>
		</div>
	</div>
[TAB1]
[TAB1NEXT]
</div>

<!-- tab view schließen -->
</div>

