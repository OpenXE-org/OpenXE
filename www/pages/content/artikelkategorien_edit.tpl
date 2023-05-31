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
                                <legend>{|Artikelkategorien|}</legend><i>Info like this.</i>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr>
                                        <td>
                                            {|Bezeichnung|}:
                                        </td>
                                        <td>
                                            <input type="text" name="bezeichnung" id="bezeichnung" value="[BEZEICHNUNG]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Next_nummer|}:
                                        </td>
                                        <td>
                                            <input type="text" name="next_nummer" id="next_nummer" value="[NEXT_NUMMER]" size="20">
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
                                            {|Geloescht|}:
                                        </td>
                                        <td>
                                            <input type="text" name="geloescht" id="geloescht" value="[GELOESCHT]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Externenummer|}:
                                        </td>
                                        <td>
                                            <input type="text" name="externenummer" id="externenummer" value="[EXTERNENUMMER]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Parent|}:
                                        </td>
                                        <td>
                                            <input type="text" name="parent" id="parent" value="[PARENT]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Steuer_erloese_inland_normal|}:
                                        </td>
                                        <td>
                                            <input type="text" name="steuer_erloese_inland_normal" id="steuer_erloese_inland_normal" value="[STEUER_ERLOESE_INLAND_NORMAL]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Steuer_aufwendung_inland_normal|}:
                                        </td>
                                        <td>
                                            <input type="text" name="steuer_aufwendung_inland_normal" id="steuer_aufwendung_inland_normal" value="[STEUER_AUFWENDUNG_INLAND_NORMAL]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Steuer_erloese_inland_ermaessigt|}:
                                        </td>
                                        <td>
                                            <input type="text" name="steuer_erloese_inland_ermaessigt" id="steuer_erloese_inland_ermaessigt" value="[STEUER_ERLOESE_INLAND_ERMAESSIGT]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Steuer_aufwendung_inland_ermaessigt|}:
                                        </td>
                                        <td>
                                            <input type="text" name="steuer_aufwendung_inland_ermaessigt" id="steuer_aufwendung_inland_ermaessigt" value="[STEUER_AUFWENDUNG_INLAND_ERMAESSIGT]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Steuer_erloese_inland_steuerfrei|}:
                                        </td>
                                        <td>
                                            <input type="text" name="steuer_erloese_inland_steuerfrei" id="steuer_erloese_inland_steuerfrei" value="[STEUER_ERLOESE_INLAND_STEUERFREI]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Steuer_aufwendung_inland_steuerfrei|}:
                                        </td>
                                        <td>
                                            <input type="text" name="steuer_aufwendung_inland_steuerfrei" id="steuer_aufwendung_inland_steuerfrei" value="[STEUER_AUFWENDUNG_INLAND_STEUERFREI]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Steuer_erloese_inland_innergemeinschaftlich|}:
                                        </td>
                                        <td>
                                            <input type="text" name="steuer_erloese_inland_innergemeinschaftlich" id="steuer_erloese_inland_innergemeinschaftlich" value="[STEUER_ERLOESE_INLAND_INNERGEMEINSCHAFTLICH]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Steuer_aufwendung_inland_innergemeinschaftlich|}:
                                        </td>
                                        <td>
                                            <input type="text" name="steuer_aufwendung_inland_innergemeinschaftlich" id="steuer_aufwendung_inland_innergemeinschaftlich" value="[STEUER_AUFWENDUNG_INLAND_INNERGEMEINSCHAFTLICH]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Steuer_erloese_inland_eunormal|}:
                                        </td>
                                        <td>
                                            <input type="text" name="steuer_erloese_inland_eunormal" id="steuer_erloese_inland_eunormal" value="[STEUER_ERLOESE_INLAND_EUNORMAL]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Steuer_erloese_inland_nichtsteuerbar|}:
                                        </td>
                                        <td>
                                            <input type="text" name="steuer_erloese_inland_nichtsteuerbar" id="steuer_erloese_inland_nichtsteuerbar" value="[STEUER_ERLOESE_INLAND_NICHTSTEUERBAR]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Steuer_erloese_inland_euermaessigt|}:
                                        </td>
                                        <td>
                                            <input type="text" name="steuer_erloese_inland_euermaessigt" id="steuer_erloese_inland_euermaessigt" value="[STEUER_ERLOESE_INLAND_EUERMAESSIGT]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Steuer_aufwendung_inland_nichtsteuerbar|}:
                                        </td>
                                        <td>
                                            <input type="text" name="steuer_aufwendung_inland_nichtsteuerbar" id="steuer_aufwendung_inland_nichtsteuerbar" value="[STEUER_AUFWENDUNG_INLAND_NICHTSTEUERBAR]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Steuer_aufwendung_inland_eunormal|}:
                                        </td>
                                        <td>
                                            <input type="text" name="steuer_aufwendung_inland_eunormal" id="steuer_aufwendung_inland_eunormal" value="[STEUER_AUFWENDUNG_INLAND_EUNORMAL]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Steuer_aufwendung_inland_euermaessigt|}:
                                        </td>
                                        <td>
                                            <input type="text" name="steuer_aufwendung_inland_euermaessigt" id="steuer_aufwendung_inland_euermaessigt" value="[STEUER_AUFWENDUNG_INLAND_EUERMAESSIGT]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Steuer_erloese_inland_export|}:
                                        </td>
                                        <td>
                                            <input type="text" name="steuer_erloese_inland_export" id="steuer_erloese_inland_export" value="[STEUER_ERLOESE_INLAND_EXPORT]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Steuer_aufwendung_inland_import|}:
                                        </td>
                                        <td>
                                            <input type="text" name="steuer_aufwendung_inland_import" id="steuer_aufwendung_inland_import" value="[STEUER_AUFWENDUNG_INLAND_IMPORT]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Steuertext_innergemeinschaftlich|}:
                                        </td>
                                        <td>
                                            <input type="text" name="steuertext_innergemeinschaftlich" id="steuertext_innergemeinschaftlich" value="[STEUERTEXT_INNERGEMEINSCHAFTLICH]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Steuertext_export|}:
                                        </td>
                                        <td>
                                            <input type="text" name="steuertext_export" id="steuertext_export" value="[STEUERTEXT_EXPORT]" size="20">
                                        </td>
                                    </tr>
                                    
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
                                    <tr>
                                        <td>
                                            {|Bezeichnung|}:
                                        </td>
                                        <td>
                                            <input type="text" name="bezeichnung" id="bezeichnung" value="[BEZEICHNUNG]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Next_nummer|}:
                                        </td>
                                        <td>
                                            <input type="text" name="next_nummer" id="next_nummer" value="[NEXT_NUMMER]" size="20">
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
                                            {|Geloescht|}:
                                        </td>
                                        <td>
                                            <input type="text" name="geloescht" id="geloescht" value="[GELOESCHT]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Externenummer|}:
                                        </td>
                                        <td>
                                            <input type="text" name="externenummer" id="externenummer" value="[EXTERNENUMMER]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Parent|}:
                                        </td>
                                        <td>
                                            <input type="text" name="parent" id="parent" value="[PARENT]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Steuer_erloese_inland_normal|}:
                                        </td>
                                        <td>
                                            <input type="text" name="steuer_erloese_inland_normal" id="steuer_erloese_inland_normal" value="[STEUER_ERLOESE_INLAND_NORMAL]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Steuer_aufwendung_inland_normal|}:
                                        </td>
                                        <td>
                                            <input type="text" name="steuer_aufwendung_inland_normal" id="steuer_aufwendung_inland_normal" value="[STEUER_AUFWENDUNG_INLAND_NORMAL]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Steuer_erloese_inland_ermaessigt|}:
                                        </td>
                                        <td>
                                            <input type="text" name="steuer_erloese_inland_ermaessigt" id="steuer_erloese_inland_ermaessigt" value="[STEUER_ERLOESE_INLAND_ERMAESSIGT]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Steuer_aufwendung_inland_ermaessigt|}:
                                        </td>
                                        <td>
                                            <input type="text" name="steuer_aufwendung_inland_ermaessigt" id="steuer_aufwendung_inland_ermaessigt" value="[STEUER_AUFWENDUNG_INLAND_ERMAESSIGT]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Steuer_erloese_inland_steuerfrei|}:
                                        </td>
                                        <td>
                                            <input type="text" name="steuer_erloese_inland_steuerfrei" id="steuer_erloese_inland_steuerfrei" value="[STEUER_ERLOESE_INLAND_STEUERFREI]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Steuer_aufwendung_inland_steuerfrei|}:
                                        </td>
                                        <td>
                                            <input type="text" name="steuer_aufwendung_inland_steuerfrei" id="steuer_aufwendung_inland_steuerfrei" value="[STEUER_AUFWENDUNG_INLAND_STEUERFREI]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Steuer_erloese_inland_innergemeinschaftlich|}:
                                        </td>
                                        <td>
                                            <input type="text" name="steuer_erloese_inland_innergemeinschaftlich" id="steuer_erloese_inland_innergemeinschaftlich" value="[STEUER_ERLOESE_INLAND_INNERGEMEINSCHAFTLICH]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Steuer_aufwendung_inland_innergemeinschaftlich|}:
                                        </td>
                                        <td>
                                            <input type="text" name="steuer_aufwendung_inland_innergemeinschaftlich" id="steuer_aufwendung_inland_innergemeinschaftlich" value="[STEUER_AUFWENDUNG_INLAND_INNERGEMEINSCHAFTLICH]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Steuer_erloese_inland_eunormal|}:
                                        </td>
                                        <td>
                                            <input type="text" name="steuer_erloese_inland_eunormal" id="steuer_erloese_inland_eunormal" value="[STEUER_ERLOESE_INLAND_EUNORMAL]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Steuer_erloese_inland_nichtsteuerbar|}:
                                        </td>
                                        <td>
                                            <input type="text" name="steuer_erloese_inland_nichtsteuerbar" id="steuer_erloese_inland_nichtsteuerbar" value="[STEUER_ERLOESE_INLAND_NICHTSTEUERBAR]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Steuer_erloese_inland_euermaessigt|}:
                                        </td>
                                        <td>
                                            <input type="text" name="steuer_erloese_inland_euermaessigt" id="steuer_erloese_inland_euermaessigt" value="[STEUER_ERLOESE_INLAND_EUERMAESSIGT]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Steuer_aufwendung_inland_nichtsteuerbar|}:
                                        </td>
                                        <td>
                                            <input type="text" name="steuer_aufwendung_inland_nichtsteuerbar" id="steuer_aufwendung_inland_nichtsteuerbar" value="[STEUER_AUFWENDUNG_INLAND_NICHTSTEUERBAR]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Steuer_aufwendung_inland_eunormal|}:
                                        </td>
                                        <td>
                                            <input type="text" name="steuer_aufwendung_inland_eunormal" id="steuer_aufwendung_inland_eunormal" value="[STEUER_AUFWENDUNG_INLAND_EUNORMAL]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Steuer_aufwendung_inland_euermaessigt|}:
                                        </td>
                                        <td>
                                            <input type="text" name="steuer_aufwendung_inland_euermaessigt" id="steuer_aufwendung_inland_euermaessigt" value="[STEUER_AUFWENDUNG_INLAND_EUERMAESSIGT]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Steuer_erloese_inland_export|}:
                                        </td>
                                        <td>
                                            <input type="text" name="steuer_erloese_inland_export" id="steuer_erloese_inland_export" value="[STEUER_ERLOESE_INLAND_EXPORT]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Steuer_aufwendung_inland_import|}:
                                        </td>
                                        <td>
                                            <input type="text" name="steuer_aufwendung_inland_import" id="steuer_aufwendung_inland_import" value="[STEUER_AUFWENDUNG_INLAND_IMPORT]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Steuertext_innergemeinschaftlich|}:
                                        </td>
                                        <td>
                                            <input type="text" name="steuertext_innergemeinschaftlich" id="steuertext_innergemeinschaftlich" value="[STEUERTEXT_INNERGEMEINSCHAFTLICH]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Steuertext_export|}:
                                        </td>
                                        <td>
                                            <input type="text" name="steuertext_export" id="steuertext_export" value="[STEUERTEXT_EXPORT]" size="20">
                                        </td>
                                    </tr>
                                    
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

