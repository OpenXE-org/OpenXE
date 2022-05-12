<script type="text/javascript">

  $('body').on('change', '#rechnungsdatum', function() {
    $('#zahlbarbis').val("");
    $('#skontobis').val("");
  });

  $(document).ready(function(){

      var art = $('select[name=art]').val();

      if(art=='lieferant') {
        document.getElementById('lieferant_span').style.display="";
        document.getElementById('kunde_span').style.display="none";
        document.getElementById('mitarbeiter_span').style.display="none";
        document.getElementById('sonstige_span').style.display="none";
      }

      if(art=='kunde') {
        document.getElementById('lieferant_span').style.display="none";
        document.getElementById('kunde_span').style.display="";
        document.getElementById('mitarbeiter_span').style.display="none";
        document.getElementById('sonstige_span').style.display="none";
      }

      if(art=='mitarbeiter') {

        document.getElementById('lieferant_span').style.display="none";
        document.getElementById('kunde_span').style.display="none";
        document.getElementById('mitarbeiter_span').style.display="";
        document.getElementById('sonstige_span').style.display="none";
      }

      if(art=='sonstige') {
        document.getElementById('lieferant_span').style.display="none";
        document.getElementById('kunde_span').style.display="none";
        document.getElementById('mitarbeiter_span').style.display="none";
        document.getElementById('sonstige_span').style.display="";
      }

    $('#rechnung').on('change',function(){testrechnung();});
    testrechnung();
  });

  function testrechnung()
  {
    $.ajax({
        url: 'index.php?module=verbindlichkeit&action=edit&cmd=checkrechnung&id=[ID]',
        type: 'POST',
        dataType: 'json',
        data: { rechnung: $('#rechnung').val(), adresse :  $('#adresse').val()},
        success: function(data) {
          if(data && data.status == 1)
          {
            $('#rechnungmsg').remove();
          }else{
            var rechnungenex = $('#rechnungmsg').length;
            if(rechnungenex == 0)$('#rechnung').after('<span id="rechnungmsg" style="color:red;">&nbsp;{|Warnung: Doppelt!|}</span>');
          }

          if($('#showinmonitoring').prop('checked')) window.location.href = window.location.href.split("#")[0];
        },
        beforeSend: function() {

        }
    });
  }

	function onchange_art(el)  {
    if(el=='lieferant') {
      document.getElementById('lieferant_span').style.display="";
      document.getElementById('kunde_span').style.display="none";
      document.getElementById('mitarbeiter_span').style.display="none";
      document.getElementById('sonstige_span').style.display="none";
    }

    else if(el=='kunde') {
      document.getElementById('lieferant_span').style.display="none";
      document.getElementById('kunde_span').style.display="";
      document.getElementById('mitarbeiter_span').style.display="none";
      document.getElementById('sonstige_span').style.display="none";
    }

    else if(el=='mitarbeiter') {
      document.getElementById('lieferant_span').style.display="none";
      document.getElementById('kunde_span').style.display="none";
      document.getElementById('mitarbeiter_span').style.display="";
      document.getElementById('sonstige_span').style.display="none";
    }
 		else {
      document.getElementById('lieferant_span').style.display="none";
      document.getElementById('kunde_span').style.display="none";
      document.getElementById('mitarbeiter_span').style.display="none";
      document.getElementById('sonstige_span').style.display="";
    } 
  }

</script>

