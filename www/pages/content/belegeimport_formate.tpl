<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">

In der ersten Zeilen der heraufzuladenden CSV Datei müssen die Spaltendefinitionen für Xentral stehen.

<ul>
<li>Bitte eine CSV mit Semilokon separierte Werte übergeben</li>
<li>Wenn die Kundennummer nicht vorhanden ist, wird die Adresse und der Kunde neu angelegt</li>
<li>Wenn die Artikelnummer nicht vorhanden ist, wird der Artikel neu angelegt</li>
</ul>

  <table class="mkTable">
    <tr><th>Spalte</th><th>Beschreibung</th><th>Pflicht</th></tr>
    <tr><td>art</td><td>Belegtyp <b>auftrag, angebot, bestellung, rechnung, adresse etc.</b></td><td>nein</td></tr>
    <tr><td>beleg_belegnr</td><td>Manuell vergebene Belegnummer</td><td>ja</td></tr>
    <tr><td>beleg_datum</td><td>Manuell vergebenes Belegdatum</td><td></td></tr>
    <tr><td>beleg_lieferdatum</td><td>Manuell vergebenes Wunschlieferdatum</td><td></td></tr>
    <tr><td>beleg_tatsaechlicheslieferdatum</td><td>Manuell Auslieferung Lager im Auftrag</td><td></td></tr>
    <tr><td>beleg_versandart</td><td>Manuell vergebene Versandart</td><td></td></tr>
    <tr><td>beleg_status</td><td>Manuell vergebener Belegstatus. Ersetzt - wenn vorhanden - den Status, der beim Dateiupload ausgewählt wurde</td><td></td></tr>
    <tr><td>beleg_hauptbelegnr</td><td>Haupt-Belegnummer falls Teilauftrag</td><td></td></tr>
    <tr><td>beleg_kundennummer / adresse_kundennummer</td><td>Kundennummer</td><td>ja</td></tr>
    <tr><td>beleg_lieferantennummer</td><td>Lieferantennummer</td><td>ja</td></tr>
    <tr><td>beleg_name / adresse_name</td><td>Name des Kunden</td><td></td></tr>
    <tr><td>beleg_abteilung / adresse_abteilung</td><td>Abteilung des Kunden</td><td></td></tr>
    <tr><td>beleg_unterabteilung / adresse_unterabteilung</td><td>Unterabteilung des Kunden</td><td></td></tr>
    <tr><td>beleg_adresszusatz / adresse_adresszusatz</td><td>Adresszusatz des Kunden</td><td></td></tr>
    <tr><td>beleg_ansprechpartner / adresse_ansprechpartner</td><td>Ansprechpartner bei Kunden</td><td></td></tr>
    <tr><td>beleg_telefon / adresse_telefon</td><td>Telefonnummer des Kunden</td><td></td></tr>
    <tr><td>beleg_email / adresse_email</td><td>E-Mail Adresse des Kunden</td><td></td></tr>
    <tr><td>beleg_land / adresse_land</td><td>Land des Kunden im Format ISO-3166 (zweistellige Länderkürzel, z.b. DE, AT, FR,... )</td><td></td></tr>
    <tr><td>beleg_strasse / adresse_strasse</td><td>Strasse inkl. Hausnummer des Kunden</td><td></td></tr>
    <tr><td>beleg_plz / adresse_plz</td><td>PLZ des Kunden</td><td></td></tr>
    <tr><td>beleg_ort / adresse_ort</td><td>Ort des Kunden</td><td></td></tr>
    <tr><td>beleg_projekt / adresse_projekt</td><td>Abkürzung aus Xentral</td><td></td></tr>
    <tr><td>beleg_ihrebestellnummer</td><td>Ihre Bestellnummer / Kommission aus Xentral</td><td></td></tr>
    <tr><td>beleg_aktion</td><td>Aktionscode aus Xentral</td><td></td></tr>
    <tr><td>beleg_internebemerkung</td><td>Interne Bemerkung aus Xentral</td><td></td></tr>
    <tr><td>beleg_internebezeichnung</td><td>Interne Bezeichnung aus Xentral</td><td></td></tr>
    <tr><td>beleg_zahlungsweise</td><td>Zahlungsweise (Bitte die Spalte 'Typ' verwenden)</td><td></td></tr>
    <tr><td>beleg_zahlungszieltage</td><td>Beleg Zahlungsziel</td><td></td></tr>
    <tr><td>beleg_zahlungszieltageskonto</td><td>Beleg Zahlungsziel Skonto</td><td></td></tr>
    <tr><td>beleg_zahlungszielskonto</td><td>Beleg Skonto</td><td></td></tr>
    <tr><td>beleg_freitext</td><td>Freitext aus Xentral</td><td></td></tr>
    <tr><td>beleg_bodyzusatz</td><td>Kopftext aus Xentral</td><td></td></tr>
    <tr><td>beleg_lieferbedingung</td><td>Lieferbedingung aus Xentral</td><td></td></tr>
    <tr><td>beleg_art</td><td>Belege im Autoversand erstellen <b>standardauftrag, lieferung, rechnung</b></td><td></td></tr>
    <tr><td>beleg_auftragsnummer</td><td>Dient zur Verknüpfung von Rechnungen und Lieferscheinen mit dem jew. Auftrag</td><td></td></tr>
    <tr><td>beleg_rechnungsnumer</td><td>Dient zur Verknüpfung von Aufträgen, Lieferscheinen und Gutschriften mit der jew. Rechnung</td><td></td></tr>
    <tr><td>beleg_bearbeiter</td><td>Der Bearbeiter des Beleges</td><td></td></tr>
    <tr><td>beleg_sprache</td><td>Sprache des Belegs</td><td></td></tr>
    <tr><td>beleg_liefername</td><td>Abweichende Lieferadresse - Name </td><td></td></tr>
    <tr><td>beleg_lieferabteilung</td><td>Abweichende Lieferadresse - Abteilung</td><td></td></tr>
    <tr><td>beleg_lieferunterabteilung</td><td>Abweichende Lieferadresse - Unterabteilung</td><td></td></tr>
    <tr><td>beleg_lieferland</td><td>Abweichende Lieferadresse - Land</td><td></td></tr>
    <tr><td>beleg_lieferstrasse</td><td>Abweichende Lieferadresse - Straße</td><td></td></tr>
    <tr><td>beleg_lieferort</td><td>Abweichende Lieferadresse - Ort</td><td></td></tr>
    <tr><td>beleg_lieferplz</td><td>Abweichende Lieferadresse - Postleitzahl</td><td></td></tr>
    <tr><td>beleg_lieferadresszusatz</td><td>Abweichende Lieferadresse - Adresszusatz</td><td></td></tr>
    <tr><td>beleg_lieferansprechpartner</td><td>Abweichende Lieferadresse - Ansprechpartner</td><td></td></tr>
    <tr><td>beleg_abschlagauftrag</td><td>Zur Abschlagsrechnung zugehörige Auftragsnummer</td><td></td></tr>
    <tr><td>beleg_abschlagauftragbezeichnung</td><td>Bezeichnung der Abschlagsrechnung</td><td></td></tr>
    <tr><td>beleg_waehrung</td><td>Währung, z.b. Euro = EUR, US-Dollar = USD</td><td></td></tr>
    <tr><td>beleg_bundesstaat</td><td>Bundesland / Bundesstaat ISO-codiert, z.b. Bayern = BY </td><td></td></tr>
    <tr><td>beleg_internet</td><td>z.B. Shopbestellnummer (über Shopimport) oder Fremdnummer</td><td></td></tr>
    <tr><td>artikel_nummer</td><td>Artikelnummer für Position</td><td>ja</td></tr>
    <tr><td>artikel_bezeichnung</td><td>Bezeichnung für Position</td><td></td></tr>
    <tr><td>artikel_beschreibung</td><td>Beschreibung für Position</td><td></td></tr>
    <tr><td>artikel_menge</td><td>Mengenangabe für Position</td><td>ja</td></tr>
    <tr><td>artikel_preis</td><td>Einzelpreis (netto) für Position</td><td></td></tr>
    <tr><td>artikel_preisfuermenge</td><td>Preis bezieht sich auf folgende Menge von Artikeln (Kein Eintrag entspricht 1)</td><td></td></tr>
    <tr><td>artikel_waehrung</td><td>Währung für Position</td><td></td></tr>
    <tr><td>artikel_lieferdatum</td><td>Lieferdatum für Position</td><td></td></tr>
    <tr><td>artikel_sort</td><td>Reihenfolge in Positionstabelle</td><td></td></tr>
    <tr><td>artikel_umsatzsteuer</td><td>Umsatzsteuer: Leer lassen bzw. <b>normal</b> oder <b>ermaessigt</b> als Wort angeben</td><td></td></tr>
    <tr><td>artikel_steuersatz</td>in Prozent, entspricht dem Feld 'Individueller Steuersatz'<td></td><td></td></tr>
    <tr><td>artikel_einheit</td><td>Einheit für Position</td><td></td></tr>
    <tr><td>artikel_rabatt</td><td>Rabatt für Position (in Prozent)</td><td></td></tr>
    <tr><td>artikel_lieferdatum</td><td>Lieferdatum für Position</td><td></td></tr>
    <tr><td>artikel_zolltarifnummer</td><td>Zolltarifnummer für Position</td><td></td></tr>
    <tr><td>artikel_herkunftsland</td><td>Herkunftsland für Position</td><td></td></tr>
    <tr><td>artikel_artikelnummerkunde</td><td>Artikelnummer bei Kunden</td><td></td></tr>
    <tr><td>artikel_freifeld1 - artikel_freifeld10</td><td>Freifeld 1 bis Freifeld 10</td><td></td></tr>
    <tr><td>adresse_typ</td><td>Anrede des Kunden <b>firma, herr, frau</b></td><td></td></tr>
    <tr><td>adresse_ustid</td><td>Ust-Id des Kunden</td><td></td></tr>
    <tr><td>adresse_anschreiben</td><td>Anschreiben bei Kunde</td><td></td></tr>
    <tr><td>adresse_freifeld1 - adresse_freifeld20</td><td>Freifeld 1 bis Freifeld 20</td><td></td></tr>
  </table>


</div>

<!-- tab view schließen -->
</div>

