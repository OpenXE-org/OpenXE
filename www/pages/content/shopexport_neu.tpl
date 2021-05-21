<!-- gehort zu tabview -->
<div id="tabs">
	<ul>
		<li><a href="#tabs-1"><!--[TABTEXT]--></a></li>
	</ul>
	<!-- ende gehort zu tabview -->

	<!-- erstes tab -->
	<div id="tabs-1">

		[MESSAGE]
		[TAB1]
		<fieldset id="shopexportcreate">
			<legend>{|Auswahl|}</legend>
			<div id="searchdiv">
				<label for="suche">{|Suche|}:</label> <input type="text" id="suche" />
			</div>
			[MODULEINSTALLIERT]
		</fieldset>
		[BEFOREMODULESTOBUY]
		<fieldset>
			<legend>{|Kaufschnittstellen|}</legend>
			[MODULEVERFUEGBAR]
			<br/>
		</fieldset>
	  [AFTERMODULESTOBUY]
		[TAB1NEXT]
	</div>

	<!-- tab view schließen -->
</div>

<click-by-click-assistant
				id="onlineshop-create"
				v-if="showAssistant"
				@close="showAssistant = false"
				:pages="pages"
				:allowClose="allowClose"
				:pagination="pagination">
</click-by-click-assistant>
<click-by-click-assistant
				id="onlineshop-booster"
				v-if="showAssistant"
				@close="showAssistant = false"
				:pages="pages"
				:allowClose="allowClose"
				:pagination="pagination">
</click-by-click-assistant>
