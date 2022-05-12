<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1"></a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]
[HINWEISSTEUER]

<div class="row">
<div class="row-height">   

<div class="col-xs-12 col-md-6 col-sm-height">
<div class="inside inside-full-height">


<form action="" method="POST">
<fieldset><legend>{|Export Rechnungen / Gutschriften|}</legend>

<table >
<tr><td>{|Sortiert nach|}</td><td><select name="sort"><option value="datum">{|Datum|}</option><option value="belegnr">{|Rechnungsnummer|}</option></select></td></tr>
<tr><td>{|Gruppiert nach|}</td><td><select name="gruppierung"><option value="erloese">{|Konten|}</option><option value="positionen">{|Positionen|}</option></select>&nbsp;</td></tr>
<tr><td>{|Projekt|}:</td><td><input type="name" value="[PROJEKT]" name="projekt" id="projekt">&nbsp;<i>({|Optional / Standardm&auml;&szlig;ig leer lassen|})</i></td></tr>
<tr><td width="100">{|von|}:</td><td><input type="text" size="12" id="von" name="von" value="[VON]">&nbsp;
{|bis|}&nbsp;<input type="text" size="12" name="bis" id="bis" value="[BIS]">&nbsp;[DATUMBIS]&nbsp;<input type="submit" name="export" value="{|Export|}"></td></tr>
<tr><td></td><td>[LETZTEREXPORTRECHNUNG]</td></tr>
<!--<tr><td colspan="2">[SCHLUESSEL]</td></tr>-->
</table>

</fieldset>
</form>

<form action="" method="POST">
<fieldset><legend>{|Export Verbindlichkeiten|}</legend>
<table >
<tr><td>{|Projekt|}:</td><td><input type="name" value="[PROJEKT3]" name="projekt3" id="projekt3">&nbsp;<i>({|Optional / Standardm&auml;&szlig;ig leer lassen|})</i></td></tr>
<tr><td width="100">{|von|}</td><td><input type="text" size="12" id="von3" name="von3" value="[VON3]">&nbsp;
{|bis|}&nbsp;<input type="text" size="12" name="bis3" id="bis3" value="[BIS3]">&nbsp;<input type="submit" name="exportverbindlichkeit" value="{|Export|}"></td></tr>
<tr><td></td><td>[LETZTEREXPORTVERBINDLICHKEITEN]</td></tr>
<!--<tr><td>mit exportierten</td><td><input type="checkbox" value="1" name="exportiert"></td></tr>-->
<!--<tr><td>{|Format|}:</td><td>[SCHLUESSEL3]</td></tr>-->
</table>
</fieldset>
</form>


<form action="" method="POST">
<fieldset><legend>{|Export Konten|}</legend>
<table>
<tr><td width="100">{|Konto|}</td><td><select name="konto2">[KONTOAUSWAHL]</select></td></tr>
<tr><td>{|von|}</td><td><input type="text" size="12" name="von2" id="von2" value="[VON2]">
&nbsp;{|bis|}&nbsp;<input type="text" size="12" name="bis2" id="bis2" value="[BIS2]">&nbsp;[DATUMBIS2]&nbsp;<input type="submit" name="buchhaltungexport" value="{|Export|}"></td></tr>
<tr><td></td><td>[LETZTEREXPORTKONTO]</td></tr>
</table>
</fieldset>
</form>


</div>
</div>
<div class="col-xs-12 col-md-6 col-sm-height">
<div class="inside inside-full-height">
<fieldset><legend>{|Einstellungen|}</legend>
<table>
<tr><td colspan="3"><strong>{|Rechnungen / Gutschriften|}</strong></td></tr>
<tr><td colspan="3"><input type="checkbox" name="datevbelegfeld1" id="datevbelegfeld1" value="1" [DATEVBELEGFELD1]>&nbsp;{|Belegfeld1 bei Kontoauszugsexport mit Rechnungsnummer füllen wenn möglich|}</td></tr>
<tr><td colspan="3"><input type="checkbox" name="datevbelegfeld1ext" id="datevbelegfeld1ext" value="1" [DATEVBELEGFELD1EXT]>&nbsp;{|Belegfeld1 bei Rechnungs- u. Gutschrift um Internet- und Transaktionsnummer erweitern|} </td></tr>
<tr><td colspan="3"><input type="checkbox" name="datevbelegfeld2faelligam" id="datevbelegfeld2faelligam" value="1" [DATEVBELEGFELD2FAELLIGAM]>&nbsp;{|Belegfeld2 bei Rechnungs- u. Gutschrift mit F&auml;lligkeitsdatum f&uuml;llen|} </td></tr>
<tr><td colspan="3"><br></td></tr>
<tr><td colspan="3"><strong>{|Verbindlichkeit|}</strong></td></tr>
<tr><td>{|Verbindlichkeit Auswahl|}</td>
    <td colspan="2"> <select name="datevverbindlichkeitrechnungsdatum"><option value="0" [DATEVVERBINDLICHKEITRECHNUNGSDATUM0]>{|nach Eingangsdatum|}</option><option value="1" [DATEVVERBINDLICHKEITRECHNUNGSDATUM1]>{|nach Rechnungsdatum|}</option></select></td></tr>
