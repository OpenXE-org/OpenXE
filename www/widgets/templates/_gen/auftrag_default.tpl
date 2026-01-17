<script type="text/javascript"><!--

jQuery(document).ready(function() {
    lieferantenauftraganzeige(0);
    abweichend2();
    });


function lieferantenauftraganzeige(cmd)
{
  if(document.getElementById('lieferantenauftrag').checked)
  {
    document.getElementById('kundestyle').style.display="none";
    document.getElementById('lieferantenauftragstyle').style.display="";
  } else {

    document.getElementById('lieferantenauftragstyle').style.display="none";
    document.getElementById('kundestyle').style.display="";
  }
}

function abweichend2()
{

  var inp = 'in'+'put';
  var sel = 'sel'+'ect';
  jQuery('table.tableabweichend').find(inp).prop('disabled', true);
  jQuery('table.tableabweichend').find(sel).prop('disabled', true);
  jQuery('table.tableabweichend').find(inp).first().prop('disabled', false);
    if(document.getElementById('abweichendelieferadresse').checked && !document.getElementById('schreibschutz').checked)
  {
    jQuery('table.tableabweichend').find(inp).prop('disabled', false);
    jQuery('table.tableabweichend').find(sel).prop('disabled', false);
  }

}
//-->
</script>

[SAVEPAGEREALLY]

<!-- gehort zu tabview -->
<div id="tabs">
<ul>
<li><a href="#tabs-1">Auftrag</a></li>
<li><a href="#tabs-2" onclick="callCursor();">Positionen</a></li>
<li><a href="index.php?module=auftrag&action=inlinepdf&id=[ID]&frame=true#tabs-3">Vorschau</a></li>
[FURTHERTABS]
</ul>



<div id="tabs-1">
[MESSAGE]
<form action="" method="post" name="eprooform" id="eprooform">
[LIEFERID][MSGLIEFERID]
[ANSPRECHPARTNERID][MSGANSPRECHPARTNERID]
[FORMHANDLEREVENT]

<!-- // rate anfang -->
<div class="row">
<div class="row-height">
<div class="col-xs-12 col-sm-height">
<div class="inside inside-full-height">
<table width="100%" align="center">
<tr>
<td>&nbsp;<b style="font-size: 14pt">[BEZEICHNUNGTITEL] <font color="blue">[NUMMER]</font></b>[KUNDE][RABATTANZEIGE]</td>
<td >[STATUSICONS]</td>
<td width="" align="right">[ICONMENU]&nbsp;[SAVEBUTTON]</td>
</tr>
</table>
</div>
</div>
</div>
</div>



<div class="row">
<div class="row-height">
<div class="col-xs-12 col-sm-6 col-sm-height">
<div class="inside inside-full-height">

