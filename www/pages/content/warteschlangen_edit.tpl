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
                                <legend>{|<!--Legend for this form area goes here>-->warteschlangen|}</legend>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr><td>{|Warteschlange|}:</td><td><input type="text" name="warteschlange" value="[WARTESCHLANGE]" size="40"></td></tr>
<tr><td>{|Kennung|}:</td><td><input type="text" name="label" value="[LABEL]" size="40"><i>Nur Buchstaben, keine Leerzeichen, keine Sonderzeichen und jede Kennung darf es nur einmal geben.</i></td></tr>
 <tr valign="top"><td width="200">{|Verantwortlicher|}:</td><td width="">[ADRESSEAUTOSTART]<input type="text" name="adresse" id="adresse" value="[ADRESSE]" size="40">[ADRESSEAUTOEND]</td></tr>

                                </table>
                            </fieldset>            
                        </div>
               		</div>
               	</div>	
            </div>
            <!-- Example for 2nd row            
            <div class="row">
	        	<div class="row-height">
	        		<div class="col-xs-12 col-md-12 col-md-height">
	        			<div class="inside inside-full-height">
	        				<fieldset>
                                <legend>{|Another legend|}</legend>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr><td>{|Warteschlange|}:</td><td><input type="text" name="warteschlange" value="[WARTESCHLANGE]" size="40"></td></tr>
<tr><td>{|Label|}:</td><td><input type="text" name="label" value="[LABEL]" size="40"></td></tr>
<tr><td>{|Wiedervorlage|}:</td><td><input type="text" name="wiedervorlage" value="[WIEDERVORLAGE]" size="40"></td></tr>
<tr><td>{|Adresse|}:</td><td><input type="text" name="adresse" value="[ADRESSE]" size="40"></td></tr>

                                </table>
                            </fieldset>            
                        </div>
               		</div>
               	</div>	
            </div> -->
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

