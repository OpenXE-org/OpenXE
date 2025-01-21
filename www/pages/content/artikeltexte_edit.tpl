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
                                <legend>{|Artikeltexte (&Uuml;bersetzung)|}</legend>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr>
                                        <td>
                                            {|Artikel|}:
                                        </td>
                                        <td>
                                            <input type="text" value="[NAME_DE]" size="20" disabled>
                                            <input type="text" value="[ARTIKEL]" name="artikel" size="20" hidden>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Sprache|}:
                                        </td>
                                        <td>
                                            <select name="sprache">[SPRACHE]</select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Aktiv|}:
                                        </td>
                                        <td>
                                            <input type="checkbox" name="aktiv" id="aktiv" value="1" [AKTIV] size="20">
                                        </td>
                                    </tr>                                    
                                    <tr>
                                        <td>
                                            {|Name|}:
                                        </td>
                                        <td>
                                            <textarea name="name" id="name" cols="160">[NAME]</textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Kurztext|}:
                                        </td>
                                        <td>
                                            <textarea name="kurztext" id="kurztext" cols="160">[KURZTEXT]</textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Beschreibung|}:
                                        </td>
                                        <td>
                                            <textarea name="beschreibung" id="beschreibung" cols="160">[BESCHREIBUNG]</textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Beschreibung online|}:
                                        </td>
                                        <td>
                                            <textarea name="beschreibung_online" id="beschreibung_online" cols="160">[BESCHREIBUNG_ONLINE]</textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Meta title|}:
                                        </td>
                                        <td>
                                            <textarea name="meta_title" id="meta_title" cols="160">[META_TITLE]</textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Meta description|}:
                                        </td>
                                        <td>
                                            <textarea name="meta_description" id="meta_description" cols="160">[META_DESCRIPTION]</textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Meta keywords|}:
                                        </td>
                                        <td>
                                            <textarea name="meta_keywords" id="meta_keywords" cols="160">[META_KEYWORDS]</textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Katalogartikel|}:
                                        </td>
                                        <td>
                                            <input type="checkbox" name="katalogartikel" id="katalogartikel" value="1" [KATALOGARTIKEL] size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Katalogbezeichnung|}:
                                        </td>
                                        <td>
                                            <textarea name="katalog_bezeichnung" id="katalog_bezeichnung" cols="160">[KATALOG_BEZEICHNUNG]</textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Katalogtext|}:
                                        </td>
                                        <td>
                                            <textarea name="katalog_text" id="katalog_text" cols="160">[KATALOG_TEXT]</textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Shop|}:
                                        </td>
                                        <td>
                                            <input type="text" name="shop" id="shop" value="[SHOP]" size="20">
                                        </td>
                                    </tr>
                                </table>
                            </fieldset>            
                        </div>
               		</div>
               	</div>	
            </div>
			<input type="submit" name="submit" value="Speichern" style="float:right" />
        </form>
    </div>    
</div>

