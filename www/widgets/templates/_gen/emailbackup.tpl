 <script type="text/javascript"><!--

      jQuery(document).ready(function() {
        extrasmtp();
        sentfolder();
      });

      function extrasmtp(cmd)      {
        jQuery('table.tableextrasmtp').hide();
        if(document.getElementById('smtp_extra').checked)
        {
          jQuery('table.tableextrasmtp').show();
        }
      }

      function sentfolder(cmd)      {
        jQuery('table.tablesentfolder').hide();
        if(document.getElementById('imap_sentfolder_aktiv').checked)
        {
          jQuery('table.tablesentfolder').show();
        }
      }


      //-->
  </script>


<!-- gehort zu tabview -->
<div id="tabs">
    <ul class="yui-nav">
        <li><a href="#tabs-1"></a></li>
    </ul>
<!-- ende gehort zu tabview -->
<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]
<form action="" method="post" name="eprooform">
[FORMHANDLEREVENT]


<div class="row">
  <div class="row-height">
    <div class="col-xs-12 col-sm-6 col-sm-height">
      <div class="inside inside-full-height">

<fieldset><legend>{|Einstellungen Empfangen IMAP|}</legend>
    <table width="100%">
	<tr><td width="200">{|Angezeigter Name|}:</td><td>[ANGEZEIGTERNAME][MSGANGEZEIGTERNAME]</td></tr>
	<tr><td width="200">{|E-Mail Adresse|}:</td><td>[EMAIL][MSGEMAIL]</td></tr>
	<tr><td width="200">{|Benutzername|}:</td><td>[BENUTZERNAME][MSGBENUTZERNAME]</td></tr>
	<tr><td width="200">{|Interne Beschreibung|}:</td><td>[INTERNEBESCHREIBUNG][MSGINTERNEBESCHREIBUNG]</td></tr>
	      <tr><td>{|Passwort|}:</td><td>[PASSWORT][MSGPASSWORT]</td></tr>
	      <tr><td>{|IMAP-Server|}:</td><td>[SERVER][MSGSERVER]</td></tr>
	      <tr><td>{|IMAP-Port|}:</td><td>[IMAP_PORT][MSGIMAP_PORT]&nbsp;[IMAP_TYPE][MSGIMAP_TYPE]</td></tr>
	      <tr><td>{|Versendetordner aktivieren|}:</td><td>[IMAP_SENTFOLDER_AKTIV][MSGIMAP_SENTFOLDER_AKTIV]<i>E-Mails in Ausgangsordner kopieren</i></td></tr>
</table>
<table class="tablesentfolder" width="100%">
	      <tr><td width="200">{|IMAP Gesendet Ordner|}:</td><td>[IMAP_SENTFOLDER][MSGIMAP_SENTFOLDER][BUTTONIMAPFOLDER]&nbsp;<br><i>z.B. INBOX.Sent Hinweis: IMAP Pfad muss existieren.</i></td></tr>
</table>
<table width="100%">
	      <tr><td width="200"></td><td><input type="submit" value="Speichern" />&nbsp;[BUTTONTEST] <br>(Bitte erst speichern und dann testen!)</td></tr>
</table></fieldset>

</div>
</div>


    <div class="col-xs-12 col-sm-6 col-sm-height">
      <div class="inside inside-full-height">

<fieldset><legend>{|Einstellungen Senden SMTP (Optional)|}</legend>
    <table width="100%">
     <tr><td width="200">{|Extra SMTP Account aktivieren|}:</td><td>[SMTP_EXTRA][MSGSMTP_EXTRA]</td></tr>
      </table>
    <table width="100%" class="tableextrasmtp">
	<tr><td width="200">{|E-Mail Adresse|}:</td><td><i>Einstellung von "Einstellungen von IMAP"</i></td></tr>
	<tr><td width="200">{|Benutzername|}:</td><td><i>Einstellung von "Einstellungen von IMAP"</i></td></tr>
      <tr><td>{|Passwort|}:</td><td><i>Einstellung von "Einstellungen von IMAP"</i></td></tr>
     <tr><td>{|SMTP-Server|}:</td><td>[SMTP][MSGSMTP]</td></tr>
     <tr><td>{|SMTP-Port|}:</td><td>[SMTP_PORT][MSGSMTP_PORT]</td></tr>
     <tr><td>{|SMTP-SSL|}:</td><td>[SMTP_SSL][MSGSMTP_SSL]</td></tr>
     <tr><td>{|SMTP-Authentifizierung|}:</td><td>[SMTP_AUTHTYPE][MSGSMTP_AUTHTYPE]</td></tr>
      <tr><td width="200">{|SMTP-DEBUG aktivieren|}:</td><td>[SMTP_LOGLEVEL][MSGSMTP_LOGLEVEL]</td></tr>
     <tr><td>{|Sender E-Mailadresse|}:</td><td>[SMTP_FROMMAIL][MSGSMTP_FROMMAIL]&nbsp;<i>Optional</i></td></tr>
     <tr><td>{|Sender Name|}:</td><td>[SMTP_FROMNAME][MSGSMTP_FROMNAME]&nbsp;<i>Optional</i></td></tr>
     <tr><td>{|Client Alias|}:</td><td>[CLIENT_ALIAS][MSGCLIENT_ALIAS]&nbsp;<i>Optional</i></td></tr>
