<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Produktion</a></li>
        <li><a href="#tabs-2">Positionen</a></li>
        <li><a href="#tabs-3">Vorschau</a></li>
        <li><a href="#tabs-4">Protokoll</a></li>
    </ul>
    <div id="tabs-1">
        [MESSAGE]
        <form action="" method="post">   
            [FORMHANDLEREVENT]
            <div class="row">
	        	<div class="row-height">
	        		<div class="col-xs-12 col-md-12 col-md-height">
	        			<div class="inside inside-full-height">
	        				<fieldset>
                                <legend>{|<b>Produktion <font color="blue">[BELEGNR]</font></b>[ARTIKELNR] - [ARTIKELNAME]|}</legend>
                                [STATUSICONS]                                
                            </fieldset>            
                        </div>
               		</div>
               	</div>	
            </div>
            <div class="row">
	        	<div class="row-height">
	        		<div class="col-xs-14 col-md-6 col-md-height">
	        			<div class="inside inside-full-height">
	        				<fieldset>
                                <table width="100%" border="0" class="mkTableFormular">                                    
                                        <tr><td>{|Kundennummer|}:</td><td><input type="text" name="kundennummer" value="[KUNDENNUMMER]" size="40"></td></tr>
                                        <tr><td>{|Adresse|}:</td><td><input type="text" name="adresse" value="[ADRESSE]" size="40"></td></tr>
                                        <tr><td>{|Projekt|}:</td><td><input type="text" name="projekt" value="[PROJEKT]" size="40"></td></tr>
                                        <tr><td>{|Auftragid|}:</td><td><input type="text" name="auftragid" value="[AUFTRAGID]" size="40"></td></tr>
                                        <tr><td>{|Internebezeichnung|}:</td><td><input type="text" name="internebezeichnung" value="[INTERNEBEZEICHNUNG]" size="40"></td></tr>               
                                </table>
                            </fieldset> 
                        </div>
               		</div>
	        		<div class="col-xs-14 col-md-6 col-md-height">
	        			<div class="inside inside-full-height">
	        				<fieldset>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr><td>{|Status|}:</td><td><input type="text" name="status" value="[STATUS]" size="40"></td></tr>
                                    <tr><td>{|Datum|}:</td><td><input type="text" name="datum" value="[DATUM]" size="40"></td></tr>
                                    <tr><td>{|Standardlager|}:</td><td><input type="text" name="standardlager" value="[STANDARDLAGER]" size="40"></td></tr>
                                    <tr><td>{|Schreibschutz|}:</td><td><input type="text" name="schreibschutz" value="[SCHREIBSCHUTZ]" size="40"></td></tr>                                   
                                </table>
                            </fieldset> 
                        </div>
               		</div>
	        		<div class="col-xs-14 col-md-2 col-md-height">
	        			<div class="inside inside-full-height">
                            <fieldset>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <legend>{|Aktionen|}</legend>   
                                    <td><button name="submit" value="speichern" class="ui-button-icon" style="width:100%;">Speichern</button></td></tr>                                 
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
                                <legend>{|Einstellungen|}</legend>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr><td>{|Reservierart|}:</td><td><input type="text" name="reservierart" value="[RESERVIERART]" size="40"></td></tr>
                                    <tr><td>{|Auslagerart|}:</td><td><input type="text" name="auslagerart" value="[AUSLAGERART]" size="40"></td></tr>
                                    <tr><td>{|Unterlistenexplodieren|}:</td><td><input type="text" name="unterlistenexplodieren" value="[UNTERLISTENEXPLODIEREN]" size="40"></td></tr>
                                    <tr><td>{|Funktionstest|}:</td><td><input type="text" name="funktionstest" value="[FUNKTIONSTEST]" size="40"></td></tr>
                                    <tr><td>{|Arbeitsschrittetextanzeigen|}:</td><td><input type="text" name="arbeitsschrittetextanzeigen" value="[ARBEITSSCHRITTETEXTANZEIGEN]" size="40"></td></tr>
                                    <tr><td>{|Seriennummer_erstellen|}:</td><td><input type="text" name="seriennummer_erstellen" value="[SERIENNUMMER_ERSTELLEN]" size="40"></td></tr>
                                    <tr><td>{|Datumauslieferung|}:</td><td><input type="text" name="datumauslieferung" value="[DATUMAUSLIEFERUNG]" size="40"></td></tr>
                                    <tr><td>{|Datumbereitstellung|}:</td><td><input type="text" name="datumbereitstellung" value="[DATUMBEREITSTELLUNG]" size="40"></td></tr>
                                    <tr><td>{|Datumproduktionende|}:</td><td><input type="text" name="datumproduktionende" value="[DATUMPRODUKTIONENDE]" size="40"></td></tr>
                                    <tr><td>{|Datumproduktion|}:</td><td><input type="text" name="datumproduktion" value="[DATUMPRODUKTION]" size="40"></td></tr>
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
                                <legend>{|Freitext|}</legend><i>Info like this.</i>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr><td>{|Freitext|}:</td><td><input type="text" name="freitext" value="[FREITEXT]" size="40"></td></tr>
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
                                <legend>{|Interne Bemerkung|}</legend><i>Info like this.</i>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr><td>{|Internebemerkung|}:</td><td><input type="text" name="internebemerkung" value="[INTERNEBEMERKUNG]" size="40"></td></tr>
                                </table>
                            </fieldset>            
                        </div>
               		</div>
               	</div>	
            </div>
        </form>
    </div>    
    <div id="tabs-2">
        [MESSAGE]
        <form action="" method="post">   
            [FORMHANDLEREVENT]
            <div class="row">
	        	<div class="row-height">
	        		<div class="col-xs-14 col-md-12 col-md-height">
	        			<div class="inside inside-full-height">
                        <fieldset>
                            <legend>{|Zu produzierende Artikel|}</legend>
                            [PRODUKTION_POSITION_TARGET_TABELLE]
                        </fieldset>
                        </div>
               		</div>
                    <div class="col-xs-14 col-md-2 col-md-height">
	        			<div class="inside inside-full-height">
                            <fieldset>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <legend>{|Aktionen|}</legend>   
                                    <tr><td>{|Menge|}:</td></tr>
                                    <tr><td><input type="text" name="menge_produzieren" id="menge_produzieren" value="[MENGE_PRODUZIEREN]" size="20" style="width:100%;"></td></tr>
                                    <tr><td><button name="submit" value="speichern" class="ui-button-icon" style="width:100%;">Reservieren</button></td></tr>                                                                  
                                    <tr><td><button name="submit" value="speichern" class="ui-button-icon" style="width:100%;">Produzieren</button></td></tr>
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
                            <legend>{|Materialbedarf|}</legend>
                        </fieldset>
                        [PRODUKTION_POSITION_SOURCE_TABELLE]
                        </div>
               		</div>
               	</div>	
            </div>
        </form>
    </div>    
    <div id="tabs-3">
        [MESSAGE]
        <form action="" method="post">   
            [FORMHANDLEREVENT]
            <div class="row">
	        	<div class="row-height">
	        		<div class="col-xs-12 col-md-12 col-md-height">
	        			<div class="inside inside-full-height">
	        				<fieldset>
                                <legend>{|<!--Legend for this form area goes here>-->produktion|}</legend><i>Info like this.</i>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr><td>{|Internebemerkung|}:</td><td><input type="text" name="internebemerkung" value="[INTERNEBEMERKUNG]" size="40"></td></tr>
                                </table>
                            </fieldset>            
                        </div>
               		</div>
               	</div>	
            </div>
        </form>
    </div>    

    <div id="tabs-4">
        [MESSAGE]
        <form action="" method="post">   
            [FORMHANDLEREVENT]
            <div class="row">
	        	<div class="row-height">
	        		<div class="col-xs-12 col-md-12 col-md-height">
	        			<div class="inside inside-full-height">
	        				<fieldset>
                                <legend>{|<!--Legend for this form area goes here>-->produktion|}</legend><i>Info like this.</i>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr><td>{|Internebemerkung|}:</td><td><input type="text" name="internebemerkung" value="[INTERNEBEMERKUNG]" size="40"></td></tr>
                                </table>
                            </fieldset>            
                        </div>
               		</div>
               	</div>	
            </div>
        </form>
    </div>    
</div>

