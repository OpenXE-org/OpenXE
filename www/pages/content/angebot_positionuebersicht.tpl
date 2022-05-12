<script>

	function anlegen(id, sid) {
	document.location.href='index.php?module=adresse&action=addposition&id='+id+'&sid='+sid+'&menge='+ document.getElementById('menge'+sid).value +
		'&datum=' + document.getElementById('datum'+sid).value;
	}
</script>



<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">&Uuml;bersicht</a></li>
        <li><a href="#tabs-2">neue Position einf&uuml;gen</a></li>
    </ul>

<!-- erstes tab -->
<div id="tabs-1">

[MENU]


<fieldset><legend>{|Positionen|}</legend>
[TAB1]
<form action="" method="post">
<table width="100%">
<tr><td>
[LOESCHEN]</td><td align="right">
    <input type="submit" name="weiter"
    value="weiter" name="weiter" />
</td></tr></table>
</form>
</fieldset>
</div>

<div id="tabs-2">


[TAB2][UEBERSICHT]
</div>

<!-- tab view schließen -->
</div>
<!-- ende tab view schließen -->

