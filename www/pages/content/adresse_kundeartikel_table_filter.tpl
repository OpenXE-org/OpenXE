<script>
function editkommentar(id)
{
  $('#rechnung_position').val(id);
  $('#internerkommentar').val($('#kommentar_'+id).val());
  $('#editpopup').dialog(
  {
    modal: true,
    minWidth: 540,
    title:'interner Kommentar',
    close: function(event, ui){
    
    }
  }
);
}
</script>

<div class="filter-box filter-usersave">
  <div class="filter-block filter-reveal">
    <div class="filter-title">{|Filter|}<span class="filter-icon"></span></div>
    <ul class="filter-list">
      [VORAUFTRAG]<li class="filter-item"><input type="checkbox" value="1" id="auftrag" title="auftrag" /><label for="auftrag">{|Auftrag|}</label></li>[NACHAUFTRAG]
      [VORRECHNUNG]<li class="filter-item"><input type="checkbox" value="1" id="rechnung" title="rechnung" /><label for="rechnung">{|Rechnung|}</label></li>[NACHRECHNUNG]
      [VORGUTSCHRIFT]<li class="filter-item"><input type="checkbox" value="1" id="gutschrift" title="gutschrift" /><label for="gutschrift">{|Gutschrift|}</label></li>[NACHGUTSCHRIFT]
      [VORANGEBOT]<li class="filter-item"><input type="checkbox" value="1" id="angebot" title="angebot" /><label for="angebot">{|Angebot|}</label></li>[NACHANGEBOT]
      [VORBESTELLUNG]<li class="filter-item"><input type="checkbox" value="1" id="bestellung" title="bestellung" /><label for="bestellung">{|Bestellung|}</label></li>[NACHBESTELLUNG]
      [VORLIEFERSCHEIN]<li class="filter-item"><input type="checkbox" value="1" id="lieferschein" title="lieferschein" /><label for="lieferschein">{|Lieferschein|}</label></li>[NACHLIEFERSCHEIN]
      [VORPRODUKTION]<li class="filter-item"><input type="checkbox" value="1" id="produktion" title="produktion" /><label for="produktion">{|Produktion|}</label></li>[NACHPRODUKTION]
    </ul>
  </div>

  <div class="filter-block filter-inline">
    <div class="filter-title">{|Auswahl|}</div>
    <ul class="filter-list">
      <li class="filter-item">
        <label for="nurgeraete" class="switch">
          <input type="checkbox" id="nurgeraete" value="1" title="nur geraete" />
          <span class="slider round"></span>
        </label>
        <label for="nurgeraete">{|Nur Ger&auml;te|}</label>
      </li>
      <li class="filter-item">
        <label for="nurservice" class="switch">
          <input type="checkbox" id="nurservice" value="1" title="nur service" />
          <span class="slider round"></span>
        </label>
        <label for="nurservice">{|Nur Serviceartikel|}</label>
      </li>
      <li class="filter-item">
        <label for="nurgebuehr" class="switch">
          <input type="checkbox" id="nurgebuehr" value="1" title="nur gebuehr" />
          <span class="slider round"></span>
        </label>
        <label for="nurgebuehr">{|Nur Geb&uuml;hr|}</label>
      </li>
      <li class="filter-item">
        <label for="mitfreifelder" class="switch">
          <input type="checkbox" id="mitfreifelder" value="1" title="mit freifelder" />
          <span class="slider round"></span>
        </label>
        <label for="mitfreifelder">{|Mit Freifelder|}</label>
      </li>
    </ul>
  </div>
</div>

<div id="editpopup" style="display:none;">
<form method="POST">
  <table>
    <tr>
      <td>{|Kommentar|}:<input type="hidden" name="rechnung_position" value="" id="rechnung_position" ></td>
      <td><input type="text" name="internerkommentar" id="internerkommentar" value="" /></td>
      <td><input type="submit" name="speichern" value="{|speichern|}" /></td>
    </tr>
  </table>
</form>
</div>



<div id="editInternerKommentar" style="display:none;" title="Bearbeiten"> 
  <form action="" method="post">
    <input type="hidden" id="e_id">
    <input type="hidden" id="e_art">
    <fieldset>
      <legend>{|Interner Kommentar|}</legend>
      <table>
        <tr>
          <td>{|Interner Kommentar|}:</td>
          <td><input type="text" name="e_internerkommentar" id="e_internerkommentar" size="40"></td>
        </tr>
      </table>
    </fieldset>
  </form>
</div>



<script type="text/javascript">

$(document).ready(function() {
  $('#e_internerkommentar').focus();

  $("#editInternerKommentar").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    minWidth:600,
    maxHeight:800,
    autoOpen: false,
    buttons: {
      ABBRECHEN: function() {
        InternerKommentarReset();
        $(this).dialog('close');
      },
      SPEICHERN: function() {
        InternerKommentarEditSave();
      }
    }
  });

  $("#editInternerKommentar").dialog({
    close: function( event, ui ) {InternerKommentarReset();}
  });

});


function InternerKommentarReset(){
  $('#editInternerKommentar').find('#e_id').val('');
  $('#editInternerKommentar').find('#e_internerkommentar').val('');
  $('#editInternerKommentar').find('#e_art').val('');
}

function InternerKommentarEditSave() {

  $.ajax({
    url: 'index.php?module=adresse&action=kundeartikel&cmd=save',
    data: {
      //Alle Felder die fürs editieren vorhanden sind
      id: $('#e_id').val(),
      internerkommentar: $('#e_internerkommentar').val(),
      art: $('#e_art').val()
            
    },
    method: 'post',
    dataType: 'json',
    beforeSend: function() {
      App.loading.open();
    },
    success: function(data) {
      App.loading.close();
      if (data.status == 1) {
        InternerKommentarReset();
        updateLiveTable();
        $("#editInternerKommentar").dialog('close');
      } else {
        alert(data.statusText);                              
      }
    }
  });
}

function InternerKommentarEdit(art,id) {

  if(id > 0 && art > 0)
  { 
    $.ajax({
      url: 'index.php?module=adresse&action=kundeartikel&cmd=edit',
      data: {
        id: id,
        art: art
      },
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
        App.loading.open();
      },
      success: function(data) {
        if(data.id > 0)
        {
          $('#editInternerKommentar').find('#e_id').val(data.id);
          $('#editInternerKommentar').find('#e_internerkommentar').val(data.internerkommentar);
          $('#editInternerKommentar').find('#e_art').val(data.art);            
        } 
        App.loading.close();
        $("#editInternerKommentar").dialog('open');
      }
    });
  } else {
    InternerKommentarReset(); 
    $("#editInternerKommentar").dialog('open');
  }

}

function updateLiveTable(i) {
    var oTableL = $('#adresseartikel').dataTable();
    var tmp = $('.dataTables_filter input[type=search]').val();
    oTableL.fnFilter('%');
    //oTableL.fnFilter('');
    oTableL.fnFilter(tmp);  
}

</script>