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
      <fieldset><legend>{|Einstellungen|}</legend>
        <table width="100%" border="0" class="mkTableFormular">
          <tr><td>{|Bezeichnung|}:</td><td><input type="text" name="bezeichnung" value="[BEZEICHNUNG]" size="40" rule="notempty" msg="Pflichfeld!" tabindex="1"></td></tr>
          <tr><td>{|Typ|}:</td><td><input type="text" name="type" size="40" value="[TYPE]" rule="notempty" msg="Pflichfeld!" tabindex="2">&nbsp;<i>z.B. novatel, lastschriftspezial, heidelberg, etc.</i></td></tr>
          <tr><td>{|Modul|}:</td><td><select name="selmodul" id="selmodul" onchange="changemodul();">[SELMODUL]</select></td></tr>
          <tr><td>{|Text auf Beleg|}:</td><td><textarea id="freitext" name="freitext" data-lang="zahlungsweise_freitext_[ID]">[FREITEXT]</textarea><br>Variabeln: {ZAHLUNGBISDATUM}, {ZAHLUNGSZIELTAGE}, {ZAHLUNGSZIELSKONTO}, {ZAHLUNGSZIELSKONTOTOTAL}, {ZAHLUNGSZIELTAGESKONTO}, {ZAHLUNGSZIELSKONTODATUM}, {SOLL}, {SOLLMITSKONTO}, {SKONTOBETRAG}, {SKONTOFAEHIG}, {SKONTOFAEHIGNETTO}, {BELEGNR}, {NAME}, {STEUERNORMAL}, {GESAMTNETTONORMAL}, {STEUERERMAESSIGT}, {GESAMTNETTOERMAESSIGT}, {WAEHRUNG}, {TAGXMONAT} (Bei Modul Tagxmonat)</td></tr>
          <tr><td>{|Projekt|}:</td><td><input type="text" size="30" value="[PROJEKT]" name="projekt" id="projekt" tabindex="3"></td></tr>
          <tr><td>{|Autom. bezahlt (Rechnung)|}:</td><td><input type="checkbox" [AUTOMATISCHBEZAHLT] name="automatischbezahlt" value="1"><i>{|Rechnungen werden automatisch auf bezahlt gesetzt <p style="color:red; display:inline">Achtung: nur setzen, wenn keine Verknüpfung im Zahlungseingang vorgenommern werden soll</b>|}.</i></td></tr>
          <tr><td>{|Autom. bezahlt (Verbindlichkeit)|}:</td><td><input type="checkbox" [AUTOMATISCHBEZAHLTVERBINDLICHKEIT] name="automatischbezahltverbindlichkeit" value="1"><i>{|Verbindlichkeiten werden automatisch auf bezahlt gesetzt|}.</i></td></tr>
          <tr><td>{|Verhalten wie|}:</td><td><select name="verhalten">[SELVERHALTEN]</select></td></tr>
          <tr><td>{|Aktiv|}:</td><td><input type="checkbox" name="aktiv" [AKTIV] value="1"><i>{|Nicht mehr verwendete Zahlungsweisen k&ouml;nnen deaktiviert werden|}.</i></td></tr>
          [JSON]
          [ZAHLUNGSWEISEN_EDIT_HOOK1]
        </table>
      </fieldset>
      <table width="100%"><tr><td align="right"><input type="submit" name="speichern" value="Speichern" id="speichern" style="float:right"/></td></tr></table>
    </form>
  </div>
<!-- tab view schließen -->
</div>
<script>
var modulname = '[AKTMODUL]';
function changemodul()
{
  if($('#selmodul').val() != modulname)
  {
    if(confirm('Wollen Sie das Modul wirklich ändern? Die Einstellungen werden dabei überschrieben'))
    {
      $( '#speichern' ).trigger( 'click' );
    }else{
      $('#selmodul').val(modulname);
    }
  }
}
</script>
