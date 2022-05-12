<form action="" method="post">
      [FORMHANDLEREVENT]

  <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>
      <tr classname="orange1" class="orange1" bordercolor="" align="" bgcolor="" height="" valign="">
        <td colspan="3" bordercolor="" class="" align="" bgcolor="" height="" valign="">Gesch&auml;ftsfall Kunde<br></td>
      </tr>

      <tr valign="top" colspan="3">
        <td>
          <table border="0" width="100%">
            <tbody>
	      <tr><td>Vorgang</td><td>[VORGANG][MSGVORGANG]<input type="button" value="Vorlagen"></td></tr>
	      <tr><td>Nr.</td><td>1002</td></tr>
	      <tr><td>Erledigt</td><td>[ERLEDIGT][MSGERLEDIGT]</td></tr>
	      <tr><td>Versand</td><td>[AUTOMATISCHERVERSAND][MSGAUTOMATISCHERVERSAND]&nbsp;kein automatischen Versand!</td></tr>
	      <tr><td>Versendet</td><td>20.01.1983 {Lieferung zum Gesch&auml;ftsfall}</td></tr>
	      <tr><td><br><br></td><td><br><br></td></tr>
	      <tr><td>Validier-Status</td><td><font color="red">Bitte kl&auml;ren! UST-ID Problem! Weiterbearbeitung stoppt!</font><br><a href="">Validierung f&uuml;r Dokument wiederholen</a></td></tr>
	      <tr><td><br><br></td><td><br><br></td></tr>
	      <tr><td>Bearbeiter</td><td>Benedikt Sauter</td></tr>
	      <tr><td>Datum</td><td>[DATUM][MSGDATUM]<input type="button" value="Kalender"></td></tr>
	      <tr><td>Projekt</td><td>[PROJEKT][MSGPROJEKT]&nbsp;
	      <input type="button" value="Suche"></td></tr>
	      <tr><td>Unter-Projekt</td><td>[PROJEKT][MSGPROJEKT]&nbsp;
	      <input type="button" value="Suche"></td></tr>
	      <tr><td>Dokument</td><td>
	      [DOKUMENT][MSGDOKUMENT]</td></tr>
	      <tr><td><br><br></td><td><br><br></td></tr>
	      <tr><td>Kunde</td><td>[KUNDE][MSGKUNDE]&nbsp;<input type="button" value="Suche"></td></tr>
	      <tr><td></td><td><a href="">Kundendaten f&uuml;r dieses Dokument bearbeiten</a>
	      <br><font color="green"><b>Info:</b> Bestellung mit g&uuml;ltiger UST-ID!</font>
	      <br><font color="red"><b>Achtung:</b> Bestellung mit ung&uuml;ltiger UST-ID!</font></td></tr>
	      <tr><td>Lieferadresse</td><td>[EMPFAENGER][MSGEMPFAENGER]&nbsp;<input type="button" value="Suche"></td></tr>
	      <tr><td></td><td><a href="">Lieferadresse f&uuml;r dieses Dokument bearbeiten</a>
	      <br><font color="red"><b>Achtung:</b> Falsches Land f&uuml;r Lieferung bei aktueller UST-ID!</font></td></tr>
	      <tr><td><br><br></td><td><br><br></td></tr>
	      <tr><td>Zahlungsweise</td><td>
	       [ZAHLUNGSWEISE][MSGZAHLUNGSWEISE]
	      </td></tr>
	      <tr><td>Versandart</td><td>
	       [VERSAND][MSGVERSAND]
	      </td></tr>


	       
            </tbody>
          </table>
            </td>
      </tr>
      <!-- speichern -->
      <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
      <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
      <input type="submit"
      value="Speichern" /> <input type="button" value="Abbrechen" /></td>
      </tr>
      <tr><td colspan="3"><br></td></tr>

      <!-- Artikel --> 
      <tr>
      <td class="orange2" colspan="3" bordercolor="" classname="orange2" align="" bgcolor="" height="" valign="" 
      width="">Artikel<br></td>
      </tr>
      <tr><td><i>LIste</i></td></tr>
      
 
     <!-- Fortfueheren --> 
      <tr>
      <td class="orange2" colspan="3" bordercolor="" classname="orange2" align="" bgcolor="" height="" valign="" 
      width="">Fortf&uuml;hren als<br></td>
      </tr>
      <tr><td align="center">Aktuelles Dokument fortf&uuml;hren als:&nbsp;<br><br>
      [WEITERFUEHREN][MSGWEITERFUEHREN] &nbsp;<input type="button" value="Fortf&uuml;hren"><br><br>
      </td></tr>



      <!-- Dokumente --> 
      <tr>
      <td class="orange2" colspan="3" bordercolor="" classname="orange2" align="" bgcolor="" height="" valign="" 
      width="">Alle Dokumente zu diesem Gesch&auml;ftsfall<br></td>
      </tr>
      <tr><td><i>wann auftrag war, wann ... war LIste mit datum inkl versandmitteilung an versand und versand datum</i></td></tr>


      <!-- Dokument senden -->
      <tr>
      <td class="orange2" colspan="3" bordercolor="" classname="orange2" align="" bgcolor="" height="" valign="" 
      width="">Dokument jetzt versenden<br></td>
      </tr>
      <tr><td><i>Drucker, E-Mail, Fax, Brief</i></td></tr>

    </tbody>
  </table>
  </form>
</body></html>
