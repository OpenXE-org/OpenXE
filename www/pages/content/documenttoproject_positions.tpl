<div id="tabs">
	<ul>
		<li><a href="#tabs-1">[TABTEXT]</a></li>
	</ul>

	<div id="tabs-1">
	<fieldset>
		<legend>Positionen</legend>
		[MESSAGE]

		<form action="index.php?module=documenttoproject&action=project&documenttype=[DOCUMENTTYPE]&documentid=[DOCUMENTID]" method="post">
		<input type="submit" value="Zurück" class="btnBlue">
		</form>

		<br/>
		[PROJECT]
		<br/><br/>

		<form action="index.php?module=documenttoproject&action=do&documenttype=[DOCUMENTTYPE]&documentid=[DOCUMENTID]" method="post">
		  <input type="hidden" name="projectid" value="[PROJECTID]">
			[TAB1]
			[TAB1NEXT]

			<br/><br/>
			<input type="submit" value="Ausführen" class="btnGreen">
		</form>
</fieldset>

	</div>

