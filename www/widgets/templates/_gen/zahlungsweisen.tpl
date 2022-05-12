<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1"></a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]
<form action="" method="post" name="eprooform">
[FORMHANDLEREVENT]

<fieldset><legend>{|Einstellungen|}</legend>
    <table width="100%">
          <tr><td width="150">{|Bezeichnung|}:</td><td>[BEZEICHNUNG][MSGBEZEICHNUNG]</td></tr>
          <tr><td>{|Typ|}:</td><td>[TYPE][MSGTYPE]&nbsp;<i>z.B. novatel, lastschriftspezial, heidelberg, etc.</i></td></tr>
          <tr><td>{|Text auf Beleg|}:</td><td>[FREITEXT][MSGFREITEXT]<br>Variabeln: {ZAHLUNGBISDATUM}, {ZAHLUNGSZIELTAGE}, {ZAHLUNGSZIELSKONTO}, {ZAHLUNGSZIELTAGESKONTO}, {ZAHLUNGSZIELSKONTODATUM}, {SOLL}, {SOLLMITSKONTO}, {SKONTOBETRAG}, {SKONTOFAEHIG}, {SKONTOFAEHIGNETTO}, {BELEGNR}, {NAME}, {STEUERNORMAL},{GESAMTNETTO}, {GESAMTNETTONORMAL}, {STEUERERMAESSIGT}, {GESAMTNETTOERMAESSIGT}, {WAEHRUNG}</td></tr>
          <tr><td>{|Projekt|}:</td><td>[PROJEKT][MSGPROJEKT]</td></tr>
          <tr><td>{|Automatisch Bezahlt (Rechnung)|}:</td><td>[AUTOMATISCHBEZAHLT][MSGAUTOMATISCHBEZAHLT]<i>Rechnungen werden automatisch auf bezahlt gesetzt.</i></td></tr>
          <tr><td>{|Automatisch Bezahlt (Verbindlichkeit)|}:</td><td>[AUTOMATISCHBEZAHLTVERBINDLICHKEIT][MSGAUTOMATISCHBEZAHLTVERBINDLICHKEIT]<i>Verbindlichkeiten werden automatisch auf bezahlt gesetzt.</i></td></tr>
          <tr><td>{|Aktiv|}:</td><td>[AKTIV][MSGAKTIV]<i>Nicht mehr verwendete Zahlungsweisen k&ouml;nnen deaktiviert werden.</i></td></tr>
</table></fieldset>

<table width="100%"><tr><td align="right"><input type="submit" value="Speichern" /></td></tr></table>
</form>

</div>

<!-- tab view schließen -->
</div>
<!-- ende tab view schließen -->


