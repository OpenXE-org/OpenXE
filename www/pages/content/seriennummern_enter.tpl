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
                    <div class="col-xs-14 col-md-6 col-md-height">
                        <div class="inside inside-full-height">
                            <fieldset>
                                <legend >{|[LEGEND]|}</legend>
                                [POSITIONEN]
                            </fieldset>
                        </div>
                    </div>                   
                    <div class="col-xs-14 col-md-6 col-md-height">
                        <div class="inside inside-full-height">                          
                            <fieldset>
                                <legend>{|Seriennummern erfassen|}</legend>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr [ARTIKEL_HIDDEN]>
                                        <td>
                                            {|Lagermenge|}:
                                        </td>
                                        <td>
                                            <input type="text" value="[ANZLAGER]" size="40" disabled />
                                        </td>
                                    </tr>
                                    <tr [ARTIKEL_HIDDEN]>
                                        <td>
                                            {|Seriennummern verf&uuml;gbar|}:
                                        </td>
                                        <td>
                                            <input type="text" value="[ANZVORHANDEN]" size="40" disabled />
                                        </td>
                                    </tr>
                                    <tr [BELEG_HIDDEN]>
                                        <td>
                                            {|Menge auf Beleg|}:
                                        </td>
                                        <td>
                                            <input type="text" value="[ANZBELEG]" size="40" disabled />
                                        </td>
                                    </tr>
                                    <tr [BELEG_HIDDEN]>
                                        <td>
                                            {|Seriennummern zugeordnet|}:
                                        </td>
                                        <td>
                                            <input type="text" value="[ANZVORHANDEN]" size="40" disabled />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Seriennummern fehlen|}:
                                        </td>
                                        <td>
                                            <input type="text" value="[ANZFEHLT]" size="40" disabled />
                                        </td>
                                    </tr>
                                    <tr [ARTIKEL_HIDDEN] [EINGABE_HIDDEN]>
                                        <td>
                                            {|Seriennummer scannen|}:
                                        </td>
                                        <td>
                                            <input type="text" name="eingabescan" id="eingabescan" value="[EINGABESCAN]" size="40" autofocus />
                                        </td>
                                    </tr>
                                    <tr [WARENEINGANG_HIDDEN] [EINGABE_HIDDEN]>
                                        <td>
                                            {|Seriennummer scannen|}:
                                        </td>
                                        <td>
                                            <input type="text" name="eingabescan" id="eingabescan" value="[EINGABESCAN]" size="40" autofocus />
                                        </td>
                                    </tr>
                                    <tr [LIEFERSCHEIN_HIDDEN] [EINGABE_HIDDEN]>
                                        <td>
                                            {|Seriennummer w&auml;hlen|}:
                                        </td>
                                        <td>
                                            <input type="text" name="eingabe" id="eingabe" value="[EINGABE]" size="40" autofocus/>
                                        </td>
                                    </tr>
                                    <tr [EINGABE_HIDDEN]>
                                        <td>
                                            <fieldset>                      
                                                <legend>{|Seriennummernassistent|}</legend>
                                            </fieldset>   
                                        </tr>
                                    </tr>
                                    <tr [EINGABE_HIDDEN]>
                                        <td [ARTIKEL_HIDDEN]>
                                            {|Letzte Seriennummer|}:
                                        </td>
                                        <td [WARENEINGANG_HIDDEN]>
                                            {|Letzte Seriennummer|}:
                                        </td>
                                        <td [LIEFERSCHEIN_HIDDEN]>
                                            {|N&auml;chste Seriennummer|}:
                                        </td>
                                        <td>
                                            <input type="text" name="muster" id="muster" value="[LETZTE]" size="40" disabled />
                                        </td>
                                    </tr>
                                    <tr [EINGABE_HIDDEN]>
                                        <td>
                                            {|Pr&auml;fix|}:
                                        </td>
                                        <td>
                                            <input type="text" name="praefix" id="praefix" value="[PRAEFIX]" size="40" />
                                        </td>
                                    </tr>
                                    <tr [EINGABE_HIDDEN]>
                                        <td>
                                            {|Start|}:
                                        </td>
                                        <td>
                                            <input type="number" name="start" id="start" value="[START]" size="40" />
                                        </td>
                                    </tr>
                                    <tr [EINGABE_HIDDEN]>
                                        <td>
                                            {|Postfix|}:
                                        </td>
                                        <td>
                                            <input type="text" name="postfix" id="postfix" value="[POSTFIX]" size="40" />
                                        </td>
                                    </tr>
                                    <tr [EINGABE_HIDDEN]>
                                        <td>
                                            {|Anzahl|}:
                                        </td>
                                        <td>
                                            <input type="number" name="anzahl" id="anzahl" value="[ANZAHL]" size="40" />
                                        </td>
                                    </tr>      
                                    <tr [EINGABE_HIDDEN]>
                                        <td>
                                            <fieldset>                      
                                                <legend>{|Liste der gew&auml;hlten Seriennummern|}</legend>
                                            </fieldset>   
                                        </tr>
                                    </tr>
                                    <tr [EINGABE_HIDDEN]>
                                        <td>
                                            {|Seriennummern|}:
                                        </td>
                                        <td>
                                            <textarea name="seriennummern" id="seriennummern" rows="20" style="width:100%;">[SERIENNUMMERN]</textarea>
                                            <i>Liste der Seriennummern, 1 pro Zeile</i>
                                        </td>
                                    </tr>  
                                </table>
                            <fieldset>
                        </div>                                                
                    </div>                  
                    <div class="col-xs-14 col-md-2 col-md-height">
                        <div class="inside inside-full-height">                          
            				<fieldset>
                                <legend>{|<!--Legend for this form area goes here>-->Aktionen|}</legend>
                                <table width="100%" border="0" class="mkTableFormular" [EINGABE_HIDDEN]>                                  
                                    <tr>
                                        <td>
                                            <button name="submit" value="hinzufuegen" class="ui-button-icon" style="width:100%;">Zur Liste hinzuf&uuml;gen</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <button name="submit" value="assistent" class="ui-button-icon" style="width:100%;">Assistent ausf&uuml;hren</button>
                                        </td>
                                    </tr>         
                                    <tr [ARTIKEL_HIDDEN]>
                                        <td>
                                            <input type="checkbox" name="allowold" id="allowold" value="1" [PRIO] size="20">{|Ausgelieferte erlauben|}
                                        </td>
                                    </tr>                                    
                                    <tr [ARTIKEL_HIDDEN]>
                                        <td>
                                            <button name="submit" value="einlagern" class="ui-button-icon" style="width:100%;">Speichern</button>
                                        </td>
                                    </tr>
                                    <tr [LIEFERSCHEIN_HIDDEN]>
                                        <td>
                                            <button name="submit" value="lieferscheinzuordnen" class="ui-button-icon" style="width:100%;">Speichern</button>
                                        </td>
                                    </tr>
                                    <tr [WARENEINGANG_HIDDEN]>
                                        <td>
                                            <input type="checkbox" name="allowold" id="allowold" value="1" [PRIO] size="20">{|Ausgelieferte erlauben|}
                                        </td>
                                    </tr>
                                    <tr [WARENEINGANG_HIDDEN]>
                                        <td>
                                            <button name="submit" value="wareneingangzuordnen" class="ui-button-icon" style="width:100%;">Speichern</button>
                                        </td>
                                    </tr>
                                </table>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

