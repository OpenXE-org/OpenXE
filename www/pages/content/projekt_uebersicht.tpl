<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
<form action="" method="post">
[MESSAGE]
[TAB1]
  <div class="row">
    <div class="row-height">

      <div class="col-xs-12 col-md-4 col-md-height">
        <div class="inside inside-full-height">

          <fieldset>
            <legend>{|Projekt Details|}</legend>
              <table >
                <tr>
                  <td width="120">Bezeichnung:</td>
                  <td>
                    <input type="text" size="30" name="name" id="name" value="[NAME]">
                  </td>
                </tr>
                <tr>
                  <td>Kennung:</td>
                  <td>
                    <input type="text" size="30" name="abkuerzung" id="abkuerzung" value="[ABKUERZUNG]">
                  </td>
                </tr>
               

                <tr class="gruppebestehend">
                  <td>Kunde:</td>
                  <td>
                    <input type="text" size="30" name="kunde" id="kunde" value="[KUNDE]">
                  </td>
                </tr>

                <tr class="gruppeneu">
                  <td>Verantwortlicher:</td>
                  <td>
                    <input type="text" name="verantwortlicher" id="verantwortlicher" size="30" value="[VERANTWORTLICHER]">
                  </td>
                </tr>
                <tr>
                  <td>Status:</td>
                  <td>
                    <select id="status" name="status"><option [STATUSGEPLANT]>geplant</option><option [STATUSGESTARTET]>gestartet</option><option [STATUSABGESCHLOSSEN]>abgeschlossen</option></select>
                  </td>
                </tr>
                [FREIFELDER]
              </table>
            </fieldset>


          </div>
        </div>

        <div class="col-xs-12 col-md-8 col-md-height">
          <div class="inside inside-full-height">
            <fieldset>
              <legend>{|Beschreibungen|}</legend>

              <table class="mkTableFormular">
                <tr>
                  <td width="200">Allgemeine Information:</td>
                  <td>
                    <textarea name="beschreibung" id="beschreibung">[BESCHREIBUNG]</textarea>
                  </td>
                </tr>
                <tr>
                  <td width="200">Interne Bemerkung:</td>
                  <td>
                    <textarea name="sonstiges" id="sonstiges">[SONSTIGES]</textarea>
                  </td>
                </tr>

              </table>


          </fieldset>
        </div>
      </div>

</div>
</div>
<table width="100%"><tr><td align="right"><input type="submit" value="Speichern" name="speichern"></td></tr></table>
</form>
[TAB1NEXT]
</div>

<!-- tab view schließen -->
</div>
