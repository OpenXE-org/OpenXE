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
	        		<div class="col-xs-12 col-md-8 col-md-height">
	        			<div class="inside inside-full-height">
                            <div class="row">
	                        	<div class="row-height">
	                        		<div class="col-xs-12 col-md-8 col-md-height">
	                        			<div class="inside inside-full-height">
	                        				<fieldset>
                                                <legend>{|<b>Verbindlichkeit <font color="blue">[BELEGNR]</font></b> Lf-Nr. <a href="index.php?module=adresse&action=edit&id=[ADRESSE_ID]">[LIEFERANTENNUMMER]|}</a></legend>                              
                                                [STATUSICONS]
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
                                                            {|Adresse|}:
                                                        </td>
                                                        <td>
                                                            <input type="text" name="adresse" id="adresse" value="[ADRESSE]" size="20">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            {|Rechnung|}:
                                                        </td>
                                                        <td>
                                                            <input type="text" name="rechnung" id="rechnung" value="[RECHNUNG]" size="20">
                                                        </td>
                                                    </tr>     
                                                    <tr>
                                                        <td>
                                                            {|Rechnungsdatum|}:
                                                        </td>
                                                        <td>
                                                            <input type="text" name="rechnungsdatum" id="rechnungsdatum" value="[RECHNUNGSDATUM]" size="20">
                                                        </td>
                                                    </tr>  
                                                    <tr>
                                                        <td>
                                                            {|Betrag|}:
                                                        </td>
                                                        <td>
                                                            <input type="number" name="betrag" id="betrag" value="[BETRAG]" size="20">
                                                            <select name="waehrung">[WAEHRUNG]</select>
                                                        </td>
                                                    </tr>                                                                                                                                        
                                                    <tr>
                                                        <td>
                                                            {|Zahlbarbis|}:
                                                        </td>
                                                        <td>
                                                            <input type="text" name="zahlbarbis" id="zahlbarbis" value="[ZAHLBARBIS]" size="20">
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
                                                            {|Eingangsdatum|}:
                                                        </td>
                                                        <td>
                                                            <input type="text" name="eingangsdatum" id="eingangsdatum" value="[EINGANGSDATUM]" size="20">
                                                        </td>
                                                    </tr>   
                                                    <tr>
                                                        <td>
                                                            {|Zahlungsweise|}:
                                                        </td>
                                                        <td>
                                                            <input type="text" name="zahlungsweise" id="zahlungsweise" value="[ZAHLUNGSWEISE]" size="20">
                                                        </td>
                                                    </tr>                                             
                                                    <tr>
                                                        <td>
                                                            {|Skonto|}:
                                                        </td>
                                                        <td>
                                                            <input type="text" name="skonto" id="skonto" value="[SKONTO]" size="20">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            {|Skontobis|}:
                                                        </td>
                                                        <td>
                                                            <input type="text" name="skontobis" id="skontobis" value="[SKONTOBIS]" size="20">
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
                                                            {|Sachkonto|}:
                                                        </td>
                                                        <td>
                                                            <input type="text" name="sachkonto" id="sachkonto" value="[SACHKONTO]" size="20">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            {|Waren-/Leistungsprüfung (Einkauf)|}:
                                                        </td>
                                                        <td>
                                                            <input type="text" name="freigabe" id="freigabe" value="[FREIGABE]" size="20">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            {|Rechnungseingangsprüfung (Buchhaltung)|}:
                                                        </td>
                                                        <td>
                                                            <input type="text" name="rechnungsfreigabe" id="rechnungsfreigabe" value="[RECHNUNGSFREIGABE]" size="20">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            {|Bezahlt|}:
                                                        </td>
                                                        <td>
                                                            <input type="text" name="bezahlt" id="bezahlt" value="[BEZAHLT]" size="20">
                                                        </td>
                                                    </tr>          
                                                    <tr>
                                                        <td>
                                                            {|Internebemerkung|}:
                                                        </td>
                                                        <td>
                                                            <input type="text" name="internebemerkung" id="internebemerkung" value="[INTERNEBEMERKUNG]" size="20">
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
                    <div class="col-xs-12 col-md-4 col-md-height">
                        <div class="inside inside-full-height">
                            <fieldset>
                                <legend>{|Vorschau|}</legend>
                                [INLINEPDF]
                            </fieldset>
                        </div>
                    </div>
               	</div>	
            </div>           
            <input type="submit" name="submit" value="Speichern" style="float:right"/>
        </form>
    </div>    
</div>

