
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

<div id="ansprechpartneredit" style="display:none" title="Ansprechpartner">
  <input type="hidden" name="lid" id="lid" value="0">
  <input type="hidden" name="landselected" id="landselected" value="[LANDSELECTED]">
  <fieldset>
    <legend>{|Ansprechpartner|}</legend>
    <table class="mkTableFormular" width="100%">
      <tr>
        <td>{|Typ|}:</td><td><select name="typ" id="typ"><option value="herr">Herr</option><option value="frau">Frau</option><option value="firma">Firma / Filiale</option></select></td>
        <td width="5%">&nbsp;</td>
        <td>{|Telefon|}:</td><td><input type="text" name="telefon" id="telefon" size="32"></td>
      </tr>
      <tr>
        <td>{|Name|}:</td><td><input type="text" name="name" id="name" size="32"></td>
        <td></td>
        <td>{|Telefax|}:</td><td><input type="text" name="telefax" id="telefax" size="32"></td>
      </tr>
      <tr>
        <td>{|Titel|}:</td><td><input type="text" name="titel" id="titel" size="32"></td>
        <td></td>
        <td>{|Mobil|}:</td><td><input type="text" name="mobil" id="mobil" size="32"></td>
      </tr>
      <tr>
        <td>{|Abteilung|}:</td><td><input type="text" name="abteilung" id="abteilung" size="32"></td>
        <td></td>
        <td>{|Anschreiben|}:</td><td><input type="text" name="anschreiben" id="anschreiben" size="32" placeholder="Sehr geehrte Frau Dr. Müller"></td>
      </tr>
      <tr>
        <td colspan="5"></td>
        <tr><td></td></tr>
      </tr>
      <tr>
        <td>{|Unterabteilung|}:</td><td><input type="text" name="unterabteilung" id="unterabteilung" size="32"></td>
        <td></td>
        <td>{|E-Mail|}:</td><td><input type="text" name="email" id="email" size="32"></td>
      </tr>
      <tr>
        <td>{|Adresszusatz|}:</td><td><input type="text" name="adresszusatz" id="adresszusatz" size="32"></td>
        <td></td>
        <td>{|Zust&auml;ndig bzw. Position|}:</td><td><input type="text" name="bereich" id="bereich" size="32"></td>
      </tr>
      <tr>
        <td>{|Stra&szlig;e|}:</td><td><input type="text" name="strasse" id="strasse" size="32"></td>
        <td></td>
        <td>{|Vorname (f&uuml;r Altdaten)|}:</td><td><input type="text" name="vorname" id="vorname" size="32"></td>
      </tr>
      <tr>
        <td>{|PLZ/Ort|}:</td><td nowrap><input type="text" name="plz" id="plz" size="6">&nbsp;<input type="text" name="ort" id="ort" size="21" style="margin-left:4pt;"></td>
        <td></td>
        <td>{|Geburtstag|}:</td><td><input type="text" name="geburtstag" id="geburtstag" size="10"></td>
      </tr>
      <tr>
        <td>{|Land|}:</td><td><select name="ansprechpartner_land" id="ansprechpartner_land">[ANSPRECHPARTNERLAENDER]</select></td>
        <td></td>
      </tr>
      <tr>
        <td colspan="5"></td>
      </tr>
      <tr><td></td></tr>
      <tr>
        <td>{|Sonstiges|}:</td><td rowspan="3"><textarea rows="4" cols="30" name="sonstiges" id="sonstiges"></textarea></td>
        <td></td>
        <td>{|im Kalender anzeigen|}:</td><td><input type="checkbox" name="geburtstagkalender" id="geburtstagkalender" value="1"></td>
      </tr>
      <tr>  
        <td></td>
        <td></td>
        <td>{|Geburtstagskarte|}:</td><td><input type="checkbox" name="geburtstagskarte" id="geburtstagskarte" value="1"></td>
      </tr>
      <tr>
        <td></td>
        <td></td>
        <td>{|Marketingsperre|}:</td><td><input type="checkbox" name="marketingsperre" id="marketingsperre" value="1"></td>
      </tr>
      <tr>
        <td>{|Interne Bemerkung|}:</td><td colspan="4"><textarea rows="4" cols="30" name="interne_bemerkung" id="interne_bemerkung"></textarea></td>
      </tr>
      <tr>
        <td colspan="5">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="5">[GRUPPEN]</td>
      </tr>
    </table>
  </fieldset>
