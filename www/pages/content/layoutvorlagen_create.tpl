<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TEXT]</a></li>
    </ul>
    <div id="tabs-1">

            <form action="" method="post" enctype="multipart/form-data">
<table class="tableborder" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tbody>
      <tr valign="top">
        <td >
<table width="100%" align="center" style="background-color:#cfcfd1;">
<tr>
<td width="33%"></td>
<td align="center" nowrap><b style="font-size: 14pt">Layoutvorlage</b> </td>
<td width="33%" align="right">&nbsp; <input type="submit" name="layouterstellen"
    value="Speichern" onclick="this.form.action += '#tabs-1';"/> <input type="button" value="Abbrechen" onclick="window.location.href='index.php?module=layoutvorlagen&action=list';"></td>
</tr>
</table>
<fieldset><legend>{|Einstellungen|}</legend>

                <table width="100%">
                    <tr>
                        <td width="110">Name:</td>
                        <td><input type="text" name="name" value="[NAME]" size="40"></td>
                        <td></td>
                        <td>Typ:</td>
                        <td>
                            <select name="typ">
                                <option value="pdf">PDF</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Format:</td>
                        <td>
                            <select name="format">
                                <option value="A4">A4 Hoch</option>
                                <option value="A4L">A4 Quer</option>
                                <option value="A5">A5 Hoch</option>
                                <option value="A5L">A5 Quer</option>
                                <option value="A6">A6 Hoch</option>
                                <option value="A6L">A6 Quer</option>
                            </select>
                        </td>
                        <td></td>
                        <td>Kategorie:</td>
                        <td>
                            <input type="text" name="kategorie" value="[KATEGORIE]" size="40" id="kategorie">
                        </td>
                    </tr>
                    <tr>
                        <td>Hintergrund:</td>
                        <td><input type="file" name="pdf_hintergrund"></td>
                        <td></td>
                        <td>Projekt:</td>
                        <td><input type="text" name="layoutvorlagen_projekt" id="layoutvorlagen_projekt" size="40"></td>
                    </tr>
                </table>


</fieldset>
      </td>
      </tr>

    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="1" bordercolor="" classname="orange2" class="orange2">
    <input type="submit" name="layouterstellen"
    value="Speichern" /> <input type="button" value="Abbrechen" onclick="window.location.href='index.php?module=layoutvorlagen&action=list';"/></td>
    </tr>
  
    </tbody>
  </table>
            </form>
    </div>
</div>
