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
                <fieldset>
                    <legend>{|Anrufen|}</legend>
                    <form action="" method="post">
                        <table>
                        <tr><td width="200">Telefonnummer:</td><td><input type="text" size="30" name="telefon" value="[TELEFON]">&nbsp;<input type="submit" value="anrufen"></td></tr>
                        </table>
                    </form>
                </fieldset>
                <fieldset>
                    <legend>Über Sipgate</legend>
                    <p>Telefonie für zu Hause, unterwegs und das Büro.</p>
                    <ul>
                        <li><a href="https://www.sipgate.de/" target="_blank">Zur Webseite</a></li>
                    </ul>
                </fieldset>
            </div>
        </div>
        
        <div class="col-xs-12 col-md-8 col-md-height">
            <div class="inside inside-full-height">
                <fieldset>
                    <legend>{|Telefonbuch|}</legend>
                    [TELEFONBUCH]
                </fieldset>
            </div>
        </div>
    </div>
</div>

[TAB1NEXT]
</div>

<!-- tab view schließen -->
</div>
