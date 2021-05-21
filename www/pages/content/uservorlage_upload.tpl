<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1"><!--[TABTEXT]--></a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]
<form enctype="multipart/form-data" action="" method="post" name="eprooform">
<fieldset><legend>{|Rechtedatei heraufladen|}</legend>
        <table><tr><td width="200">Datei auswählen:</td><td><input type="hidden" name="MAX_FILE_SIZE" value="30000" /><input name="jsonvorlage" type="file" /><input type="button" name="templatesubmit" value="heraufladen und &uuml;bernehmen" style="margin-left: 15px" 
                onclick="if(!confirm('Neue Benutzervorlage hinzufügen. Fortfahren?')) return false;else form.submit();">
        </td></tr></table>
</fieldset>
</form>
[TAB1]
[TAB1NEXT]
</div>

<!-- tab view schließen -->
</div>

