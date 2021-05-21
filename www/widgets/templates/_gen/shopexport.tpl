<style type="text/css">
  #erstezeile > td  {
    padding-top:8px;
  }
  input.aktionbutton
  {
    min-width:150px;height:50px;width:99%;
  }
</style>
<!-- gehort zu tabview -->
<div id="tabs">
  <ul>
    <li><a href="#tabs-1">{|Schnittstelle|}</a></li>
    <li><a href="#tabs-2">{|Einstellungen|}</a></li>
    <li><a href="#tabs-3">{|Zahlweisen|}</a></li>
    <li><a href="#tabs-4">{|Versandarten|}</a></li>
    <li><a href="#tabs-5">{|Freifelder|}</a></li>
    <li><a href="#tabs-6">{|Subshop|}</a></li>
    <li><a href="#tabs-7">{|Sprache-/Lieferland|}</a></li>
    <li><a href="#tabs-8">{|Gruppenmapping|}</a></li>
    <li><a href="#tabs-9">{|Smarty|}</a></li>
    [HOOKLITABS]
  </ul>
  <!-- erstes tab -->
  <form action="" method="post" id="frmpruefen">
    [PRUEFEN][MSGPRUEFEN]
  </form>
  <form action="" method="post" id="frmabholen">
    [AUFTRAGABHOLEN][MSGAUFTRAGABHOLEN]
  </form>
  [VORFORMULAR]