<fieldset><legend>{|Allgemein|}</legend>
<table class="mkTableFormular">
<tr id="kundestyle"><td><legend>{|Kunde|}</legend></td><td nowrap>[ADRESSE][MSGADRESSE]&nbsp;[BUTTON_UEBERNEHMEN]</td></tr>
<tr id="lieferantenauftragstyle"><td><legend>{|Lieferant|}</legend></td><td nowrap>[LIEFERANT][MSGLIEFERANT]&nbsp;[BUTTON_UEBERNEHMEN2]</td></tr>
<tr><td>{|an Lieferanten|}:</td><td nowrap>[LIEFERANTENAUFTRAG][MSGLIEFERANTENAUFTRAG]&nbsp;</td></tr>
<tr><td>{|Projekt|}:</td><td>[PROJEKT][MSGPROJEKT]</td></tr>
<tr><td>[BEZEICHNUNGAKTIONSCODE]:</td><td>[AKTION][MSGAKTION]</td></tr>
<tr><td>{|Status|}:</td><td>[STATUS]</td></tr>
<tr><td>{|Internet|}:</td><td>[INTERNET][MSGINTERNET]</td></tr>
<tr><td>{|Angebot|}:</td><td>[ANGEBOTAUTOSTART][ANGEBOTID][MSGANGEBOTID][ANGEBOTAUTOEND]</td></tr>
<tr><td>{|Debitorennummer|}:</td><td>[KUNDENNUMMER_BUCHHALTUNG][MSGKUNDENNUMMER_BUCHHALTUNG]</td></tr>
<tr><td>{|Ihre Bestellnummer / Kommission|}:</td><td>[IHREBESTELLNUMMER][MSGIHREBESTELLNUMMER]</td></tr>
<tr><td>{|Interne Bezeichnung|}:</td><td>[INTERNEBEZEICHNUNG][MSGINTERNEBEZEICHNUNG]</td></tr>
<tr><td>{|Auftragsdatum|}:</td><td>[DATUM][MSGDATUM]&nbsp;per&nbsp;[AUFTRAGSEINGANGPER][MSGAUFTRAGSEINGANGPER]</td></tr>
<tr><td>{|Wunsch Liefertermin|}:</td><td>[LIEFERDATUM][MSGLIEFERDATUM]&nbsp;[LIEFERDATUMKW][MSGLIEFERDATUMKW]&nbsp;KW</td></tr>
<tr><td>{|Auslieferung Lager|}:</td><td>[TATSAECHLICHESLIEFERDATUM][MSGTATSAECHLICHESLIEFERDATUM]</td></tr>
<tr><td>{|Reservierungs Datum|}:</td><td>[RESERVATIONDATE][MSGRESERVATIONDATE]</td></tr>
<tr><td>[VORWUNSCHLAGER]Bevorzugtes Lager:[NACHWUNSCHLAGER]<br></td><td>[VORWUNSCHLAGER][STANDARDLAGER][MSGSTANDARDLAGER][NACHWUNSCHLAGER]</td></tr>
[VORKOMMISSIONSKONSIGNATIONSLAGER]<tr><td>[KOMMISSIONIERLAGER]:<br></td><td>[KOMMISSIONSKONSIGNATIONSLAGER][MSGKOMMISSIONSKONSIGNATIONSLAGER]</td></tr>[NACHKOMMISSIONSKONSIGNATIONSLAGER]
<tr><td>Schreibschutz:</td><td>[SCHREIBSCHUTZ][MSGSCHREIBSCHUTZ]&nbsp;</td></tr>
<tr><td>[ABWEICHENDEBEZEICHNUNGBESCHRIFTUNG]:</td><td>[ABWEICHENDEBEZEICHNUNG][MSGABWEICHENDEBEZEICHNUNG]&nbsp;</td></tr>
[EXTRABEREICHALLGEMEIN]
</table>
</fieldset>


</div>

</div>

<div class="col-xs-12 col-sm-6 col-sm-height">
<div class="inside_turkey inside-full-height">


