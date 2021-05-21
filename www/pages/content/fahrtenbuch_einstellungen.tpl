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
        <div class="col-xs-12 col-md-12 col-md-height">
            <div class="inside inside-full-height">
                <fieldset><legend>{|Zugangsdaten Openstreetmap|}</legend>
                    <table>
                        <tr><td width="200">API-Key:</td><td><input type="text" size="30" name="apikey" value="[APIKEY]"></td></tr>
                        <tr><td width="200">Logging:</td><td><input type="checkbox" value="1" id ="apilog" name="apilog" [APILOG]></td></tr>
                    </table>
                </fieldset>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="row-height">
        <div class="col-xs-12 col-md-12 col-md-height">
            <div class="inside inside-full-height">
                <fieldset><legend>Über Openstreetmap</legend>
                <p>Um die Entfernungsberechnung nutzen zu können, muss ein API Account erstellt werden.</p>
                <ul>
                <li><a href="https://openrouteservice.org/sign-up/" target="_blank">Zum Portal für API-Key </a></li>
                </ul>
                </fieldset>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="row-height">
        <div class="col-xs-12 col-md-12 col-md-height">
            <div class="inside inside-full-height">
                <fieldset><legend>{|Optional Geodaten Startort|}</legend>
                    <table>
                        <tr><td width="200">Breitengrad:</td><td><input type="text" size="30" name="lat" id="lat" value="[LAT]"></td></tr>
                        <tr><td width="200">Längengrad:</td><td><input type="text"size = "30" id ="lng" name="lng" value="[LNG]"></td></tr>
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

