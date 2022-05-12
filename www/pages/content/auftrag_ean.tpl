<br><br>
<center>
<form action="" method="post">
Artikelnummern zur schnelleren Auftragseingabe scannen:
	<br><br>
	<label for="inputmenge">{|Menge|}:</label>&nbsp;<input type="text" size="10" name="menge" id="inputmenge" placeholder="1">&nbsp;
	<label for="scanner">{|Scannen|}:</label> <input type="text" name="scanner" size="20" id="scanner">&nbsp;

	<input type="checkbox" id="sumposition" name="sumposition" value="1" [SUMPOSITION] />
	<label for="sumposition">{|Gleiche Artikel summieren bei nacheinander Eingabe|}</label>
	<input type="submit" value="OK">
	[GESCANNTEARTIKEL]
	[POSITIONENSPEICHERN]
</form>
<script type="text/javascript">document.getElementById("scanner").focus(); </script>