<form action="" method="post" name="eprooform" id="eprooform">
  <div id="tabs-1">
    <script type="application/javascript">
    function verpruefen()
    {
      $('#frmpruefen').submit();
      $('#tabs').loadingOverlay('show');
    }
    function Holeauftrag()
    {
      $('#frmabholen').submit();
      $('#tabs').loadingOverlay('show');
    }
    </script>
    [MESSAGE]
    <div class="row">
      <div class="row-height">
        <div class="col-xs-12 col-sm-10 col-sm-height">
          <div class="inside inside-full-height">
            <fieldset><legend>{|Einstellungen|}</legend>
              <table style="width:100%;" id="einstellungentab">
                <tr>
                  <td style="min-width:130px;"><label for="bezeichnung">{|Bezeichnung|}:</label></td>
                  <td>[BEZEICHNUNG][MSGBEZEICHNUNG]</td>
                  <td><label for="modus">{|Import-Modus|}:</label></td><td>[SELMODUS]</td>
                </tr>
                <tr>
                  <td><label for="aktiv">{|Aktiv|}:</label></td>
                  <td>[AKTIV][MSGAKTIV]</td>
                  <td><label for="einzelsync">{|Nur 1 Auftrag pro Anfrage|}:</label></td>
                  <td class="einzelsyncclass">[EINZELSYNC][MSGEINZELSYNC]</td>
                </tr>
                <tr>
                  <td><label for="projekt">{|Projekt|}:</label></td>
                  <td>[PROJEKT][MSGPROJEKT]</td>
                  <td><label for="warteschlange">{|Aufträge in Zwischentabelle|}:</label></td><td>[WARTESCHLANGE] <i>{|Freigabe erfolgt manuell|}</i>
                </td></tr>
                <tr><td><label for="abholmodus">{|Abholmodus|}:</label></td><td>[SELABHOLMODUS]</td><td class="manuellebegrenzung">{|Anzahl abholen begrenzen|}:</td><td class="manuellebegrenzung">[MAXMANUELL][MSGMAXMANUELL]&nbsp;<i>{|0 bedeutet Begrenzung auf 100|}</i></td></tr>
                <tr class="ab_nummerzeitraum zeitraum">
                  <td><label for="vondatum">{|Datum von|}:</label></td>
                  <td nowrap>
                    [VONDATUM][MSGVONDATUM]&nbsp;
                    <label for="vonzeit">{|Zeit|}:</label>
                    [VONZEIT][MSGVONZEIT]
                  </td>
                  <td></td>
                </tr>
                <tr class="ab_nummerzeitraum zeitraum">
                  <td class="trstartderschnittstelle"><label for="startdate">{|Start der Schnitstelle|}:</label></td>
                  <td class="trstartderschnittstelle">[STARTDATE][MSGSTARTDATE]</td>
                </tr>
                <tr class="ab_nummerzeitraum ab_nummer"><td><label for="ab_nummer">{|ab Nummer|}:</label></td><td>[AB_NUMMER][MSGAB_NUMMER]&nbsp;<i>{|Es werden alle Auftr&auml;ge ab dieser Nummer &uuml;bertragen|}.</i></td><td></td></tr>
                <!--<tr class="ab_nummerzeitraum ab_nummer"><td>{|Status &auml;ndern|}:</td><td>[NUMMERSYNCSTATUSAENDERN][MSGNUMMERSYNCSTATUSAENDERN]&nbsp;<i>{|Es wird der Status nach dem Abholen ge&auml;ndert|}.</i></td><td></td></tr>-->
                [VOREXTRA]
                <tr><td colspan="4" style="font-weight:bold;padding-top:40px;">{|Einstellungen f&uuml;r Shop oder Marktplatz|}:</td></tr>
                [NACHEXTRA]
                [EXTRAEINSTELLUNGEN]
                <tr>
                  <td colspan="3"></td>
                  <td><input type="submit" name="speichern" value="{|Speichern|}" /></td>
                </tr>
              </table>
            </fieldset>
          </div>
        </div>
        <div class="col-xs-12 col-sm-2 col-sm-height">
          <div class="inside inside-full-height">
            <fieldset><legend>{|Aktion|}</legend>
              <table width="100%">
                <tr><td><input type="button" onclick="verpruefen();" value="{|Verbindung pr&uuml;fen|}" class="aktionbutton" ></td></tr>
                <tr><td width="50%"><input type="button" onclick="Holeauftrag();" value="{|Auftr&auml;ge abholen|}" class="aktionbutton" ></td></tr>
                [AKTIONBUTTONS]
              </table>
            </fieldset>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="row-height">
        <div class="col-xs-12 col-sm-12 col-sm-height">
          <div class="inside-full-height">
            <fieldset><legend>{|Filter|}</legend>
            <table>
              <tr>
                <td>[NURFEHLER][MSGNURFEHLER]&nbsp;{|nur Fehler|}</td>
                <td>[AUFTRAEGE][MSGAUFTRAEGE]&nbsp;{|Auftr&auml;ge anzeigen|}</td>
                <td>[AENDERUNGEN][MSGAENDERUNGEN]&nbsp;{|&Auml;nderungen anzeigen|}</td>
              </tr>
            </table>
            </fieldset>
            [LOGTABELLE]
          </div>
        </div>
      </div>
    </div>
  </div>

  <div id="tabs-2">
    [MESSAGE]

    [FORMHANDLEREVENT]

    <script>
    function changedirektimport(el)
    {
      if($(el).prop('checked'))
      {
        $('#direktimport').prop('checked',false);
      }else{
        $('#direktimport').prop('checked',true);
      }
    }
    function changemodus(el)
    {
      switch($(el).val())
      {
        case 'demomodus':
          $('#demomodus').prop('checked', true);
          $('#cronjobaktiv').prop('checked', false);
        break;
        case 'manuell':
          $('#demomodus').prop('checked', false);
          $('#cronjobaktiv').prop('checked', false);
        break;
        case 'automatisch':
          $('#demomodus').prop('checked', false);
          $('#cronjobaktiv').prop('checked', true);
        break;
      }
    }
    function changeabholmodus(el)
    {
      switch($(el).val())
      {
        case 'status':
          $('#anzgleichzeitig').val(0);
          $('#holealle').prop('checked', false);
        break;
        case 'ab_nummer':
          $('#holealle').prop('checked', true);
          $('#anzgleichzeitig').val(0);
        break;
        case 'zeitbereich':
          var anzgleichzeitig = parseInt($('#anzgleichzeitig').val());
          if(isNaN(anzgleichzeitig))anzgleichzeitig = 0;
          if(anzgleichzeitig < 2)$('#anzgleichzeitig').val(50);
        break;
      }
    }

    </script>

    <div class="row">
      <div class="row-height">
        <div class="col-xs-12 col-md-12 col-md-height">
          <div class="inside inside-full-height">
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="row-height">
        <div class="col-xs-12 col-md-8 col-md-height">
          <div class="inside inside-full-height">
            <fieldset><legend>{|Auftrag Import|}</legend>
              <table width="100%">   
                <tr><td><u>{|Zahlungweisen-Mapping verwenden|}:</u></td><td>[ZAHLUNGSWEISENMAPPING][MSGZAHLUNGSWEISENMAPPING]</td></tr>
                <tr><td><u>{|Versandarten-Mapping verwenden|}:</u></td><td>[VERSANDARTENMAPPING][MSGVERSANDARTENMAPPING]</td></tr>
                <tr><td>{|Vorab als bezahlt markieren|}:</td><td>[VORABBEZAHLTMARKIEREN_OHNEVORKASSE_BAR][MSGVORABBEZAHLTMARKIEREN_OHNEVORKASSE_BAR]&nbsp;<i>({|Ohne Vorkasse, Bar, Nachnahme und Rechnung|})</i></td></tr>
                <tr><td>{|UTF8 Codierung|}:</td><td>[UTF8CODIERUNG][MSGUTF8CODIERUNG]</td><td></tr>
                <tr><td width="300">{|Multiprojekt Shop|}:</td><td>[MULTIPROJEKT][MSGMULTIPROJEKT]&nbsp;<i>{|In diesem Shop werden Artikel aus verschiedenen Projekten angeboten|}</i></td></tr>
                <tr><td>{|UST gepr&uuml;ft + Freigabe f&uuml;r Versand|}:</td><td>[UST_OK][MSGUST_OK]&nbsp;<i>({|Haken im Auftrag wird immer gesetzt|})</i></td></tr>
                <tr><td width="300"><u>{|Porto|}:</u></td><td>[ARTIKELPORTOAUTOSTART][ARTIKELPORTO][MSGARTIKELPORTO][ARTIKELPORTOAUTOEND]&nbsp;<i>{|Artikel-Nr. auf die das Porto gebucht wird.|}</i></td></tr>
                <tr><td width="300"><u>{|Porto erm&auml;&szlig;igt|}:</u></td><td>[ARTIKELPORTOERMAESSIGT][MSGARTIKELPORTOERMAESSIGT]&nbsp;<i>{|Artikel-Nr. auf die das erm&auml;&szlig;igte Porto gebucht wird.|}</i></td></tr>
                <tr><td><u>{|Portoartikel anlegen|}:</u></td><td>[PORTOARTIKELANLEGEN][MSGPORTOARTIKELANLEGEN]&nbsp;<i>({|falls nicht vorhanden|})</i></td></tr>
                <tr><td>{|Nachnahmegeb&uuml;hr als extra Position|}:</td><td>[ARTIKELNACHNAHME_EXTRAARTIKEL][MSGARTIKELNACHNAHME_EXTRAARTIKEL]</td></tr>
                <tr><td>{|Nachnahmegeb&uuml;hr|}:</td><td>[ARTIKELNACHNAHMEAUTOSTART][ARTIKELNACHNAHME][MSGARTIKELNACHNAHME][ARTIKELNACHNAHMEAUTOEND]&nbsp;<i>{|Artikel-Nr. f&uuml;r die Nachnahme Geb&uuml;hr.|}</i></td></tr>

                <tr><td><u>{|Auftragsstatus r&uuml;ckmelden|}:</u></td><td>[AUFTRAGABGLEICH][MSGAUFTRAGABGLEICH]&nbsp;<i>({|Von Xentral zu Shop|})</i></td></tr>
                <tr><td><label for="sendonlywithtracking">{|Automatische Rückmeldung deaktivieren|}:</label></td><td>[SENDONLYWITHTRACKING][MSGSENDONLYWITHTRACKING]</td></tr>
                <tr class="ab_nummerzeitraum ab_nummer"><td>{|Hole jeden Status|}:</td><td>[HOLEALLESTATI][MSGHOLEALLESTATI]&nbsp;<i>{|Es werden alle Auftr&auml;ge &uuml;bertragen von Shop auf Xentral unabh&auml;ngig vom Status.|}</i></td></tr>
                <tr><td><u>{|Freitext aus Shopschnittstelle|}:</u></td><td>
                [FREITEXT][MSGFREITEXT]
                </td></tr>
                <tr><td>{|Belege im Auto-Versand erstellen|}:</td><td>
                [AUTOVERSANDOPTION][MSGAUTOVERSANDOPTION]
                </td></tr>
                <tr><td>{|Angebote statt Auftr&auml;ge anlegen|}:</td><td>[ANGEBOTEANLEGEN][MSGANGEBOTEANLEGEN]</td></tr>
                <tr><td>{|Autoversand bei Kommentar in Warenkorb deaktivieren|}:</td><td>[AUTOVERSANDBEIKOMMENTARDEAKTIVIEREN][MSGAUTOVERSANDBEIKOMMENTARDEAKTIVIEREN]</td></tr>
                <tr><td><br><strong>{|ab hier importerspezifische Einstellungen|}:</strong><br><br></td></tr>
                <tr><td>{|Stornierung r&uuml;ckmelden|}:</td><td>[STORNOABGLEICH][MSGSTORNOABGLEICH]&nbsp;<i>({|Von Xentral zu Shop|})</i></td></tr>
                <tr><td>{|Besteuerung im Drittland abh&auml;ngig von Lieferadresse machen|}:</td><td>[STEUERFREILIEFERLANDEXPORT][MSGSTEUERFREILIEFERLANDEXPORT]</td></tr>
                <tr><td>{|Gesamtbetrag festsetzen|}:</td><td>[GESAMTBETRAGFESTSETZEN][MSGGESAMTBETRAGFESTSETZEN]</td></tr>
                <tr><td>{|Maximale Differenz zur berechneten Summe|}:</td><td>[GESAMTBETRAGFESTSETZENDIFFERENZ][MSGGESAMTBETRAGFESTSETZENDIFFERENZ]</td></tr>
                <tr><td>{|Lastschriftdaten in Adresse überschreiben|}:</td><td>[LASTSCHRIFTDATENUEBERSCHREIBEN][MSGLASTSCHRIFTDATENUEBERSCHREIBEN]</td></tr>
              </table>
            </fieldset>
          </div>
        </div>
        <div class="col-xs-12 col-md-4 col-md-height">
          <div class="inside_turkey inside-full-height">
            <fieldset class="turkey"><legend>{|Positionen im Auftrag|}</legend>
            <table>
              <tr><td width="300"><u>{|Fehlende Artikel anlegen|}:</u></td><td>[ARTIKELIMPORT][MSGARTIKELIMPORT]&nbsp;<span class="hinweisartikelimport" style="color:red;font-weight:bold;"> {|Hinweis: Achtung bei nicht vorhandenen Verkaufspreisen werden Artikel nicht &uuml;bernommen!|}</span></td></tr>
              <tr><td><u>{|Rabatte Porto festschreiben|}:</u></td><td>[RABATTEPORTOFESTSCHREIBEN][MSGRABATTEPORTOFESTSCHREIBEN]&nbsp;</td></tr>
              <tr><td>{|Beschreibungstexte aus Shop|}:</td><td>[ARTIKELTEXTEUEBERNEHMEN][MSGARTIKELTEXTEUEBERNEHMEN]</td></tr>
              <tr><td>{|Artikelnummern aus Nummernkreis|}:</td><td>[ARTIKELNUMMERNUMMERKREIS][MSGARTIKELNUMMERNUMMERKREIS]</td></tr>
              <tr><td></td><td>[ARTIKELIMPORTEINZELN][MSGARTIKELIMPORTEINZELN]&nbsp;einzeln&nbsp;<!--<i>(Nur bei Artikeln mit Option: Artikel->Online-Shop Optionen->Online Shop Abgleich)</i>--></td></tr>
              <tr><td>{|Artikelnummern aus Shop|}:</td><td>[ARTIKELNUMMERUEBERNEHMEN][MSGARTIKELNUMMERUEBERNEHMEN]</td></tr>
              <tr><td>{|Artikelbezeichnung aus Xentral|}:</td><td>[ARTIKELBEZEICHNUNGAUSWAWISION][MSGARTIKELBEZEICHNUNGAUSWAWISION]</td></tr>
              <tr><td>{|Artikelbeschreibungen aus Xentral|}:</td><td>[ARTIKELBESCHREIBUNGAUSWAWISION][MSGARTIKELBESCHREIBUNGAUSWAWISION]</td></tr>
              <tr><td>{|Artikelbeschreibungen aus Shop|}:</td><td>[ARTIKELBESCHREIBUNGENUEBERNEHMEN][MSGARTIKELBESCHREIBUNGENUEBERNEHMEN]</td></tr>
              <tr><td>{|St&uuml;cklisten erg&auml;nzen|}:</td><td>[STUECKLISTEERGAENZEN][MSGSTUECKLISTEERGAENZEN]</td></tr>
              <tr><td>{|Spezielle Steuers&auml;tze pro Positionen|}:</td><td>[POSITIONSTEUERSAETZEERLAUBEN][MSGPOSITIONSTEUERSAETZEERLAUBEN]</td></tr>
            </table>
          </fieldset>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="row-height">
      <div class="col-xs-12 col-md-8 col-md-height">
        <div class="inside inside-full-height">
          <fieldset><legend>{|Artikel Import / Export|}</legend>
            <table width="100%">
              <tr><td width="300"><u>{|Lagerzahlen &Uuml;bertragung erlauben|}:</u></td><td>[LAGEREXPORT][MSGLAGEREXPORT]&nbsp;<i>({|Von Xentral zu Shop|})</i></td></tr>
              <tr><td>{|Lager Grundlage|}:</u></td><td>[LAGERGRUNDLAGE][MSGLAGERGRUNDLAGE]</td></tr>
              <tr><td>{|Lagerkorrektur überschreiben|}:</td><td>[UEBERSCHREIBE_LAGERKORREKTURWERT][MSGUEBERSCHREIBE_LAGERKORREKTURWERT]</td></tr>
              <tr class="lagerkorrektur"><td>{|Lagerkorrektur|}:</td><td>[LAGERKORREKTURWERT][MSGLAGERKORREKTURWERT]</td></tr>
              [HOOK_STORAGE]
              <tr><td><u>{|Artikel &Uuml;bertragung erlauben|}:</u></td><td>[ARTIKELEXPORT][MSGARTIKELEXPORT]&nbsp;<i>({|Von Xentral zu Shop|})</i></td></tr>
              <tr><td>{|Alle geänderten Artikel automatisch übertragen|}:</td><td>[AUTOSENDARTICLE][MSGAUTOSENDARTICLE]&nbsp;<i>({|Von Xentral zu Shop|})</i></td></tr>
              <tr><td><br><strong>{|Ab hier importerspezifische Einstellungen|}:</strong><br><br></td></tr>
              <tr><td>{|Bilder &uuml;bertragen|}:</td><td>[SHOPBILDERUEBERTRAGEN][MSGSHOPBILDERUEBERTRAGEN]&nbsp;<i>({|Von Xentral zu Shop|})</i></td></tr>
              <tr><td>{|Eigenschaften &uuml;bertragen|}:</td><td>[EIGENSCHAFTENUEBERTRAGEN][MSGEIGENSCHAFTENUEBERTRAGEN]&nbsp;<i>({|Von Xentral zu Shop|})</i></td></tr>
              <tr><td>{|Kategorien &uuml;bertragen|}:</td><td>[KATEGORIENUEBERTRAGEN][MSGKATEGORIENUEBERTRAGEN]&nbsp;<i>({|Von Xentral zu Shop|})</i></td></tr>
              <tr><td>{|Varianten &uuml;bertragen|}:</td><td>[VARIANTENUEBERTRAGEN][MSGVARIANTENUEBERTRAGEN]&nbsp;<i>({|Von Xentral zu Shop|})</i></td></tr>
              <tr><td>{|Crossselling &uuml;bertragen|}:</td><td>[CROSSSELLINGARTIKELUEBERTRAGEN][MSGCROSSSELLINGARTIKELUEBERTRAGEN]&nbsp;<i>({|Von Xentral zu Shop|})</i></td></tr>
              <tr><td>{|Staffelpreise &uuml;bertragen|}:</td><td>[STAFFELPREISEUEBERTRAGEN][MSGSTAFFELPREISEUEBERTRAGEN]&nbsp;<i>({|Von Xentral zu Shop|})</i></td></tr>
              <tr><td>{|Gutscheine &uuml;bertragen|}:</td><td>[GUTSCHEINEUEBERTRAGEN][MSGGUTSCHEINEUEBERTRAGEN]&nbsp;<i>({|Via Prozessstarter|})</i></td></tr>
              <tr [NURPREISESTYLE]><td>{|Artikeltext &Uuml;bertragung unterdr&uuml;cken|}:</td><td>[NURPREISE][MSGNURPREISE]&nbsp;<i>({|Von Xentral zu Shop|})</i></td></tr>
              <tr [NURARTIKELLISTESTYLE]><td>{|Artikelliste abholen nur neue Artikel anlegen|}:</td><td>[NURNEUEARTIKEL][MSGNURNEUEARTIKEL]&nbsp;<i>({|Von Shop zu Xentral|})</i></td></tr>
              <tr [NURARTIKELLISTESTYLE]><td>{|Artikelnummer beim Anlegen aus Shop &uuml;bernehmen|}:</td><td>[ARTIKELNUMMERBEIMANLEGENAUSSHOP][MSGARTIKELNUMMERBEIMANLEGENAUSSHOP]&nbsp;<i>({|Von Shop zu Xentral|})</i></td></tr>

            </table>
          </fieldset>
        </div>
      </div>

      <div class="col-xs-12 col-md-4 col-md-height">
        <div class="inside inside-full-height">
          <fieldset><legend>{|Adressen Import|}</legend>
            <table width="100%">
              <tr><td>{|Bestehende Kunden nur aus Projekt verwenden|}:</td><td>[KUNDENURVONPROJEKT][MSGKUNDENURVONPROJEKT]&nbsp;</td></tr>
              <tr><td>{|Bestehende Adressen nicht &uuml;berschreiben|}:</td><td>[ADRESSENNICHTUEBERSCHREIBEN][MSGADRESSENNICHTUEBERSCHREIBEN]&nbsp;</td></tr>
              <tr><td width="300">{|Manuelle Adress&uuml;bertragung|}:</td><td>[ADRESSUPDATE][MSGADRESSUPDATE]&nbsp;</td></tr>
              <tr><td>{|Vertrieb|}:</td><td>[VERTRIEB][MSGVERTRIEB]</td></tr>
            </table>
          </fieldset>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="row-height">
      <div class="col-xs-12 col-md-8 col-md-height">
        <div class="inside inside-full-height">
          <fieldset><legend>{|Rabatte|}</legend>
            <table width="100%">
              <tr><td width="300">{|Rabatt-Artikel|}:</td><td>[ARTIKELRABATT][MSGARTIKELRABATT]&nbsp;</td></tr>
              <tr><td>{|Steuersatz für Rabatt-Artikel|}:</td><td>[ARTIKELRABATTSTEUER][MSGARTIKELRABATTSTEUER]&nbsp;<i>% Steuer.</i></td></tr>
            </table>
          </fieldset>
        </div>
      </div>
      <div class="col-xs-12 col-md-4 col-md-height">
        <div class="inside inside-full-height">
          <fieldset><legend>{|Extra Verkaufspreise|}</legend>
            <table width="100%">
              <tr><td>{|Preisgruppe|}:</td><td>[PREISGRUPPE][MSGPREISGRUPPE]&nbsp;</td></tr>
            </table>
          </fieldset>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="row-height">
      <div class="col-xs-12 col-md-8 col-md-height">
        <div class="inside inside-full-height">
          <fieldset><legend>Zugangsdaten f&uuml;r Xentral Import Plugin</legend>
            <table width="100%">
              <tr><td>{|URL|}:</td><td>[URL][MSGURL]&nbsp;<i>URL zur externen Importer</i></td><td></tr>
              <tr><td width="300">{|ImportKey|}:</td><td>[PASSWORT][MSGPASSWORT]&nbsp;<i>32 Zeichen langes Sicherheitspasswort</i></td><td></tr>
              <tr><td>{|ImportToken|}:</td><td>[TOKEN][MSGTOKEN]&nbsp;<i>6 Zeichen langes Sicherheitstoken</i></td><td></tr>
            </table>
          </fieldset>
          <div style="display:none;">[DATUMVON][MSGDATUMVON][CRONJOBAKTIV][MSGCRONJOBAKTIV][DEMOMODUS][MSGDEMOMODUS][DIREKTIMPORT][MSGDIREKTIMPORT][HOLEALLE][MSGHOLEALLE][ANZGLEICHZEITIG][MSGANZGLEICHZEITIG]</div>
        </div>
      </div>
      
      <div class="col-xs-12 col-md-4 col-md-height">
        <div class="inside inside-full-height">
          <fieldset><legend>{|Kommunikations-Einstellungen|}</legend>
            <table width="100%">
              <tr><td>{|Schnittstelle|}:</td><td>
                [MODULENAME][MSGMODULENAME]
                &nbsp;</td></tr>
            </table>
          </fieldset>
        </div>
      </div>
    </div>
  </div>
  <div style="float:right"><input type="submit" name="speichern" value="Speichern" /></div>
