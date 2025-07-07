<script type="text/javascript">
    var intAnzahl = [MENGE];  // Anzahl gesetzter Checkboxen
    var intGesamt = [MENGE];  // Gesamtanzahl Checkboxen, die gesetzt werden dürfen
    var anzsn = '[ANZSN]';
    var anzmhd = '[ANZMHD]';
    var anzchargen = '[ANZCHARGEN]';
    var summe = '[SUMME]';
    function countChecks(objCheck){
      // Falls die Checkbox angewählt wurde
      if(objCheck.checked == true){
        intAnzahl++;
        // Falls die Gesamtanzahl überschritten wurde
        if(intAnzahl > intGesamt){
          alert("Maximal " + intGesamt + " auswählen!");
          intAnzahl--;                // Anzahl wieder zurücksetzen
          objCheck.checked = false;   // Checkbox wieder abwählen
        }
      // Falls eine Checkbox wieder abgewählt wird
      }else{
        intAnzahl--;  // Anzahl dekrementieren
      }
    }
</script>


<input type="hidden" id="anzsn" value="[ANZSN]" />
<input type="hidden" id="anzmhd" value="[ANZMHD]" />
<input type="hidden" id="anzchargen" value="[ANZCHARGEN]" />
<input type="hidden" id="summe" value="[SUMME]" />
<fieldset>
    <form action="index.php?module=lager&action=[ACTION]&cmd=[CMD]" method="post" id="eprooform" name="eprooform">
        <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
            <tbody>
                <tr valign="top" colspan="3">
                    <td>
                        [MESSAGELAGER]
                        <table width="80%" align="center">
                            <tr valign="top">
                                <td align="center">
                                    <table width="90%">
                                        <tr>
                                            <td width="170">{|Lagerbewegung|}:</td>
                                            <td align="left">
                                                <select name="grund">
                                                    [STARTNICHTUMLAGERN]
                                                    <option [DIFFERENZ]>{|Manuelle Lageranpassung|}</option>
                                                    [ENDENICHTUMLAGERN] [STARTUMLAGERN]
                                                    <option [UMLAGERN]>{|Umlagern|}</option>[ENDEUMLAGERN]
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><b>{|Menge|}:</b></td>
                                            <td align="left">
                                                <input type="text" name="menge" [ONCHANGEMENGE] id="menge" value="[MENGE]" size="27" style="width:200px" id="menge">[MSGMENGE]
                                            </td>
                                        </tr>
                                        <tr valign="top">
                                            <td><b>{|Artikelnummer|}:</b></td>
                                            <td align="left">[NUMMERAUTOSTART]
                                                <input type="text" name="nummer" style="width:200px" id="nummer" value="[NUMMER]" [ARTIKELSTYLE] size="27">[NUMMERAUTOEND][MSGARTIKEL]
                                            </td>
                                        </tr>
                                        [BEZEICHNUNG] [ZWISCHENLAGERINFO] [STARTDISABLESTOCK]
                                        <tr>
                                            <td>{|Projekt|}:</td>
                                            <td align="left">[PROJEKTSTART]
                                                <input name="projekt" id="projekt" type="text" value="[PROJEKT]" size="27" style="width:200px">[PROJEKTENDE][MSGPROJEKT]</td>
                                        </tr>
                                        <tr>
                                            <td>{|Kunde|} / {|Lieferant|} / {|Mitarbeiter|}:</td>
                                            <td align="left">[ADRESSESTART]
                                                <input type="text" name="adresse" value="[ADRESSE]" style="width:200px" id="adresse" size="27">[ADRESSEEND][MSGADRESSE]</td>
                                        </tr>
                                        [ENDEDISABLESTOCK]
                                        <tr>
                                            <td>{|Grund|}:</td>
                                            <td>
                                                <input type="text" id="grundreferenz" name="grundreferenz" value="[GRUNDREFERENZ]" size="27" style="width:200px">
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
                    <td width="" valign="" height="" bgcolor="" align="right" bordercolor="" classname="orange2" class="orange2">
                        <table width="100%">
                            <tr>
                                <td>
                                    <input type="button" name="zurueck" onclick="window.location.href='';" value="{|Nein, doch nicht|}" />
                                </td>
                                <td align="right">
                                    <input type="submit" id="weiter" name="submit" value="{|Weiter|}" />
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

            </tbody>
        </table>
    </form>
</fieldset>
<script type="text/javascript">
    [FOCUSFIELD]
