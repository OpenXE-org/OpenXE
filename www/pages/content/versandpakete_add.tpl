<div id="tabs">
    <ul>
        <li><a href="#tabs-1"></a></li>
    </ul>
    <div id="tabs-1">
        [MESSAGE]
        <form action="index.php?module=versandpakete&action=add&id=[ID]&lieferschein=[LIEFERSCHEIN_ID]" method="post">   
            [FORMHANDLEREVENT]
            <div class="row">
	        	<div class="row-height">
	        		<div class="col-xs-14 col-md-10 col-md-height">
	        			<div class="inside inside-full-height">
	        				<fieldset>
                                <legend>{|Hinzuf&uuml;gen von Artikeln aus <a href="index.php?module=lieferschein&action=edit&id=[LIEFERSCHEIN_ID]"><b>Lieferschein [LIEFERSCHEIN]</b></a> zu Versandpaket <b>Nr. [ID]</b>|}</legend>
                                <table width="100%" border="0" class="mkTableFormular">                                
                                    <tr>
                                        <td>
                                            {|Artikel|}:
                                        </td>
                                        <td>
                                            <input type="text" name="artikel" id="artikel" value="" size="40" autofocus>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Menge|}:
                                        </td>
                                        <td>
                                            <input type="number" name="menge" id="menge" value="" min="1" size="40">
                                        </td>
                                    </tr>          
                                </table>                               
                            </fieldset>            
                        </div>
               		</div>
                    <div class="col-xs-14 col-md-2 col-md-height">
	        			<div class="inside inside-full-height">
	        				<fieldset>
                                <legend>{|Aktionen|}</legend>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr><td><button name="submit" value="hinzufuegen" class="ui-button-icon" style="width:100%;">Hinzuf&uuml;gen</button></td></tr>
                                    <tr><td><button name="submit" value="lieferschein_komplett_hinzufuegen" class="ui-button-icon" style="width:100%;">Alle hinzuf&uuml;gen</button></td></tr>
                                    <tr><td><button form="back" name="submit" value="fertig" class="ui-button-icon" style="width:100%;">Fertig</button></td></tr>
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
                                <legend>{|Lieferscheininhalt|}</legend>
                                [LIEFERSCHEININHALT]
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
                                <legend>{|Paketinhalt|}</legend>
                                [PAKETINHALT]
                            </fieldset>            
                        </div>
               		</div>
               	</div>	
            </div> 
            <input type="text" name="lieferschein" id="lieferschein" value="[LIEFERSCHEIN]" size="40" hidden>
        </form>
        <form action="index.php?module=versandpakete&action=edit&id=[VERSANDPAKET_ID]" id="back" method="post">
        </form>                                    
    </div>      
</div>

