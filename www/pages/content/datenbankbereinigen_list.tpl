<!-- gehort zu tabview -->
<form method="post">
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
  [MESSAGE]
  	<fieldset>
  		<legend>L&ouml;schen</legend>
  		<select name="optionen">
  			<option value="lager" [LAGERSELECTED]>Alle Lagerbestände ohne Lager</option>
  			<option value="adresse" [ADRESSESELECTED]>Gel&ouml;schte Adresse wiederherstellen</option>
  			<option value="artikel" [ARTIKELSELECTED]>Gel&ouml;schte Artikel wiederherstellen</option>
  		</select>
  		<input type="submit" name="vorschau" id="vorschau" value="Vorschau" />
  	</fieldset>
  [TAB1]
  [TAB1NEXT]
</div>

<!-- tab view schließen -->
</div>



<div id="editArtikelWiederherstellen" style="display:none;" title="Wiederherstellen">
  <input type="hidden" name="e_id" id="e_id">
  <input type="hidden" name = "e_artikelid" id="e_artikelid" value="[ID]">
  <center><b>Artikel wirklich wiederherstellen?</b></center>
  <br />
  <table>
  	<tr>
  	  <td>Alte Artikelnummer bisher:</td>
  	  <td><input type="text" name="e_bisherigenummer" id="e_bisherigenummer" readonly></td>
  	</tr>
  	<tr>
  	  <td>Neue Artikelnummer:</td>
  	  <td><input type="text" name="e_neuenummer" id="e_neuenummer"></td>
  	</tr>
  </table>
</div>



<div id="editAdresseWiederherstellen" style="display:none;" title="Wiederherstellen">
  <input type="hidden" name="es_id" id="es_id">
  <center><b>Adresse wirklich wiederherstellen?</b></center>
  <br />
  <table>
  	<tr>
  	  <td>Alte Kundennummer bisher:</td>
  	  <td><input type="text" name="e_bisherigenummerk" id="e_bisherigenummerk" readonly></td>
  	</tr>
  	<tr>
  	  <td>Alte Lieferantennummer bisher:</td>
  	  <td><input type="text" name="e_bisherigenummerl" id="e_bisherigenummerl" readonly></td>
  	</tr>
  	<tr>
  	  <td>Alte Mitarbeiternummer bisher:</td>
  	  <td><input type="text" name="e_bisherigenummerm" id="e_bisherigenummerm" readonly></td>
  	</tr>
  	<tr>
  	  <td>Neue Kundennummer:</td>
  	  <td><input type="text" name="e_neuenummerk" id="e_neuenummerk"></td>
  	</tr>
  	<tr>
  	  <td>Neue Lieferantennummer:</td>
  	  <td><input type="text" name="e_neuenummerl" id="e_neuenummerl"></td>
  	</tr>
  	<tr>
  	  <td>Neue Mitarbeiternummer:</td>
  	  <td><input type="text" name="e_neuenummerm" id="e_neuenummerm"></td>
  	</tr>
  </table>
</div>


</form>
<script type="text/javascript">

$(document).ready(function() {
    
    $("#editArtikelWiederherstellen").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    minWidth:400,
    maxHeight:700,
    autoOpen: false,
    buttons: {
      ABBRECHEN: function() {
        $(this).dialog('close');
      },
      SPEICHERN: function() {
        artikelwiederherstellenEditSave();
      }
    }
  });

  $("#editAdresseWiederherstellen").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    minWidth:400,
    maxHeight:700,
    autoOpen: false,
    buttons: {
      ABBRECHEN: function() {
        $(this).dialog('close');
      },
      SPEICHERN: function() {
        adressewiederherstellenEditSave();
      }
    }
  });    

});


function artikelwiederherstellenEditSave() {
	$.ajax({
        url: 'index.php?module=datenbankbereinigen&action=artikelwiederherstellensave',
        data: {
            //Alle Felder die fürs editieren vorhanden sind
            id: $('#e_id').val(),
            artikelid: $('#e_artikelid').val(),
            bisherigenummer: $('#e_bisherigenummer').val(),
            neuenummer: $('#e_neuenummer').val()
            
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {
        	App.loading.close();
            if (data.status == 1) {
                updateLiveTableartikel();
                $("#editArtikelWiederherstellen").dialog('close');
            } else {
                alert(data.statusText);
            }
        }
    });

}


function ArtikelwiederherstellenEdit(id) {
    $.ajax({
        url: 'index.php?module=datenbankbereinigen&action=artikelwiederherstellen&cmd=get',
        data: {
            id: id
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
        	App.loading.open();
        },
        success: function(data) {
            $('#editArtikelWiederherstellen').find('#e_id').val(data.id);
            $('#editArtikelWiederherstellen').find('#e_artikelid').val([ID]);
            $('#editArtikelWiederherstellen').find('#e_bisherigenummer').val(data.bisherigenummer);
            $('#editArtikelWiederherstellen').find('#e_neuenummer').val(data.bisherigenummer);            
            App.loading.close();
            $("#editArtikelWiederherstellen").dialog('open');
        }
    });
}


function adressewiederherstellenEditSave(){
	$.ajax({
        url: 'index.php?module=datenbankbereinigen&action=adressewiederherstellensave',
        data: {
            //Alle Felder die fürs editieren vorhanden sind
            eid: $('#es_id').val(),
            bisherigenummerk: $('#e_bisherigenummerk').val(),
            bisherigenummerl: $('#e_bisherigenummerl').val(),
            bisherigenummerm: $('#e_bisherigenummerm').val(),
            neuenummerk: $('#e_neuenummerk').val(),
            neuenummerl: $('#e_neuenummerl').val(),
            neuenummerm: $('#e_neuenummerm').val()
            
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {
        	App.loading.close();
            if (data.status == 1) {
                updateLiveTableadresse();
                $("#editAdresseWiederherstellen").dialog('close');
            } else {
                alert(data.statusText);
            }
        }
    });
}


function AdressewiederherstellenEdit(id) {
    $.ajax({
        url: 'index.php?module=datenbankbereinigen&action=adressewiederherstellen&cmd=get',
        data: {
            id: id
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
        	App.loading.open();
        },
        success: function(data) {
        	$('#editAdresseWiederherstellen').find('#es_id').val(data.id);
            $('#editAdresseWiederherstellen').find('#e_bisherigenummerk').val(data.kundennummer);
            $('#editAdresseWiederherstellen').find('#e_bisherigenummerl').val(data.lieferantennummer);
            $('#editAdresseWiederherstellen').find('#e_bisherigenummerm').val(data.mitarbeiternummer);
            $('#editAdresseWiederherstellen').find('#e_neuenummerk').val(data.kundennummer);
            $('#editAdresseWiederherstellen').find('#e_neuenummerl').val(data.lieferantennummer);
            $('#editAdresseWiederherstellen').find('#e_neuenummerm').val(data.mitarbeiternummer);

            
            App.loading.close();
            $("#editAdresseWiederherstellen").dialog('open');
        }
    });
}




function updateLiveTableartikel(i) {
    var oTableL = $('#artikel_list').dataTable();
    oTableL.fnFilter('%');
    oTableL.fnFilter('');   
}

function updateLiveTableadresse(i){
	var oTableL = $('#adressen_list').dataTable();
	oTableL.fnFilter('%');
	oTableL.fnFilter('');
}



</script>