</script>
<script type="text/javascript">
    // Artikel rescan
    function regalchange() {
        regal = document.getElementById('regal').value;
        artikel = document.getElementById('nummer').value;
        if ((artikel == regal) && (artikel != '')) {
            document.getElementById('menge').value = Number(document.getElementById('menge').value) + 1;
            document.getElementById('menge').style.fontSize = "200%";
            document.getElementById('eprooform').addEventListener('submit', eprooform_submit);
        }
    };

    function eprooform_submit(event) {
        regal = document.getElementById('regal').value;
        artikel = document.getElementById('nummer').value;
        if ((artikel == regal) && (artikel != '')) {
            document.getElementById('regal').value = '';
            event.preventDefault();
        }
    };

  function checklagermengen()
  {
    var menge = parseFloat(($('#menge').val()+'').replace(',','.'));
    if(isNaN(menge))menge = 0;
    var checkmenge = 0;
    var fehler = false;
    var gesamtanzahlchargen = 0.0;
    var ismhd = false;
    $('.chargenmengen').each(function(){
      if($(this).hasClass('mhdmenge'))ismhd = true;
      var tmpmenge = parseFloat(($(this).val()+'').replace(',','.'));
      if(isNaN(tmpmenge))tmpmenge = 0;
      var tmpmenge2 = parseFloat(($(this).parents('tr').first().find('.lager_charge_menge').first().val()+'').replace(',','.'));
      if(isNaN(tmpmenge2))tmpmenge2 = 0;
      gesamtanzahlchargen += tmpmenge2;
      if(tmpmenge > tmpmenge2)
      {
        $('#jsinfo').html('<div class="error">Chargenmenge '+(tmpmenge+'').replace('.',',')+' ist gr&ouml;&szlig;er als die vorhandene Menge</div>');
        fehler = true;
      }
      checkmenge += tmpmenge;
    });
    if(checkmenge.toFixed(8) < menge.toFixed(8))
    {
      $('#jsinfo').html('<div class="warning">Gesamtchargenmenge '+(checkmenge+'').replace('.',',')+' ist noch kleiner als die angegbene Menge</div>');
      fehler = true;
    }else{
      if(checkmenge.toFixed(8) > menge.toFixed(8))
      {
        $('#jsinfo').html('<div class="error">Gesamtchargenmenge '+(checkmenge+'').replace('.',',')+' ist gr&ouml;&szlig;er als die angegbene Menge</div>');
        fehler = true;
      }else{
        if(!fehler)
        {
          $('#jsinfo').html('');
        }
      }
    }
    $('#weiter').prop('disabled',false);
    if(gesamtanzahlchargen.toFixed(8) > 0)
    {
      if(typeof summe == 'undefined')summe = $('#summe').val();
      if(typeof anzmhd == 'undefined')anzmhd = $('#anzmhd').val();
      if(typeof anzchargen == 'undefined')anzchargen = $('#anzchargen').val();
      summe = parseFloat(summe);
      if(isNaN(summe))summe = 0;
      anzmhd = parseFloat(anzmhd);
      if(isNaN(anzmhd))anzmhd = 0;
      anzchargen = parseFloat(anzchargen);
      if(isNaN(anzchargen))anzchargen = 0;
      if(ismhd)
      {
        if(summe.toFixed(8) == anzmhd.toFixed(8))
        {
          if(fehler)
          {
            $('#weiter').prop('disabled',true);
          }
        }else{
          if(fehler)
          {
            if(anzmhd.toFixed(8) < summe.toFixed(8) && checkmenge.toFixed(8) < menge.toFixed(8))
            {
              $('#jsinfo').html($('#jsinfo').html()+'<div class="error">Die Lageranzahleintr&auml;ge stimmen nicht mit der Anzahl der MHD-Eintr&auml;ge &uuml;berein (Der Fehler w&uuml;rde sich noch vergr&ouml;&szlig;ern).</div>');
            }
            if(anzmhd.toFixed(8) > summe.toFixed(8) && checkmenge.toFixed(8) > menge.toFixed(8))
            {
              $('#jsinfo').html($('#jsinfo').html()+'<div class="error">Die Lageranzahleintr&auml;ge stimmen nicht mit der Anzahl der MHD-Eintr&auml;ge &uuml;berein (Der Fehler w&uuml;rde sich noch vergr&ouml;&szlig;ern).</div>');
            }
          }else{
            
          }
        }
      }else{
        if(summe.toFixed(8) == anzchargen.toFixed(8))
        {
          if(fehler)
          {
            $('#weiter').prop('disabled',true);
          }
        }else{
          if(fehler)
          {
            if(anzchargen.toFixed(8) < summe.toFixed(8) && checkmenge.toFixed(8) < menge.toFixed(8))
            {
              $('#jsinfo').html($('#jsinfo').html()+'<div class="error">Die Lageranzahleintr&auml;ge stimmen nicht mit der Anzahl der Chargen-Eintr&auml;ge &uuml;berein (Der Fehler w&uuml;rde sich noch vergr&ouml;&szlig;ern).</div>');
            }
            if(anzchargen.toFixed(8) > summe.toFixed(8) && checkmenge.toFixed(8) > menge.toFixed(8))
            {
              $('#jsinfo').html($('#jsinfo').html()+'<div class="error">Die Lageranzahleintr&auml;ge stimmen nicht mit der Anzahl der Chargen-Eintr&auml;ge &uuml;berein (Der Fehler w&uuml;rde sich noch vergr&ouml;&szlig;ern).</div>');
            }
          }else{
            
          }
        }
      }
    }
  }
</script>
