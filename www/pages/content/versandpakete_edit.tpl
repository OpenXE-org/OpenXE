<div id="tabs">
    <ul>
        <li><a href="#tabs-1"></a></li>
    </ul>
    <div id="tabs-1">
        [MESSAGE]        
        [FORMHANDLEREVENT]
        <form id="save" action="" method="post">   
            <div class="row">
            	<div class="row-height">
            		<div class="col-xs-14 col-md-6 col-md-height">
            			<div class="inside inside-full-height">
            				<fieldset>
                                <legend>{|Versandpaket <b>Nr. [ID]</b> vom [DATUM] f&uuml;r Adresse '[ADRESSE]'|}</legend>
                                <table width="100%" border="0" class="mkTableFormular">                                   
                                    <tr>
                                        <td>
                                            {|Status|}:
                                        </td>
                                        <td>
                                            <input type="text" name="" id="" value="[STATUS]" size="40" disabled>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Versender|}:
                                        </td>
                                        <td>
                                            <input type="text" name="" id="" value="[VERSENDER]" size="40" disabled>
                                        </td>
                                    </tr>    
                                    <tr>
                                        <td>
                                            {|Versandart|}:
                                        </td>
                                        <td>
                                            <input type="text" name="" id="" value="[VERSANDART]" size="40" disabled>
                                        </td>
                                    </tr>    
                                    <tr>
                                        <td>
                                            {|Tracking|}:
                                        </td>
                                        <td>
                                            <input type="text" name="" id="" value="[TRACKING]" size="40" disabled><a href="[TRACKING_LINK]"><img src="themes/new/images/forward.svg" border="0" style="top:6px; position:relative"></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Gewicht Kg|}:
                                        </td>
                                        <td>
                                            <input type="number" name="gewicht" id="gewicht" min="1" value="[GEWICHT]" size="40" [LIEFERSCHEIN_GEWICHT_DISABLED]>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Bemerkung|}:
                                        </td>
                                        <td>
                                            <textarea name="bemerkung" id="bemerkung" rows="3" style="width:100%;">[BEMERKUNG]</textarea>
                                        </td>
                                    </tr>                                
                                </table>
                            </fieldset>            
                        </div>
               		</div>
                    <div class="col-xs-14 col-md-6 col-md-height">
            			<div class="inside inside-full-height">
                            <fieldset [LIEFERSCHEIN_ADD_POS_HIDDEN]>
                                <legend>{|Artikel aus Lieferschein hinzuf&uuml;gen|}</legend>
                                <table width="100%" border="0" class="mkTableFormular"> 
                                    <tr>
                                        <td>
                                            {|Lieferschein|}:
                                        </td>
                                        <td>
                                            <input form="add" type="text" name="lieferschein" id="lieferschein" value="[LIEFERSCHEIN]" autofocus style="width:99%;">
                                        </td>
                                    </tr>      
                                </table>
                            </fieldset>
                            <fieldset [LIEFERSCHEIN_OHNE_POS_HIDDEN]>
                                <legend>{|Lieferschein|}</legend>
                                <table width="100%" border="0" class="mkTableFormular"> 
                                    <tr>
                                        <td>
                                            {|Lieferschein|}:
                                        </td>
                                        <td>
                                            <input form="add" type="text" name="" id="" value="[LIEFERSCHEIN_OHNE_POS]" size="40" disabled>
                                            <a href="index.php?module=lieferschein&action=edit&id=[LIEFERSCHEIN_OHNE_POS_ID]"><img src="themes/new/images/forward.svg" border="0" style="top:6px; position:relative"></a>
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
                                    <tr><td><button form="save" name="submit" value="speichern" class="ui-button-icon" style="width:100%;">Speichern</button></td></tr>
                                    <tr [LIEFERSCHEIN_ADD_POS_HIDDEN]><td><button form="add" name="submit" value="lieferschein_hinzufuegen" class="ui-button-icon" style="width:100%;">Artikel hinzuf&uuml;gen</button></td></tr>
                                    <tr [LIEFERSCHEIN_ADD_POS_HIDDEN]><td><button form="add" name="submit" value="lieferschein_komplett_hinzufuegen" class="ui-button-icon" style="width:100%;">Kompletten Lieferschein hinzuf&uuml;gen</button></td></tr>
                                    <tr [PAKETMARKE_HIDDEN]><td><button form="paketmarke" name="submit" value="paketmarke" class="ui-button-icon" style="width:100%;">Parketmarke drucken und absenden</button></td></tr>
                                </table>
                            </fieldset>            
                        </div>
               		</div>
               	</div>	
            </div>          
        </form>
        <form id="add" action="index.php?module=versandpakete&action=add&id=[ID]" method="post">
        </form>
        <form id="paketmarke" action="index.php?module=versandpakete&action=paketmarke&id=[ID]" method="post">
        </form>
        <div class="row" [LIEFERSCHEIN_POS_HIDDEN]>
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
    </div>      
</div>

