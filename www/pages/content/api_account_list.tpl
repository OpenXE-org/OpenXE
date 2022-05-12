<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1"><!--[TABTEXT]--></a></li>
    </ul>
    <!-- ende gehort zu tabview -->

    <!-- erstes tab -->
    <div id="tabs-1">
    [MESSAGE]
    [TAB1]
    [TAB1NEXT]
    </div>

    <!-- tab view schließen -->
</div>

<div id="apiAccountPopup" class="hidden">
    <fieldset><legend>{|API Account|}</legend>
        <table width="100%">
            <tr>
                <td><label for="api-account-id">{|API Account ID|}:</label></td>
                <td><span id="api-account-id"></span></td>
            </tr>
            <tr>
                <td><label for="aktiv">{|Aktiv|}:</label></td>
                <td><input type="checkbox" id="aktiv" name="aktiv" value="1"></td>
            </tr>
            <tr>
                <td><label for="bezeichnung">{|Bezeichnung|}:</label></td>
                <td><input type="text" id="bezeichnung" name="bezeichnung" size="40"></td><td>
            </tr>
            <tr>
                <td><label for="projekt">{|Projekt|}:</label></td>
                <td><input id="projekt" type="text" size="40" name="projekt"></td>
            </tr>
            <tr>
                <td><label for="remotedomain">{|App Name|} / {|Benutzername|}:</label></td>
                <td><input type="text" id="remotedomain" name="remotedomain" size="40"></td><td>
            </tr>
            <tr>
                <td><label for="initkey">{|Initkey|} / {|Passwort|}:</label></td>
                <td><input type="text" id="initkey" name="initkey" size="40"></td><td>
            </tr>
            <tr>
                <td>{|Aktueller Key|}:</td>
                <td><span id="apitempkey">[APITEMPKEY]</span> <i>F&uuml;r Testzwecke</i></td><td>
            </tr>
            <tr>
                <td><label for="event_url">{|Event URL|}:</label></td>
                <td><input type="text" id="event_url" name="event_url" size="40"></td><td>
            </tr>
            <tr>
                <td><label for="importwarteschlange_name">{|Warteschlangename Bezeichnung|}:</label></td>
                <td><input type="text" id="importwarteschlange_name" name="importwarteschlange_name" size="40"></td><td>
            </tr>
            <tr>
                <td><label for="importwarteschlange">{|Import Warteschlange|}:</label></td>
                <td><input type="checkbox" id="importwarteschlange" name="importwarteschlange" value="1"></td>
            </tr>
            <tr>
                <td><label for="cleanutf8">{|UTF8 Clean|}:</label></td>
                <td><input type="checkbox" id="cleanutf8" name="cleanutf8" value="1"></td>
            </tr>
            <tr>
                <td><label for="ishtmltransformation">{|Ohne HTML Umwandlung|}:</label></td>
                <td><input type="checkbox" id="ishtmltransformation" name="ishtmltransformation" value="1"></td>
            </tr>
            <tr>
                <td><span>Permissions</span></td>
            </tr>
            [API_PERMISSIONS_HTML]
        </table>
    </fieldset>
</div>