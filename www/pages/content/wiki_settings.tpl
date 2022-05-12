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

<div id="popupworkspace">
    <fieldset><legend>{|Workspace|}</legend>
    <table>
        <tr>
            <td>
                <label for="workspace_name">{|Bezeichnung|}:</label>
            </td>
            <td>
                <input type="text" size="40" id="workspace_name" /><input type="hidden" id="workspace_id" />
            </td>
        </tr>
        <tr>
            <td>
                <label for="workspace_active">{|Aktiv|}</label>
            </td>
            <td>
                <input type="checkbox" value="1" id="workspace_active" />
            </td>
        </tr>
        <tr>
            <td>
                <label for="workspace_savein">{|Speichern in|}:</label>
            </td>
            <td>
                <select id="workspace_savein">
                    <option value="">{|Datenbank|}</option>
                    <option value="userdata">{|Userdata|}</option>
                </select>
            </td>
        </tr>
    </table>
    </fieldset>
</div>

<div id="popupsites">
    <fieldset><legend>{|Seiten|}</legend>
        [TABSITES]
    </fieldset>
</div>