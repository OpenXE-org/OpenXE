<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs1"></a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs1">
[MESSAGE]
<form action="" method="post" name="eprooform">
[FORMHANDLEREVENT]

  <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>
      <tr valign="top" colspan="3">
        <td >
<fieldset><legend>{|Einstellung|}</legend>
    <table width="100%">
          <tr><td width="150">{|Bezeichnung|}:</td><td>[BEZEICHNUNG][MSGBEZEICHNUNG]&nbsp;</td></tr>
          <tr><td width="150">{|Zuletzt vergebene Artikelnummer|}:</td><td>[NEXT_NUMMER][MSGNEXT_NUMMER]&nbsp;
            [EXTERNENUMMER][MSGEXTERNENUMMER]&nbsp; Zentralen Nummernkreis verwenden <i>(Feld "Zuletzt vergebene Artikelnummer" kann als Prefix f&uuml;r neue Nummer verwendet)</i></td></tr>
          <tr><td width="150">{|Projekt|}:</td><td>[PROJEKT][MSGPROJEKT]&nbsp;</td></tr>
</table>


</fieldset>
<fieldset><legend>{|Export Kontenrahmen|}</legend>
<table width="100%">
  <tr>
    <td width="300"></td><td><!--Steuersatz--></td><td>Erl&ouml;se</td><td>Steuertext</td>
    <td width="300"></td><td>Aufwendungen</td>
  </tr>
  <tr>
    <td width="300">Inland (normal)</td><td><!--[STEUERSATZ_ERLOESE_NORMAL][MSGSTEUERSATZ_ERLOESE_NORMAL]--></td><td>[STEUER_ERLOESE_INLAND_NORMAL][MSGSTEUER_ERLOESE_INLAND_NORMAL]</td><td></td>
    <td width="300">Inland (normal)</td><td>[STEUER_AUFWENDUNG_INLAND_NORMAL][MSGSTEUER_AUFWENDUNG_INLAND_NORMAL]</td>
  </tr>

 <tr>
    <td width="300">Inland (erm&auml;&szlig;igt)</td><td><!--[STEUERSATZ_ERLOESE_ERMAESSIGT][MSGSTEUERSATZ_ERLOESE_ERMAESSIGT]--></td><td>[STEUER_ERLOESE_INLAND_ERMAESSIGT][MSGSTEUER_ERLOESE_INLAND_ERMAESSIGT]</td><td></td>
    <td width="300">Inland (erm&auml;&szlig;igt)</td><td>[STEUER_AUFWENDUNG_INLAND_ERMAESSIGT][MSGSTEUER_AUFWENDUNG_INLAND_ERMAESSIGT]</td>
  
        <tr>
    <td width="300">Inland (steuerfrei)</td><td></td><td>[STEUER_ERLOESE_INLAND_NICHTSTEUERBAR][MSGSTEUER_ERLOESE_INLAND_NICHTSTEUERBAR]</td><td></td>
    <td width="300">Inland (steuerfrei)</td><td>[STEUER_AUFWENDUNG_INLAND_NICHTSTEUERBAR][MSGSTEUER_AUFWENDUNG_INLAND_NICHTSTEUERBAR]</td>
  </tr>


  </tr>
<!--  <tr>
    <td width="300">Inland (steuerfrei)</td><td>[STEUER_ERLOESE_INLAND_STEUERFREI][MSGSTEUER_ERLOESE_INLAND_STEUERFREI]</td>
    <td width="300">Inland (steuerfrei)</td><td>[STEUER_AUFWENDUNG_INLAND_STEUERFREI][MSGSTEUER_AUFWENDUNG_INLAND_STEUERFREI]</td>
  </tr>
-->
  <tr>
    <td width="300">Innergemeinschaftlich EU</td><td><!--[STEUERSATZ_ERLOESE_INNERGEMEINSCHAFTLICH][MSGSTEUERSATZ_ERLOESE_INNERGEMEINSCHAFTLICH]--></td><td>[STEUER_ERLOESE_INLAND_INNERGEMEINSCHAFTLICH][MSGSTEUER_ERLOESE_INLAND_INNERGEMEINSCHAFTLICH]</td><td>[STEUERTEXT_INNERGEMEINSCHAFTLICH][MSGSTEUERTEXT_INNERGEMEINSCHAFTLICH]</td>
    <td width="300">Innergemeinschaftlich EU</td><td>[STEUER_AUFWENDUNG_INLAND_INNERGEMEINSCHAFTLICH][MSGSTEUER_AUFWENDUNG_INLAND_INNERGEMEINSCHAFTLICH]</td>
  </tr>
      <tr>
              <td width="300">EU (normal)</td><td><!--[STEUERSATZ_ERLOESE_EUNORMAL][MSGSTEUERSATZ_ERLOESE_EUNORMAL]--></td><td>[STEUER_ERLOESE_INLAND_EUNORMAL][MSGSTEUER_ERLOESE_INLAND_EUNORMAL]</td><td></td>
              <td width="300">EU (normal)</td><td>[STEUER_AUFWENDUNG_INLAND_EUNORMAL][MSGSTEUER_AUFWENDUNG_INLAND_EUNORMAL]</td>
      </tr>
      <tr>
       <td width="300">EU (erm&auml;&szlig;igt)</td><td><!--[STEUERSATZ_ERLOESE_EUERMAESSIGT][MSGSTEUERSATZ_ERLOESE_EUERMAESSIGT]--></td><td>[STEUER_ERLOESE_INLAND_EUERMAESSIGT][MSGSTEUER_ERLOESE_INLAND_EUERMAESSIGT]</td><td></td>
       <td width="300">EU (erm&auml;&szlig;igt)</td><td>[STEUER_AUFWENDUNG_INLAND_EUERMAESSIGT][MSGSTEUER_AUFWENDUNG_INLAND_EUERMAESSIGT]</td>
      </tr>

       <tr>
    <td width="300">Export</td><td><!--[STEUERSATZ_ERLOESE_EXPORT][MSGSTEUERSATZ_ERLOESE_EXPORT]--></td><td>[STEUER_ERLOESE_INLAND_EXPORT][MSGSTEUER_ERLOESE_INLAND_EXPORT]</td><td>[STEUERTEXT_EXPORT][MSGSTEUERTEXT_EXPORT]</td>
    <td width="300">Import</td><td>[STEUER_AUFWENDUNG_INLAND_IMPORT][MSGSTEUER_AUFWENDUNG_INLAND_IMPORT]</td>
  </tr>
</table>
</fieldset>
</td></tr>

    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
    <input type="submit" value="Speichern" name="submit"/>
    </tr>
  
    </tbody>
  </table>
</form>

</div>

<!-- tab view schließen -->
</div>


