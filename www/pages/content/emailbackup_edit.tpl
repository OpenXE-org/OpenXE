<div id="tabs">
    <ul>
        <li><a href="#tabs-1"></a></li>
    </ul>
    <!-- Example for multiple tabs
    <ul hidden">
        <li><a href="#tabs-1">First Tab</a></li>
        <li><a href="#tabs-2">Second Tab</a></li>
    </ul>
    -->
    <div id="tabs-1">
        [MESSAGE]
        <form action="" method="post">   
            [FORMHANDLEREVENT]
            <div class="row">
   	        	<div class="row-height">
  	        		<div class="col-xs-12 col-md-12 col-md-height">
  	        			<div class="inside inside-full-height">
  	        				<fieldset>
                      <legend>{|Allgemein|}</legend>
                        <table width="100%" border="0" class="mkTableFormular">
<tr><td>{|E-Mail-Adresse|}:</td><td><input type="text" name="email" value="[EMAIL]" size="40"></td></tr>
<tr><td>{|Angezeigter Name|}:</td><td><input type="text" name="angezeigtername" value="[ANGEZEIGTERNAME]" size="40"></td></tr>
<tr><td>{|Interne Beschreibung|}:</td><td><input type="text" name="internebeschreibung" value="[INTERNEBESCHREIBUNG]" size="40"></td></tr>
<tr><td>{|Benutzername|}:</td><td><input type="text" name="benutzername" value="[BENUTZERNAME]" size="40"></td></tr>
<tr><td>{|Passwort|}:</td><td><input type="password" name="passwort" value="[PASSWORT]" size="40"></td></tr>
                        </table>
                      </fieldset>            
                    </div>
     		      </div>
           	    </div>	
              </div>
              <div class="row">
 	        	<div class="row-height">
        		    <div class="col-xs-12 col-md-12 col-md-height">
	        			<div class="inside inside-full-height">
	        				<fieldset>
                            <legend>{|SMTP|}</legend>
                            <table width="100%" border="0" class="mkTableFormular">
<tr><td>{|SMTP benutzen|}:</td><td><input type="text" name="smtp_extra" value="[SMTP_EXTRA]" size="40"><i>0 = nein, 1 = ja</i></td></tr>
<tr><td>{|Server|}:</td><td><input type="text" name="smtp" value="[SMTP]" size="40"></td></tr>
<tr><td>{|Verschl&uuml;sselung|}:</td><td><input type="text" name="smtp_ssl" value="[SMTP_SSL]" size="40"><i>0 = keine, 1 = TLS, 2 = SSL</i></td></tr>
<tr><td>{|Port|}:</td><td><input type="text" name="smtp_port" value="[SMTP_PORT]" size="40"></td></tr>
<tr><td>{|Authtype|}:</td><td><input type="text" name="smtp_authtype" value="[SMTP_AUTHTYPE]" size="40"><i>'', 'smtp', 'oauth_google'</i></td></tr>
<tr><td>{|Authparam|}:</td><td><input type="text" name="smtp_authparam" value="[SMTP_AUTHPARAM]" size="40"></td></tr>
<tr><td>{|Client_alias|}:</td><td><input type="text" name="client_alias" value="[CLIENT_ALIAS]" size="40"></td></tr>
<tr><td>{|Loglevel|}:</td><td><input type="text" name="smtp_loglevel" value="[SMTP_LOGLEVEL]" size="40"></td></tr>

<tr><td width="50">Testmail:</td><td>
        <input type="submit" form="smtp_test" value="Testmail senden" id="testmail-senden-button">&nbsp;<i>Bitte erst speichern und dann senden!</i>
</td></tr>              

                            </table>
                          </fieldset>            
                        </div>
         		      </div>
               	    </div>	
                  </div>
                    <div class="row">
 	        	<div class="row-height">
        		    <div class="col-xs-12 col-md-12 col-md-height">
	        			<div class="inside inside-full-height">
	        				<fieldset>
                            <legend>{|IMAP|}</legend>
                            <table width="100%" border="0" class="mkTableFormular">
<tr><td>{|IMAP server|}:</td><td><input type="text" name="server" value="[SERVER]" size="40"></td></tr>
<tr><td>{|imap_sentfolder_aktiv|}:</td><td><input type="text" name="imap_sentfolder_aktiv" value="[IMAP_SENTFOLDER_AKTIV]" size="40"></td></tr>
<tr><td>{|imap_sentfolder|}:</td><td><input type="text" name="imap_sentfolder" value="[IMAP_SENTFOLDER]" size="40"></td></tr>
<tr><td>{|imap_port|}:</td><td><input type="text" name="imap_port" value="[IMAP_PORT]" size="40"></td></tr>
<tr><td>{|imap_type|}:</td><td><input type="text" name="imap_type" value="[IMAP_TYPE]" size="40"><i>1 = standard, 3 = SSL, 5 = OAuth</i></td></tr>
<tr><td width="50">Testmail:</td><td>
        <input type="submit" form="imap_test" value="IMAP testen" id="testimap-button">&nbsp;<i>Bitte erst speichern und dann testen!</i>
</td></tr>
</table>
                      </fieldset>            
                    </div>
     		      </div>
           	    </div>	
              </div>             
<div class="row">
<div class="row-height">
        		    <div class="col-xs-12 col-md-12 col-md-height">
	        			<div class="inside inside-full-height">
	        				<fieldset>
                            <legend>{|Archiv|}</legend>
                            <table width="100%" border="0" class="mkTableFormular">