<div id="abweichendelieferadressestyle">
<fieldset class="turkey"><legend>{|Abweichende Lieferadresse|}</legend>
<table class="tableabweichend">
<tr><td width="200">{|Abweichende Lieferadresse|}:</td><td>[ABWEICHENDELIEFERADRESSE][MSGABWEICHENDELIEFERADRESSE]</td></tr>
<tr><td>{|Name|}:*</td><td>[LIEFERNAME][MSGLIEFERNAME]</td></tr>
<tr><td>{|Titel|}:</td><td>[LIEFERTITEL][MSGLIEFERTITEL]</td></tr>
<tr><td>{|Ansprechpartner|}:</td><td>[LIEFERANSPRECHPARTNER][MSGLIEFERANSPRECHPARTNER]</td></tr>
<tr><td>{|Abteilung|}:</td><td>[LIEFERABTEILUNG][MSGLIEFERABTEILUNG]</td></tr>
<tr><td>{|Unterabteilung|}:</td><td>[LIEFERUNTERABTEILUNG][MSGLIEFERUNTERABTEILUNG]</td></tr>
<tr><td>{|Adresszusatz|}:</td><td>[LIEFERADRESSZUSATZ][MSGLIEFERADRESSZUSATZ]</td></tr>
<tr><td>{|Stra&szlig;e|}:</td><td>[LIEFERSTRASSE][MSGLIEFERSTRASSE]</td></tr>
<tr><td>{|PLZ/Ort|}:</td><td>[LIEFERPLZ][MSGLIEFERPLZ]&nbsp;[LIEFERORT][MSGLIEFERORT]</td>
</tr>
[VORBUNDESSTAAT]<tr valign="top"><td><label for="lieferbundesstaat">{|Bundesstaat|}:</label></td><td colspan="2">[EPROO_SELECT_LIEFERBUNDESSTAAT]</td></tr>[NACHBUNDESSTAAT]
<tr><td>{|Land|}:</td><td>[EPROO_SELECT_LIEFERLAND]</td></tr>
<tr><td>{|GLN|}:</td><td>[LIEFERGLN][MSGLIEFERGLN]</td></tr>
<tr><td>{|E-Mail|}:</td><td>[LIEFEREMAIL][MSGLIEFEREMAIL]</td></tr>
<tr><td></td><td>[LIEFERADRESSEPOPUP]&nbsp;[ANSPRECHPARTNERLIEFERADRESSEPOPUP]&nbsp;[ADRESSELIEFERADRESSEPOPUP]</td></tr>
</table>
</fieldset>
</div>


</div>
</div>
</div>
</div> <!-- spalte 2 zu -->





<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-9 col-md-height">
<div class="inside inside-full-height">


<fieldset><legend>{|Stammdaten|}</legend>

<table border="0" class="mkTableFormular">
<tr><td width="200">{|Typ|}:</td><td width="200">[TYP][MSGTYP]</td></tr>
<tr><td>{|Name|}:</td><td>[NAME][MSGNAME]</td></tr>
<tr><td>{|Titel|}:</td><td>[TITEL][MSGTITEL]</td></tr>
<tr><td>{|Ansprechpartner|}:</td><td>[ANSPRECHPARTNER][MSGANSPRECHPARTNER]</td></tr>            <tr><td>{|Abteilung|}:</td><td>[ABTEILUNG][MSGABTEILUNG]</td></tr>
<tr><td>{|Unterabteilung|}:</td><td>[UNTERABTEILUNG][MSGUNTERABTEILUNG]</td></tr>
<tr><td>{|Adresszusatz|}:</td><td>[ADRESSZUSATZ][MSGADRESSZUSATZ]</td></tr>
<tr><td>{|Stra&szlig;e|}:</td><td>[STRASSE][MSGSTRASSE]</td></tr>
<tr><td>{|PLZ/Ort|}:</td><td>[PLZ][MSGPLZ]&nbsp;[ORT][MSGORT]</td></tr>
[VORBUNDESSTAAT]<tr valign="top"><td><label for="bundesstaat">{|Bundesstaat|}:</label></td><td colspan="2">[EPROO_SELECT_BUNDESSTAAT]</td></tr>[NACHBUNDESSTAAT]
<tr><td>{|Land|}:</td><td>[EPROO_SELECT_LAND]</td></tr>
</table>

<table class="mkTableFormular">
<tr><td>{|Telefon|}:</td><td>[TELEFON][MSGTELEFON]</td></tr>
<tr><td>{|Telefax|}:</td><td>[TELEFAX][MSGTELEFAX]</td></tr>
<tr><td>{|E-Mail|}:</td><td>[EMAIL][MSGEMAIL]</td></tr>
<tr><td>{|Anschreiben|}:</td><td>[ANSCHREIBEN][MSGANSCHREIBEN]</td></tr>
<tr><td></td><td>[ANSPRECHPARTNERPOPUP]</td></tr>
</table>    

</fieldset>

</div>
</div>

<div class="col-xs-12 col-md-3 col-md-height further-grid">
<div class="inside inside-full-height">
[INFOFUERAUFTRAGSERFASSUNG]
</div>
</div>

