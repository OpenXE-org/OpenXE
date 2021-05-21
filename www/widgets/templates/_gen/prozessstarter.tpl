<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">{|Prozessstarter|}</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]
<form action="" method="post" name="eprooform">
[FORMHANDLEREVENT]

  <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>
      <tr valign="top" colspan="3">
        <td >
<fieldset><legend>{|Prozess|}</legend>
    <table width="100%">
          <tr><td>{|Bezeichnung|}:</td><td>[BEZEICHNUNG][MSGBEZEICHNUNG]</td></tr>
          <tr><td>{|Art|}:</td><td>[ART][MSGART]</td></tr>
          <tr><td>{|Wochentag|}:</td><td>[ART_FILTER][MSGART_FILTER]</td></tr>
	  <tr><td>{|Startzeit|}:</td><td>[STARTZEIT][MSGSTARTZEIT]</td><td></tr>
	  <tr><td>{|Letzte Ausf&uuml;hrung|}:</td><td>[LETZTEAUSFUERHUNG][MSGLETZTEAUSFUERHUNG]</td><td></tr>
	  <tr><td>{|Periode|}:</td><td>[PERIODE][MSGPERIODE] {|(in Minuten)|}</td><td></tr>
          <tr><td>{|Typ|}:</td><td>[TYP][MSGTYP]</td></tr>
          <tr><td>{|Parameter|}:</td><td>[PARAMETER][MSGPARAMETER]</td></tr>
          <tr><td>{|Aktiv|}:</td><td>[AKTIV][MSGAKTIV]</td></tr>
          <!--<tr><td>{|Mutex|}:</td><td>[MUTEX][MSGMUTEX]</td></tr>
          <tr><td>{|Mutex-Counter|}:</td><td>[MUTEXCOUNTER][MSGMUTEXCOUNTER]</td></tr>-->
          [MUTEXTBUTTON]
</table></fieldset>
</td></tr>

    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
    <input type="submit" value="{|Speichern|}" />
    </tr>
  
    </tbody>
  </table>
</form>

</div>

<!-- tab view schließen -->
</div>

