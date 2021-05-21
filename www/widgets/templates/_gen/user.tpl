<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Benutzer</a></li>
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
<fieldset><legend>{|Benutzer|}</legend>
    <table width="100%">
          <tr><td width="150">{|Name / Beschreibung|}:</td><td>[DESCRIPTION][MSGDESCRIPTION]</td></tr>
	    <option value="mitarbeiter">Mitarbeiter</option><option value="produktion">Produktion</option>[SELECT1][MSGSELECT1]</td></tr>
	  <tr><td>{|Benutzername|}:</td><td>[USERNAME][MSGUSERNAME]</td><td></tr>
	  <tr><td>{|Interne Beschreibung|}:</td><td>[INTERNEBEZEICHNUNG][MSGINTERNEBEZEICHNUNG]</td><td></tr>
	  <tr><td>{|Adresse|}:</td><td width="70%">[ADRESSEAUTOSTART][ADRESSE][MSGADRESSE][ADRESSEAUTOEND]</td></tr>
          <tr><td>{|Externer Login|}:</td><td>[EXTERNLOGIN][MSGEXTERNLOGIN]</td></tr>
          <tr><td>{|Aktiv|}:</td><td>[ACTIV][MSGACTIV]</td></tr>
	  <tr><td>{|Vorlage|}:</td><td>[VORLAGE][MSGVORLAGE]</td><td></tr>
	  <tr><td>{|Startseite|}:</td><td>[STARTSEITE][MSGSTARTSEITE]</td><td></tr>
	  <tr><td>{|Fehllogins|}:</td><td>[FEHLLOGINS][MSGFEHLLOGINS]</td><td></tr>
          <tr><td>{|R&uuml;cksprung auf Oberfl&auml;che|}:</td><td>[NEUANMELDENLINKUEBERNEHMEN][MSGNEUANMELDENLINKUEBERNEHMEN]&nbsp;<i>Ist eine Oberfl&auml;che offen und meldet man sich ab springt man nach dem anmelden auf dem offenen Fenster wieder in die Oberfl&auml;che.</i></td></tr>

	  <tr><td>{|Passwort|}:</td><td>[PASSWORD][MSGPASSWORD]</td><td></tr>
	  <tr><td>{|Passwort wdh|}:</td><td>[REPASSWORD][MSGREPASSWORD]</td><td></tr>
</table></fieldset>

<fieldset><legend>{|Externer Login (wenn freigegeben)|}</legend>
    <table width="100%">
          <tr><td width="150">{|HW-Token|}:</td><td>[HWTOKEN][MSGHWTOKEN]</td></tr>
          <tr><td>{|mOTP Pin|}:</td><td>[MOTPPIN][MSGMOTPPIN]</td></tr>
          <tr><td>{|mOTP Secret|}:</td><td>[MOTPSECRET][MSGMOTPSECRET]</td></tr>
					<tr><td>{|FRED OTP HW-Key|}:</td><td>[HWKEY][MSGHWKEY]</td></tr>
          <tr><td>{|FRED OTP HW-Counter|}:</td><td>[HWCOUNTER][MSGHWCOUNTER]</td></tr>

</table></fieldset>


<fieldset><legend>{|Standards|}</legend>
    <table width="100%">
          <tr><td width="150">{|Standarddrucker|}:</td><td>[STANDARDDRUCKER][MSGSTANDARDDRUCKER]</td></tr>
          <tr><td>{|Parameter|}:</td><td>[SETTINGS][MSGSETTINGS]</td></tr>
</table></fieldset>

</td></tr>

    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
    <input type="submit" value="Speichern" />
    </tr>
  
    </tbody>
  </table>
</form>

</div>

<!-- tab view schließen -->
</div>
