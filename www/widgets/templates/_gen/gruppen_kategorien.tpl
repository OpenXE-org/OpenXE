<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs1"></a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs1">
  <table width="100%">
    <tr>
      <td>
        <form action="" method="post" name="eprooform">
        [FORMHANDLEREVENT]
        <fieldset><legend>{|Einstellungen|}</legend>
          <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
            <tbody>
              <tr valign="top" colspan="3">
                <td>
                  <table border="0" width="100%">
                    <tbody>
                      <tr><td width="200">{|Bezeichnung|}:</td><td>[BEZEICHNUNG][MSGBEZEICHNUNG]</td></tr>
                      <tr><td width="200">{|Projekt|}:</td><td>[PROJEKT][MSGPROJEKT]</td></tr>
                      <tr class="klein" classname="klein"><td align="right"  classname="orange2" class="orange2"><input type="submit" value="Speichern" /></tr>
                    </tbody>
                  </table>
                </td>
              </tr>
            </tbody>
          </table>
        </fieldset>
        </form>
      </td>
    </tr>
  </table>
</div>
</div>
