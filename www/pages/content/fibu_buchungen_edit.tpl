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
                                <legend>{|Buchung|}</legend>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr>
                                    <tr>
                                        <td>
                                            {|Von|}:
                                        </td>
                                        <td>
                                            <a href="index.php?module=[VON_TYP]&id=[VON_ID]&action=edit">[VON]</a>
                                            <input hidden type="text" name="von_typ" id="von_typ" value="[VON_TYP]" size="20">
                                            <input hidden type="text" name="von_id" id="von_id" value="[VON_ID]" size="20">
                                        </td>
                                    </tr>                                   
                                    <tr>
                                        <td>
                                            {|Nach|}:
                                        </td>
                                        <td>
                                            <a href="index.php?module=[NACH_TYP]&id=[NACH_ID]&action=edit">[NACH]</a>
                                            <input hidden type="text" name="nach_typ" id="nach_typ" value="[NACH_TYP]" size="20">
                                            <input hidden type="text" name="nach_id" id="nach_id" value="[NACH_ID]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Betrag|}:
                                        </td>
                                        <td>
                                            <input type="text" name="betrag" id="betrag" value="[BETRAG]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|W&auml;hrung|}:
                                        </td>
                                        <td>
                                            <select name="waehrung">[WAEHRUNG]</select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Benutzer|}:
                                        </td>
                                        <td>
                                            [BENUTZER]
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Zeit|}:
                                        </td>
                                        <td>
                                            [ZEIT]
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Internebemerkung|}:
                                        </td>
                                        <td>
                                            <textarea type="text" name="internebemerkung" id="internebemerkung" size="20">[INTERNEBEMERKUNG]</textarea>
                                        </td>
                                    </tr>                                    
                                </table>
                            </fieldset>            
                            <input type="submit" name="submit" value="Speichern" style="float:right"/>
                        </div>
               		</div>
               	</div>	
            </div>
            [TAB1]
            <!-- Example for 2nd row            
            <div class="row">
	        	<div class="row-height">
	        		<div class="col-xs-12 col-md-12 col-md-height">
	        			<div class="inside inside-full-height">
	        				<fieldset>
                                <legend>{|Another legend|}</legend>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr>
                                        <td>
                                            {|Von_typ|}:
                                        </td>
                                        <td>
                                            <input type="text" name="von_typ" id="von_typ" value="[VON_TYP]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Von_id|}:
                                        </td>
                                        <td>
                                            <input type="text" name="von_id" id="von_id" value="[VON_ID]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Nach_typ|}:
                                        </td>
                                        <td>
                                            <input type="text" name="nach_typ" id="nach_typ" value="[NACH_TYP]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Nach_id|}:
                                        </td>
                                        <td>
                                            <input type="text" name="nach_id" id="nach_id" value="[NACH_ID]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Betrag|}:
                                        </td>
                                        <td>
                                            <input type="text" name="betrag" id="betrag" value="[BETRAG]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Waehrung|}:
                                        </td>
                                        <td>
                                            <input type="text" name="waehrung" id="waehrung" value="[WAEHRUNG]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Benutzer|}:
                                        </td>
                                        <td>
                                            <input type="text" name="benutzer" id="benutzer" value="[BENUTZER]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Zeit|}:
                                        </td>
                                        <td>
                                            <input type="text" name="zeit" id="zeit" value="[ZEIT]" size="20">
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
            </div> -->
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

