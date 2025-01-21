<div id="tabs">
    <ul>
            <li><a href="#tabs-1">[TABTEXT1]</a></li>
    </ul>
    <div id="tabs-1">
        [MESSAGE]
        [MESSAGETABLE]
        <fieldset>
            <form action="" method="post" id="eprooform" name="eprooform">
                <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
                    <tbody>
                        <tr valign="top" colspan="3">
                            <td>                                
                                <table width="80%" align="center">
                                    <tr valign="top">
                                    <td align="center">
                                    <table width="90%">
                                      <tr><td><b>{|Quelllagerplatz|}:</b></td><td><input type="text" id="quelllagerplatz" name="quelllagerplatz" value="[QUELLLAGERPLATZ]"  size="27" style="width:200px"></td></tr>
                                      <tr><td><b>{|Ziellagerplatz|}:</b></td><td><input type="text" id="ziellagerplatz" name="ziellagerplatz" value="[ZIELLAGERPLATZ]"  size="27" style="width:200px"></td></tr>
                                    <tr>
                                        <td>
                                            <p [ERNEUT_UMLAGERN_HIDDEN]><input type="checkbox" name="erneut" id="erneut" value="1" size="20" [ERNEUT_CHECKED]>{|Erneut umlagern|}</input></p>
                                        </td>
                                        <td>
                                            <button name="submit" value="umlagern" class="ui-button-icon" style="width:200px;">
                                                Umlagern
                                            </button>
                                        </td>
                                    </tr>
                                </table>
                                <br>
                            </td>
                            <td>
                            </td>
                        </tr>
                    </table>
                    <br>
                    <br>
                </td>
              </tr>
          </table>
        </form>
        </fieldset>
    </div>
</div>
