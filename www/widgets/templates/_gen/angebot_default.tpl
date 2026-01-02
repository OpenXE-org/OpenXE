[SAVEPAGEREALLY]

  <script type="text/javascript"><!--
  
      jQuery(document).ready(function() {
        abweichend2();
      });

      function aktion_buchen(cmd)
      {
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

      function abweichend2(cmd)
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


<!-- gehort zu tabview -->
<div id="tabs">
  <ul>
    <li><a href="#tabs-1">{|Angebot|}</a></li>
    <li><a href="#tabs-2" onclick="callCursor();">{|Positionen|}</a></li>
    <li><a href="index.php?module=angebot&action=inlinepdf&id=[ID]&frame=true#tabs-3">{|Vorschau|}</a></li>
    [FURTHERTABS]
  </ul>

<div id="tabs-1">
[MESSAGE]
<form action="" method="post" name="eprooform" id="eprooform">
[LIEFERID][MSGLIEFERID]
[ANSPRECHPARTNERID][MSGANSPRECHPARTNERID]
[FORMHANDLEREVENT]

<!-- // rate anfang -->


<!-- // ende anfang -->

<div class="row">
  <div class="row-height">
    <div class="col-xs-12 col-sm-height">
      <div class="inside inside-full-height">
      <table width="100%" align="center">
      <tr> 
      <td>&nbsp;<b style="font-size: 14pt">[BEZEICHNUNGTITEL] <font color="blue">[NUMMER]</font></b>[KUNDE][RABATTANZEIGE]</td> 
      <td><!--[STATUSICONS]--></td>
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
        <tr><td>{|Kunde|}:</td><td>[ADRESSE][MSGADRESSE]
      [BUTTON_UEBERNEHMEN]
      </td></tr>
        <tr><td>{|Projekt|}:</td><td>[PROJEKT][MSGPROJEKT]</td></tr>
        <tr><td>[BEZEICHNUNGAKTIONSCODE]:</td><td>[AKTION][MSGAKTION]</td></tr>
        <tr><td>{|Status|}:</td><td>[STATUS]</td></tr>
        <tr><td>{|Ihre Anfrage|}:</td><td>[ANFRAGE][MSGANFRAGE]</td></tr>
        <tr><td>{|Interne Bezeichnung|}:</td><td>[INTERNEBEZEICHNUNG][MSGINTERNEBEZEICHNUNG]</td></tr>
        <tr><td>{|Datum|}:</td><td>[DATUM][MSGDATUM]</td></tr>
        <tr><td>{|Liefertermin|}:</td><td>[LIEFERDATUM][MSGLIEFERDATUM]&nbsp;[LIEFERDATUMKW][MSGLIEFERDATUMKW]&nbsp;KW</td></tr>
        <tr><td>{|Angebot g&uuml;ltig bis|}:</td><td>[GUELTIGBIS][MSGGUELTIGBIS]</td></tr>
        <tr><td>{|gepl. Auftragsdatum|}:</td><td>[PLANEDORDERDATE][MSGPLANEDORDERDATE]</td></tr>
        <tr><td>[VORWUNSCHLAGER]Bevorzugtes Lager:[NACHWUNSCHLAGER]<br></td><td>[VORWUNSCHLAGER][STANDARDLAGER][MSGSTANDARDLAGER][NACHWUNSCHLAGER]</td></tr>
        <tr><td>{|Schreibschutz|}:</td><td>[SCHREIBSCHUTZ][MSGSCHREIBSCHUTZ]&nbsp;</td></tr>
        <tr><td>[ABWEICHENDEBEZEICHNUNGBESCHRIFTUNG]:</td><td>[ABWEICHENDEBEZEICHNUNG][MSGABWEICHENDEBEZEICHNUNG]&nbsp;</td></tr>
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
            <tr><td>{|Name|}:</td><td>[LIEFERNAME][MSGLIEFERNAME]</td></tr>
            <tr><td>{|Titel|}:</td><td>[LIEFERTITEL][MSGLIEFERTITEL]</td></tr>
            <tr><td>{|Ansprechpartner|}:</td><td>[LIEFERANSPRECHPARTNER][MSGLIEFERANSPRECHPARTNER]</td></tr>
            <tr><td>{|Abteilung|}:</td><td>[LIEFERABTEILUNG][MSGLIEFERABTEILUNG]</td></tr>
            <tr><td>{|Unterabteilung|}:</td><td>[LIEFERUNTERABTEILUNG][MSGLIEFERUNTERABTEILUNG]</td></tr>
            <tr><td>{|Adresszusatz|}:</td><td>[LIEFERADRESSZUSATZ][MSGLIEFERADRESSZUSATZ]</td></tr>
            <tr><td>{|Stra&szlig;e|}:</td><td>[LIEFERSTRASSE][MSGLIEFERSTRASSE]</td><td>&nbsp;</td></tr>
            <tr><td>{|PLZ/Ort|}:</td><td>[LIEFERPLZ][MSGLIEFERPLZ]&nbsp;[LIEFERORT][MSGLIEFERORT]</td>
            </tr>
            [VORBUNDESSTAAT]<tr valign="top"><td><label for="lieferbundesstaat">{|Bundesstaat|}:</label></td><td colspan="2">[EPROO_SELECT_LIEFERBUNDESSTAAT]</td></tr>[NACHBUNDESSTAAT]
            <tr><td>{|Land|}:</td><td>[EPROO_SELECT_LIEFERLAND]</td>
            <tr><td>{|GLN|}:</td><td>[LIEFERGLN][MSGLIEFERGLN]</td><td>&nbsp;</td></tr>
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
                  <tr><td>{|Ansprechpartner|}:</td><td>[ANSPRECHPARTNER][MSGANSPRECHPARTNER]</td></tr>
                  <tr><td>{|Abteilung|}:</td><td>[ABTEILUNG][MSGABTEILUNG]</td></tr>
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
                    <tr><td></td><td>[ANSPRECHPARTNERPOPUP]&nbsp;</td></tr>
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


<!-- ende row 2 -->




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


      <fieldset><legend>{|Angebot|}</legend>
      <table width="100%">

      <tr><td>{|Zahlungsweise|}:</td><td>[ZAHLUNGSWEISE][MSGZAHLUNGSWEISE]</td></tr>
      <tr><td width="200">{|Versandart|}:</td><td>[VERSANDART][MSGVERSANDART]</td></tr>
      <tr><td><label for="lieferbedingung">{|Lieferbedingung|}:</label></td><td>[LIEFERBEDINGUNG][MSGLIEFERBEDINGUNG]</td></tr>

      <tr><td>{|Vertrieb|}:</td><td>[VERTRIEB][MSGVERTRIEB]&nbsp;[VERTRIEBBUTTON]</td></tr>
      <tr><td>{|Bearbeiter|}:</td><td>[BEARBEITER][MSGBEARBEITER]&nbsp;[INNENDIENSTBUTTON]</td></tr>

      <!--<tr><td>{|Auto-Versand|}:</td><td>[AUTOVERSAND][MSGAUTOVERSAND]-->
      <tr><td>{|Kein Porto|}:</td><td>[KEINPORTO][MSGKEINPORTO]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      {|Kein Briefpapier und Logo|}:&nbsp;[OHNE_BRIEFPAPIER][MSGOHNE_BRIEFPAPIER]</td></tr>
      <tr><td>{|Gesamtsumme ausblenden|}:</td><td>[GESAMTSUMMEAUSBLENDEN][MSGGESAMTSUMMEAUSBLENDEN]</td></tr>
      <tr><td>{|Artikeltexte ausblenden|}:</td><td>[OHNE_ARTIKELTEXT][MSGOHNE_ARTIKELTEXT]</td></tr>
      </table>
      </fieldset>

      </div>
    </div>

    <div class="col-xs-12 col-sm-6 col-sm-height">
      <div class="inside inside-full-height">

<fieldset><legend>{|Sonstiges|}</legend>
<table><tr><td width="200">{|GLN|}:</td><td>[GLN][MSGGLN]</td></tr></table>
</fieldset>

        <div id="rechnung" style="display:[RECHNUNG]">
          <fieldset><legend>{|Rechnung|}</legend>
          <table width="100%">
          <tr><td width="200">{|Zahlungsziel (in Tagen)|}:</td><td>[ZAHLUNGSZIELTAGE][MSGZAHLUNGSZIELTAGE]</td></tr>
          <tr><td nowrap>{| Skonto (in Tagen)|}:</td><td>[ZAHLUNGSZIELTAGESKONTO][MSGZAHLUNGSZIELTAGESKONTO]</td></tr>
          </table>
          </fieldset>
        </div>

        <div style="display:[EINZUGSERMAECHTIGUNG]" id="einzugsermaechtigung">
          <fieldset><legend>Einzugserm&auml;chtigung</legend>
          <table width="100%">
          <tr><td width="150">{|Inhaber|}:</td><td>[BANK_INHABER][MSGBANK_INHABER]</td></tr>
          <tr><td>{|Institut|}:</td><td>[BANK_INSTITUT][MSGBANK_INSTITUT]</td></tr>
          <tr><td>{|BLZ|}:</td><td>[BANK_BLZ][MSGBANK_BLZ]</td></tr>
          <tr><td>{|Konto|}:</td><td>[BANK_KONTO][MSGBANK_KONTO]</td></tr>
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

        <div><fieldset><legend>{|Skonto (nur bei Rechnung und Lastschrift)|}</legend>
          <table width="100%"><tr><td width="200">{|Skonto|}:</td><td>[ZAHLUNGSZIELSKONTO][MSGZAHLUNGSZIELSKONTO]</td></tr>
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
    <tr>
      <td align="right">
        <input type="submit" name="speichern" value="Speichern" />
      </td>
    </tr>
  </table>
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
        <td align="center"><b style="font-size: 14pt">Angebot <font color="blue">[NUMMER]</font></b>[KUNDE][RABATTANZEIGE]</td>
        <td width="33%" align="right">[ICONMENU2]</td>
      </tr>
    </table>

    [POS]


    </td></tr>
  </table>




</div>
</div>

<div id="tabs-3">
</div>

      [FURTHERTABSDIV]

 <!-- tab view schließen -->
</div>