<form action="" method="post" name="eprooform" enctype="multipart/form-data">
  <div id="tabs">
    <ul>
      <li><a href="#tabs-1">{|Zahlung|}</a></li>
      <li><a href="#tabs-2">{|Zuordnung Bestellungen|}</a></li>
      <li><a href="#tabs-3">{|Vorkontierung|}</a></li>
      <li><a href="#tabs-4">{|Positionen|}</a></li>
      <li><a href="#tabs-5">{|Protokoll|}</a></li>
    </ul>

    <div id="tabs-1">
      [FORMHANDLEREVENT]
      [MESSAGE]

      <div class="row">
      <div class="row-height">
      <div class="col-xs-12 col-md-8 col-md-height">
      <div class="inside inside-full-height">

        <fieldset>
          <legend>{|Rechnungsdaten|}</legend>
          <table border="0">
	          <tr valign="top">
              <td width="150">{|Lieferant|}:</td>
              <td colspan="2">[ADRESSEAUTOSTART][ADRESSE][MSGADRESSE]
                [ADRESSEAUTOEND][BUTTONADRESSE]&nbsp;<input type="submit" value="&uuml;bernehmen" name="uebernehmen"/></td>
              <td colspan="2" align="center"><b style="color:green">[MELDUNG]</b><br><font size="7">[BELEGNR]</font></td>
            </tr>

            <tr>
              <td>Rechnungs Nr.:</td>
              <td>[RECHNUNG][MSGRECHNUNG]</td>
              <td>&nbsp;</td>
              <td>{|Eingangsdatum|}:</td>
              <td>[EINGANGSDATUM][MSGEINGANGSDATUM]</td>
            </tr>
 
						<tr>
              <td>{|Bestellung|}:</td>
              <td width="250">[DISABLESTART][BESTELLUNG][MSGBESTELLUNG][LINKBESTELLUNG][MULTIBESTELLUNG][DISABLEENDE]</td>
              <td>&nbsp;</td>
              <td width="200">{|Zahlweise|}:</td>
              <td>[ZAHLUNGSWEISE][MSGZAHLUNGSWEISE]</td>
            </tr>

            <tr>
              <td>{|Rechnungsdatum|}:</td>
              <td width="250">[RECHNUNGSDATUM][MSGRECHNUNGSDATUM]</td>
              <td>&nbsp;</td>
              <td width="200">{|Zahlbar bis|}:</td>
              <td>[ZAHLBARBIS][MSGZAHLBARBIS][DATUM_ZAHLBARBIS]</td>
            </tr>

	  			  <tr>
              <td>{|Betrag/Total (Brutto)|}:</td>
              <td nowrap>[BETRAG][MSGBETRAG]&nbsp;[WAEHRUNG][MSGWAEHRUNG]</td><td>&nbsp;</td>
						  <td>{|Skonto in %|}:</td>
              <td>[SKONTO][MSGSKONTO]</td>
				    </tr>

	  			  <tr>
              <td nowrap>{|USt. normal|}:</td>
              <td nowrap>[USTNORMAL][MSGUSTNORMAL]%[SUMMENORMAL][MSGSUMMENORMAL]&nbsp;<input type="button" value="{|berechnen|}" onclick="steuerberechnen(this);"></td><td>&nbsp;</td>
              <td>{|Skonto festsetzen|}:<!--Umsatzsteuer--></td>
              <td>[SKONTOFESTSETZEN][MSGSKONTOFESTSETZEN]<!--[UMSATZSTEUER][MSGUMSATZSTEUER]--></td>
             					  </tr>

            <tr>
              <td nowrap>{|USt. ermässigt|}:</td>
              <td nowrap>[USTERMAESSIGT][MSGUSTERMAESSIGT]%[SUMMEERMAESSIGT][MSGSUMMEERMAESSIGT]&nbsp;<input type="button" value="{|berechnen|}" onclick="steuerberechnen(this);"></td>
              <td>&nbsp;</td>
              <td>{|Skonto bis|}:</td>
              <td>[SKONTOBIS][MSGSKONTOBIS][DATUM_SKONTOBIS]</td>
  					</tr>

            <tr>
              <td nowrap>[WEITERESTEUER1]:</td>
              <td nowrap>[USTSTUER3][MSGUSTSTUER3]%[SUMMESATZ3][MSGSUMMESATZ3]&nbsp;<input type="button" value="{|berechnen|}" onclick="steuerberechnen(this);"></td>
              <td>&nbsp;</td>
              <td>Umsatzsteuer</td>
              <td>[UMSATZSTEUER][MSGUMSATZSTEUER]</td>
   				  </tr>

            <tr>
              <td nowrap>[WEITERESTEUER2]:</td>
              <td nowrap>[USTSTUER4][MSGUSTSTUER4]%[SUMMESATZ4][MSGSUMMESATZ4]&nbsp;<input type="button" value="{|berechnen|}" onclick="steuerberechnen(this);"></td>
              <td>&nbsp;</td>
              <td>USt-ID:</td>
              <td>[USTID][MSGUSTID]
            </tr>
          
            <tr>
              <td>{|Buchungstext|}:</td>
              <td>[VERWENDUNGSZWECK][MSGVERWENDUNGSZWECK]</td>
              <td>&nbsp;</td>
              <td>{|Frachtkosten|}:</td>
              <td>[FRACHTKOSTEN][MSGFRACHTKOSTEN]</td>
					  </tr>

            <tr>
              <td>{|Projekt|}:</td>
              <td>[PROJEKT][MSGPROJEKT]</td>
              <td>&nbsp;</td>
	            <td>{|Freigabe|}:</td>
              <td>[FREIGABE][MSGFREIGABE]&nbsp;<i>{|Waren-/Leistungsprüfung (Einkauf)|}</i></td>
	          </tr>

            <tr>
              <td>{|Teilprojekt|}:</td>
              <td>[TEILPROJEKT][MSGTEILPROJEKT]</td>
              <td>&nbsp;</td>
	            <td></td>
	            <td></td>
	          </tr>


            <tr>
              <td>{|Auftrag|}:</td>
              <td>[AUFTRAG][MSGAUFTRAG]</td>
              <td>&nbsp;</td>
	            <td></td>
              <td>[RECHNUNGSFREIGABE][MSGRECHNUNGSFREIGABE]&nbsp;<i>{|Rechnungseingangspr&uuml;fung (Buchhaltung)|}</i></td>
	          </tr>

            <tr>
              <td>{|Kostenstelle|}:</td>
              <td>[KOSTENSTELLE][MSGKOSTENSTELLE]</td>
              <td>&nbsp;</td>
	            <td>{|Betrag bezahlt|}:</td>
              <td>[BETRAGBEZAHLT][MSGBETRAGBEZAHLT]</td>
	          </tr>

            <tr>
              <td>{|Sachkonto|}:</td>
              <td>[SACHKONTO][MSGSACHKONTO]</td>
              <td>&nbsp;</td>
              <td>{|Skonto erhalten|}:</td>
              <td>[SKONTO_ERHALTEN][MSGSKONTO_ERHALTEN]</td>
	          </tr>

            <tr>
              <td>{|Kl&auml;rfall|}:</td>
              <td>[KLAERFALL][MSGKLAERFALL]</td>
              <td>&nbsp;</td>
              <td>{|bezahlt am|}:</td>
              <td>[BEZAHLTAM][MSGBEZAHLTAM]</td>
	          </tr>

            <tr>
              <td>{|Grund|}:</td>
              <td>[KLAERGRUND][MSGKLAERGRUND]</td>
              <td></td>
              <td>{|Aktion|}:</td>
              <td>[BUTTONBEZAHLT]</td>

            </tr>
            <tr>
              <td></td>
              <td></td>
              <td></td>
            <td>{|Schreibschutz|}:</td>
            <td>[SCHREIBSCHUTZ][MSGSCHREIBSCHUTZ]</td>
            <tr>
              <td>{|Interne Bemerkung|}:</td>
              <td colspan="4">[INTERNEBEMERKUNG][MSGINTERNEBEMERKUNG]</td>
	          </tr>
          </table>
        </fieldset>
        <input type="submit" name="speichern" value="Speichern" /> <input type="button" onclick="window.location.href='index.php?module=verbindlichkeit&action=list'" value="Abbrechen" />
      </div>
      </div>
      <div class="col-xs-12 col-md-4 col-md-height">
      <div class="inside inside-full-height">
        <fieldset>
          <legend>Vorschau
            <span id="ocr-scanner" data-liability="[ID]">
              <button type="button" id="ocr-start-button" disabled="disabled">OCR starten</button>
              <button type="button" id="ocr-settings-button">OCR einrichten</button>
            </span>
          </legend>
          [VORSCHAUDIV]
        </fieldset>
      </div>
      </div>
      </div>
      </div>

      <script>
        function steuerberechnen(el)
        {
          var steuersatz = ($(el).parents('tr').first().find('input').first().val()+'').replace(',','.');
          if(steuersatz == '')return;
          steuersatz = parseFloat(steuersatz);
          var brutto = parseFloat(($('#betrag').val()+'').replace(',','.'));
          if(isNaN(steuersatz))steuersatz = 0;
          if(isNaN(brutto))brutto = 0;
          $(el).parents('td').first().find('input').first().next().val( ((((brutto * (steuersatz / 100.00) / (1+steuersatz/100.00))).toFixed(2))+'').replace('.',','));
        }
      </script>
    </div>


    <div id="tabs-2">
      <div class="overflow-scroll">
        [TAB2TPL]
      </div>
    </div>

