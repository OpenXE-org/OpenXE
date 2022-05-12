<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1"></a></li>
    </ul>
    <!-- ende gehort zu tabview -->

    <!-- erstes tab -->
    <div id="tabs-1">
        [WIKISUBMENU]
        <div class="row">
            <div class="row-height">
                <div class="col-xs-12 col-sm-10 col-sm-height">
                    <div class="inside inside-full-height">
                        [MESSAGE]
                        <div id="wikicontent" data-site="[WIKISITE]">
                        [TAB1]
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-2 col-sm-height">
                    <div class="inside inside-full-height">
                        <table id="wikitabnew">
                            <tr><td>[WIKIICONS]</td></tr>
                            <!--<tr><td><label for="tags">{|Tags|}:</label></td></tr>-->
                            <!--<tr><td><input type="text" id="tags" name="tags" /></td></tr>-->
                            <!--<tr><td><label for="workspacenew">{|Workspace|}:</label></td></tr>
                            <tr>
                                <td>
                                    <select id="workspacenew" name="workspacenew">
                                        <option value="0">{|Default|}</option>
                                        [SELWORKSPACE]
                                    </select>
                                </td>
                            </tr>-->
                            <tr><td><label for="languagenew">{|Sprache|}:</label></td></tr>
                            <tr>
                                <td>
                                    <select id="languagenew" name="languagenew">
                                        <option value="">{|Default|}</option>
                                        [SELLANGUAGE]
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2"><input type="button" id="save" class="btnGreenNew" value="{|Speichern|}" /></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="changepopup">
    <fieldset>
        <legend>{|Was haben Sie ge&auml;ndert?|}</legend>
        <table>
            <tr>
                <td><label for="comment">{|Grund|}</label></td>
                <td><input type="text" id="comment" size="50" /></td>
            </tr>
            <tr>
                <td></td>
                <td><input type="checkbox" value="1" id="notify" />
                    <label for="notify">{|Beobachter benachrichtigen|}</label>
                </td>
            </tr>
        </table>
    </fieldset>
</div>