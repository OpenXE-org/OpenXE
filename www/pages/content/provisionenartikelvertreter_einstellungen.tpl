<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]
<form method="POST">
    <div class="row">
        <div class="row-height">
            <div class="col-xs-12 col-md-6 col-md-height">
                <div class="inside inside-full-height">
                    <fieldset>
                        <legend>{|Einstellungen|}</legend>
                        <table>
                            <tr><td width="300">{|Anzeige in Brutto|}:</td><td><input type="checkbox" value="1" name="brutto" [BRUTTO] /></td></tr>
                            <tr><td>{|Rechnung bezahlt am Verwenden|}:</td><td><input type="checkbox" value="1" name="rebezahltam" [REBEZAHLTAM] /></td></tr>
                            <tr><td>{|Abrechnungsartikel f&uuml;r Gutschriften|}:</td><td><input type="text" value="[ARTIKEL]" name="artikel" id="artikel" /></td></tr>
                            <tr><td>{|Detailierte Aufstellung in Gutschrift|}:</td><td><input type="checkbox" value="1" name="gutschriftdetails" [GUTSCHRIFTDETAILS] /></td></tr>
                            <tr><td>{|Rabatt von Provision absolut abziehen|}:</td><td><input type="checkbox" value="1" name="absolutrabatt" [ABSOLUTRABATT] /></td></tr>
                            <tr><td>{|Skonto beachten|}:</td><td><input type="checkbox" value="1" name="skontobeachten" [SKONTOBEACHTEN] /></td></tr>
                        </table>
                    </fieldset>
                </div>
            </div>
            <div class="col-xs-12 col-md-6 col-md-height">
                <div class="inside inside-full-height">
                    <fieldset></fieldset>
                </div>
            </div>
        </div>
    </div>
    <input type="submit" value="{|Speichern|}" name="speichern" style="float:right" />
</form>
[TAB1NEXT]
</div>

<!-- tab view schließen -->
</div>

