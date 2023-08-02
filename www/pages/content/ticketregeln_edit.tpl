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
                                <legend>{|Ticketregeln|}</legend><i>Ticketregeln f&uuml;r die Verarbeitung bei Ticketeingang. Platzhalter werden mit % angegeben.</i>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr><td>{|E-Mail Empf&auml;nger|}:</td><td><input type="text" name="empfaenger_email" value="[EMPFAENGER_EMAIL]" size="40"></td></tr>
<tr><td>{|E-Mail Verfasser|}:</td><td><input type="text" name="sender_email" value="[SENDER_EMAIL]" size="40"></td></tr>
<tr><td>{|Verfasser Name|}:</td><td><input type="text" name="name" value="[NAME]" size="40"></td></tr>
<tr><td>{|Betreff|}:</td><td><input type="text" name="betreff" value="[BETREFF]" size="40"></td></tr>
<tr><td>{|Papierkorb|}:</td><td><input type="checkbox" name="spam" value="1" [SPAM] size="40"></td></tr>
<tr><td>{|Pers&ouml;nlich|}:</td><td><input type="checkbox" name="persoenlich" value="1" [PERSOENLICH] size="40"></td></tr>
<tr><td>{|Prio|}:</td><td><input type="checkbox" name="prio" value="1" [PRIO] size="40"></td></tr>
<tr><td>{|DSGVO|}:</td><td><input type="checkbox" name="dsgvo" value="1" [DSGVO] size="40"></td></tr>
<tr><td>{|Verantwortliche Warteschlange|}:</td><td><input type="text" name="warteschlange" id="warteschlange" value="[WARTESCHLANGE]" size="40"></td></tr>
<tr><td>{|Aktiv|}:</td><td><input type="checkbox" name="aktiv" value="1" [AKTIV] size="40"></td></tr>

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

