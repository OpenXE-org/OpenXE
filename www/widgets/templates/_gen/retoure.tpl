 <script type="text/javascript"><!--

  $(document).ready(function(){
		lieferantenretoureanzeige(0);
		$('#fortschritt').prop('disabled',false);
    $('#fortschritt').css('background-color','');
  });


  function lieferantenretoureanzeige(cmd)
  {
    if(document.getElementById('lieferantenretoure').checked)
		{
      document.getElementById('kundestyle').style.display="none";
      document.getElementById('lieferantenretourestyle').style.display="";
		} else {
      document.getElementById('lieferantenretourestyle').style.display="none";
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
    <li><a href="#tabs-2">{|Retoure|}</a></li>
    <li><a href="#tabs-4" onclick="callCursor();">{|Positionen|}</a></li>
    <li><a href="index.php?module=retoure&action=inlinepdf&id=[ID]&frame=true#tabs-3">{|Vorschau|}</a></li>
    [FURTHERTABS]
  </ul>


  <div id="tabs-2">
  [MESSAGE]
    <form action="" method="post" name="eprooform" id="eprooform">
      [LIEFERID][MSGLIEFERID]
      [ANSPRECHPARTNERID][MSGANSPRECHPARTNERID]
      [FORMHANDLEREVENT]


    <!-- // ende anfang -->
    <div class="row">
    <div class="row-height">
    <div class="col-xs-12 col-sm-height">
    <div class="inside inside-full-height">
    <table width="100%" align="center">
    <tr>
    <td>&nbsp;<b style="font-size: 14pt">Retoure <font color="blue">[NUMMER]</font></b>[KUNDE]</td>
    <td>[STATUSICONS]</td>
    <td align="right" nowrap>[ICONMENU]&nbsp;[SAVEBUTTON]</td>
    </tr>
    </table>

    </div>
    </div>
    </div>
    </div>

    <div class="row">
    <div class="row-height">
    <div class="col-xs-12 col-sm-5 col-sm-height">
    <div class="inside inside-full-height">

    <fieldset><legend>{|Allgemein|}</legend>
    <table class="mkTableFormular">
      <tr id="kundestyle"><td><label for="adresse">{|Kunde|}:</label></td><td>[ADRESSE][MSGADRESSE]&nbsp;[BUTTON_UEBERNEHMEN]</td></tr>
      <tr id="lieferantenretourestyle"><td><label for="lieferant">{|Lieferant|}:</label></td><td>[LIEFERANT][MSGLIEFERANT]&nbsp;[BUTTON_UEBERNEHMEN2]</td></tr>
      <tr><td><label for="lieferantenretoure">{|an Lieferanten|}:</label></td><td>[LIEFERANTENRETOURE][MSGLIEFERANTENRETOURE]&nbsp;</td></tr>
      <tr><td><label for="projekt">{|Projekt|}:</label></td><td>[PROJEKTAUTOSTART][PROJEKT][MSGPROJEKT][PROJEKTAUTOEND]</td></tr>
      <tr><td><label for="status">{|Status|}:</label></td><td>[STATUS]</td></tr>
      <tr><td><label for="auftragid">{|Auftrag|}:</label></td><td>[AUFTRAGID][MSGAUFTRAGID]</td></tr>
      <tr><td><label for="lieferscheinid">{|Lieferschein|}:</label></td><td>[LIEFERSCHEINID][MSGLIEFERSCHEINID]</td></tr>
      <tr><td><label for="gutschrift_id">{|Gutschrift|}:</label></td><td>[GUTSCHRIFT_ID][MSGGUTSCHRIFT_ID]</td></tr>
      <tr><td><label for="replacementorder_id">{|Ersatz-Auftrag|}:</label></td><td>[REPLACEMENTORDER_ID][MSGREPLACEMENTORDER_ID]</td></tr>
      <tr><td><label for="ihrebestellnummer">{|Ihre Bestellnummer|}:</label></td><td>[IHREBESTELLNUMMER][MSGIHREBESTELLNUMMER]</td></tr>
      <tr><td><label for="internebezeichnung">{|Interne Bezeichnung|}:</label></td><td>[INTERNEBEZEICHNUNG][MSGINTERNEBEZEICHNUNG]</td></tr>
      <tr><td><label for="datum">{|Datum|}:</label></td><td>[DATUM][MSGDATUM]</td></tr>
      <tr><td>[VORWUNSCHLAGER]Bevorzugtes Lager:[NACHWUNSCHLAGER]<br></td><td>[VORWUNSCHLAGER][STANDARDLAGER][MSGSTANDARDLAGER][NACHWUNSCHLAGER]</td></tr>
    [VORKOMMISSIONSKONSIGNATIONSLAGER]<tr><td>[KOMMISSIONIERLAGER]:<br></td><td>[KOMMISSIONSKONSIGNATIONSLAGER][MSGKOMMISSIONSKONSIGNATIONSLAGER]</td></tr>[NACHKOMMISSIONSKONSIGNATIONSLAGER]
      <tr><td><label for="schreibschutz">{|Schreibschutz|}:</label></td><td>[SCHREIBSCHUTZ][MSGSCHREIBSCHUTZ]&nbsp;</td></tr>
      <tr><td>[ABWEICHENDEBEZEICHNUNGBESCHRIFTUNG]:</td><td>[ABWEICHENDEBEZEICHNUNG][MSGABWEICHENDEBEZEICHNUNG]&nbsp;</td></tr>
      <tr><td><label for="fortschritt">{|Fortschritt|}:</label></td>
        <td>
          [FORTSCHRITT][MSGFORTSCHRITT]
        </td>
      </tr>

    </table>
    </fieldset>

    </div>
    </div>
    <div class="col-xs-12 col-sm-4 col-sm-height">
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
    <tr><td></td><td>[LIEFERADRESSEPOPUP]&nbsp;[ANSPRECHPARTNERLIEFERADRESSEPOPUP]&nbsp;[ADRESSELIEFERADRESSEPOPUP]</td></tr>
    </table>
    </fieldset>
    </div>

    </div>
    </div>
      <div class="col-xs-12 col-sm-3 col-sm-height">
        <div class="inside-full-height">
          <fieldset><legend>{|Aktion|}</legend>
            [RETOUREBUTTONS]
          </fieldset>
        </div>
      </div>
    </div>
    </div>


    <div class="row">
      <div class="row-height">
        <div class="col-xs-12 col-md-9 col-sm-height">
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
              </table>

              <table class="mkTableFormular">
                  <tr><td>{|Telefon|}:</td><td>[TELEFON][MSGTELEFON]</td></tr>
                  <tr><td>{|Telefax|}:</td><td>[TELEFAX][MSGTELEFAX]</td></tr>
                <tr><td>{|E-Mail|}:</td><td>[EMAIL][MSGEMAIL]</td></tr>
                 <tr><td>Anschreiben</td><td>[ANSCHREIBEN][MSGANSCHREIBEN]</td></tr>
                  <tr><td></td><td>[LIEFERADRESSEPOPUP]&nbsp;[ANSPRECHPARTNERPOPUP]&nbsp;[ADRESSEANSPRECHPARTNERPOPUP]</td></tr>
              </table>
            </fieldset>
          </div>
        </div>
        <div class="col-xs-12 col-md-3 col-sm-height">
          <div class="inside inside-full-height">
            <fieldset><legend>&nbsp;</legend></fieldset>
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
        <div class="col-xs-12 col-md-6 col-sm-height">
          <div class="inside inside-full-height">
            <fieldset><legend>{|Retoure|}</legend>
              <table class="mkTableFormular">
                <tr><td>{|Versandart|}:</td><td>[VERSANDART][MSGVERSANDART]</td></tr>
                <tr><td><label for="lieferbedingung">{|Lieferbedingung|}:</label></td><td>[LIEFERBEDINGUNG][MSGLIEFERBEDINGUNG]</td></tr>

                <tr><td>{|Vertrieb|}:</td><td>[VERTRIEB][MSGVERTRIEB]&nbsp;[VERTRIEBBUTTON]</td></tr>
                <tr><td>{|Bearbeiter|}:</td><td>[BEARBEITER][MSGBEARBEITER]&nbsp;[INNENDIENSTBUTTON]</td></tr>
                <tr><td>{|Keine Rechnung|}:</td><td>[KEINERECHNUNG][MSGKEINERECHNUNG]</td></tr>
                <tr><td>{|Kein Briefpapier und Logo|}:</td><td>[OHNE_BRIEFPAPIER][MSGOHNE_BRIEFPAPIER]</td></tr>
                <tr><td>{|Artikeltexte ausblenden|}:</td><td>[OHNE_ARTIKELTEXT][MSGOHNE_ARTIKELTEXT]</td></tr>
              </table>
            </fieldset>
          </div>
        </div>
        <div class="col-xs-12 col-md-6 col-sm-height">
          <div class="inside inside-full-height">
            <fieldset><legend>{|Sonstiges|}</legend>
              <table class="mkTableFormular">
              <tr><td>{|GLN|}:</td><td>[GLN][MSGGLN]</td></tr>
              </table>
            </fieldset>
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
              <tr><td>{|Besteuerung|}:</td><td>[UST_BEFREIT][MSGUST_BEFREIT]</td></tr>
              </table>
            </fieldset>
          </div>
        </div>

        <div class="col-xs-12 col-sm-6 col-sm-height">
          <div class="inside inside-full-height">
            <fieldset><legend>{|Einstellung|}</legend>
              <table width="100%">
                <tr width="200"><td>{|Sprache|}:</td><td>[SPRACHE][MSGSPRACHE]</td></tr>
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
  </form>
</div>

  <div id="tabs-4">

  <!-- // rate anfang -->
    <table width="100%" cellpadding="0" cellspacing="5" border="0" align="center">
      <tr><td>
      <!-- // ende anfang -->
      <table width="100%" style="" align="center">
        <tr>
          <td width="33%"></td>
          <td align="center"><b style="font-size: 14pt">{|Retoure|} <font color="blue">[NUMMER]</font></b>[KUNDE][RABATTANZEIGE]</td>
          <td width="33%" align="right">[ICONMENU2]</td>
        </tr>
      </table>
      [POS]
      </td></tr>
    </table>

  </div>

  <div id="tabs-3"></div>

  [FURTHERTABSDIV]

 <!-- tab view schließen -->
</div>

