<div id="tabs">
    <ul>
        <li><a href="#tabs-1"></a></li>
    </ul>
    <div id="tabs-1">
        [MESSAGE]
        [FORMHANDLEREVENT]
        <form id="save" action="" method="post">
            <div class="row">
            	<div class="row-height">
            		<div class="col-xs-14 col-md-6 col-md-height">
            			<div class="inside inside-full-height">
            				<fieldset>
                                <legend>
                                    {|<b>Versandpaket Nr. [ID]</b> vom [DATUM]<span [NO_ADDRESS_HIDDEN]> f&uuml;r Adresse '[ADRESSE]'|}</span>
                                </legend>
                                [ICONS]
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr>
                                        <td>
                                            {|Status|}:
                                        </td>
                                        <td>
                                            <input type="text" name="" id="" value="[STATUS]" size="40" disabled>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Versender|}:
                                        </td>
                                        <td>
                                            <input type="text" name="" id="" value="[VERSENDER]" size="40" disabled>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Versandart|}:
                                        </td>
                                        <td>
                                            <input type="text" name="" id="" value="[VERSANDART]" size="40" disabled>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Tracking|}:
                                        </td>
                                        <td>
                                            <input type="text" name="tracking" id="tracking" value="[TRACKING]" size="40" [TRACKING_DISABLED]>
                                            <a href="[PAKETMARKE_LINK]" [PAKETMARKE_HIDDEN]>
                                                <img src="themes/new/images/portogo.png" border="0" title="Zur Paketmarke" style="top:6px; position:relative">
                                            </a>
                                            <a href="[TRACKING_LINK]" [TRACKING_LINK_HIDDEN]>
                                                <img src="themes/new/images/forward.svg" border="0" title="Zum Tracking" style="top:6px; position:relative">
                                            </a>
                                        </td>
                                    </tr>
                                    <tr [TRACKING_LINK_EDIT_HIDDEN]>
                                        <td>
                                            {|Tracking link|}:
                                        </td>
                                        <td>
                                            <input type="text" name="tracking_link" id="tracking_link" value="[TRACKING_LINK]" size="40" [TRACKING_DISABLED]>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Gewicht Kg|}:
                                        </td>
                                        <td>
                                            <input type="text" name="" id="" value="[GEWICHT]" size="40" disabled>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Bemerkung|}:
                                        </td>
                                        <td>
                                            <textarea name="bemerkung" id="bemerkung" rows="3" style="width:100%;">[BEMERKUNG]</textarea>
                                        </td>
                                    </tr>
                                </table>
                            </fieldset>
                        </div>
               		</div>
                    <div class="col-xs-14 col-md-6 col-md-height">
            			<div class="inside inside-full-height">
                            <fieldset >
                                <legend>{|Lieferschein|}</legend>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr [LIEFERSCHEIN_OHNE_POS_HIDDEN]>
                                        <td>
                                            {|Zugeordnet|}:
                                        </td>
                                        <td>
                                            <input form="add" type="text" name="" id="" value="[LIEFERSCHEIN_OHNE_POS]" size="40" disabled>
                                            <a href="index.php?module=lieferschein&action=edit&id=[LIEFERSCHEIN_OHNE_POS_ID]"><img src="themes/new/images/forward.svg" title="Zum Lieferschein" border="0" style="top:6px; position:relative"></a>
                                        </td>
                                    </tr>
                                    <tr [LIEFERSCHEIN_ADD_POS_HIDDEN]>
                                        <td>
                                            {|Lieferschein für Artikel hinzuf&uuml;gen|}:
                                        </td>
                                        <td>
                                            <input form="add" type="text" name="lieferschein" id="lieferschein" value="[LIEFERSCHEIN]" autofocus size="40">
                                        </td>
                                    </tr>
                                </table>
                            </fieldset>
                        </div>
               		</div>
                    <div class="col-xs-14 col-md-2 col-md-height">
            			<div class="inside inside-full-height">
            				<fieldset>
                                <legend>{|Aktionen|}</legend>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr><td><button form="save" name="submit" value="speichern" class="ui-button-icon" style="width:100%;">Speichern</button></td></tr>
                                    <tr [LIEFERSCHEIN_ADD_POS_HIDDEN]><td><button form="add" name="submit" value="lieferschein_hinzufuegen" class="ui-button-icon" style="width:100%;">Artikel hinzuf&uuml;gen</button></td></tr>
                                    <tr [LIEFERSCHEIN_ADD_POS_HIDDEN]><td><button form="add" name="submit" value="lieferschein_komplett_hinzufuegen" class="ui-button-icon" style="width:100%;">Alle Artikel hinzuf&uuml;gen</button></td></tr>
                                    <tr><td><button form="save" name="submit" value="lieferscheinedrucken" class="ui-button-icon" style="width:100%;">Lieferscheine drucken</button></td></tr>
                                    <tr [PAKETMARKE_ADD_HIDDEN]><td><button form="paketmarke" name="submit" value="paketmarke" class="ui-button-icon" style="width:100%;">Paketmarke drucken</button></td></tr>
                                    <tr [ABSENDEN_HIDDEN]><td><button name="submit" value="absenden" class="ui-button-icon" style="width:100%;">Absenden</button></td></tr>
                                    <tr [ABSCHLIESSEN_HIDDEN]><td><button name="submit" value="abschliessen" class="ui-button-icon" style="width:100%;">Abschlie&szlig;en</button></td></tr>
                                </table>
                            </fieldset>
                        </div>
               		</div>
               	</div>	
            </div>
        </form>
        <form id="add" action="index.php?module=versandpakete&action=add&id=[ID]" method="post">
        </form>
        <form id="paketmarke" action="index.php?module=versandpakete&action=paketmarke&id=[ID]" method="post">
        </form>
        <div class="row" [LIEFERSCHEIN_POS_HIDDEN]>
        	<div class="row-height">
        		<div class="col-xs-12 col-md-12 col-md-height">
        			<div class="inside inside-full-height">
        				<fieldset>
                            <legend>{|Paketinhalt|}</legend>
                            [PAKETINHALT]
                        </fieldset>
                    </div>
           		</div>
           	</div>	
        </div>
    </div>
</div>

