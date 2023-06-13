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
                                <legend>{|<!--Legend for this form area goes here>-->Mahnwesen-Einstellungen|}</legend><i>Info like this.</i>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr>
                                        <td>
                                            {|Name|}:
                                        </td>
                                        <td>
                                            <input type="text" name="name" id="name" value="[NAME]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Tage|}:
                                        </td>
                                        <td>
                                            <input type="number" name="tage" id="tage" value="[TAGE]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Gebuehr|}:
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="gebuehr" id="gebuehr" value="[GEBUEHR]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|E-Mail senden|}:
                                        </td>
                                        <td>
                                            <input type="checkbox" name="mail" id="mail" [MAIL] size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Mahnung ausdrucken|}:
                                        </td>
                                        <td>
                                            <input type="checkbox" name="druck" id="druck" [DRUCK] size="20">
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
                                            {|Name|}:
                                        </td>
                                        <td>
                                            <input type="text" name="name" id="name" value="[NAME]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Tage|}:
                                        </td>
                                        <td>
                                            <input type="text" name="tage" id="tage" value="[TAGE]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Gebuehr|}:
                                        </td>
                                        <td>
                                            <input type="text" name="gebuehr" id="gebuehr" value="[GEBUEHR]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Versandmethode|}:
                                        </td>
                                        <td>
                                            <input type="text" name="versandmethode" id="versandmethode" value="[VERSANDMETHODE]" size="20">
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

