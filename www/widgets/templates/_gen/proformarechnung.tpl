<script type="text/javascript"><!--

jQuery(document).ready(function() {
           abweichend2();
    });



function abweichend2()
{
  var inp = 'in'+'put';
  var sel = 'sel'+'ect';
  jQuery('table.tableabweichend').find(inp).prop('disabled', true);
  jQuery('table.tableabweichend').find(sel).prop('disabled', true);
  jQuery('table.tableabweichend').find(inp).first().prop('disabled', false);
  if(document.getElementById('abweichendelieferadresse').checked)
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
    <li><a href="#tabs-1">Proformarechnung</a></li>
    <li><a href="#tabs-2" onclick="callCursor();">Positionen</a></li>
    <li><a href="index.php?module=proformarechnung&action=inlinepdf&id=[ID]&frame=true#tabs-3">Vorschau</a></li>
		[FURTHERTABS]
  </ul>



<div id="tabs-1">
[MESSAGE]
<form action="" method="post" name="eprooform" id="eprooform">
[LIEFERID][MSGLIEFERID]
[ANSPRECHPARTNERID][MSGANSPRECHPARTNERID]
[FORMHANDLEREVENT]

<!-- // rate anfang -->

<div class="row"><div class="row-height"><div class="col-xs-12 col-sm-height"><div class="inside inside-full-height">
<!-- // ende anfang -->
<table width="100%" align="center">
<tr>
<td>&nbsp;<b style="font-size: 14pt">Proformarechnung <font color="blue">[NUMMER]</font></b>[KUNDE][RABATTANZEIGE]</td>
<td></td>
<td align="right" nowrap>[ICONMENU]&nbsp;[SAVEBUTTON]</td>
</tr>
</table>
</div></div></div></div>


<div class="row"><div class="row-height">
<div class="col-xs-12 col-md-6 col-sm-height">
<div class="inside inside-full-height">
<fieldset><legend>{|Allgemein|}</legend>
<table class="mkTableFormular">
<tr><td width="200">{|Kunde|}:</td><td>[ADRESSE][MSGADRESSE]&nbsp;
[BUTTON_UEBERNEHMEN]
</td></tr>  <tr><td>{|Projekt|}:</td><td>[PROJEKTAUTOSTART][PROJEKT][MSGPROJEKT][PROJEKTAUTOEND]</td></tr>
  <tr><td>{|Aktionscode|}:</td><td>[AKTION][MSGAKTION]</td></tr>
  <tr><td>{|Status|}:</td><td>[STATUS]</td></tr>
  <tr><td>{|Auftrag|}:</td><td>[AUFTRAGID][MSGAUFTRAGID]</td></tr>
  <tr><td>{|Ihre Bestellnummer|}:</td><td>[IHREBESTELLNUMMER][MSGIHREBESTELLNUMMER]</td></tr>
  <tr><td>{|Interne Bezeichnung|}:</td><td>[INTERNEBEZEICHNUNG][MSGINTERNEBEZEICHNUNG]</td></tr>
  <tr><td>{|Lieferdatum|}:</td><td>[LIEFERDATUM][MSGLIEFERDATUM]</td></tr>
  <tr><td>{|Lieferschein|}:</td><td>[LIEFERSCHEINAUTOSTART][LIEFERSCHEIN][MSGLIEFERSCHEIN][LIEFERSCHEINAUTOEND]</td></tr>
  <tr><td>{|Datum|}:</td><td>[DATUM][MSGDATUM]</td></tr>
  <tr><td>{|Proformarechnungskopie|}:</td><td>[DOPPEL][MSGDOPPEL]&nbsp;</td></tr>
  <tr><td>{|Schreibschutz|}:</td><td>[SCHREIBSCHUTZ][MSGSCHREIBSCHUTZ]&nbsp;</td></tr>
  <tr><td>[ABWEICHENDEBEZEICHNUNGBESCHRIFTUNG]:</td><td>[ABWEICHENDEBEZEICHNUNG][MSGABWEICHENDEBEZEICHNUNG]&nbsp;</td></tr>
</table>
</fieldset>
</div></div>


<div class="col-xs-12 col-sm-6 col-sm-height">
<div class="inside_turkey inside-full-height">


<div id="abweichendelieferadressestyle">
<fieldset class="turkey"><legend>{|Abweichende Lieferadresse|}</legend>
  <table class="tableabweichend">
    <tr><td width="200">{|Abweichende Lieferadresse|}:</td><td>[ABWEICHENDELIEFERADRESSE][MSGABWEICHENDELIEFERADRESSE]</td></tr>
    <tr><td>{|Name|}:</td><td>[LIEFERNAME][MSGLIEFERNAME]</td></tr>
                  <tr><td>{|Titel|}:</td><td>[LIEFERTITEL][MSGLIEFERTITEL]</td></tr>
    <tr><td>{|Ansprechpartner|}:</td><td>[LIEFERANSPRECHPARTNER][MSGLIEFERANSPRECHPARTNER]</td></tr>
    <tr><td>{|Abteilung|}:</td><td>[LIEFERABTEILUNG][MSGLIEFERABTEILUNG]</td></tr>
    <tr><td>{|Unterabteilung|}:</td><td>[LIEFERUNTERABTEILUNG][MSGLIEFERUNTERABTEILUNG]</td></tr>
    <tr><td>{|Adresszusatz|}:</td><td>[LIEFERADRESSZUSATZ][MSGLIEFERADRESSZUSATZ]</td></tr>
    <tr><td>{|Stra&szlig;e|}:</td><td>[LIEFERSTRASSE][MSGLIEFERSTRASSE]</td></tr>
    <tr><td>{|PLZ/Ort|}:</td><td>[LIEFERPLZ][MSGLIEFERPLZ]&nbsp;[LIEFERORT][MSGLIEFERORT]</td>
    </tr>
    [VORBUNDESSTAAT]<tr valign="top"><td><label for="lieferbundesstaat">{|Bundesstaat|}:</label></td><td colspan="2">[EPROO_SELECT_LIEFERBUNDESSTAAT]</td></tr>[NACHBUNDESSTAAT]
    <tr><td>{|Land|}:</td><td>[EPROO_SELECT_LIEFERLAND]</td></tr>    <tr><td>{|GLN|}:</td><td>[LIEFERGLN][MSGLIEFERGLN]</td></tr><tr><td></td><td>[LIEFERADRESSEPOPUP]&nbsp;[ANSPRECHPARTNERLIEFERADRESSEPOPUP]&nbsp;[ADRESSELIEFERADRESSEPOPUP]</td></tr>
</table>
</fieldset>
</div>


</div>
</div>
</div>
</div> <!-- spalte 2 zu -->



<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-height">
<div class="inside inside-full-height">

<fieldset><legend>{|Stammdaten|}</legend>
<table border="0" class="mkTableFormular">
            <tr><td width="200">{|Typ|}:</td><td width="200">[TYP][MSGTYP]</td></tr>
            <tr><td>{|Name|}:</td><td>[NAME][MSGNAME]</td></tr>
<tr><td>{|Titel|}:</td><td>[TITEL][MSGTITEL]</td></tr>
            <tr><td>{|Ansprechpartner|}:</td><td>[ANSPRECHPARTNER][MSGANSPRECHPARTNER]</td></tr>            <tr><td>{|Abteilung|}:</td><td>[ABTEILUNG][MSGABTEILUNG]</td></tr>
            <tr><td>{|Unterabteilung|}:</td><td>[UNTERABTEILUNG][MSGUNTERABTEILUNG]</td></tr>
            <tr><td>{|Adresszusatz|}:</td><td>[ADRESSZUSATZ][MSGADRESSZUSATZ]</td></tr>
            <tr><td>Stra&szlig;e</td><td>[STRASSE][MSGSTRASSE]</td></tr>
            <tr><td>PLZ/Ort</td><td>[PLZ][MSGPLZ]&nbsp;[ORT][MSGORT]</td></tr>
            [VORBUNDESSTAAT]<tr valign="top"><td><label for="bundesstaat">{|Bundesstaat|}:</label></td><td colspan="2">[EPROO_SELECT_BUNDESSTAAT]</td></tr>[NACHBUNDESSTAAT]
            <tr><td>{|Land|}:</td><td>[EPROO_SELECT_LAND]</td></tr>
            <tr><td>{|extra Verzollungsadresse|}:</td><td>[VERZOLLUNGADRESSE][MSGVERZOLLUNGADRESSE]</td></tr>
            
  </table>

    <table class="mkTableFormular">
              <tr><td>{|Telefon|}:</td><td>[TELEFON][MSGTELEFON]</td></tr>
              <tr><td>{|Telefax|}:</td><td>[TELEFAX][MSGTELEFAX]</td></tr>
            <tr><td>{|E-Mail|}:</td><td>[EMAIL][MSGEMAIL]</td></tr>
             <tr><td>Anschreiben</td><td>[ANSCHREIBEN][MSGANSCHREIBEN]</td></tr>
              <tr><td></td><td>[ANSPRECHPARTNERPOPUP]</td></tr>
  </table>
</fieldset>

</div>
</div>

</div>
</div>


<div class="row verzollung">
<div class="row-height">
<div class="col-xs-12 col-md-6 col-sm-height">
<div class="inside inside-full-height">

<fieldset><legend>{|Verzollung|}</legend>
<table border="0" class="mkTableFormular">
            <tr><td>{|Name|}:</td><td>[VERZOLLUNGNAME][MSGVERZOLLUNGNAME]</td></tr>
            <tr><td>{|Titel|}:</td><td>[VERZOLLUNGTITEL][MSGVERZOLLUNGTITEL]</td></tr>
            <tr><td>{|Ansprechpartner|}:</td><td>[VERZOLLUNGANSPRECHPARTNER][MSGVERZOLLUNGANSPRECHPARTNER]</td></tr>            
            <tr><td>{|Abteilung|}:</td><td>[VERZOLLUNGABTEILUNG][MSGVERZOLLUNGABTEILUNG]</td></tr>
            <tr><td>{|Unterabteilung|}:</td><td>[VERZOLLUNGUNTERABTEILUNG][MSGVERZOLLUNGUNTERABTEILUNG]</td></tr>
            <tr><td>{|Adresszusatz|}:</td><td>[VERZOLLUNGADRESSZUSATZ][MSGVERZOLLUNGADRESSZUSATZ]</td></tr>
            <tr><td>Stra&szlig;e</td><td>[VERZOLLUNGSTRASSE][MSGVERZOLLUNGSTRASSE]</td></tr>
            <tr><td>{|PLZ/Ort|}:</td><td>[VERZOLLUNGPLZ][MSGVERZOLLUNGPLZ]&nbsp;[VERZOLLUNGORT][MSGVERZOLLUNGORT]</td></tr>
            <tr><td>{|Land|}:</td><td>[EPROO_SELECT_LAND_VERZOLLUNG]</td></tr>
            <tr><td></td><td>[ADRESSEVERZOLLUNGADRESSEPOPUP]</td></tr>
  </table>
</fieldset>

</div>
</div>
<div class="col-xs-12 col-md-6 col-sm-height">
<div class="inside inside-full-height">

<fieldset><legend>{|Zollinformationen|}</legend>
  [VERZOLLINFORMATIONEN][MSGVERZOLLINFORMATIONEN]
</fieldset>

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
    </div>  </div>
</div>


<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-6 col-sm-height">
<div class="inside inside-full-height">

<fieldset><legend>{|Proformarechnung|}</legend>
<table width="100%">
<tr><td width="200">{|Zahlungsweise|}:</td><td>[ZAHLUNGSWEISE][MSGZAHLUNGSWEISE]</td></tr>
<!--<tr><td>{|Buchhaltung|}:</td><td>[BUCHHALTUNG][MSGBUCHHALTUNG]</td></tr>-->
<tr><td>{|Vertrieb|}:</td><td>[VERTRIEB][MSGVERTRIEB]&nbsp;[VERTRIEBBUTTON]</td></tr>
<tr><td>{|Bearbeiter|}:</td><td>[BEARBEITER][MSGBEARBEITER]</td></tr>
<tr><td><label for="lieferbedingung">{|Lieferbedingung|}:</label></td><td>[LIEFERBEDINGUNG][MSGLIEFERBEDINGUNG]</td></tr>
<tr><td>{|Kein Briefpapier und Logo|}:</td><td>[OHNE_BRIEFPAPIER][MSGOHNE_BRIEFPAPIER]</td></tr>
<tr><td>{|Inkl. Zollinformationen|}:</td><td>[ZOLLINFORMATION][MSGZOLLINFORMATION]&nbsp;<i>{|Gewicht, Zolltarifnummer, Herkunftsland|}</i></td></tr>
</table>
</fieldset>

</div>
</div>

<div class="col-xs-12 col-md-6 col-sm-height">
<div class="inside inside-full-height">

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
<fieldset><legend>{|Proformarechnung|}</legend>
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
<!--
<tr><td>{|Inhaber|}:</td><td>[BANK_INHABER][MSGBANK_INHABER]</td></tr>
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
        <tr><td width="200">{|Kreditkarte|}:</td><td>[KREDITKARTE_TYP][MSGKREDITKARTE_TYP]</td>
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
<fieldset><legend>{|Skonto (nur bei Proformarechnung und Lastschrift)|}</legend>
<table width="100%">
<tr><td width="200">{|Skonto|}:</td><td>[ZAHLUNGSZIELSKONTO][MSGZAHLUNGSZIELSKONTO]</td></tr>
</table>
</fieldset>
</div>


[STARTDISABLEVERBAND]
<div style="">
<fieldset><legend>{|Verband|}</legend>
<table width="100%">
[VERBANDINFOSTART]<tr><td width="200">{|Verband / Gruppe|}:</td><td colspan="6">[VERBAND]</td></tr>[VERBANDINFOENDE]<tr><td>{|Rabatt|}:</td><td>Grund %</td><td>1 in %</td><td>2 in %</td><td>3 in %</td><td>4 in %</td><td>5 in %</td></tr>
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


<fieldset><legend>{|UST-Pr&uuml;fung|}</legend>
<table width="100%">
<tr><td width="200">{|UST ID|}:</td><td>[USTID][MSGUSTID]</td></tr>

<tr><td>{|Besteuerung|}:</td><td>[UST_BEFREIT][MSGUST_BEFREIT]&nbsp;[KEINSTEUERSATZ][MSGKEINSTEUERSATZ]&nbsp;{|ohne gesetzlichen Hinweistext bei EU oder Export|}</td></tr>
<tr><td>{|Brief bestellt|}:</td><td>[USTBRIEF][MSGUSTBRIEF]</td></tr>
<tr><td>{|Brief Eingang|}:</td><td>[USTBRIEF_EINGANG][MSGUSTBRIEF_EINGANG]</td></tr>
<tr><td>{|Brief Eingang am|}:</td><td>[USTBRIEF_EINGANG_AM][MSGUSTBRIEF_EINGANG_AM]</td></tr>
</table>
</fieldset>
</div>
</div>

    <div class="col-xs-12 col-sm-6 col-sm-height">
      <div class="inside inside-full-height">

        <fieldset><legend>{|Einstellung|}</legend>
        <table width="100%">
        <tr><td width="200">{|Währung|}:</td><td>[WAEHRUNG][MSGWAEHRUNG]</td></tr>
        <tr><td>{|Sprache|}:</td><td>[SPRACHE][MSGSPRACHE]</td></tr>
        </table>


        </fieldset>

      </div>
    </div>
</div>
</div>



<br><br>
<table width="100%">
<tr><td align="right">
    <input type="submit" name="speichern"
    value="Speichern" />
</td></tr></table>

</form>
  </div>

<div id="tabs-2">
<div class="overflow-scroll">

<!-- // rate anfang -->
<table width="100%" cellpadding="0" cellspacing="5" border="0" align="center">
<tr><td>


<!-- // ende anfang -->
<table width="100%" style="" align="center">
<tr>
<td width="33%"></td>
<td align="center"><b style="font-size: 14pt">{|Proformarechnung|} <font color="blue">[NUMMER]</font></b>[KUNDE][RABATTANZEIGE]</td>
<td width="33%" align="right">[ICONMENU2]</td>
</tr>
</table>



[POS]

</td></tr></table>


</div>
</div>
<div id="tabs-3">
</div>

[FURTHERTABSDIV]
 <!-- tab view schließen -->
</div>

