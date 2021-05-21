<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1"></a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">

[MESSAGE]

<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-10 col-md-height">
<div class="inside_white inside-full-height">

  <fieldset class="white">
    <legend>&nbsp;</legend>
    [TAB1]
    <i style="float:right; font-size:10px;color:#6d6d6f;"><span style="color:red">*</span> Interne Bemerkung vorhanden </i>
  </fieldset>

</div>
</div>
<div class="col-xs-12 col-md-2 col-md-height">
<div class="inside inside-full-height">

  <fieldset>
    <legend>{|Aktionen|}</legend>
    <center><input class="btnGreenNew" type="button" name="neuedit" value="&#10010; Neuen Eintrag anlegen" onclick="neuedit(0);"></center>
  </fieldset>

</div>
</div>
</div>
</div>


[TABNEXT]
</div>

<!-- tab view schließen -->
</div>

<div id="lieferadressenaddedit" style="display:none" title="Lieferadressen">
  <input type="hidden" name="lid" id="lid" value="0">
  <input type="hidden" name="landselected" id="landselected" value="[LANDSELECTED]">
  <fieldset>
    <legend>{|Adressdaten|}</legend>
    <table class="mkTableFormular" width="100%">
      <tr>
        <td>{|Typ|}:</td><td><select name="typ" id="typ">
          <option value="firma">Firma</option><option value="herr">Herr</option><option value="frau">Frau</option>
          </select>
        </td>
        <td width="10%">&nbsp;</td>
        <td></td>
        <td></td>
      </tr>
      <tr>
        <td>{|Name|}:</td><td><input type="text" name="name" id="name" size="32"></td>
        <td></td>
        <td>{|Telefon|}:</td><td><input type="text" name="telefon" id="telefon" size="32"></td>
      </tr>
      <tr>
        <td>{|Abteilung|}:</td><td><input type="text" name="abteilung" id="abteilung" size="32"></td>
        <td></td>
        <td>{|E-Mail|}:</td><td><input type="text" name="email" id="email" size="32"></td>
      </tr>
      <tr>
        <td>{|Unterabteilung|}:</td><td><input type="text" name="unterabteilung" id="unterabteilung" size="32"></td>
        <td></td>
        <td>{|GLN|}:</td><td><input type="text" name="gln" id="gln" size="32"></td>
      </tr>
      <tr>
        <td>{|Adresszusatz|}:</td><td><input type="text" name="adresszusatz" id="adresszusatz" size="32"></td>
        <td></td>
        <td>{|Lieferbedingung|}:</td><td><input type="text" name="lieferbedingung" id="lieferbedingung" size="32"></td>
      </tr>
      <tr>
        <td>{|Stra&szlig;e|}:</td><td><input type="text" name="strasse" id="strasse" size="32"></td>
        <td></td>
        <td>{|USt-ID|} <i>{|(falls abweichend)|}</i>:</td><td><input type="text" name="ustid" id="ustid" size="32"></td>
      </tr>
      <tr>
        <td>{|PLZ/Ort|}:</td><td nowrap><input type="text" name="plz" id="plz" size="6" >&nbsp;<input type="text" name="ort" id="ort" size="22" style="margin-left:3pt;"></td>
        <td></td>
        <td>{|Besteuerung|} <i>{|(falls abweichend)|}</i>:</td><td><select name="ust_befreit" id="ust_befreit"><option=""></option><option value="0">Inland</option><option value="1">EU-Lieferung</option><option value="2">Export</option><option value="3">Steuerfrei Inland</option></select>
        </td>
      </tr>
      <tr>
        <td>{|Land|}:</td><td><select name="land" id="land">
              [LIEFERADRESSELAENDER]
            </select></td>
        <td></td>
        <td>{|Standard Lieferadresse|}:</td><td><input type="checkbox" value="1" name="standardlieferadresse" id="standardlieferadresse"></td>
      </tr>
      <tr>
        <td>{|Lieferhinweis Bemerkung|}:</td><td colspan="4"><textarea rows="2" cols="30" name="hinweis" id="hinweis"></textarea></td>
      </tr> 

      <tr>
        <td>{|Interne Bemerkung|}:</td><td colspan="4"><textarea rows="2" cols="30" name="interne_bemerkung" id="interne_bemerkung"></textarea></td>
      </tr> 
    </table>
  </fieldset>  
