<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Verbindlichkeit</a></li>
        <li [POSITIONENHIDDEN]><a href="#tabs-2">Positionen</a></li>
        <li><a href="#tabs-3">Protokoll</a></li>
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
                                                <button name="submit" value="speichern" class="ui-button-icon" style="width:100%;">Speichern</button>
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
                                                            <input type="text" name="adresse" id="adresse" value="[ADRESSE]" size="20" [SAVEDISABLED] required>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            {|Rechnungs-Nr.|}:
                                                        </td>
                                                        <td>
                                                            <input type="text" name="rechnung" id="rechnung" value="[RECHNUNG]" size="20" [SAVEDISABLED] required>
                                                        </td>
                                                    </tr>     
                                                    <tr>
                                                        <td>
                                                            {|Rechnungsdatum|}:
                                                        </td>
                                                        <td>
                                                            <input type="text" name="rechnungsdatum" id="rechnungsdatum" value="[RECHNUNGSDATUM]" size="20" [SAVEDISABLED] required>
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
                                                            {|Betrag brutto|}:
                                                        </td>
                                                        <td>
                                                            <input type="number" step="0.01" name="betrag" id="betrag" value="[BETRAG]" size="20" [SAVEDISABLED]>
                                                            <select name="waehrung" [SAVEDISABLED]>[WAEHRUNGSELECT]</select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            {|Betrag Positionen brutto|}:
                                                        </td>
                                                        <td>
                                                            <input type="number" step="0.01" name="betragbruttopos" id="betragbruttopos" value="[BETRAGBRUTTOPOS]" size="20" disabled><img class="wawitooltipicon" src="themes/new/images/tooltip_grau.png" title="Rundungsdifferenz [RUNDUNGSDIFFERENZ] wurde automatisch ber&uuml;cksichtigt" [RUNDUNGSDIFFERENZICONHIDDEN]>
                                                        </td>
                                                    </tr>          
                                                    <tr>
                                                        <td>
                                                            {|Betrag Positionen netto|}:
                                                        </td>
                                                        <td>
                                                            <input type="number" step="0.01" name="betragnetto" id="betragnetto" value="[BETRAGNETTO]" size="20" disabled [SAVEDISABLED]>
                                                        </td>
                                                    </tr>                                                                                                                                              
                                                    <tr>
                                                        <td>
                                                            {|Zahlbar bis|}:
                                                        </td>
                                                        <td>
                                                            <input type="text" name="zahlbarbis" id="zahlbarbis" value="[ZAHLBARBIS]" size="20" [SAVEDISABLED]>&nbsp;<input type="number" name="zahlbarbis_tage" id="zahlbarbis_tage" size="4" [SAVEDISABLED]>&nbsp;Tage
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
                                                            {|Skonto bis|}:
                                                        </td>
                                                        <td>
                                                            <input type="text" name="skontobis" id="skontobis" value="[SKONTOBIS]" size="20" [SAVEDISABLED]>&nbsp;<input type="number" name="skontobis_tage" id="skontobis_tage" size="4" [SAVEDISABLED]>&nbsp;Tage
                                                        </td>
                                                    </tr>                                                                                                                                                                                                                                            
                                                    <tr>
                                                        <td>
                                                            {|Bestellung|}:
                                                        </td>
                                                        <td>
                                                            <input type="text" name="bestellung" id="bestellung" value="[BESTELLUNG]" size="20" [SAVEDISABLED]>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            {|Waren-/Leistungsprüfung (Einkauf)|}:
                                                        </td>
                                                        <td>
                                                            <input type="checkbox" id="wareneingang" value="1" [WARENEINGANGCHECKED] size="20" disabled>
                                                            <a href="index.php?module=verbindlichkeit&action=freigabeeinkauf&id=[ID]" title="freigeben" [FREIGABEEINKAUFHIDDEN]><img src="themes/new/images/forward.svg" border="0" class="textfeld_icon"></a>                                                                                                                
                                                            <a href="index.php?module=verbindlichkeit&action=ruecksetzeneinkauf&id=[ID]" title="r&uuml;cksetzen" [RUECKSETZENEINKAUFHIDDEN]><img src="themes/new/images/delete.svg" border="0" class="textfeld_icon"></a>
                                                            <i [EINKAUFINFOHIDDEN]>Wird automatisch gesetzt wenn Positionen vollst&auml;ndig</a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            {|Rechnungseingangsprüfung (Buchhaltung)|}:
                                                        </td>
                                                        <td>
                                                            <input type="checkbox" id="rechnungsfreigabe" [RECHNUNGSFREIGABECHECKED] size="20" disabled>
                                                            <a href="index.php?module=verbindlichkeit&action=freigabebuchhaltung&id=[ID]" title="freigeben" [FREIGABEBUCHHALTUNGHIDDEN]><img src="themes/new/images/forward.svg" border="0" class="textfeld_icon"></a>                                                                                                                
                                                            <a href="index.php?module=verbindlichkeit&action=ruecksetzenbuchhaltung&id=[ID]" title="r&uuml;cksetzen"  [RUECKSETZENBUCHHALTUNGHIDDEN]><img src="themes/new/images/delete.svg" border="0" class="textfeld_icon"></a>                                                                                                                
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            {|Bezahlt|}:
                                                        </td>
                                                        <td>
                                                            <input type="checkbox" id="zahlungsstatus" [BEZAHLTCHECKED] size="20" disabled>
                                                            <a href="index.php?module=verbindlichkeit&action=freigabebezahlt&id=[ID]" title="auf  &apos;bezahlt&apos; setzen" [FREIGABEBEZAHLTHIDDEN]><img src="themes/new/images/forward.svg" border="0" class="textfeld_icon"></a>
                                                            <a href="index.php?module=verbindlichkeit&action=ruecksetzenbezahlt&id=[ID]" title="r&uuml;cksetzen"  [RUECKSETZENBEZAHLTHIDDEN]><img src="themes/new/images/delete.svg" border="0" class="textfeld_icon"></a>                                                                                                                                                                                                                                
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
        [POSITIONENTAB]        
    </div>
    <div id="tabs-3">
        [MINIDETAIL]
    </div>
</div>