</form>

    <div id="tabs-3">
      [TAB3]
<!--
<table class="mkTable">

<tr><th>Konto</th><th>Belegfeld</th><th>Betrag</th></tr>
<tr><td>[BUHA_KONTO1][MSGBUHA_KONTO1]</td><td>[BUHA_BELEGFELD1][MSGBUHA_BELEGFELD1]</td><td>[BUHA_BETRAG1][MSGBUHA_BETRAG1]</td></tr>
<tr><td>[BUHA_KONTO2][MSGBUHA_KONTO2]</td><td>[BUHA_BELEGFELD2][MSGBUHA_BELEGFELD2]</td><td>[BUHA_BETRAG2][MSGBUHA_BETRAG2]</td></tr>
<tr><td>[BUHA_KONTO3][MSGBUHA_KONTO3]</td><td>[BUHA_BELEGFELD3][MSGBUHA_BELEGFELD3]</td><td>[BUHA_BETRAG3][MSGBUHA_BETRAG3]</td></tr>
<tr><td>[BUHA_KONTO4][MSGBUHA_KONTO4]</td><td>[BUHA_BELEGFELD4][MSGBUHA_BELEGFELD4]</td><td>[BUHA_BETRAG4][MSGBUHA_BETRAG4]</td></tr>
<tr><td>[BUHA_KONTO5][MSGBUHA_KONTO5]</td><td>[BUHA_BELEGFELD5][MSGBUHA_BELEGFELD5]</td><td>[BUHA_BETRAG5][MSGBUHA_BETRAG5]</td></tr>

