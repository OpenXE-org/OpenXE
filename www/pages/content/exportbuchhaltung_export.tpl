<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
    <!-- ende gehort zu tabview -->


<form action="" method="post">
    <div id="tabs-1">
        [MESSAGE]
        <div class='row'>
            <div class='row-height'>
                <div class='col-xs-12 col-md-10 col-md-height'>
                    <div class='inside inside-full-height'>
                        <fieldset>
                            <legend>{|Buchungsstapel|}</legend>
                            <table width="100%" border="0">
                                <tr>
                                    <td>{|Rechnungen:|}</td>
                                    <td><input type="checkbox" name="rechnung" value="1" [RGCHECKED] /></td>
                                </tr>
                                <tr>
                                    <td>{|Gutschriften:|}</td>
                                    <td><input type="checkbox" name="gutschrift" value="1" [GSCHECKED] /></td>
                                </tr>
                                <tr>
                                    <td>{|Verbindlichkeiten:|}</td>
                                    <td><input type="checkbox" name="verbindlichkeit" value="1" [VBCHECKED] /></td>
                                </tr>
                                <tr>
                                    <td>{|Lieferantengutschriften:|}</td>
                                    <td><input type="checkbox" name="lieferantengutschrift" value="1" [LGCHECKED] /></td>
                                </tr>
                                <tr>
                                    <td>Datum von:</td>
                                    <td><input type="text" name="von" id="von" value="[VON]" /></td>
                                </tr>
                                <tr>
                                    <td>Datum bis:</td>
                                    <td><input type="text" name="bis" id="bis" value="[BIS]" /></td>
                                </tr>
                                <tr>
                                    <td>Projekt:</td>
                                    <td><input type="text" name="projekt" id="projekt" value="[PROJEKT]" /></td>
                                </tr>
                                <tr>
                                    <td>Differenzen (Kopf/Positionen) ignorieren:</td>
                                    <td><input type="checkbox" name="diffignore" value="1" [DIFFIGNORE] /></td>
                                </tr>
                                <tr>
                                    <td>Konto f&uuml;r Differenzen:</td>
                                    <td><input type="text" name="sachkonto" id="sachkonto" value="[SACHKONTO]" /></td>
                                </tr>
                                <tr>
                                    <td>Format:</td>
                                    <td>
                                        <select name="format">
                                            <option value="ISO-8859-1">ISO-8859-1</option>
                                            <option value="UTF-8">UTF-8</option>
                                            <option value="UTF-8-BOM">UTF-8 mit BOM</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>{|PDF-Dateien exportieren:|}</td>
                                    <td><input type="checkbox" name="pdfexport" value="1" [PDFEXPORT] /></td>
                                </tr>
                           </table>                            
                        </fieldset>
                    </div>
                </div>
                <div class='col-xs-12 col-md-2 col-md-height'>
                    <div class='inside inside-full-height'>                        
                        <fieldset>
                            <legend>{|Aktionen|}</legend>
                            <input type="submit" name="submit" value="Download" class="btnGreenBig">
                        </fieldset>
                    </div>
                </div>
            </div>
        </div><!-- row -->
    </form>
</div>

