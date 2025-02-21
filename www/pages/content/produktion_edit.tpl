<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Allgemein</a></li>
        <li><a href="#tabs-2">Produktion</a></li>
        <li><a href="#tabs-3">Positionen</a></li>
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
                                <legend>{|<b>Produktion <font color="blue">[BELEGNR]</font></b>|}</legend>
                                [STATUSICONS]
                                [TEILPRODUKTIONINFO]
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
                                <legend>{|Allgemein|}</legend>
                                <table width="100%" border="0" class="mkTableFormular">
                                        <tr><td>{|Kunde|}:</td><td><input type="text" name="adresse" id="adresse" value="[ADRESSE]" size="20"></td></tr>
                                        <tr><td>{|Projekt|}:</td><td><input type="text" name="projekt" id="projekt" value="[PROJEKT]" size="20"></td></tr>
                                        <tr><td>{|Auftrag|}:</td><td><input type="text" name="auftrag" id="auftrag" value="[AUFTRAG]" size="20"></td></tr>
                                        <tr><td>{|Interne Bezeichnung|}:</td><td><input type="text" name="internebezeichnung" value="[INTERNEBEZEICHNUNG]" size="20"></td></tr>
                                </table>
                            </fieldset>
                        </div>
               		</div>
	        		<div class="col-xs-14 col-md-6 col-md-height">
	        			<div class="inside inside-full-height">
	        				<fieldset>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr><td>{|Status|}:</td><td><input disabled type="text" name="status" value="[STATUS]" size="20"></td></tr>
                                    <tr><td>{|Angelegt am|}:</td><td><input type="text" name="datum" id="datum" value="[DATUM]" size="10"></td></tr>
                                    <tr><td>{|Materiallager|}:</td><td><input type="text" name="standardlager" id="standardlager" value="[STANDARDLAGER]" size="20"></td></tr>
                                </table>
                            </fieldset>
                        </div>
               		</div>
	        		<div class="col-xs-14 col-md-2 col-md-height">
	        			<div class="inside inside-full-height">
                            <fieldset>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <legend>{|Aktionen|}</legend>
                                    <tr><td><button [AKTION_SPEICHERN_DISABLED] name="submit" value="speichern" class="ui-button-icon" style="width:100%;">Speichern</button></td></tr>
                                    <tr [AKTION_FREIGEBEN_VISIBLE]><td><button name="submit" value="freigeben" class="ui-button-icon" style="width:100%;">Freigeben</button></td></tr>
                                </table>
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
                                <legend>{|Einstellungen|}</legend>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr><td>{|Reservierart|}:</td><td><input disabled type="text" name="reservierart" value="[RESERVIERART]" size="20"></td></tr>
                                    <tr><td>{|Auslagerart|}:</td><td><input disabled type="text" name="auslagerart" value="[AUSLAGERART]" size="20"></td></tr>
                                    <tr><td>{|Unterstücklisten aufl&ouml;sen|}:</td><td><input disabled type="checkbox" name="unterlistenexplodieren" value=1 [UNTERLISTENEXPLODIEREN] size="20"></td></tr>
                                    <tr><td>{|Funktionstest|}:</td><td><input disabled type="checkbox" name="funktionstest" value=1 [FUNKTIONSTEST] size="20"></td></tr>
                                    <tr><td>{|Beschreibungen von Arbeitsschritten anzeigen|}:</td><td><input disabled type="checkbox"  name="arbeitsschrittetextanzeigen" value=1 [ARBEITSSCHRITTETEXTANZEIGEN] size="20"></td></tr>
                                    <tr><td>{|Seriennummer erstellen|}:</td><td><input disabled type="checkbox"  name="seriennummer_erstellen" value=1 [SERIENNUMMER_ERSTELLEN] size="20"></td></tr>
                                    <tr><td>{|Unterseriennummer erfassen|}:</td><td><input disabled type="checkbox"  name="unterseriennummer_erfassen" value=1 [UNTERSERIENNUMMER_ERFASSEN] size="20"></td></tr>
                                </table>
                            </fieldset>
                        </div>
               		</div>               	
	        		<div class="col-xs-14 col-md-8 col-md-height">
	        			<div class="inside inside-full-height">
	        				<fieldset>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr><td>{|Auslieferung Lager|}:</td><td><input type="text" name="datumauslieferung" id="datumauslieferung" value="[DATUMAUSLIEFERUNG]" size="10"></td></tr>
                                    <tr><td>{|Bereitstellung Start|}:</td><td><input type="text" name="datumbereitstellung" id="datumbereitstellung" value="[DATUMBEREITSTELLUNG]" size="10"></td></tr>
                                    <tr><td>{|Produktion Start|}:</td><td><input type="text" name="datumproduktion" id="datumproduktion" value="[DATUMPRODUKTION]" size="10"></td></tr>
                                    <tr><td>{|Produktion Ende|}:</td><td><input type="text" name="datumproduktionende" id="datumproduktionende" value="[DATUMPRODUKTIONENDE]" size="10"></td></tr>
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
                                <legend>{|Freitext|}</legend>
                                <textarea name="freitext" id="freitext" style="min-height: 180px;">[FREITEXT]</textarea>
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
                                <legend>{|Interne Bemerkung|}</legend>
                                    <textarea name="internebemerkung" id="internebemerkung" style="min-height: 180px;">[INTERNEBEMERKUNG]</textarea>
                            </fieldset>
                        </div>
               		</div>
               	</div>	
            </div>
        </form>
    </div>
    <div [POSITIONEN_TAB_VISIBLE]>
        <div id="tabs-2">
            [MESSAGE]
            <form action="#tabs-2" method="post">
                [FORMHANDLEREVENT]
                <div class="row">
	            	<div class="row-height">
	            		<div class="col-xs-14 col-md-12 col-md-height">
	            			<div class="inside inside-full-height">
	            				<fieldset>
                                    <legend>{|<b>Produktion <font color="blue">[BELEGNR]</font></b>|}</legend>
                                    [STATUSICONS]
                                    [TEILPRODUKTIONINFO]
                                </fieldset>
                            </div>
                   		</div>
                   	</div>	
                </div>
                <div class="row">
	            	<div class="row-height">
	            		<div class="col-xs-14 col-md-5 col-md-height">
	            			<div class="inside inside-full-height">
                            <fieldset>
                                <legend [AKTION_PLANEN_VISIBLE]>{|Zu produzierende Artikel|}</legend>
                                <legend [ARTIKEL_MENGE_VISIBLE]>{|Produktionsfortschritt|}</legend>
                                <table width="100%" border="0">
                                    <tr [AKTION_PLANEN_VISIBLE]><td>{|Artikel|}:</td></tr>
                                    <tr [AKTION_PLANEN_VISIBLE]><td><input type="text" name="artikel_planen" id="artikel_planen" value="[ARTIKEL_PLANEN]" size="20"></td></tr>
                                    <tr [AKTION_PLANEN_VISIBLE]><td>{|Planmenge|}:</td></tr>
                                    <tr [AKTION_PLANEN_VISIBLE]><td><input type="text" name="artikel_planen_menge" id="artikel_planen_menge" value="[ARTIKEL_PLANEN_MENGE]" size="20"></td></tr>
                                    <tr [ARTIKEL_MENGE_VISIBLE]>
                                        <td>{|Geplant|}:</td>
                                        <td>[MENGE_GEPLANT]</td>
                                        <td>{|Offen:|}</td>
                                        <td>[MENGE_OFFEN]</td>
                                    </tr>
                                    <tr [ARTIKEL_MENGE_VISIBLE]>
                                        <td>{|Produziert|}:</td>
                                        <td>[MENGE_PRODUZIERT]</td>
                                        <td>{|Reserviert:|}</td>
                                        <td>[MENGE_RESERVIERT]</td>
                                    </tr>
                                    <tr [ARTIKEL_MENGE_VISIBLE]>
                                        <td>{|Erfolgreich|}:</td>
                                        <td>[MENGE_ERFOLGREICH]</td>
                                        <td>{|Produzierbar:|}</td>
                                        <td>[MENGE_PRODUZIERBAR]</td>
                                    </tr>
                                    </tr>
                                    <tr [ARTIKEL_MENGE_VISIBLE]>
                                        <td>{|Ausschuss|}:</td>
                                        <td>[MENGE_AUSSCHUSS]</td>
                                    </tr>
                                </table>
                            </fieldset>
                            </div>
                   		</div>
	            		<div class="col-xs-14 col-md-5 col-md-height">
	            			<div class="inside inside-full-height">
                            <fieldset>
                                <legend [AKTION_PRODUZIEREN_VISIBLE]>{|Parameter|}</legend>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr [AKTION_PRODUZIEREN_VISIBLE]>
                                        <td>{|Menge|}:</td>
                                        <td><input type="number" min="0" name="menge_produzieren" id="menge_produzieren" value="[MENGE_PRODUZIEREN]" size="20""></td>
                                    </tr>
                                    <tr [AKTION_PRODUZIEREN_VISIBLE]>
                                        <td>{|Ausschuss|}:</td>
                                        <td><input type="number" min="0" name="menge_ausschuss_produzieren" id="menge_ausschuss_produzieren" value="[MENGE_AUSSCHUSS_PRODUZIEREN]" size="20"></td>
                                    </tr>
                                    <tr [AKTION_PRODUZIEREN_VISIBLE]>
                                        <td>{|Ziellager|}:</td>
                                        <td><input type="text" name="ziellager" id="ziellager" value="[ZIELLAGER]" placeholder="Standardlager" class="placeholder_warning" size="20"></td>
                                    </tr>
                                </table>
                            </fieldset>
                            </div>
                   		</div>
                        <div class="col-xs-14 col-md-2 col-md-height">
	            			<div class="inside inside-full-height">
                                <fieldset>
                                    <table width="100%" border="0" class="mkTableFormular">
                                        <legend>{|Anpassen|}</legend>
                                        <tr [AKTION_PLANEN_VISIBLE]><td><button name="submit" value="planen" class="ui-button-icon" style="width:100%;">Planen</button></td></tr>
                                        <tr [AKTION_LEEREN_VISIBLE]><td><button name="submit" value="leeren" class="ui-button-icon" style="width:100%;">Leeren</button></td></tr>                                         
                                        <tr [AKTION_FREIGEBEN_VISIBLE]><td><button name="submit" value="freigeben" class="ui-button-icon" style="width:100%;">Freigeben</button></td></tr>
                                        <tr [AKTION_PRODUZIEREN_VISIBLE]><td><button name="submit" value="teilen" class="ui-button-icon" style="width:100%;">Teilen</button></td></tr>
                                        <tr [AKTION_PRODUZIEREN_VISIBLE]><td><button name="submit" value="anpassen" class="ui-button-icon" style="width:100%;">Anpassen</button></td></tr>
                                    </table>
                                </fieldset>
                            </div>
                   		</div>
                        <div class="col-xs-14 col-md-2 col-md-height">
	            			<div class="inside inside-full-height">
                                <fieldset>
                                    <table width="100%" border="0" class="mkTableFormular">
                                        <legend>{|Produzieren|}</legend>
                                        <tr [AKTION_RESERVIEREN_VISIBLE]><td><button name="submit" value="reservieren" class="ui-button-icon" style="width:100%;">Reservieren</button></td></tr>
                                        <tr [AKTION_PRODUZIEREN_VISIBLE]><td><button name="submit" value="etikettendrucken" class="ui-button-icon" style="width:100%;" [AKTION_ETIKETTEN_DRUCKEN_DISABLED]>Etiketten drucken</button></td></tr>
                                        <tr [AKTION_PRODUZIEREN_VISIBLE]><td><button name="submit" value="produzieren" class="ui-button-icon" style="width:100%;">Produzieren</button></td></tr>
                                        <tr [AKTION_ABSCHLIESSEN_VISIBLE]><td><button name="submit" value="abschliessen" class="ui-button-icon" style="width:100%;">Abschliessen</button></td></tr>
                                    </table>
                                </fieldset>
                            </div>
                   		</div>
                   	</div>	
                </div>
                <div [ARTIKEL_MENGE_VISIBLE] class="row">
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
    </div>
    <div id="tabs-3">
        [MESSAGE]
        <form action="index.php?module=produktion_position&action=edit&produktion=[PRODUKTION_ID]" method="post">
            [FORMHANDLEREVENT]
            <div class="row">
	        	<div class="row-height">
	        		<div class="col-xs-12 col-md-12 col-md-height">
	        			<div class="inside inside-full-height">
	        				<fieldset>
                                <legend>{|Positionen|}</legend>
                                [PRODUKTION_POSITION_SOURCE_POSITION_TABELLE]
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr [AKTION_FREIGEBEN]>
                                        <td>{|Artikel|}:</td>
                                        <td><input type="text" name="artikel" id="artikel" size="20"></td>
                                        <td>{|Menge|}:</td>
                                        <td><input type="number" min="0" name="menge" id="menge" size="20"></td>
                                        <td><button name="submit" value="hinzufuegen" class="ui-button-icon" style="width:100%;">Hinzuf&uuml;gen</button></td>
                                    </tr>
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
            [MINIDETAILINEDIT]
        </form>
    </div>
</div>