</table>
<center>
    <input type="submit" onclick="this.form.action += '#tabs-3';"
    value="Speichern" /> <input type="button" onclick="window.location.href='index.php?module=verbindlichkeit&action=list'" value="Abbrechen" /></center>
 

-->
    </div>
    <div id="tabs-4">
      <div class="overflow-scroll">

        <div class="row">
        <div class="row-height">
        <div class="col-xs-12 col-md-8 col-md-height">
        <div class="inside_white inside-full-height">

          <fieldset class="white">
            <legend>&nbsp;</legend>
            <!-- // rate anfang -->
            <table width="100%" cellpadding="0" cellspacing="5" border="0" align="center">
              <tr>
                <td>
              <!-- // ende anfang -->
                  <center><form method="POST"><input type="submit" name="ausbestellungen" class="btnGreenNew" value="{|&#10010; Positionen aus Wareneingang neu laden|}" /></form></center>

                  [POS]
                </td>
              </tr>
            </table>

          </fieldset>

        </div>
        </div>
        <div class="col-xs-12 col-md-4 col-md-height">
        <div class="inside inside-full-height">
          <fieldset>
            <legend>{|Vorschau|}</legend>
            <iframe width="100%" height="100%" style="height:calc(100vh - 100px)" class="preview" data-src="./js/production/generic/web/viewer.html?file=[FILE]&positionen"></iframe>
          </fieldset>
        </div>
        </div>
        </div>
        </div>

      </div>
    </div>


    <div id="tabs-5">

      [FORMHANDLEREVENT2]
      [MESSAGE2]

      <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
        <tbody>
          <tr valign="top" colspan="3">
            <td>
              <fieldset>
                <legend>{|Rechnungsdaten|}</legend>

                <table width="100%" border="0">
                  <tr valign="top">
                    <td width="150">Lieferant:</td>
                    <td>[ADRESSEAUTOSTART2][ADRESSE2][MSGADRESSE2][ADRESSEAUTOEND2]</td>
                    <td>&nbsp;</td>
                    <td colspan="2" rowspan="2" align="center"><b style="color:green">[MELDUNG2]</b>
                      <br><font size="7">[VERBINDLICHKEIT2]</font>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      <br><br>Rechnungs Nr.:
                    </td>
                    <td>
                      <br><br>[RECHNUNG2][MSGRECHNUNG2]
                    </td>
                    <td>&nbsp;</td>
                  </tr>

                  <tr>
                    <td>Bestellung:</td>
                    <td width="250">[DISABLESTART2]<a href="index.php?module=bestellung&action=edit&id=[BESTELLUNGID2]" target="_blank">[BESTELLUNG2]</a>[MSGBESTELLUNG2][MULTIBESTELLUNG2][DISABLEENDE2]</td>
                    <td>&nbsp;</td>
                    <td width="200">Zahlweise:</td><td>[ZAHLUNGSWEISE2][MSGZAHLUNGSWEISE2]</td>
                  </tr>

                    <td>Rechnungsdatum:</td><td width="250">[RECHNUNGSDATUM2][MSGRECHNUNGSDATUM2]</td>
                    <td>&nbsp;</td>
                    <td width="200">Zahlbar bis:</td><td>[ZAHLBARBIS2][MSGZAHLBARBIS2][DATUM_ZAHLBARBIS2]</td>
                  </tr>

                  <tr>
                    <td>Betrag/Total (Brutto):</td><td>[BETRAG2][MSGBETRAG2]&nbsp;[WAEHRUNG2][MSGWAEHRUNG2]</td><td>&nbsp;</td>
                    <td>Skonto in %:</td><td>[SKONTO2][MSGSKONTO2]</td>
                  </tr>

                  <tr>
                    <td>USt. 19%:</td><td>[SUMMENORMAL2][MSGSUMMENORMAL2]</td><td>&nbsp;</td>
                    <td>Skonto bis:</td><td>[SKONTOBIS2][MSGSKONTOBIS2][DATUM_SKONTOBIS2]</td>
                  </tr>

                  <tr>
                    <td>USt. 7%:</td><td>[SUMMEERMAESSIGT2][MSGSUMMEERMAESSIGT2]</td>
                    <td>&nbsp;</td>
                    <td>Umsatzsteuer</td><td>[UMSATZSTEUER2][MSGUMSATZSTEUER2]</td>
                  </tr>
                  <tr>
                    <td>[STEUERSATZNAME32]</td><td>[SUMMESATZ32][MSGSUMMESATZ32]</td>
                    <td>&nbsp;</td>
                    <td>[STEUERSATZNAME42]</td><td>[SUMMESATZ42][MSGSUMMESATZ42]</td>
                  </tr>
                  <tr>
                    <td>Verwendungszweck:</td><td>[VERWENDUNGSZWECK2][MSGVERWENDUNGSZWECK2]</td>
                    <td>&nbsp;</td>
                    <td>Frachtkosten:</td><td>[FRACHTKOSTEN2][MSGFRACHTKOSTEN2]</td>
                  </tr>

                  <tr>
                    <td>Projekt:</td><td>[PROJEKT2][MSGKOSTENSTELLE2]</td>
                    <td>&nbsp;</td>
                    <td></td><td></td>
                  </tr>


                  <tr>
                    <td>Kostenstelle:</td><td>[KOSTENSTELLE2][MSGKOSTENSTELLE2]</td>
                    <td>&nbsp;</td>
                    <td>Freigabe:</td><td>[MSGFREIGABE2]&nbsp;<i>Wareneingangspr&uuml;fung:</i>&nbsp;[FREIGABE2]&nbsp;[MSGRECHNUNGSFREIGABE2]&nbsp;<i>Rechnungseingangspr&uuml;fung:</i>&nbsp;[RECHNUNGSFREIGABE2]</td>
                  </tr>

                  <tr>
                    <td>Sachkonto:</td><td>[SACHKONTO2][MSGSACHKONTO2]</td>
                    <td>&nbsp;</td>
                    <td>Aktion:</td><td>[BUTTONBEZAHLT2]</td>
                  </tr>
                  <tr>
                    <td>Interne Bemerkung:</td><td colspan="4">[INTERNEBEMERKUNG2]</td>
                  </tr>

                </table>

              </fieldset>
            </td>
          </tr>
        </tbody>
      </table>


      <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
        <tr valign="top">
          <td width="50%">
            <table cellspacing="5" width="100%">
              [TABELLEBESTELLUNGEN2]
            </table>
          </td>
          <td>
            <table width="100%>">
              <tr>
                <td>Summe Verbindlichkeit</td><td>Summe Kontierung</td>
              </tr>
              <tr>
                <td class="greybox" width="25%">[SUMMEVERBINDLICHKEIT2]</td>
                <td class="greybox" width="25%">[SUMMEKONTIERUNG2]</td>
              </tr>
            </table>

            [MESSAGEVORKONTIERUNG2]
            [VORKONTIERUNG2]
            [ZAHLUNGEN2]

            <div style="background-color:white">
              <h2 class="greyh2">{|Protokoll|}</h2>
              <div style="padding:10px">
                [PROTOKOLL2]
              </div>
            </div>

          </td>
        </tr>
      </tbody>
      </table>
    </div>
  </div>

[OCRDIALOGE]
