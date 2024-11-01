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
                                <legend>{|<!--Legend for this form area goes here>-->Smarty Templates|}</legend>
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
                                            {|Template|}:
                                        </td>
                                        <td>
                                            <textarea name="template" id="template" rows="80" cols="180">[TEMPLATE]</textarea>
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
                                            {|Template|}:
                                        </td>
                                        <td>
                                            <input type="text" name="template" id="template" value="[TEMPLATE]" size="20">
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

