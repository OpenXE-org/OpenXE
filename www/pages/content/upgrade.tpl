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
        <div class="row">
        	<div class="row-height">
        		<div class="col-xs-14 col-md-12 col-md-height">
        			<div class="inside inside-full-height">
        				<fieldset>
                            <legend>{|Info|}</legend>
Das Upgrade funktioniert in 2 Schritten: Dateien aktualisieren, Datenbank auffrischen. Wenn das Upgrade lange l&auml;uft, kann der Fortschritt in einem neuen Fenster mit "Anzeige auffrischen" angezeigt werden.<br><br>
Zum Start in der Konsole, im Unterordner "upgrade" diesen Befehl starten: <pre>sudo -u www-data php upgrade.php -do</pre>
                        </fieldset>            
                    </div>
           		</div>           
       		</div>
   		</div>
        <form action="" method="post">   
            [FORMHANDLEREVENT]
            <div class="row">
	        	<div class="row-height">
	        		<div class="col-xs-14 col-md-12 col-md-height">
	        			<div class="inside inside-full-height">
                            <div class="row">
	                        	<div class="row-height">
	                        		<div class="col-xs-14 col-md-12 col-md-height">
	                        			<div class="inside inside-full-height">
	                        				<fieldset>
                                                <legend>{|Aktuelle Version|}</legend>
                                                <table width="100%" border="0" class="mkTableFormular">
                                                    <b>OpenXE [CURRENT]</b>
                                                </table>
                                            </fieldset>            
                                        </div>
                               		</div>           
                           		</div>
                       		</div>
                            <div class="row">
	                        	<div class="row-height">
	                        		<div class="col-xs-14 col-md-12 col-md-height">
	                        			<div class="inside inside-full-height">
	                        				<fieldset>
                                                <legend>{|Ausgabe|}</legend>
                                                <table width="100%" border="0" class="mkTableFormular">
[OUTPUT_FROM_CLI]
                                                </table>
                                            </fieldset>            
                                        </div>
                               		</div>           
                           		</div>
                       		</div>
                   		</div>
               		</div>               	
	            	<div class="col-xs-14 col-md-2 col-md-height">
            			<div class="inside inside-full-height">
            				<fieldset>
                                <legend>{|Aktionen|}</legend>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr><td colspan=2><button name="submit" value="refresh" class="ui-button-icon" style="width:100%;">Anzeige auffrischen</button></td></tr>
                                    <tr><td colspan=2><button name="submit" value="check_upgrade" class="ui-button-icon" style="width:100%;">Upgrades pr&uuml;fen</button></td></tr>
                                    <tr><td style="width:100%;">{|Upgrade-Details anzeigen|}:</td><td><input type="checkbox" name="details_anzeigen" value=1 [DETAILS_ANZEIGEN] size="20"></td></tr>
                                    <tr [UPGRADE_VISIBLE]><td colspan=2><button name="submit" formtarget="_blank" value="do_upgrade" class="ui-button-icon" style="width:100%;">UPGRADE</button></td></tr>
                                    <tr [UPGRADE_VISIBLE]><td style="width:100%;">{|Erzwingen (-f)|}:</td><td><input type="checkbox" name="erzwingen" value=1 [ERZWINGEN] size="20"></td></tr>
                                    <tr><td colspan=2><button name="submit" value="check_db" class="ui-button-icon" style="width:100%;">Datenbank pr&uuml;fen</button></td></tr>
                                    <tr><td style="width:100%;">{|Datenbank-Details anzeigen|}:</td><td><input type="checkbox" name="db_details_anzeigen" value=1 [DB_DETAILS_ANZEIGEN] size="20"></td></tr>
                                    <tr [UPGRADE_DB_VISIBLE]><td colspan=2><button name="submit" formtarget="_blank" value="do_db_upgrade" class="ui-button-icon" style="width:100%;">Datenbank UPGRADE</button></td></tr>
                                </table>
                            </fieldset>            
                        </div>
               		</div>
               	</div>	
            </div>
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

