<!-- gehort zu tabview -->
<div id="tabs">
  <ul>
    <li><a href="#tabs-1">[TABTEXT]</a></li>
  </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<form method="post">
<div id="tabs-1">
  
  [MESSAGE]

  <div class="row">
  <div class="row-height">
  <div class="col-xs-12 col-md-10 col-md-height">
  <div class="inside_white inside-full-height">

    <fieldset class="white">
      <legend>&nbsp;</legend>
      [TAB1]
    </fieldset>
    
  </div>
  </div>
  <div class="col-xs-12 col-md-2 col-md-height">
  <div class="inside inside-full-height">  
  
    <fieldset>
      <legend>{|Aktionen|}</legend>
      <input class="btnGreenNew calendar-group-create" type="button" name="anlegen" value="&#10010; Neue Gruppe anlegen">
    </fieldset>
  
  </div>
  </div>
  </div>
  </div>

  [TAB1NEXT]
  
</div>
</form>
</div>

<div id="editKalendergruppen" style="display:none;" title="Bearbeiten">
  <input type="hidden" id="editid">
  <fieldset>
    <legend>Kalendergruppe</legend>
    <table width="100%">
      <tr>
        <td>Bezeichnung: </td>
        <td><input type="text" id="editbezeichnung" size="30"></td>        
      </tr>
      <tr>
        <td>Farbe:</td>
        <td><input type="text" id="editfarbe" size="1" value="#0b8092"></td>
      </tr>
      <tr>
        <td>Inaktiv:</td>
        <td><input type="checkbox" id="editausblenden"></td>
      </tr>
    </table>  
  </fieldset>
  <fieldset id="kalender_gruppen_mitglieder_reihe">
    [GRUPPEN]
  </fieldset>
</div>
