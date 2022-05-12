<div id="tabs">
	<ul>
		<li><a href="#tabs-1">[TABTEXT]</a></li>
	</ul>

	<div id="tabs-1">

	[MESSAGE]
	[TAB1]
	[TAB1NEXT]

	<fieldset>
		<legend>Projekteinstellungen</legend>
		<form action="index.php?module=documenttoproject&action=positions&documenttype=[DOCUMENTTYPE]&documentid=[DOCUMENTID]" method="post">
			<table>
				<tr>
					<td><input type="radio" name="selection" id="selection1" value="existing" checked></td><td><label for="selection1">Bestehendes Projekt verwenden:</label></td><td><input type="text" name="project" id="project"></td>
				</tr>
				<tr>
					<td><input type="radio" name="selection" id="selection2" value="new" checked></td><td><label for="selection2">Neues Projekt erstellen</label></td><td></td>
				</tr>
				<tr>
					<td colspan="3"><input type="submit" value="Weiter" class="btnBlue"></td>
				</tr>
			</table>



		</form>
	</fieldset>
	</div>

