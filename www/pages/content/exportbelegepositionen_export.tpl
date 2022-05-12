<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
    <!-- ende gehort zu tabview -->


<form action="" method="post">
<div id="tabs-1">
<form method="post">
  [MESSAGE]
  <div class='row'>
  <div class='row-height'>
  <div class='col-xs-12 col-md-10 col-md-height'>
  <div class='inside inside-full-height'>
    <fieldset>
      <legend>{|Bitte Belegart(en) auswählen|}</legend>
            <table class="mkTableFormular">
                    <tr>
                        <td><input type="checkbox" name="angebot" value="1" /> {|Angebot|}</td>
                    </tr>
                    <tr>
                        <td><input type="checkbox" name="auftrag" value="1" /> {|Auftrag|}</td>
                    </tr>
                    <tr>
                        <td><input type="checkbox" name="rechnung" value="1" /> {|Rechnung|}</td>
                    </tr>
                    <tr>
                        <td><input type="checkbox" name="gutschrift" value="1" /> {|Gutschrift|}</td>
                    </tr>
                    <tr>
                        <td><input type="checkbox" name="lieferschein" value="1" /> {|Lieferschein|}</td>
                    </tr>
                </table>
                <table class="mkTableFormular">
                    <tr>
                        <td>Datum von:</td>
                        <td><input type="text" name="von" id="von"value="" /></td>
                    </tr>
                    <tr>
                        <td>Datum bis:</td>
                        <td><input type="text" name="bis" id="bis"value="" /></td>
                    </tr>
                    <tr>
                        <td>Projekt:</td>
                        <td><input type="text" name="projekt" id="projekt" value="" /></td>
                    </tr>
                </table>
    </fieldset>
  </div>
  </div>
  <div class='col-xs-12 col-md-2 col-md-height'>
  <div class='inside inside-full-height'>
    <fieldset>
      <legend>{|Aktionen|}</legend>
      <center><input type="submit" name="download" value="Download" class="btnGreenBig"></center>
    </fieldset>

  </div>
  </div>
  </div>
  </div>


</div>
</form>