</div>


<script>
$(document).ready(function() {
  $("#lieferadressenaddedit").dialog({
  modal: true,
  bgiframe: true,
  closeOnEscape:false,
  minWidth:900,
  autoOpen: false,
  buttons: {
    ABBRECHEN: function() {
      $(this).dialog('close');
    },
    SPEICHERN: function() {
      lieferadressensave();
    }
  }
  });

  $("#lieferadressenaddedit").dialog({
    close: function( event, ui ){}
  });

});

  function neuedit(nr)
  {
    if(nr == 0){

      $('#lieferadressenaddedit').find('#land').val($('#lieferadressenaddedit').find('#landselected').val());
      $('#lieferadressenaddedit').find('#lid').val('0');
      document.getElementById("ust_befreit").options[0].selected = true;
      document.getElementById("typ").options[0].selected = true;
      $('#lieferadressenaddedit').find('#name').val('');
      $('#lieferadressenaddedit').find('#telefon').val('');
      $('#lieferadressenaddedit').find('#abteilung').val('');
      $('#lieferadressenaddedit').find('#email').val('');
      $('#lieferadressenaddedit').find('#unterabteilung').val('');
      $('#lieferadressenaddedit').find('#adresszusatz').val('');
      $('#lieferadressenaddedit').find('#strasse').val('');
      $('#lieferadressenaddedit').find('#gln').val('');
      $('#lieferadressenaddedit').find('#ustid').val('');
      $('#lieferadressenaddedit').find('#ort').val('');
      $('#lieferadressenaddedit').find('#plz').val('');
      $('#lieferadressenaddedit').find('#lieferbedingung').val('');
      $('#lieferadressenaddedit').find('#standardlieferadresse').prop("checked", false);
      $('#lieferadressenaddedit').find('#interne_bemerkung').val('');
      $('#lieferadressenaddedit').find('#hinweis').val('');
      $("#lieferadressenaddedit").dialog('open');
    }else{
      $.ajax({
        url: 'index.php?module=adresse&action=lieferadresse&cmd=get&id=[ADRESSID]&lid='+nr,
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
          $('#lieferbedingung').val(data.lieferbedingung);
          $('#interne_bemerkung').val(data.interne_bemerkung);
          $('#hinweis').val(data.hinweis);
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
  }

  function lieferadressensave() {
    $.ajax({
        url: 'index.php?module=adresse&action=lieferadresse&cmd=save&id=[ADRESSID]',
        data: {
          lid: $('#lid').val(),
          name: $('#name').val(),
          telefon: $('#telefon').val(),
          abteilung: $('#abteilung').val(),
          email: $('#email').val(),
          unterabteilung: $('#unterabteilung').val(),
          adresszusatz: $('#adresszusatz').val(),
          strasse: $('#strasse').val(),
          gln: $('#gln').val(),
          ustid: $('#ustid').val(),
          ort: $('#ort').val(),
          plz: $('#plz').val(),
          ust_befreit: $('#ust_befreit').val(),
          typ: $('#typ').val(),
          land: $('#land').val(),
          lieferbedingung: $('#lieferbedingung').val(),
          interne_bemerkung: $('#interne_bemerkung').val(),
          hinweis: $('#hinweis').val(),
          standardlieferadresse: $('#standardlieferadresse').prop("checked")?1:0
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
        },
        success: function(data) {
          if (data.status == 1) {
            $("#lieferadressenaddedit").dialog('close'); 
            updateLiveTable();
          }else{
            alert(data.statusText);
          }
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
