<fieldset><legend>CSV Datei ausw&auml;hlen</legend>
<table>
<tr><td>Datei:</td><td>

    <form action="" method="post" enctype="multipart/form-data">
    <input name="userfile" type="file" />&nbsp;Kodierung: [SELCHARSET]&nbsp;<input type="text" id="charset" name="charset" value="[CHARSET]" />&nbsp;<input type="submit" name="upload" value="CSV jetzt heraufladen">
</td></tr></table>
<!--<i>*Bei Umlautproblemen empfehlen wir die Daten mit Libreoffice oder OpenOffice in das Format UTF-8 zu konveriert.  Bei Problemen gerne beim Support nachfragen.</i>-->
</fieldset>

<br>
<style type="text/css">
 table.importstyle
{
    border-width: 0 0 1px 1px;
    border-spacing: 0;
    border-collapse: collapse;
    border-style: solid;
}

.importstyle td, .importstyle th
{
    margin: 0;
    padding: 4px;
    border-width: 1px 1px 0 0;
    border-style: solid;
}
</style>
<form action="" method="post">
<div style="width:92vw; overflow: auto">
[IMPORTBUTTON]
<br>
<table border="0" class="importstyle">
[ERGEBNIS]
</table>
<br>
</div>
<br><br>
<center>[IMPORTBUTTON]</center>
</form>
