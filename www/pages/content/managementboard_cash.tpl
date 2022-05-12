<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]

<div class="row">
  <div class="row-height">
    <div class="col-xs-12 col-sm-6 col-sm-height">
      <div class="inside_white inside-full-height">
        <fieldset>
          <legend>{|Monitor|}</legend>
          <form method="POST">
          <table>
            <tr>
              <td><label for="jahr">{|Jahr|}:</label></td>
              <td><select id="jahr" name="jahr">[SELJAHR]</select></td>
              <td><label for="vergleichsjahr">{|Vergleichsjahr|}:</label></td>
              <td><select id="vergleichsjahr" name="vergleichsjahr"><option value=""></option>[SELVERGLEICHSJAHR]</select></td>
              <td><label for="projekt">{|Projekt|}:</label></td>
              <td><input type="text" name="projekt" id="projekt" value="[PROJEKT]" /></td>
              <td><input type="submit" value="{|laden|}" name="laden" /></td>
            </tr>
          </table>
        </form>
        [DIAGRAMM1]
        [DIAGRAMM2]
        [DIAGRAMM3]
        [DIAGRAMM4]
        </fieldset>
      </div>
    </div>


    <div class="col-xs-12 col-sm-6 col-sm-height">
      <div class="inside_white inside-full-height">
          <fieldset><legend>{|Live Monitor Liquidit&auml;t|}</legend>
          
          <table width="100%">
            <tr><td width="25%">{|Offene Rechnungen (brutto)|}</td><td width="25%">{|Offene Verbindlichkeiten (brutto)|}</td><td width="25%">{|Offene Auftr&auml;ge (netto)|}</td><td>{|Mahnwesen (brutto)|}</td></tr>
            <tr>
              <td class="greybox" width="25%">[OFFENERECHNUNGEN]</td>
              <td class="greybox" width="25%">[OFFENEVERBINDLICHKEITEN]</td>
              <td class="greybox" width="25%">[OFFENEAUFTRAEGE]</td>
              <td class="greybox" width="25%">[MAHNWESEN]</td>
            </tr>
          </table>
        </fieldset>
        <fieldset><legend>{|Aktueller Monat|}</legend>
          <table width="100%">
            <tr><td width="25%">{|Rechnung/Gutschrift (netto)|}</td><td width="25%">{|Verbindlichkeiten (brutto)|}</td><td width="25%">{|Kontenrahmen Ausgaben (brutto)|}</td><td width="25%">{|Bankkonten Saldo (brutto)|}</td></tr>
            <tr>
              <td class="greybox" width="25%">[RECHNUNGGUTSCHRIFT]</td>
              <td class="greybox" width="25%">[VERBINDLICHKEITEN]</td>
              <td class="greybox" width="25%">[BANKEINNAHMEN]</td>
              <td class="greybox" width="25%">[BANKAUSGABEN]</td>
            </tr>
          </table>
        </fieldset>
        <fieldset><legend>{|Letzter Monat|}</legend>
          <table width="100%">
            <tr><td width="25%">{|Rechnung/Gutschrift (netto)|}</td><td width="25%">{|Verbindlichkeiten (brutto)|}</td><td width="25%">{|Kontenrahmen Ausgaben (brutto)|}</td><td width="25%">{|Bankkonten Saldo (brutto)|}</td></tr>
            <tr>
              <td class="greybox" width="25%">[RECHNUNGGUTSCHRIFTLETZTER]</td>
              <td class="greybox" width="25%">[VERBINDLICHKEITENLETZTER]</td>
              <td class="greybox" width="25%">[BANKEINNAHMENLETZTER]</td>
              <td class="greybox" width="25%">[BANKAUSGABENLETZTER]</td>
            </tr>
          </table>
        </fieldset>
         <fieldset><legend>{|Vorletzter Monat|}</legend>
          <table width="100%">
            <tr><td width="25%">{|Rechnung/Gutschrift (netto)|}</td><td width="25%">{|Verbindlichkeiten (brutto)|}</td><td width="25%">{|Kontenrahmen Ausgaben (brutto)|}</td><td width="25%">{|Bankkonten Saldo (brutto)|}</td></tr>
            <tr>
              <td class="greybox" width="25%">[RECHNUNGGUTSCHRIFTVORLETZTER]</td>
              <td class="greybox" width="25%">[VERBINDLICHKEITENVORLETZTER]</td>
              <td class="greybox" width="25%">[BANKEINNAHMENVORLETZTER]</td>
              <td class="greybox" width="25%">[BANKAUSGABENVORLETZTER]</td>
            </tr>
          </table>
        </fieldset>



        <fieldset><legend>{|Statistik Auftr&auml;ge aktueller Monat|}</legend>
          <table width="100%">
            <tr><td width="25%">{|Anzahl Auftr&auml;ge|}</td><td width="25%">{|Umsatz (netto)|}</td><td width="25%">{|DB in EUR (netto)|}</td><td width="25%">{|DB in %|}</td></tr>
            <tr>
              <td class="greybox" width="25%">[PAKETEHEUTE]</td>
              <td class="greybox" width="25%">[UMSATZHEUTE]</td>
              <td class="greybox" width="25%">[DECKUNGSBEITRAGHEUTE]</td>
              <td class="greybox" width="25%">[DBPROZENTHEUTE]</td>
            </tr>
          </table>
        </fieldset>
        <fieldset><legend>{|Zeit gebucht|}</legend>
          <table width="100%">
            <tr><td width="25%">{|Aktueller Monat|}</td><td width="25%">{|Letzter Monat|}</td><td width="25%">{|Aktueller Monat abr.|}</td><td width="25%">{|Letzer Monat abr.|}</td></tr>
            <tr>
              <td class="greybox" width="25%">[ZEITHEUTE]</td>
              <td class="greybox" width="25%">[ZEITWOCHE]</td>
              <td class="greybox" width="25%">[ABRHEUTE]</td>
              <td class="greybox" width="25%">[ABRWOCHE]</td>
            </tr>
          </table>
        </fieldset>
        <fieldset><legend>{|Gesamt|}</legend>
          <table width="100%">
            <tr><td width="25%"></td><td width="25%"></td><td width="25%">{|Abolauf nächsten Monat|}</td><td width="25%">{|Bankkonten Gesamt|}</td></tr>
            <tr>
              <td class="greybox" width="25%"></td>
              <td class="greybox" width="25%"></td>
              <td class="greybox" width="25%">[ABOLAUF]</td>
              <td class="greybox" width="25%">[BANKKONTENGESAMT]</td>
            </tr>
            <tr>
              <td colspan="4">[LASTCALC]</td>
            </tr>
          </table>
        </fieldset>
      </div>
    </div>
  </div>
</div>

[TAB1]

[TAB1NEXT]
</div>

<!-- tab view schließen -->
</div>