<tr><td>{|E-Mailarchiv aktiv|}:</td><td><input type="text" name="emailbackup" value="[EMAILBACKUP]" size="40"></td></tr>
<tr><td>{|Löschen nach wievielen Tagen?|}:</td><td><input type="text" name="loeschtage" value="[LOESCHTAGE]" size="40"></td></tr>
</table>
                      </fieldset>            
                    </div>
     		      </div>
           	    </div>	
              </div>             
<div class="row">
<div class="row-height">
        		    <div class="col-xs-12 col-md-12 col-md-height">
	        			<div class="inside inside-full-height">
	        				<fieldset>
                            <legend>{|Ticketsystem|}</legend>
                            <table width="100%" border="0" class="mkTableFormular">
<tr><td>{|ticket|}:</td><td><input type="text" name="ticket" value="[TICKET]" size="40"></td></tr>
<tr><td>{|ticketprojekt|}:</td><td><input type="text" name="ticketprojekt" value="[TICKETPROJEKT]" size="40"></td></tr>
<tr><td>{|ticketqueue|}:</td><td><input type="text" name="ticketqueue" value="[TICKETQUEUE]" size="40"></td></tr>
<tr><td>{|abdatum|}:</td><td><input type="text" name="abdatum" value="[ABDATUM]" size="40"></td></tr>
<tr><td>{|ticketloeschen|}:</td><td><input type="text" name="ticketloeschen" value="[TICKETLOESCHEN]" size="40"></td></tr>
<tr><td>{|ticketabgeschlossen|}:</td><td><input type="text" name="ticketabgeschlossen" value="[TICKETABGESCHLOSSEN]" size="40"></td></tr>
<tr><td>{|ticketemaileingehend|}:</td><td><input type="text" name="ticketemaileingehend" value="[TICKETEMAILEINGEHEND]" size="40"></td></tr>
</table>
                      </fieldset>            
                    </div>
     		      </div>
           	    </div>	
              </div>             
<div class="row">
<div class="row-height">
        		    <div class="col-xs-12 col-md-12 col-md-height">
	        			<div class="inside inside-full-height">
	        				<fieldset>
                            <legend>{|Sonstiges|}</legend>
                            <table width="100%" border="0" class="mkTableFormular">
<tr><td>{|autosresponder_blacklist|}:</td><td><input type="text" name="autosresponder_blacklist" value="[AUTOSRESPONDER_BLACKLIST]" size="40"></td></tr>
<tr><td>{|eigenesignatur|}:</td><td><input type="text" name="eigenesignatur" value="[EIGENESIGNATUR]" size="40"></td></tr>
<tr><td>{|signatur|}:</td><td><input type="text" name="signatur" value="[SIGNATUR]" size="40"></td></tr>
<tr><td>{|adresse|}:</td><td><input type="text" name="adresse" value="[ADRESSE]" size="40"></td></tr>
<tr><td>{|firma|}:</td><td><input type="text" name="firma" value="[FIRMA]" size="40"></td></tr>
<tr><td>{|geloescht|}:</td><td><input type="text" name="geloescht" value="[GELOESCHT]" size="40"></td></tr>
<tr><td>{|mutex|}:</td><td><input type="text" name="mutex" value="[MUTEX]" size="40"></td></tr>
<tr><td>{|autoresponder|}:</td><td><input type="text" name="autoresponder" value="[AUTORESPONDER]" size="40"></td></tr>
<tr><td>{|geschaeftsbriefvorlage|}:</td><td><input type="text" name="geschaeftsbriefvorlage" value="[GESCHAEFTSBRIEFVORLAGE]" size="40"></td></tr>
<tr><td>{|autoresponderbetreff|}:</td><td><input type="text" name="autoresponderbetreff" value="[AUTORESPONDERBETREFF]" size="40"></td></tr>
<tr><td>{|autorespondertext|}:</td><td><input type="text" name="autorespondertext" value="[AUTORESPONDERTEXT]" size="40"></td></tr>
<tr><td>{|projekt|}:</td><td><input type="text" name="projekt" value="[PROJEKT]" size="40"></td></tr>
                        </table>
                      </fieldset>            
                    </div>
     		      </div>
           	    </div>	
              </div>             
              <input type="submit" name="submit" value="Speichern" style="float:right"/>
        </form>
    </div>    
    <!-- Example for 2nd tab
    <div id="tabs-2">
        [MESSAGE]
        <form action="" method="post">   
            [FORMHANDLEREVENT]
            <div class="row">
            	<div class="row-height">
            		<div class="col-xs-12 col-md-12 col-md-height">
            			<div class="inside inside-full-height">
            				<fieldset>
                                <legend>{|...|}</legend>
                                <table width="100%" border="0" class="mkTableFormular">
                                    ...
                                </table>
                            </fieldset>            
                        </div>
               		</div>
               	</div>	
            </div>
            <input type="submit" name="submit" value="Speichern" style="float:right"/>
        </form>
    </div>    
    -->
</div>

<form id="smtp_test" action = "index.php">
<input type="text" name="module" value="emailbackup" style="display:none">
<input type="text" name="action" value="test_smtp" style="display:none">
<input type="text" name="id" value="[ID]" style="display:none">
</form>

<form id="imap_test" action = "index.php">
<input type="text" name="module" value="emailbackup" style="display:none">
<input type="text" name="action" value="test_imap" style="display:none">
<input type="text" name="id" value="[ID]" style="display:none">
</form>

