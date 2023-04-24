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
                                <legend>{|Kontoauszug Eintrag|}</legend>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr>
                                        <td>
                                            {|Konto|}:
                                        </td>
                                        <td>
                                            <input type="text" name="konto" id="konto" value="[KONTO]" size="20" disabled>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Datum|}:
                                        </td>
                                        <td>
                                            <input type="text" name="buchung" id="buchung" value="[BUCHUNG]" size="20" disabled>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Betrag|}:
                                        </td>
                                        <td>
                                            <input type="text" name="soll" id="soll" value="[SOLL]" size="20" disabled>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|W&auml;hrung|}:
                                        </td>
                                        <td>
                                            <input type="text" name="waehrung" id="waehrung" value="[WAEHRUNG]" size="20" disabled>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Fertig|}:
                                        </td>
                                        <td>
                                            <input type="text" name="fertig" id="fertig" value="[FERTIG]" size="20" disabled>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Datev_abgeschlossen|}:
                                        </td>
                                        <td>
                                            <input type="text" name="datev_abgeschlossen" id="datev_abgeschlossen" value="[DATEV_ABGESCHLOSSEN]" size="20" disabled>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Buchungstext|}:
                                        </td>
                                        <td>
                                            <Textarea type="text" name="buchungstext" id="buchungstext" size="20" disabled>[BUCHUNGSTEXT]</textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Bearbeiter|}:
                                        </td>
                                        <td>
                                            <input type="text" name="bearbeiter" id="bearbeiter" value="[BEARBEITER]" size="20" disabled>
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
                                    <tr>
                                        <td>
                                            {|Importfehler|}:
                                        </td>
                                        <td>
                                            <input type="text" name="importfehler" id="importfehler" value="[IMPORTFEHLER]" size="20" disabled>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Klaerfall|}:
                                        </td>
                                        <td>
                                            <input type="text" name="klaerfall" id="klaerfall" value="[KLAERFALL]" size="20" disabled>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Klaergrund|}:
                                        </td>
                                        <td>
                                            <input type="text" name="klaergrund" id="klaergrund" value="[KLAERGRUND]" size="20" disabled>
                                        </td>
                                    </tr>                                   
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

