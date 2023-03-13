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