</div>
</div>



<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-6 col-sm-height">
<div class="inside inside-full-height">

<fieldset><legend>{|Freitext|}</legend>
[FREITEXT][MSGFREITEXT]
</fieldset>

</div>
</div>


<div class="col-xs-12 col-md-6 col-sm-height">
<div class="inside inside-full-height">

<fieldset><legend>{|Kopftext|}</legend>
[BODYZUSATZ][MSGBODYZUSATZ]
</fieldset>

</div>
</div>
</div>
</div>




<div class="row">
<div class="row-height">
<div class="col-xs-12 col-sm-6 col-sm-height">
<div class="inside inside-full-height">

<fieldset><legend>{|Auftrag|}</legend>
<table class="mkTableFormular">

<tr><td>{|Zahlungsweise|}:</td><td>[ZAHLUNGSWEISE][MSGZAHLUNGSWEISE]
<br>[VORABBEZAHLTMARKIEREN][MSGVORABBEZAHLTMARKIEREN]&nbsp;manuell Zahlungsfreigabe erteilen
</td></tr>
<tr><td>{|Versandart|}:</td><td>[VERSANDART][MSGVERSANDART]</td></tr>
<tr><td><label for="lieferbedingung">{|Lieferbedingung|}:</label></td><td>[LIEFERBEDINGUNG][MSGLIEFERBEDINGUNG]</td></tr>
<tr><td>{|Vertrieb|}:</td><td>[VERTRIEB][MSGVERTRIEB]&nbsp;[VERTRIEBBUTTON]</td></tr>
<tr><td>{|Bearbeiter|}:</td><td>[BEARBEITER][MSGBEARBEITER]&nbsp;[INNENDIENSTBUTTON]</td></tr>

<tr><td>{|Portopr&uuml;fung ausschalten|}:</td><td>[KEINPORTO][MSGKEINPORTO]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
{|Kein Briefpapier und Logo|}:&nbsp;[OHNE_BRIEFPAPIER][MSGOHNE_BRIEFPAPIER]</td></tr>
<tr><td>{|Artikeltexte ausblenden|}:</td><td>[OHNE_ARTIKELTEXT][MSGOHNE_ARTIKELTEXT]</td></tr>

</table>
</fieldset>
<fieldset><legend>{|Versandzentrum Optionen|}</legend>
<table class="mkTableFormular">
<tr><td>{|F&uuml;r den Auto-Versand freigeben|}:</td><td>[AUTOVERSAND][MSGAUTOVERSAND]</td></tr>
<tr><td>{|Fast-Lane|}:</td><td>[FASTLANE][MSGFASTLANE]</td></tr>
<tr><td>{|Belege im Auto-Versand erstellen|}:</td><td>[ART][MSGART]</td></tr>
<tr><td>{|Lieferung trotz Liefersperre|}:</td><td>[LIEFERUNGTROTZSPERRE][MSGLIEFERUNGTROTZSPERRE]</td></tr>
<tr><td>{|Keine Stornomail|}:</td><td>[KEINESTORNOMAIL][MSGKEINESTORNOMAIL]</td></tr>
<tr><td>{|Keine Trackingmail|}:</td><td>[KEINETRACKINGMAIL][MSGKEINETRACKINGMAIL]</td></tr>
<tr><td>{|Keine Zahlungseingangsmail|}:</td><td>[ZAHLUNGSMAILCOUNTER][MSGZAHLUNGSMAILCOUNTER]</td></tr>
</table>
</fieldset>



</div>
</div>

<div class="col-xs-12 col-sm-6 col-sm-height">
<div class="inside inside-full-height">
<fieldset><legend>{|Sonstiges|}</legend>
<table class="mkTableFormular"><tr><td>{|GLN|}:</td><td>[GLN][MSGGLN]</td></tr>[EXTRABEREICHSONSTIGES]</table>
</fieldset>

<script type="text/javascript"><!--