</div>
</form>
<div id="tabs-3">
[NEUTAB3]
[TAB3]
</div>
<div id="tabs-4">
[NEUTAB4]
[TAB4]
</div>
<div id="tabs-5">
[NEUTAB5]
[TAB5]
</div>
<div id="tabs-6">
[NEUTAB6]
[TAB6]
</div>
<div id="tabs-7">
[NEUTAB7]
[TAB7]
</div>
<div id="tabs-8">
[NEUTAB8]
[TAB8]
</div>
  <div id="tabs-9">
    [NEUTAB9]

    [TAB9]
  </div>
[HOOKTABS]

<!-- tab view schließen -->
</div>


[FORMULAR]


<script type="text/javascript">
  var aktmodule = '';
function selectmodule(el)
{
  if($(el).val() != aktmodule)
  {
    if(confirm('{|Wollen Sie wirklich die verwendete Kommunikations-Schnittstelle zum Shopmodul wechseln? Alle Shop spezifischen Einstellungen gehen hierdurch verloren! Es bleiben nur alle Verknüpfungen zu Artikel, Kategorien, Fremdnummer, Versand- und Zahlungsmappings sowie Subshops bestehen.|}'))
    {
      $('#eprooform').find('input[type="submit"]').first().trigger('click');
    }else{
      $(el).val(aktmodule);
    }
  }
}
$(document).ready(function() {
    $('input.btnzwischentabelle').each(function(){
      $.ajax({
        url: 'index.php?module=onlineshops&action=edit&cmd=getnotimortedorders',
        data: {
          id: [ID]
        },
        method: 'post',
        dataType: 'json',
        success: function(data) {
          if(typeof data.count != 'undefined' && data.count > 0) {
            $('input.btnzwischentabelle').val($('input.btnzwischentabelle').val()+' ('+data.count+')');
          }
        }
      });
    });
//    $('#artikel').focus();
    aktmodule = $('#modulename').val();
    $("#editZahlweisen").dialog({
    minWidth: 400,
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    autoOpen: false,
    buttons: {
      '{|ABBRECHEN|}': function() {
        $(this).dialog('close');
      },
      '{|SPEICHERN|}': function() {
        zahlweisenEditSave();
      }
    }
  });

    $("#editVersandarten").dialog({
    minWidth: 400,
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    autoOpen: false,
    buttons: {
      '{|ABBRECHEN|}': function() {
        $(this).dialog('close');
      },
      '{|SPEICHERN|}': function() {
        versandartenEditSave();
      }
    }
  });

    $("#editKundengruppen").dialog({
        modal: true,
        bgiframe: true,
        closeOnEscape:false,
        autoOpen: false,
        buttons: {
            '{|ABBRECHEN|}': function() {
                $(this).dialog('close');
            },
            '{|SPEICHERN|}': function() {
                kundengruppenEditSave();
            }
        }
    });

    $("#editFreifelder").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    autoOpen: false,
    buttons: {
      '{|ABBRECHEN|}': function() {
        $(this).dialog('close');
      },
      '{|SPEICHERN|}': function() {
        freifelderEditSave();
      }
    }
  });

    $("#editSprachen").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    autoOpen: false,
    buttons: {
      '{|ABBRECHEN|}': function() {
        $(this).dialog('close');
      },
      '{|SPEICHERN|}': function() {
        sprachenEditSave();
      }
    }
  });
  
    $("#editSubshop").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    autoOpen: false,
    buttons: {
      '{|ABBRECHEN|}': function() {
        $(this).dialog('close');
      },
      '{|SPEICHERN|}': function() {
        subshopEditSave();
      }
    }
  });
  $('#abholmodus').trigger('change');
  $('#maxmanuell').on('change',function(){changemaxmanuell();});
  $('#abholmodus').on('change',function(){changemaxmanuell();});
  changemaxmanuell();
});

