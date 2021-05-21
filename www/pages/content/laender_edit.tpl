<!--<table border="0" width="100%">
<tr><td><table width="100%"><tr><td>[USER_CREATE]</td></tr></table></td></tr>
</table>-->

<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1"></a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]
<form action="" method="post" name="eprooform">
[FORMHANDLEREVENT]
  <div class="row">
    <div class="row-height">
      <div class="col-xs-12 col-md-6 col-md-height">
        <div class="inside inside-full-height">
          <fieldset>
            <legend>{|Land|}</legend>
              <table width="100%" border="0" class="mkTableFormular">
                <tr><td>Zweistelliger ISO Code:</td><td><input type="text" name="iso" value="[ISO]" size="40"></td></tr>
           	    <tr><td>Bezeichnung Deutsch:</td><td><input type="text" name="bezeichnung_de" value="[BEZEICHNUNG_DE]" size="40"></td></tr>
                <tr><td>Bezeichnung Englisch:</td><td><input type="text" name="bezeichnung_en" value="[BEZEICHNUNG_EN]" size="40"></td></tr>
                <tr><td>EU:</td><td><input type="checkbox" name="eu" value="1" [EU]></td></tr>
              </table>
          </fieldset>
        </div>
      </div>
      <div class="col-xs-12 col-md-6 col-md-height">
        <div class="inside inside-full-height">
          <fieldset></fieldset>
        </div>
      </div>
    </div>
  </div>
  <input type="submit" name="submitland" value="Speichern" style="float:right"/>

</form>

</div>
</div>
