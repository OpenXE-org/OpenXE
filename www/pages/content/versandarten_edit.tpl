
<!-- gehort zu tabview -->
<div id="tabs">
  <ul>
    <li><a href="#tabs-1"></a></li>
  </ul>
  <div id="tabs-1">
    [MESSAGE]
      <form action="" method="post" name="eprooform">
        [FORMHANDLEREVENT]
        <fieldset>
          <legend>{|Einstellungen|}</legend>
            <table width="100%" border="0" class="mkTableFormular">
              <tr><td>{|Bezeichnung|}: </td><td><input type="text" name="bezeichnung" value="[BEZEICHNUNG]" size="40" data-lang="versandart_bezeichnung_[ID]">[MSGBEZEICHNUNG]</td></tr>
              <tr><td>{|Typ|}:</td><td><input type="text" name="typ" value="[TYP]" size="40">[MSGTYP] <i>{|z.B. dhl,ups,etc.|}</i></td></tr>
              <tr><td>{|Modul|}:</td><td><select name="selmodul" id="selmodul" onchange="changemodul();">[SELMODUL]</select></td></tr>
              <tr><td>{|Projekt|}:</td><td><input type="text" id="projekt" name="projekt" value="[PROJEKT]" size="30"></td></tr>
              <tr><td>{|Aktiv|}:</td><td><input type="checkbox" name="aktiv" value="1" [AKTIV]><i>{|Aktiv. Nicht mehr verwendete Versandarten können deaktiviert werden.|}</i></td></tr>
              <tr><td>{|Kein Portocheck|}:</td><td><input type="checkbox" name="keinportocheck" value="1" [KEINPORTOCHECK]><i>{|Porto-Check im Auftrag deaktivieren.|}</i></td></tr>

              <tr><td>{|Drucker Paketmarke|}:</td><td><select name="paketmarke_drucker" id="paketmarke_drucker">[PAKETMARKE_DRUCKER]</select></td></tr>
              <tr><td>{|Drucker Export|}:</td><td><select name="export_drucker" id="export_drucker">[EXPORT_DRUCKER]</select></td></tr>
              <tr><td>{|Versandmail|}:</td><td><select name="versandmail" id="versandmail">[SELVERSANDMAIL]</select></td></tr>
              <tr class="versandbetreff"><td>{|Textvorlage|}:</td><td><select name="geschaeftsbrief_vorlage" id="geschaeftsbrief_vorlage">[SELGESCHAEFTSBRIEF_VORLAGE]</select></td></tr>
              [JSON]
            </table>
        </fieldset>
        <input type="submit" name="speichern" value="{|Speichern|}" id="speichern" style="float:right"/>
      </form>
    </div>
  </div>
<script>
  var modulname = '[AKTMODUL]';
  function changemodul()
  {
    if($('#selmodul').val() != modulname)
    {
      if(confirm('{|Wollen Sie das Modul wirklich ändern? Die Einstellungen werden dabei überschrieben|}'))
      {
        $( '#speichern' ).trigger( 'click' );
      }else{
        $('#selmodul').val(modulname);
      }
    }
  }
</script>