<tr><td colspan="3"><br></td></tr>
<tr><td colspan="3"><strong>{|Datev|}</strong></td></tr>
<tr><td colspan="3"><input type="checkbox" name="nulleurorechnungen" id="nulleurorechnungen" value="1" [NULLEURORECHNUNGEN]>&nbsp;{|Belege mit Betrag = 0 EUR auch exportieren|}</td></tr>
<tr><td colspan="3"><input type="checkbox" name="neuesdatevformat" id="neuesdatevformat" value="1" [NEUESDATEVFORMAT]>&nbsp;{|Neues Datev Format (ab 2018)|}</td></tr>
<tr><td colspan="3"><input type="checkbox" name="neuesdatevformattestbuchung" id="neuesdatevformattestbuchung" value="1" [NEUESDATEVFORMATTESTBUCHUNG]>&nbsp;{|Neues Datev Format (Testbuchung einfügen)|}</td></tr>
<tr><td colspan="3"><input type="checkbox" name="altesdatevformat" id="altesdatevformat" value="1" [ALTESDATEVFORMAT]>&nbsp;{|Datev Komp-Modus (GoBD Festschreibekennzeichen)|}</td></tr>
<tr><td colspan="3"><input type="checkbox" name="datev_append_internet" id="datev_append_internet" value="1" [DATEV_APPEND_INTERNET]>&nbsp;{|Internetnummer im XML für Datev Unternehmen Online ergänzen|}</td></tr>
<tr><td colspan="3"><br></td></tr>
<tr><td colspan="3"><strong>{|Zahlweisen/Steuersätze|}</strong></td></tr>
<tr><td colspan="3"><input type="checkbox" name="zahlweisen" id="zahlweisen" value="1" [ZAHLWEISEN]>&nbsp;{|Zahlweisen und Steuersätze gesondert bei Rechnung und Gutschrift als extra Spalte mit ausgeben|}</td></tr>
<tr><td colspan="3"><input type="checkbox" name="zahlweisenverbindlichkeit" id="zahlweisenverbindlichkeit" value="1" [ZAHLWEISENVERBINDLICHKEIT]>&nbsp;{|Zahlweisen bei Verbindlichkeiten als extra Spalte mit ausgeben|}</td></tr>

    <tr>
        <td colspan="3"><input type="checkbox" name="alterloes2020" id="alterloes2020" value="1" [ALTERLOES2020] />
            <label for="alterloes2020">{|Alternative Erlöskonten f&uuml;r Umsatzsteuersenkung 2020|}</label>
        </td>
    </tr>
    <tr class="alterloes2020">
        <td>
            <label for="alterloes19">{|Inland (19%)|}</label></td>
        <td colspan="2">
            <input type="text" id="alterloes19" name="alterloes19" value="[ALTERLOES19]" size="6" /></td>
    </tr>
    <tr class="alterloes2020">
        <td>
            <label for="alterloes7">{|Inland (7%)|}</label></td>
        <td colspan="2"><input type="text" id="alterloes7" name="alterloes7" value="[ALTERLOES7]" size="6" /></td>
    </tr>
    <tr class="alterloes2020">
        <td>
            <label for="alterloeseu19">{|EU (19%)|}</label></td>
        <td colspan="2">
            <input type="text" id="alterloeseu19" name="alterloeseu19" value="[ALTERLOESEU19]" size="6" /></td>
    </tr>
    <tr class="alterloes2020">
        <td>
            <label for="alterloeseu7">{|EU (7%)|}</label></td>
        <td colspan="2"><input type="text" id="alterloeseu7" name="alterloeseu7" value="[ALTERLOESEU7]" size="6" /></td>
    </tr>

