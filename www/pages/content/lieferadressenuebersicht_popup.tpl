<table width="100%" border="0"><tr><td>
[MESSAGE]

<form method="post">
  <fieldset>
    <legend>{|Lieferadresse|}</legend>
    <input type="hidden" name="lid" id="lid" value="0">
    <table width="100%">
      <tr>
        <td>{|Typ|}:</td>
        <td><select name="typ" id="typ">
        			[LIEFERADRESSETYP]
        		</select></td>
      </tr>
      <tr>
        <td>{|Name|}:*</td>
        <td width="300"><input type="text" name="name" id="name" size="30"></td>
        <td width="200">Telefon:</td>
        <td><input type="text" name="telefon" id="telefon" size="30"></td>
      </tr>
      <tr>
        <td>{|Abteilung|}:</td>
        <td><input type="text" name="abteilung" id="abteilung" size="30"></td>
        <td>{|E-Mail|}:</td>
        <td><input type="text" name="email" id="email" size="30"></td>
      </tr>
      <tr>
        <td width="100">{|Unterabteilung|}:</td>
        <td><input type="text" name="unterabteilung" id="unterabteilung" size="30"></td>
        <td>{|GLN|}:</td>
        <td><input type="text" name="gln" id="gln" size="30"></td>
      </tr>
      <tr>
        <td>{|Adresszusatz|}:</td>
        <td><input type="text" name="adresszusatz" id="adresszusatz" size="30"></td>
        <td>{|Lieferbedingung|}:</td>
        <td><input type="text" name="lieferbedingung" id="lieferbedingung" size="30"></td>
      </tr>
      <tr>
        <td>{|Stra&szlig;e|}:</td>
        <td><input type="text" name="strasse" id="strasse" size="30"></td>
        <td>{|Ust-ID|} <i>({|falls abweichend|})</i>:</td>
        <td><input type="text" name="ustid" id="ustid" size="30"></td>
      </tr>
      <tr>
        <td>{|PLZ/Ort|}:</td>
        <td><input type="text" name="plz" id="plz" size="5">&nbsp;<input type="text" name="ort" id="ort" size="20"></td>
       	<td>{|Besteuerung|} <i>({|falls abweichend|})</i>:</td>
        <td><select name="ust_befreit" id="ust_befreit">
              <option value="0"></option>
              <option value="0">Inland</option>
              <option value="1">EU-Lieferung</option>
              <option value="2">Export</option>
              <option value="3">Steuerfrei Inland</option>
            </select></td>
      </tr>
      <tr>
        <td>{|Land|}:</td>
        <td><select name="land" id="land">
        			[LIEFERADRESSELAENDER]
       			</select></td>
       	<td>{|Standard Lieferadresse|}:</td>
        <td><input type="checkbox" name="standardlieferadresse" id="standardlieferadresse" size="30" value="1"></td>
      </tr>
      <tr>
        <td>{|Lieferhinweis|}:</td>
        <td colspan="4"><textarea rows="4" cols="30" name="hinweis" id="hinweis"></textarea></td>
      </tr>

      <tr>
        <td>{|Interne Bemerkung|}:</td>
        <td colspan="4"><textarea rows="4" cols="30" name="interne_bemerkung" id="interne_bemerkung"></textarea></td>
      </tr>
    </table>
    <br />
    <input type="submit" class="btnBlue" name="lieferadressespeichern" id="lieferadressespeichern" style="float:right" value="Lieferadresse speichern">
  </fieldset>
</form>




[TAB1]
<i style="float:right; font-size:10px;color:#6d6d6f;"><span style="color:red">*</span> Interne Bemerkung vorhanden </i>
[TABNEXT]
</td></tr></table>

<script>
  function neuedit(nr)
  {
    $.ajax({
      url: 'index.php?module=adresse&action=lieferadresse&cmd=get&lid='+nr,
      type: 'POST',
      dataType: 'json',
      data: {},
      success: function(data) {
        $('#lid').val(data.id);
        selectItemByValue(document.getElementById('ust_befreit'), data.ust_befreit);
        selectItemByValue(document.getElementById('typ'), data.typ);
        selectItemByValue(document.getElementById('land'), data.land);
        $('#name').val(data.name);
        $('#telefon').val(data.telefon);
        $('#abteilung').val(data.abteilung);
        $('#email').val(data.email);
        $('#unterabteilung').val(data.unterabteilung);
        $('#adresszusatz').val(data.adresszusatz);
        $('#strasse').val(data.strasse);
        $('#gln').val(data.gln);
        $('#ustid').val(data.ustid);
        $('#ort').val(data.ort);
        $('#plz').val(data.plz);
        $('#hinweis').val(data.hinweis);
        $('#lieferbedingung').val(data.lieferbedingung);
        $('#interne_bemerkung').val(data.interne_bemerkung);
        if(data.standardlieferadresse == 1){
          $('#lieferadressenaddedit').find('#standardlieferadresse').prop("checked", true);
        }else{
          $('#lieferadressenaddedit').find('#standardlieferadresse').prop("checked", false);
        }
        $('#lieferadressenaddedit').dialog('open');
      },
      beforeSend: function() {

      }
    });
  }
 function deleteeintrag(nr)
  {
    if(!confirm("Soll die Lieferadresse wirklich gelöscht werden?")) return false;
    $.ajax({
        url: 'index.php?module=adresse&action=lieferadresse&cmd=delete&lid='+nr,
        data: {
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
        },
        success: function(data) {
          updateLiveTable();
        }
    });
  }

  function selectItemByValue(elmnt, value){
    for(var i=0; i < elmnt.options.length; i++)
    {
      if(elmnt.options[i].value == value)
        elmnt.selectedIndex = i;
    }
  }

function updateLiveTable() {
  var oTableL = $('#adresse_lieferadressenlist').dataTable();
  var tmp = $('.dataTables_filter input[type=search]').val();
  oTableL.fnFilter('%');
  //oTableL.fnFilter('');
  oTableL.fnFilter(tmp);
}
</script>