function aktion_buchen(cmd)
{
  if(cmd=="lastschrift") cmd="einzugsermaechtigung";
  document.getElementById('rechnung').style.display="none";
  document.getElementById('kreditkarte').style.display="none";
  document.getElementById('einzugsermaechtigung').style.display="none";
  document.getElementById('paypal').style.display="none";
  document.getElementById(cmd).style.display="";

}

function versand(cmd)
{
  document.getElementById('packstation').style.display="none";
  document.getElementById(cmd).style.display="";
}

function abweichend(cmd)
{
  document.getElementById('abweichendelieferadressestyle').style.display="none";
  if(document.getElementById('abweichendelieferadresse').checked)
    document.getElementById('abweichendelieferadressestyle').style.display="";
}
//-->
</script>



<div id="rechnung" style="display:[RECHNUNG]">
<fieldset><legend>{|Rechnung|}</legend>
<table width="100%">
<tr><td width="200">{|Zahlungsziel (in Tagen)|}:</td><td>[ZAHLUNGSZIELTAGE][MSGZAHLUNGSZIELTAGE]</td></tr>
<tr><td nowrap>{|Zahlungsziel Skonto (in Tagen)|}:</td><td>[ZAHLUNGSZIELTAGESKONTO][MSGZAHLUNGSZIELTAGESKONTO]</td></tr>
</table>
</fieldset>
</div>

<div style="display:[EINZUGSERMAECHTIGUNG]" id="einzugsermaechtigung">
<fieldset><legend>Einzugserm&auml;chtigung</legend>
<table width="100%">
<tr><td width="200">{|Einzugsdatum (fr&uuml;hestens)|}:</td><td>[EINZUGSDATUM][MSGEINZUGSDATUM]</td></tr>
<!--<tr><td width="150">{|Inhaber|}:</td><td>[BANK_INHABER][MSGBANK_INHABER]</td></tr>
<tr><td>{|Institut|}:</td><td>[BANK_INSTITUT][MSGBANK_INSTITUT]</td></tr>
<tr><td>{|BLZ|}:</td><td>[BANK_BLZ][MSGBANK_BLZ]</td></tr>
<tr><td>{|Konto|}:</td><td>[BANK_KONTO][MSGBANK_KONTO]</td></tr>
-->
</table>
</fieldset>
</div>
<div style="display:[PAYPAL]" id="paypal">
</div>

<div style="display:[KREDITKARTE]" id="kreditkarte">
<fieldset><legend>{|Kreditkarte|}</legend>
<table>
<tr><td width="150">{|Kreditkarte|}:</td><td>[KREDITKARTE_TYP][MSGKREDITKARTE_TYP]</td>
</tr>
<tr><td>{|Karteninhaber|}:</td><td>[KREDITKARTE_INHABER][MSGKREDITKARTE_INHABER]</td>
</tr>
<tr><td>{|Kreditkartennummer|}:</td><td>[KREDITKARTE_NUMMER][MSGKREDITKARTE_NUMMER]</td>
</tr>
<tr><td>{|Pr&uuml;fnummer|}:</td><td>[KREDITKARTE_PRUEFNUMMER][MSGKREDITKARTE_PRUEFNUMMER]</td>
</tr>
<tr><td>{|G&uuml;ltig bis|}:</td><td>
[KREDITKARTE_MONAT][MSGKREDITKARTE_MONAT]&nbsp;
[KREDITKARTE_JAHR][MSGKREDITKARTE_JAHR]&nbsp;
</td>
</tr>
</table>

</fieldset>
</div>

<div>
<fieldset><legend>{|Skonto (nur bei Rechnung und Lastschrift)|}</legend>
<table width="100%">
<tr><td width="200">{|Skonto|}:</td><td>[ZAHLUNGSZIELSKONTO][MSGZAHLUNGSZIELSKONTO]</td></tr>
</table>
</fieldset>
</div>