</table></fieldset>

</div>
</div>
</div>
</div>


<div class="row">
  <div class="row-height">
    <div class="col-xs-12 col-sm-12 col-sm-height">
      <div class="inside inside-full-height">


<fieldset><legend>{|Ticketsystem|}</legend>
    <table width="100%">
	      <tr><td width="200">{|Auswahl|}:</td><td>[TICKET][MSGTICKET]&nbsp;</td></tr>
	      <tr><td>{|Standard-Projekte|}:</td><td>[AUTOPROJEKTSTART][PROJEKT][MSGPROJEKT][AUTOPROJEKTENDE]</td></tr>
	      <tr><td>{|Standard-Warteschlange|}:</td><td>[TICKETQUEUE][MSGTICKETQUEUE]&nbsp;<i>E-Mails werden automatisch der Warteschlange zugeordnet.</i></td></tr>
	      <tr><td>{|Ab Datum|}:</td><td>[ABDATUM][MSGABDATUM]&nbsp;<i>E-Mails ab dem Datum werden erst abgeholt.</i></td></tr>
	      <tr><td width="200">{|Ausgehende E-Mailadresse|}:</td><td>[TICKETEMAILEINGEHEND][MSGTICKETEMAILEINGEHEND]&nbsp;<i>Immer die eingehende E-Mailadresse als ausgehende verwenden</i></td></tr>
	      <tr><td width="200">{|E-Mails auf Server löschen|}:</td><td>[TICKETLOESCHEN][MSGTICKETLOESCHEN]&nbsp;<i>Unwideruflich nach Empfang vom Server löschen.</i></td></tr>
	      <tr><td width="200">{|Ticket als abgeschlossen markieren|}:</td><td>[TICKETABGESCHLOSSEN][MSGTICKETABGESCHLOSSEN]&nbsp;<i>Automatisch bei Import als abgeschlossen markieren.</i></td></tr>
</table></fieldset>
</div>
</div>
</div>
</div>


<div class="row">
  <div class="row-height">
    <div class="col-xs-12 col-sm-6 col-sm-height">
      <div class="inside inside-full-height">


<fieldset><legend>E-Mail Archiv</legend>
    <table width="100%">
	      <tr><td width="200">{|Auswahl|}:</td><td>[EMAILBACKUP][MSGEMAILBACKUP]</td></tr>
	      <tr><td>{|E-Mails l&ouml;schen|}:</td><td>[LOESCHTAGE][MSGLOESCHTAGE]&nbsp;<i>(Nach Anzahl Tage - Wert 0 oder leer bedeutet nie l&ouml;schen)</i></td></tr>
</table></fieldset>
</div>
</div>

    <div class="col-xs-12 col-sm-6 col-sm-height">
      <div class="inside inside-full-height">
<fieldset><legend>{|Mitarbeiter|}</legend>
    <table width="100%">
              <tr><td width="200">{|Mitarbeiter|}:</td><td>[MITARBEITERAUTOSTART][ADRESSE][MSGADRESSE][MITARBEITERAUTOEND]</td></tr>
</table></fieldset>

</div>
</div>


</div>
</div>



<div class="row">
  <div class="row-height">
    <div class="col-xs-12 col-sm-6 col-sm-height">
      <div class="inside inside-full-height">


<fieldset><legend>{|Autoresponder|}</legend>
    <table width="100%">
	      <tr><td width="200">{|Auto-Responder|}:</td><td>[AUTORESPONDER][MSGAUTORESPONDER]</td></tr>
	      <tr><td width="200">{|nur eine Mail pro Tag|}:</td><td>[AUTOSRESPONDER_BLACKLIST][MSGAUTOSRESPONDER_BLACKLIST]</td></tr>
	      <!--<tr><td>Auto-Responder Vorlage</td><td>[GESCHAEFTSBRIEFVORLAGE][MSGGESCHAEFTSBRIEFVORLAGE]</td></tr>-->
	      <tr><td>Betreff</td><td>[AUTORESPONDERBETREFF][MSGAUTORESPONDERBETREFF]</td></tr>
	      <tr><td>{|Text|}:</td><td>[AUTORESPONDERTEXT][MSGAUTORESPONDERTEXT]<br><i>Variable: {TICKET} = Ticketnummer (in Betreff und Text), {BETREFF} = Betreff der eingehenden E-Mail</i></td></tr>
</table></fieldset>
</div>
</div>


    <div class="col-xs-12 col-sm-6 col-sm-height">
      <div class="inside inside-full-height">


<fieldset><legend>{|Eigene Signatur|}</legend>
    <table width="100%">
	      <tr><td width="200">{|aktivieren|}:</td><td>[EIGENESIGNATUR][MSGEIGENESIGNATUR]</td></tr>
	      <tr><td>{|Signatur|}:</td><td>[SIGNATUR][MSGSIGNATUR]</td></tr>
</table></fieldset>


</div>
</div>
</div>
</div>

  <table width="100%"><tr><td align="right">
    <input type="submit" value="Speichern" />
    </tr>
  </table>
</form>
</div>
<!-- tab view schließen -->
</div>