function artikelbaumexport(){
    if(!confirm("Soll der Artikelbaum jetzt übertragen werden?")) return false;
    $('#tabs').loadingOverlay('show');
    $.ajax({
        url: 'index.php?module=onlineshops&action=exportartikelbaum',
        data: {
            id: [ID]
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {
            $('#tabs').loadingOverlay('remove');
            alert(data.statusText);
        }
    });
}

function changemaxmanuell()
{
  var abholmodus = $('#abholmodus').val();
  var menge = parseFloat($('#maxmanuell').val());
  if(abholmodus != 'zeitbereich' && menge > 0)
  {
    $('#einzelsync').prop('disabled',true);
    if(menge == 1)
    {
      $('#einzelsync').prop('checked', true);
    }else{
      $('#einzelsync').prop('checked', false);
    }
  }else{
    $('#einzelsync').prop('disabled',false);
  }
}

function zahlweisenEditSave() {
    var c_vorabbezahltmarkieren = $('#b_vorabbezahltmarkieren').is(':checked');
    var c_autoversand = $('#b_autoversand').is(':checked');
    var c_aktiv = $('#b_aktiv').is(':checked');
    var c_keinerechnung = $('#b_keinerechnung').is(':checked');
    var c_fastlane = $('#b_fastlane').is(':checked');
    if(c_keinerechnung)c_keinerechnung=1;else c_keinerechnung=0;
    if(c_autoversand)c_autoversand=1;else c_autoversand=0;
    if(c_vorabbezahltmarkieren)c_vorabbezahltmarkieren=1;else c_vorabbezahltmarkieren=0;
    if(c_aktiv)c_aktiv=1;else c_aktiv=0;
    if(c_fastlane)c_fastlane=1;else c_fastlane=0;

    $.ajax({
        url: 'index.php?module=onlineshops&action=zahlweiseeditsave',
        data: {
            id: $('#b_id').val(),
            zahlweise_shop: $('#b_zahlweise_shop').val(),
            zahlweise_wawision: $('#b_zahlweise_wawision').val(),
            vorabbezahltmarkieren: c_vorabbezahltmarkieren,
            autoversand: c_autoversand,
            aktiv: c_aktiv,
            keinerechnung:c_keinerechnung,
            fastlane:c_fastlane
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {

            App.loading.close();
            if (data.status == 1) {
                $('#editZahlweisen').find('#b_id').val('');

                updateLiveTable();
                $("#editZahlweisen").dialog('close');
            } else {
                alert(data.statusText);
            }
        }
    });

}

function updateLiveTable() {
    var oTableL = $('#shopexport_zahlweisen').dataTable();
    oTableL.fnFilter('%');
    oTableL.fnFilter('');
}


function updateLiveTableVersandart() {
    var oTableR = $('#shopexport_versandarten').dataTable();
    oTableR.fnFilter('%');
    oTableR.fnFilter('');
}

function updateLiveTableKundengruppen() {
    var oTableR = $('#shopexport_kundengruppen').dataTable();
    oTableR.fnFilter('%');
    oTableR.fnFilter('');
}

function updateLiveTableFreifeld() {
    var oTableR = $('#shopexport_freifelder').dataTable();
    oTableR.fnFilter('%');
    oTableR.fnFilter('');
}

function updateLiveTableSubshop() {
    var oTableR = $('#shopexport_subshop').dataTable();
    oTableR.fnFilter('%');
    oTableR.fnFilter('');
}

function updateLiveTableSprachen() {
    var oTableR = $('#shopexport_sprachen').dataTable();
    oTableR.fnFilter('%');
    oTableR.fnFilter('');
}

function sprachenEdit(id)
{
    $.ajax({
        url: 'index.php?module=onlineshops&action=sprachenget',
        data: {
            id: id
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {
            $('#editSprachen').find('#d_id').val(data.id);
            $('#editSprachen').find('#d_land').val(data.land);
            $('#editSprachen').find('#d_sprache').val(data.sprache);
            $('#editSprachen').find('#d_projekt').val(data.projekt);

            if(data.aktiv==1)
              $('#editSprachen').find('#d_aktiv').prop('checked', true);
            else
              $('#editSprachen').find('#d_aktiv').prop('checked', false);
            App.loading.close();
            $("#editSprachen").dialog('open');
        }
    });
}

function zahlweisenEdit(id) {

    $.ajax({
        url: 'index.php?module=onlineshops&action=zahlweiseget',
        data: {
            id: id
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {
            $('#editZahlweisen').find('#b_id').val(data.id);
            $('#editZahlweisen').find('#b_zahlweise_shop').val(data.zahlweise_shop);
            $('#editZahlweisen').find('#b_zahlweise_wawision').val(data.zahlweise_wawision);

            if(data.vorabbezahltmarkieren==1)
              $('#editZahlweisen').find('#b_vorabbezahltmarkieren').prop('checked', true);
            else
              $('#editZahlweisen').find('#b_vorabbezahltmarkieren').prop('checked', false);

            if(data.autoversand==1)
              $('#editZahlweisen').find('#b_autoversand').prop('checked', true);
            else
              $('#editZahlweisen').find('#b_autoversand').prop('checked', false);

            if(data.keinerechnung==1)
              $('#editZahlweisen').find('#b_keinerechnung').prop('checked', true);
            else
              $('#editZahlweisen').find('#b_keinerechnung').prop('checked', false);
            if(data.aktiv==1)
              $('#editZahlweisen').find('#b_aktiv').prop('checked', true);
            else
              $('#editZahlweisen').find('#b_aktiv').prop('checked', false);
            if(data.fastlane==1)
              $('#editZahlweisen').find('#b_fastlane').prop('checked', true);
            else
              $('#editZahlweisen').find('#b_fastlane').prop('checked', false);
            App.loading.close();
            $("#editZahlweisen").dialog('open');
        }
    });

}

function zahlweiseSave(formular) {

    var formularDatas = $(formular).serialize();
    $.ajax({
        url: 'index.php?module=onlineshops&action=zahlweisesave',
        data: formularDatas,
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {
            if (data.status == 1) {
                updateLiveTable();
                $(formular).find('input[type="text"]').val('');
            } else {
                alert(data.statusText);
            }
            App.loading.close();
        }
    });
    return false;
}

function sprachenSave(formular) {

    var formularDatas = $(formular).serialize();
    $.ajax({
        url: 'index.php?module=onlineshops&action=sprachensave',
        data: formularDatas,
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {
            if (data.status == 1) {
                updateLiveTableSprachen();
                $(formular).find('input[type="text"]').val('');
            } else {
                alert(data.statusText);
            }
            App.loading.close();
        }
    });
    return false;
}

function kundengruppenSave(formular) {
    var formularDatas = $(formular).serialize();
    $.ajax({
        url: 'index.php?module=onlineshops&action=kundengruppensave',
        data: formularDatas,
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {
            if (data.status == 1) {
                updateLiveTableKundengruppen();
                $(formular).find('input[type="text"]').val('');
            } else {
                alert(data.statusText);
            }
            App.loading.close();
        }
    });
    return false;
}

function freifelderSave(formular)
{
    var formularDatas = $(formular).serialize();
    $.ajax({
        url: 'index.php?module=onlineshops&action=freifeldersave',
        data: formularDatas,
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {
            if (data.status == 1) {
                updateLiveTableFreifeld();
                $(formular).find('input[type="text"]').val('');
            } else {
                alert(data.statusText);
            }
            App.loading.close();
        }
    });
    return false;
}


function subshopSave(formular)
{
    var formularDatas = $(formular).serialize();
    $.ajax({
        url: 'index.php?module=onlineshops&action=subshopsave',
        data: formularDatas,
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {
            if (data.status == 1) {
                updateLiveTableSubshop();
                $(formular).find('input[type="text"]').val('');
            } else {
                alert(data.statusText);
            }
            App.loading.close();
        }
    });
    return false;
}

function versandartenEditSave() {
    var c_aktiv = $('#b_aktiv2').is(':checked');
    if(c_aktiv)c_aktiv=1;else c_aktiv=0;
    var c_fastlane = $('#b_fastlane2').is(':checked');
    if(c_fastlane)c_fastlane=1;else c_fastlane=0;
    var c_autoversand = $('#b_autoversand2').is(':checked');
    if(c_autoversand)c_autoversand=1;else c_autoversand=0;
 
    $.ajax({
        url: 'index.php?module=onlineshops&action=versandarteditsave',
        data: {
            id: $('#b_id2').val(),
            versandart_shop: $('#b_versandart_shop').val(),
            versandart_wawision: $('#b_versandart_wawision').val(),
            versandart_ausgehend: $('#b_versandart_ausgehend').val(),
            produkt_ausgehend: $('#b_produkt_ausgehend').val(),
            land: $('#b_land').val(),
            autoversand: c_autoversand,
            aktiv: c_aktiv,
            fastlane: c_fastlane
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {

            App.loading.close();
            if (data.status == 1) {
                $('#editVersandarten').find('#b_id2').val('');
                $('#editVersandarten').find('#b_versandart_shop').val('');
                $('#editVersandarten').find('#b_versandart_wawision').val('');
                $('#editVersandarten').find('#b_versandart_ausgehend').val('');
                $('#editVersandarten').find('#b_produkt_ausgehend').val('');
                updateLiveTableVersandart();
                $("#editVersandarten").dialog('close');
            } else {
                alert(data.statusText);
            }
        }
    });

}

function freifelderEditSave() {
    var c_aktiv = $('#b_aktiv3').is(':checked');
    if(c_aktiv)c_aktiv=1;else c_aktiv=0;

 
    $.ajax({
        url: 'index.php?module=onlineshops&action=freifeldereditsave',
        data: {
            id: $('#b_id3').val(),
            freifeld_shop: $('#b_freifeld_shop').val(),
            freifeld_wawi: $('#b_freifeld_wawi').val(),
            aktiv: c_aktiv
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {

            App.loading.close();
            if (data.status == 1) {
                $('#editFreifelder').find('#b_id3').val('');
                $('#editFreifelder').find('#b_freifeld_wawi').val('');
                $('#editFreifelder').find('#b_freifeld_shop').val('');
                updateLiveTableFreifeld();
                $("#editFreifelder").dialog('close');
            } else {
                alert(data.statusText);
            }
        }
    });

}

function sprachenEditSave() {
    var d_aktiv = $('#d_aktiv').is(':checked');
    if(d_aktiv)d_aktiv=1;else d_aktiv=0;

 
    $.ajax({
        url: 'index.php?module=onlineshops&action=spracheneditsave',
        data: {
            id: $('#d_id').val(),
            land: $('#d_land').val(),
            projekt: $('#d_projekt').val(),
            sprache: $('#d_sprache').val(),
            aktiv: d_aktiv?1:0
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {

            App.loading.close();
            if (data.status == 1) {
                $('#editSprachen').find('#d_id').val('');
                $('#editSprachen').find('#d_land').val('');
                $('#editSprachen').find('#d_sprache').val('');
                $('#editSprachen').find('#d_projekt').val('');
                updateLiveTableSprachen();
                $("#editSprachen").dialog('close');
            } else {
                alert(data.statusText);
            }
        }
    });

}

function subshopEditSave() {
    var c_aktiv = $('#b_aktiv4').is(':checked');
    if(c_aktiv)c_aktiv=1;else c_aktiv=0;

 
    $.ajax({
        url: 'index.php?module=onlineshops&action=subshopeditsave',
        data: {
            id: $('#b_id4').val(),
            subshopkennung: $('#b_subshopkennung').val(),
            projekt: $('#b_projekt').val(),
            aktiv: c_aktiv,
            sprache: $('#b_sprache').val()
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {

            App.loading.close();
            if (data.status == 1) {
                $('#editSubshop').find('#b_id4').val('');
                $('#editSubshop').find('#b_subshopkennung').val('');
                $('#editSubshop').find('#b_sprache').val('');
                $('#editSubshop').find('#b_projekt').val('');
                updateLiveTableSubshop();
                $("#editSubshop").dialog('close');
            } else {
                alert(data.statusText);
            }
        }
    });

}

function kundengruppenEditSave() {
    var c_aktiv = $('#k_aktiv').is(':checked');
    if(c_aktiv)c_aktiv=1;else c_aktiv=0;
    var c_neukundenzuweisen = $('#k_neukundengruppezuweisen').is(':checked');
    if(c_neukundenzuweisen)c_neukundenzuweisen=1;else c_neukundenzuweisen=0;


    $.ajax({
        url: 'index.php?module=onlineshops&action=kundengruppeneditsave',
        data: {
            id: $('#k_id').val(),
            kundengruppexentral: $('#k_kundengruppe').val(),
            projekt: $('#k_projekt').val(),
            aktiv: c_aktiv,
            kundengruppeneukundenzuweisen: c_neukundenzuweisen,
            kundengruppeshop: $('#k_extbezeichnung').val(),
            shop: $('#k_shop').val(),
            zuweisungrolle:$('#k_rolle').val(),
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {

            App.loading.close();
            if (data.status == 1) {
                updateLiveTableKundengruppen();
                $("#editKundengruppen").dialog('close');
            } else {
                alert(data.statusText);
            }
        }
    });

}

function kundengruppenEdit(id)
{
    $.ajax({
        url: 'index.php?module=onlineshops&action=kundengruppenget',
        data: {
            id: id
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {
            $('#editKundengruppen').find('#k_id').val(data.id);
            $('#editKundengruppen').find('#k_shop').val(data.shopid);
            $('#editKundengruppen').find('#k_extbezeichnung').val(data.extgruppename);
            $('#editKundengruppen').find('#k_projekt').val(data.projekt);
            $('#editKundengruppen').find('#k_rolle').val(data.type);

            if(data.aktiv==1)
                $('#editKundengruppen').find('#k_aktiv').prop('checked',true);
            else
                $('#editKundengruppen').find('#k_aktiv').prop('checked',false);

            if(data.neukundengruppezuweisen==1)
                $('#editKundengruppen').find('#k_neukundengruppezuweisen').prop('checked',true);
            else
                $('#editKundengruppen').find('#k_neukundengruppezuweisen').prop('checked',false);

            $('#editKundengruppen').find('#k_kundengruppe').val(data.gruppeid);


            App.loading.close();
            $("#editKundengruppen").dialog('open');
        }
    });
}


function versandartenEdit(id) 
{
    $.ajax({
        url: 'index.php?module=onlineshops&action=versandartget',
        data: {
            id: id
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {
            $('#editVersandarten').find('#b_id2').val(data.id);
            $('#editVersandarten').find('#b_versandart_shop').val(data.versandart_shop);
            $('#editVersandarten').find('#b_versandart_wawision').val(data.versandart_wawision);
            $('#editVersandarten').find('#b_versandart_ausgehend').val(data.versandart_ausgehend);
            $('#editVersandarten').find('#b_produkt_ausgehend').val(data.produkt_ausgehend);
            $('#editVersandarten').find('#b_land').val(data.land);

            if(data.aktiv==1)
              $('#editVersandarten').find('#b_aktiv2').prop('checked',true);
            else
              $('#editVersandarten').find('#b_aktiv2').prop('checked',false);

            if(data.autoversand==1)
              $('#editVersandarten').find('#b_autoversand2').prop('checked',true);
            else
              $('#editVersandarten').find('#b_autoversand2').prop('checked',false);

            if(data.fastlane==1)
              $('#editVersandarten').find('#b_fastlane2').prop('checked',true);
            else
              $('#editVersandarten').find('#b_fastlane2').prop('checked',false);

            App.loading.close();
            $("#editVersandarten").dialog('open');
        }
    });
}

function versandartSave(formular) {

    var formularDatas = $(formular).serialize();
    $.ajax({
        url: 'index.php?module=onlineshops&action=versandartsave',
        data: formularDatas,
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {
            if (data.status == 1) {
                updateLiveTableVersandart();
                $(formular).find('input[type="text"]').val('');
            } else {
                alert(data.statusText);
            }
            App.loading.close();
        }
    });
    return false;
}

function freifelderEdit(id) {
    $.ajax({
        url: 'index.php?module=onlineshops&action=freifeldget',
        data: {
            id: id
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {
            $('#editFreifelder').find('#b_id3').val(data.id);
            $('#editFreifelder').find('#b_freifeld_shop').val(data.freifeld_shop);
            $('#editFreifelder').find('#b_freifeld_wawi').val(data.freifeld_wawi);
            

            if(data.aktiv==1)
              $('#editFreifelder').find('#b_aktiv3').prop('checked',true);
            else
              $('#editFreifelder').find('#b_aktiv3').prop('checked',false);

            App.loading.close();
            $("#editFreifelder").dialog('open');
        }
    });

}


function subshopEdit(id) {
    $.ajax({
        url: 'index.php?module=onlineshops&action=subshopget',
        data: {
            id: id
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {
            $('#editSubshop').find('#b_id4').val(data.id);
            $('#editSubshop').find('#b_subshopkennung').val(data.subshopkennung);
            $('#editSubshop').find('#b_projekt').val(data.projekt);
            $('#editSubshop').find('#b_sprache').val(data.sprache);
            

            if(data.aktiv==1)
              $('#editSubshop').find('#b_aktiv4').prop('checked',true);
            else
              $('#editSubshop').find('#b_aktiv4').prop('checked',false);

            App.loading.close();
            $("#editSubshop").dialog('open');
        }
    });

}



</script>
