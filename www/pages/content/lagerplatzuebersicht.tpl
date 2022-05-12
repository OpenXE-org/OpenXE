<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">&Uuml;bersicht</a></li>
        <li><a href="#tabs-2">neues Regal anlegen</a></li>
        <li><a href="#tabs-3">Regal Import</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]
[TAB1]
</div>

<div id="tabs-2">
[TAB2]
</div>

<div id="tabs-3">
[MESSAGE3]
<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-6 col-md-height">
<div class="inside inside-full-height">
<fieldset><legend>{|Lagerpl&auml;tze Schnellimport|}</legend>
<form action="#tabs-3" method="post">
<table width="100%"><tr><td>
<textarea name="lagerimport" rows="10" style="width:98%">[IMPORT]</textarea><br><i>{|Lagerpl&auml;tze Komma getrennt oder untereinander pro Zeile angeben. Bereits angelegte werden ignoriert.|}</i></td></tr>
<tr><td align="right"><input type="submit" name="import" value="Lagerpl&auml;tze anlegen"></td></tr>
</table>
</form>
</fieldset>

</div>
</div>
<div class="col-xs-12 col-md-6 col-md-height">
<div class="inside inside-full-height">
<fieldset><legend>{|Lagerpl&auml;tze CSV Import|}</legend>
<form enctype="multipart/form-data" action="#tabs-3" method="POST">
<table class="mkTableForm">
<tr><td><input type="file" name="csv">&nbsp;<input type="submit" name="importcsv" value="Lagerpl&auml;tze anlegen"><br><i>{|Format: Lagerplatz;Reihenfolge|}</i></td></tr>
</table>
</form>
</fieldset>
</div>
</div>
</div>
</div>

</div>



<!-- tab view schließen -->
</div>
<!-- ende tab view schließen -->

