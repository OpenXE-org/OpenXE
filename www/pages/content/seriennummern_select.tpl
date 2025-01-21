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
	        		<div class="col-xs-14 col-md-6 col-md-height">
	        			<div class="inside inside-full-height">
	        				<fieldset>
                                <legend>{|Seriennummern w&auml;hlen Artikel [ARTIKELNUMMER] [ARTIKEL]|}</legend>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr>
                                        <td>
                                            {|Lagermenge|}:
                                        </td>
                                        <td>
                                            <input type="text" value="[ANZLAGER]" size="40" disabled>
                                        </td>
                                    </tr>                                   
                                    <tr>
                                        <td>
                                            {|Seriennummern verf&uuml;gbar|}:
                                        </td>
                                        <td>
                                            <input type="text" value="[ANZVORHANDEN]" size="40" disabled>
                                        </td>
                                    </tr>                                   
                                    <tr>
                                        <td>
                                            {|Seriennummern fehlen|}:
                                        </td>
                                        <td>
                                            <input type="text" value="[ANZFEHLT]" size="40" disabled>
                                        </td>
                                    </tr>                                   
                                    <tr>
                                        <td>
                                            {|Seriennummer scannen|}:
                                        </td>
                                        <td>
                                            <input type="text" name="eingabeneu" id="eingabeneu" value="[EINGABENEU]" size="40" autofocus>
                                        </td>
                                    </tr>                                   
                                </table>
                            </fieldset>            
                        </div>
               		</div>
                    <div class="col-xs-14 col-md-6 col-md-height">
	        			<div class="inside inside-full-height">
	        				<fieldset>
                                <legend>{|Seriennummernassistent|}</legend>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr>
                                        <td>
                                            {|Letzte Seriennummer|}:
                                        </td>
                                        <td>
                                            <input type="text" name="muster" id="muster" value="[LETZTE]" size="40" disabled>
                                        </td>
                                    </tr>                                   
                                    <tr>
                                        <td>
                                            {|Pr&auml;fix|}:
                                        </td>
                                        <td>
                                            <input type="text" name="praefix" id="praefix" value="[PRAEFIX]" size="40">
                                        </td>
                                    </tr>                                   
                                    <tr>
                                        <td>
                                            {|Start|}:
                                        </td>
                                        <td>
                                            <input type="number" name="start" id="start" value="[START]" size="40">
                                        </td>
                                    </tr>                                   
                                    <tr>
                                        <td>
                                            {|Postfix|}:
                                        </td>
                                        <td>
                                            <input type="text" name="postfix" id="postfix" value="[POSTFIX]" size="40">
                                        </td>
                                    </tr>                                   
                                    <tr>
                                        <td>
                                            {|Anzahl|}:
                                        </td>
                                        <td>
                                            <input type="number" name="anzahl" id="anzahl" value="[ANZAHL]" size="40">
                                        </td>
                                    </tr>                                                                      
                                </table>
                            </fieldset>            
                        </div>
               		</div>
                    <div class="col-xs-14 col-md-2 col-md-height">
            			<div class="inside inside-full-height">
            				<fieldset>
                                <legend>{|<!--Legend for this form area goes here>-->Aktionen|}</legend>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr>
                                        <td>
                                            <button name="submit" value="hinzufuegen" class="ui-button-icon" style="width:100%;">Hinzuf&uuml;gen</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <button name="submit" value="assistent" class="ui-button-icon" style="width:100%;">Assistent ausf&uuml;hren</button>
                                        </td>
                                    </tr>                                  
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
                                <legend>{|Gew&auml;hlte Seriennummern|}</legend>
                                <table width="100%" border="0" class="mkTableFormular"> 
                                    <tr>
                                        <td>
                                            {|Seriennummern|}:
                                        </td>
                                        <td>
                                            <textarea name="seriennummern" id="seriennummern" rows="20" style="width:100%;">[SERIENNUMMERN]</textarea>
                                            <i>Liste der Seriennummern, 1 pro Zeile</i>
                                        </td>
                                    </tr>                               
                                </table>
                            </fieldset>            
                        </div>
               		</div>                   
                    <div class="col-xs-14 col-md-2 col-md-height">
            			<div class="inside inside-full-height">
            				<fieldset>
                                <legend>{|<!--Legend for this form area goes here>-->Aktionen|}</legend>
                                <table width="100%" border="0" class="mkTableFormular">                                  
                                    <tr>
                                        <td>
                                            <button name="submit" value="speichern" class="ui-button-icon" style="width:100%;">Speichern</button>
                                        </td>
                                    </tr>
                                </table>
                            </fieldset>            
                        </div>                   	
                	</div>
               	</div>	
            </div>
        </form>
    </div>        
</div>

