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
                                <legend>{|<!--Legend for this form area goes here>-->kommissionierung|}</legend><i>Info like this.</i>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr>
                                        <td>
                                            {|Zeitstempel|}:
                                        </td>
                                        <td>
                                            <input type="text" name="zeitstempel" id="zeitstempel" value="[ZEITSTEMPEL]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Bearbeiter|}:
                                        </td>
                                        <td>
                                            <input type="text" name="bearbeiter" id="bearbeiter" value="[BEARBEITER]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|User|}:
                                        </td>
                                        <td>
                                            <input type="text" name="user" id="user" value="[USER]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Kommentar|}:
                                        </td>
                                        <td>
                                            <input type="text" name="kommentar" id="kommentar" value="[KOMMENTAR]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Abgeschlossen|}:
                                        </td>
                                        <td>
                                            <input type="text" name="abgeschlossen" id="abgeschlossen" value="[ABGESCHLOSSEN]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Improzess|}:
                                        </td>
                                        <td>
                                            <input type="text" name="improzess" id="improzess" value="[IMPROZESS]" size="20">
                                        </td>
                                    </tr>
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
                                            {|Skipconfirmboxscan|}:
                                        </td>
                                        <td>
                                            <input type="text" name="skipconfirmboxscan" id="skipconfirmboxscan" value="[SKIPCONFIRMBOXSCAN]" size="20">
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
                                            {|Zeitstempel|}:
                                        </td>
                                        <td>
                                            <input type="text" name="zeitstempel" id="zeitstempel" value="[ZEITSTEMPEL]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Bearbeiter|}:
                                        </td>
                                        <td>
                                            <input type="text" name="bearbeiter" id="bearbeiter" value="[BEARBEITER]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|User|}:
                                        </td>
                                        <td>
                                            <input type="text" name="user" id="user" value="[USER]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Kommentar|}:
                                        </td>
                                        <td>
                                            <input type="text" name="kommentar" id="kommentar" value="[KOMMENTAR]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Abgeschlossen|}:
                                        </td>
                                        <td>
                                            <input type="text" name="abgeschlossen" id="abgeschlossen" value="[ABGESCHLOSSEN]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Improzess|}:
                                        </td>
                                        <td>
                                            <input type="text" name="improzess" id="improzess" value="[IMPROZESS]" size="20">
                                        </td>
                                    </tr>
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
                                            {|Skipconfirmboxscan|}:
                                        </td>
                                        <td>
                                            <input type="text" name="skipconfirmboxscan" id="skipconfirmboxscan" value="[SKIPCONFIRMBOXSCAN]" size="20">
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

