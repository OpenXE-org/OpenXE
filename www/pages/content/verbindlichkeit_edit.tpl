<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Details</a></li>
        <li><a href="#tabs-2">Positionen</a></li>
    </ul>
    <div id="tabs-1">
        [MESSAGE]
        <form action="" method="post">   
            [FORMHANDLEREVENT]
            <div class="row">
	        	<div class="row-height">
	        		<div class="col-xs-12 col-md-6 col-md-height">
	        			<div class="inside inside-full-height">
                            <div class="row">
	                        	<div class="row-height">
	                        		<div class="col-xs-12 col-md-8 col-md-height">
	                        			<div class="inside inside-full-height">
	                        				<fieldset style="float: left;">
                                                <legend>{|<b>Verbindlichkeit <font color="blue">[BELEGNR]</font></b> Lf-Nr. <a href="index.php?module=adresse&action=edit&id=[ADRESSE_ID]">[LIEFERANTENNUMMER]|}</a></legend>                              
                                                [STATUSICONS]    
                                            </fieldset>      
	                        				<fieldset style="float: right;">
                                                <input type="submit" name="submit" value="Speichern"/>      
                                            </fieldset>
                                        </div>
                               		</div>                                    
                               	</div>	
                            </div>
                            <div class="row">
	                        	<div class="row-height">
	                        		<div class="col-xs-12 col-md-8 col-md-height">
	                        			<div class="inside inside-full-height">
	                        				<fieldset>                                    
                                                <table width="100%" border="0" class="mkTableFormular">       
                                                    <tr>
                                                        <td>
                                                            {|Status|}:
                                                        </td>
                                                        <td>
                                                            <input type="text" value="[STATUS]" size="20" disabled>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            {|Adresse|}:
                                                        </td>
                                                        <td>
                                                            <input type="text" name="adresse" id="adresse" value="[ADRESSE]" size="20" [SAVEDISABLED]>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            {|Rechnung|}:
                                                        </td>
                                                        <td>
                                                            <input type="text" name="rechnung" id="rechnung" value="[RECHNUNG]" size="20" [SAVEDISABLED]>
                                                        </td>
                                                    </tr>     
                                                    <tr>
                                                        <td>
                                                            {|Rechnungsdatum|}:
                                                        </td>
                                                        <td>
                                                            <input type="text" name="rechnungsdatum" id="rechnungsdatum" value="[RECHNUNGSDATUM]" size="20" [SAVEDISABLED]>
                                                        </td>
                                                    </tr>  
                                                    <tr>
                                                        <td>
                                                            {|Eingangsdatum|}:
                                                        </td>
                                                        <td>
                                                            <input type="text" name="eingangsdatum" id="eingangsdatum" value="[EINGANGSDATUM]" size="20" [SAVEDISABLED]>
                                                        </td>
                                                    </tr>   
                                                    <tr>
                                                        <td>
                                                            {|Betrag|}:
                                                        </td>
                                                        <td>
                                                            <input type="number" name="betragbrutto" id="betragbrutto" value="[BETRAGBRUTTO]" size="20" [BETRAGDISABLED] [SAVEDISABLED]>
                                                            <select name="waehrung" [SAVEDISABLED]>[WAEHRUNG]</select>
                                                        </td>
                                                    </tr>      
                                                    <tr>
                                                        <td>
                                                            {|Betrag netto|}:
                                                        </td>
                                                        <td>
                                                            <input type="number" name="betragnetto" id="betragnetto" value="[BETRAGNETTO]" size="20" disabled [SAVEDISABLED]>
                                                        </td>
                                                    </tr>                                                                                                                                              
                                                    <tr>
                                                        <td>
                                                            {|Zahlbarbis|}:
                                                        </td>
                                                        <td>
                                                            <input type="text" name="zahlbarbis" id="zahlbarbis" value="[ZAHLBARBIS]" size="20" [SAVEDISABLED]>
                                                        </td>
                                                    </tr>                                               
                                                    <tr>
                                                        <td>
                                                            {|Skonto %|}:
                                                        </td>
                                                        <td>
                                                            <input type="text" name="skonto" id="skonto" value="[SKONTO]" size="20" [SAVEDISABLED]>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            {|Skontobis|}:
                                                        </td>
                                                        <td>
                                                            <input type="text" name="skontobis" id="skontobis" value="[SKONTOBIS]" size="20" [SAVEDISABLED]>
                                                        </td>
                                                    </tr>                                                                                                                                                                                                                                            
                                                    <tr>
                                                        <td>
                                                            {|Waren-/Leistungsprüfung (Einkauf)|}:
                                                        </td>
                                                        <td>
                                                            <input type="checkbox" id="freigabe" value="1" [FREIGABECHECKED] size="20" [SAVEDISABLED] disabled>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            {|Rechnungseingangsprüfung (Buchhaltung)|}:
                                                        </td>
                                                        <td>
                                                            <input type="checkbox" id="rechnungsfreigabe" value="1" [RECHNUNGSFREIGABECHECKED] size="20" [SAVEDISABLED] disabled>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            {|Bezahlt|}:
                                                        </td>
                                                        <td>
                                                            <input type="checkbox" id="bezahlt" value="1" [BEZAHLTCHECKED] size="20" [SAVEDISABLED] disabled>
                                                        </td>
                                                    </tr>          
                                                    <tr>
                                                        <td>
                                                            {|Projekt|}:
                                                        </td>
                                                        <td>
                                                            <input type="text" name="projekt" id="projekt" value="[PROJEKT]" size="20">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            {|Kostenstelle|}:
                                                        </td>
                                                        <td>
                                                            <input type="text" name="kostenstelle" id="kostenstelle" value="[KOSTENSTELLE]" size="20">
                                                        </td>
                                                    </tr>                                                 
                                                    <tr>
                                                        <td>
                                                            {|Internebemerkung|}:
                                                        </td>
                                                        <td>
                                                            <textarea name="internebemerkung" id="internebemerkung" rows="6" style="width:100%;">[INTERNEBEMERKUNG]</textarea>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </fieldset>            
                                        </div>
                               		</div>                 
                               	</div>	
                            </div>
                        </div>
               		</div>
                    <div class="col-xs-12 col-md-6 col-md-height">
                        <div class="inside inside-full-height">
                            <fieldset>
                                <legend>{|Vorschau|}</legend>
                                [INLINEPDF]
                            </fieldset>
                        </div>
                    </div>
               	</div>	
            </div>           
        </form>
    </div>    
    <div id="tabs-2">
        [POS]
    </div>
</div>