[STARTDISABLEVERBAND]
<div style="">
<fieldset><legend>{|Verband|}</legend>
<table width="100%">
[VERBANDINFOSTART]<tr><td>{|Verband / Gruppe|}:</td><td colspan="6">[VERBAND]</td></tr>[VERBANDINFOENDE]<tr><td>{|Rabatt|}:</td><td>Grund %</td><td>1 in %</td><td>2 in %</td><td>3 in %</td><td>4 in %</td><td>5 in %</td></tr>
<tr><td></td>
<td>[RABATT][MSGRABATT]</td>
<td>[RABATT1][MSGRABATT1]</td>
<td>[RABATT2][MSGRABATT2]</td>
<td>[RABATT3][MSGRABATT3]</td>
<td>[RABATT4][MSGRABATT4]</td>
<td>[RABATT5][MSGRABATT5]</td>
</tr>
<tr><td colspan="7">Information:<br>[VERBANDINFO]</td></tr>
</table>
</fieldset>
</div>
[ENDEDISABLEVERBAND]


</div>
</div>
</div>
</div>


<div class="row">
<div class="row-height">
<div class="col-xs-12 col-sm-height">
<div class="inside inside-full-height">

<fieldset><legend>{|Interne Bemerkung|}</legend>
[INTERNEBEMERKUNG][MSGINTERNEBEMERKUNG]
</fieldset>

</div>
</div>
</div>
</div>

<div class="row">
<div class="row-height">
<div class="col-xs-12 col-sm-6 col-sm-height">
<div class="inside inside-full-height">

<fieldset><legend>UST-Pr&uuml;fung</legend>
<table width="100%">
<tr><td width="200">{|UST ID|}:</td><td>[USTID][MSGUSTID]</td></tr>
<tr><td>{|Besteuerung|}:</td><td>[UST_BEFREIT][MSGUST_BEFREIT]&nbsp;[KEINSTEUERSATZ][MSGKEINSTEUERSATZ]&nbsp;{|ohne Hinweis bei EU oder Export|}</td></tr>
<tr><td>{|UST-ID gepr&uuml;ft|}:</td><td>[UST_OK][MSGUST_OK]&nbsp;UST / Export gepr&uuml;ft + Freigabe f&uuml;r Versand</td></tr>
</table>
</fieldset>

</div>
</div>

<div class="col-xs-12 col-sm-6 col-sm-height">
<div class="inside inside-full-height">

<fieldset><legend>{|Einstellung|}</legend>
<table width="100%">
<tr><td width="200">{|Anzeige Steuer|}:</td><td>[ANZEIGESTEUER][MSGANZEIGESTEUER]</td></tr>
<tr><td>{|Währung|}:</td><td>[WAEHRUNG][MSGWAEHRUNG]</td></tr>
<tr><td>{|Sprache|}:</td><td>[SPRACHE][MSGSPRACHE]</td></tr>
<tr><td>{|Wechselkurs|}:</td><td>[KURS][MSGKURS]</td></tr>
<tr><td>{|Kostenstelle|}:</td><td>[KOSTENSTELLE][MSGKOSTENSTELLE]</td></tr>
</table>


</fieldset>

</div>
</div>
</div>
</div>



<table width="100%">
<tr><td align="right">
<input type="submit" name="speichern"
value="Speichern" />
</td></tr></table>
</div>


</form>

<div id="tabs-2">
<div class="overflow-scroll">

<!-- // rate anfang -->
<table width="100%" cellpadding="0" cellspacing="5" border="0" align="center">
<tr><td>


<!-- // ende anfang -->
<table width="100%" style="" align="center">
<tr>
<td width="33%"></td>
<td align="center"><b style="font-size: 14pt">Auftrag <font color="blue">[NUMMER]</font></b>[KUNDE][RABATTANZEIGE]</td>
<td width="33%" align="right">[ICONMENU2]</td>
</tr>
</table>


[POS]

</td></tr></table>

</div>
</div>

<div id="tabs-3">
</div>
<!-- tab view schließen -->
</div>

[FURTHERTABSDIV]