</div>


<script>
[JSPLACETEL]
$(document).ready(function() {
  $("#ansprechpartneredit").dialog({
  modal: true,
  bgiframe: true,
  closeOnEscape:false,
  minWidth:800,
  autoOpen: false,
  buttons: {
    ABBRECHEN: function() {
      $(this).dialog('close');
    },
    SPEICHERN: function() {
      ansprechpartnersave();
    }
  }
  });

  $("#ansprechpartneredit").dialog({
    close: function( event, ui ){}
  });

});

  function neuedit(nr)
  {
    if(nr == 0){
      $('#ansprechpartneredit').find('#ansprechpartner_land').val($('#ansprechpartneredit').find('#landselected').val());
      $('#ansprechpartneredit').find('#lid').val('0');
      document.getElementById("typ").options[0].selected = true;
      $('#ansprechpartneredit').find('#name').val('');
      $('#ansprechpartneredit').find('#titel').val('');
      $('#ansprechpartneredit').find('#bereich').val('');
      $('#ansprechpartneredit').find('#abteilung').val('');
      $('#ansprechpartneredit').find('#unterabteilung').val('');
      $('#ansprechpartneredit').find('#adresszusatz').val('');
      $('#ansprechpartneredit').find('#anschreiben').val('');
      $('#ansprechpartneredit').find('#vorname').val('');
      $('#ansprechpartneredit').find('#geburtstag').val('');

      $('#ansprechpartneredit').find('#geburtstagkalender').prop("checked", false);
      $('#ansprechpartneredit').find('#geburtstagskarte').prop("checked", false);
      $('#ansprechpartneredit').find('#marketingsperre').prop("checked", false);

      $('#ansprechpartneredit').find('#strasse').val('');
      $('#ansprechpartneredit').find('#plz').val('');
      $('#ansprechpartneredit').find('#ort').val('');
      $('#ansprechpartneredit').find('#email').val('');
      $('#ansprechpartneredit').find('#telefon').val('');
      $('#ansprechpartneredit').find('#telefax').val('');
      $('#ansprechpartneredit').find('#mobil').val('');
      $('#ansprechpartneredit').find('#sonstiges').val('');
      $('#ansprechpartneredit').find('#interne_bemerkung').val('');

      $('#adresse_ansprechpartnergruppen input[type=checkbox]').prop('checked', false);
      $("#ansprechpartneredit").dialog('open');
    }else{
      $.ajax({
        url: 'index.php?module=adresse&action=ansprechpartner&cmd=get&id=[ADRESSID]&lid='+nr,
        type: 'POST',
        dataType: 'json',
        data: {},
        success: function(data) {
          $('#lid').val(data.id);
          selectItemByValue(document.getElementById('typ'), data.typ);
          selectItemByValue(document.getElementById('ansprechpartner_land'), data.land);
          $('#name').val(data.name);
          $('#titel').val(data.titel);
          $('#bereich').val(data.bereich);
          $('#abteilung').val(data.abteilung);
          $('#unterabteilung').val(data.unterabteilung);
          $('#adresszusatz').val(data.adresszusatz);
          $('#anschreiben').val(data.anschreiben);
          $('#vorname').val(data.vorname);
          $('#geburtstag').val(data.geburtstag);
          if(data.geburtstagkalender == 1){
            $('#ansprechpartneredit').find('#geburtstagkalender').prop("checked", true);  
          }else{
            $('#ansprechpartneredit').find('#geburtstagkalender').prop("checked", false);  
          }
          if(data.geburtstagskarte == 1){
            $('#ansprechpartneredit').find('#geburtstagskarte').prop("checked", true);  
          }else{
            $('#ansprechpartneredit').find('#geburtstagskarte').prop("checked", false);  
          }
          if(data.marketingsperre == 1){
            $('#ansprechpartneredit').find('#marketingsperre').prop("checked", true);  
          }else{
            $('#ansprechpartneredit').find('#marketingsperre').prop("checked", false);  
          }         

          $('#strasse').val(data.strasse);
          $('#plz').val(data.plz);
          $('#ort').val(data.ort);
          $('#email').val(data.email);
          $('#telefon').val(data.telefon);
          $('#telefax').val(data.telefax);
          $('#mobil').val(data.mobil);
          $('#sonstiges').val(data.sonstiges);
          $('#interne_bemerkung').val(data.interne_bemerkung);
          $('#ansprechpartneredit').dialog('open');
        },
        beforeSend: function() {

        }
      });
      fnFilterColumn1(nr);  
    } 
  }

  function ansprechpartnersave() {
    var inpfields = document.getElementsByTagName('input');
    var gruppen = '';
    for(var i=0; i<inpfields.length; i++) {
      if(inpfields[i].type == 'checkbox' && inpfields[i].checked == true) 
        gruppen += "|"+inpfields[i].name;
    }
    $.ajax({
        url: 'index.php?module=adresse&action=ansprechpartner&cmd=save&id=[ADRESSID]',
        data: {
          gruppen: gruppen,
          lid: $('#lid').val(),
          typ: $('#typ').val(),
          land: $('#ansprechpartner_land').val(),
          name: $('#name').val(),
          titel: $('#titel').val(),
          bereich: $('#bereich').val(),
          abteilung: $('#abteilung').val(),
          unterabteilung: $('#unterabteilung').val(),
          adresszusatz: $('#adresszusatz').val(),
          anschreiben: $('#anschreiben').val(),
          vorname: $('#vorname').val(),
          geburtstag: $('#geburtstag').val(),
          geburtstagkalender: $('#geburtstagkalender').prop("checked")?1:0,
          geburtstagskarte: $('#geburtstagskarte').prop("checked")?1:0,
          marketingsperre: $('#marketingsperre').prop("checked")?1:0,
          strasse: $('#strasse').val(),
          plz: $('#plz').val(),
          ort: $('#ort').val(),
          email: $('#email').val(),
          telefon: $('#telefon').val(),
          telefax: $('#telefax').val(),
          mobil: $('#mobil').val(),
          sonstiges: $('#sonstiges').val(),
          interne_bemerkung: $('#interne_bemerkung').val()
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
        },
        success: function(data) {
          if (data.status == 1) {
            $("#ansprechpartneredit").dialog('close'); 
            updateLiveTable();
          }else{
            alert(data.statusText);
          }

          
        }
    });
  }

 function deleteeintrag(nr){
    if(!confirm("Soll der Ansprechpartner wirklich gelöscht werden?")) return false;
    $.ajax({
        url: 'index.php?module=adresse&action=ansprechpartner&cmd=delete&lid='+nr,
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
    var oTableL = $('#adresse_ansprechpartnerlist').dataTable();
    var tmp = $('.dataTables_filter input[type=search]').val();
    oTableL.fnFilter('%');
    //oTableL.fnFilter('');
    oTableL.fnFilter(tmp);
  }

  function grchange(grid, el, lid)
  {
    $.ajax({
      url: "index.php?module=adresse&action=ansprechpartner&cmd=change&id=[ID]",
      type: 'POST',
          type: 'POST',
          dataType: 'json',
          data: {
            lid: lid, 
            gruppe: grid, 
            wert : $(el).prop('checked')?1:0}
          }
      ).done( function(data) {
      }).fail( function( jqXHR, textStatus ) {
     });
  }

</script>
<script>
	function call(id, dummy)
	{
		$.ajax({
			url: 'index.php?module=placetel&action=call&id='+id,
			type: 'POST',
			dataType: 'json',
			data: {},
			success: function(data) {
				if(data)
				{

				}
			}
		});

	}
</script>