</table>

</fieldset>


</div>
</div>
</div>
</div>


<div class="row">
<div class="row-height">   
<div class="col-xs-12 col-md-6 col-sm-height">
<div class="inside inside-full-height">


<form action="" method="POST">
<fieldset><legend>{|Datev Unternehmen Online (Beta)|}</legend>
<table>
<tr><td width="100">{|Beleg|}</td><td><select name="belegart"><option value="rechnung">{|Rechnungen|}</option><option value="gutschrift">{|Gutschriften|}</option><option value="verbindlichkeit">{|Verbindlichkeiten|}</option></select></td></tr>
<tr><td>{|Projekt|}:</td><td><input type="name" value="[PROJEKT6]" name="projekt6" id="projekt6">&nbsp;<i>({|Optional / Standardm&auml;&szlig;ig leer lassen|})</i></td></tr>
<tr><td>{|von|}</td><td><input type="text" size="12" name="von6" id="von6" value="[VON6]">&nbsp;
{|bis|}&nbsp;<input type="text" size="12" name="bis6" id="bis6" value="[BIS6]">&nbsp;[DATUMBIS6]&nbsp;<input type="submit" name="buchhaltungexportdatevxml" value="{|Export|}"></td></tr>
<tr><td></td><td>[LETZTEREXPORTDATEVXML]</td></tr>
</table>
</fieldset>
</form>





</div>
</div>
<div class="col-xs-12 col-md-6 col-sm-height">
<div class="inside inside-full-height">

<form action="" method="POST">
<fieldset><legend>{|Export Kunden/Lieferanten|}</legend>
<table>
<tr valign="top"><td width="100">{|Auswahl|}:</td><td>

<table>
<tr><td><input type="button" value="{|Kunden|}" onclick="window.location.href='index.php?module=buchhaltungexport&action=exportadressen&cmd=kunden&info='+document.getElementById('kunde').value"></td>
<td><input type="text" size="40" name="kunde" value="" id="kunde">&nbsp;<i>{|Export ab Kd.-Nr.|}</i></td></tr>
<tr><td><input type="button" value="{|Lieferanten|}" onclick="window.location.href='index.php?module=buchhaltungexport&action=exportadressen&cmd=lieferanten&info='+document.getElementById('lieferant').value"></td>
<td><input type="text" size="40" name="lieferant" value="" id="lieferant">&nbsp;<i>{|Export ab Lf.-Nr.|}</i></td></tr>
<!--<tr><td>[BUTTONVERBAND]</td>
<td><input type="text" size="40" name="kundeverband" value="" id="kundeverband">&nbsp;<i>Export ab Kd.-Nr.</i></td></tr>-->
</table>

</td></tr>
<!--<tr><td>{|Format|}:</td><td>"Kundennummer bzw. Lieferantennummer";"Kundenname";"Strasse";"PLZ","Ort";"USTID";"Zahlungsziel in Tage";"Konto";"BLZ";"IBAN";"BIC";"Bank";"Konto Buchhaltung";</td></tr>-->
</table>
</fieldset>
</form>


</div>
</div>
</div>
</div>








</div>




<!--
<div id="tabs-2">
<form action="" method="POST">
<fieldset><legend>{|Stapeldruck Rechnungen / Gutschriften|}</legend>

<table >
<tr><td>Sortiert nach</td><td><select name="sort"><option value="datum">Datum</option><option value="belegnr">Rechnungsnummer</option></select></td></tr>
<tr><td>Projekt:</td><td><input type="name" value="[PROJEKT]" name="projekti2" id="projekt2">&nbsp;<i>(Optional / Standardm&auml;&szlig;ig leer lassen)</i></td></tr>
<tr><td width="100">von:</td><td><input type="text" size="12" id="von4" name="von4" value="[VON4]">&nbsp;[DATUMVON]</td></tr>
<tr><td>bis:</td><td><input type="text" size="12" name="bis4" id="bis4" value="[BIS4]">&nbsp;[DATUMBIS]&nbsp;<select name="drucker">[DRUCKER]</select>&nbsp;<input type="submit" name="stapeldruck" value="Stapeldruck"></td></tr>
</table>

</fieldset>
</form>

</div>
-->



<!-- tab view schließen -->
</div>




