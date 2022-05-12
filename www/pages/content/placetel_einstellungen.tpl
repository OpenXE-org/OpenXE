<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]
[TAB1]
<!--
<div class="row">
<div class="row-height">
<div class="col-xs-12 col-sm-height">
<div class="inside inside-full-height">

<fieldset><legend>{|Status|}</legend>
<form id="meiappsform" action="" method="post" enctype="multipart/form-data">
<div class="info">Das letzte Backup wurde am 24.12.2001 um 06:12 erfolgreich durchgeführt.</div>
</form>

</fieldset>
</div>
</div>
</div>
</div>
-->


<div class="row">
<div class="row-height">


<div class="col-xs-12 col-md-4 col-md-height">
<div class="inside inside-full-height">

<fieldset><legend>{|Zugangsdaten|}</legend>
<table>
<tr><td width="200">API-Key:</td><td><input type="text" size="30" name="apikey" value="[APIKEY]"></td></tr>
<tr><td width="200">Accounts:</td><td><textarea cols="50" rows="20" name="accounts">[ACCOUNTS]</textarea><br><i>pro Zeile: <br>Benutzername WaWision:UID von Placetel<br><br>Beispiel:<br>admin:77777777@fpbx.de<br>admin2:771777171@fpbx.de</i></td></tr>
<tr><td width="200">Shared Secret:</td><td><input type="text" size="30" name="sharedsecret" value="[SHAREDSECRET]"></td></tr>
</table>
</fieldset>
<fieldset><legend>Über Placetel</legend>
<p>Die skalierbare Telefonanlage aus der Cloud</p>
<ul>
<li><a href="https://www.placetel.io/" target="_blank">Zur Webseite</a></li>
</ul>
</fieldset>


</div>
</div>

<div class="col-xs-12 col-md-8 col-md-height">
<div class="inside inside-full-height">
<fieldset><legend>{|Accounts|}</legend>




<table class="mkTable">

<tr>
  <th>Name</th>
  <th>Typ</th>
  <th>UID</th>
</tr>
[ACCOUNTROW]

</table>

</fieldset>
</div>
</div>

</div>
</div>

[TAB1NEXT]
</div>

<!-- tab view schließen -->
</div>

