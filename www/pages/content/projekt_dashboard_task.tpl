
[MESSAGE]
<div style="padding:10px;">

	<div class="filter-box filter-usersave">
		<div class="filter-block filter-inline">
			<div class="filter-title">{|Filter|}</div>
			<ul class="filter-list">
				<li class="filter-item">
					<label for="nuroffene" class="switch">
						<input type="checkbox" id="nuroffene" />
						<span class="slider round"></span>
					</label>
					<label for="nuroffene" class="switch">{|nur offene|}</label>
				</li>
				<li class="filter-item">
					<label for="nurabgeschlossene" class="switch">
						<input type="checkbox" id="nurabgeschlossene" />
						<span class="slider round"></span>
					</label>
					<label for="nurabgeschlossene" class="switch">{|nur abgeschlossene|}</label>
				</li>
			</ul>
		</div>
	</div>

	[TABLE]

	<table width="100%"><tr>
			<td width="50%">
				<fieldset style="height:40px"><legend>&nbsp;</legend>
					&nbsp;
				</fieldset>
			</td><td>
				<fieldset style="height:40px"><legend>{|Aktionen|}</legend>
					<input [SELECTDISABLED] type="button" value="neue Aufgabe anlegen" onclick="AufgabenEdit(0,'[PROJEKTKENNUNG]');" />
				</fieldset>
			</td></tr>
	</table>

</div>

[AUFGABENPOPUP]
