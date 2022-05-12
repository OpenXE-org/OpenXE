<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
  [MESSAGE]

  <div class="filter-box filter-usersave">
    <div class="filter-block filter-inline">
      <div class="filter-title">{|Filter|}</div>
      <ul class="filter-list">
        <li class="filter-item">
          <label for="meinerueckrufe" class="switch">
            <input type="checkbox" name="meinerueckrufe" id="meinerueckrufe">
            <span class="slider round">
          </label>
          <label for="meinerueckrufe">{|meine R&uuml;ckrufe|}</label>
        </li>
        <li class="filter-item">
          <label for="meinevergebenenrueckrufe" class="switch">
            <input type="checkbox" name="meinevergebenenrueckrufe" id="meinevergebenenrueckrufe">
            <span class="slider round">
          </label>
          <label for="meinevergebenenrueckrufe">{|meine vergebenen R&uuml;ckrufe|}</label>
        </li>
        <li class="filter-item">
          <label for="auchabgeschlossene" class="switch">
            <input type="checkbox" name="auchabgeschlossene" id="auchabgeschlossene">
            <span class="slider round">
          </label>
          <label for="auchabgeschlossene">{|auch abgeschlossene|}</label>
        </li>
      </ul>
    </div>
  </div>

  [TAB1]
  
  [TAB1NEXT]
</div>

<!-- tab view schließen -->
</div>

<div id="editTelefonrueckruf" style="display:none;" title="Bearbeiten">
<form method="post">
  <input type="hidden" id="e_id" value="0">
  <div class='row'>
  <div class='row-height'> 
  <div class='col-xs-12 col-md-5 col-md-height'>
  <div class='inside_white inside-full-height'>

    <fieldset>
    	<legend>{|Anruf|}</legend>
    	<table>
        <tr>
          <td>{|Anrufer|}:</td>
          <td colspan="3"><input type="text" name="evon" id="evon" size="42"></td>
        </tr>
        <tr>
          <td width="115">{|Datum|}:</td>
          <td width="180"><input type="text" name="edatum" id="edatum"></td>
          <td width="30">{|Zeit|}:</td>
          <td><input type="text" name="ezeit" id="ezeit" size="8"></td>
        </tr>
        <tr>
          <td>{|Grund|}:</td>
          <td colspan="3"><input type="text" name="egrund" id="egrund" size="42"></td>
        </tr>
        <tr>
          <td>{|Kommentar|}:</td>
          <td colspan="3" ><textarea rows="10" cols="38" name="ebeschreibung" id="ebeschreibung">
          </textarea></td>
        </tr>
        <tr>
          <td>{|R&uuml;ckruf-Telefonnr.|}:</td>
          <td colspan="3"><input type="text" name="etelefon" id="etelefon" size="42"></td>
        </tr>
        <tr>
          <td>{|Bitte erledigen von|}:</td>
          <td colspan="3"><input type="text" name="emitarbeiter" id="emitarbeiter" size="42"></td>
        </tr>
      </table>
    </fieldset>

  </div>
  </div>
  <div id="zweitesfieldset" style='display:none' class='col-xs-12 col-md-7 col-md-height'>
  <div class='inside inside-full-height'>
    <fieldset>
      <legend>{|Versuche|}</legend>

      [VERSUCHE]

      <center><input class='btnGreenNew' type='button' name='neuerversuch' id="neuerversuch" value='&#10010; Neuen Versuch anlegen' onclick='NeuerVersuchEdit(0);''></center>
    </fieldset>
  </div>
  </div>
  </div>
  </div>

</form>
</div>



<div id="editNeuerVersuch" style="display:none;" title="Bearbeiten">
<form method="post">
  <input type="hidden" id="eid" value="0">
  <input type="hidden" id="rid" value="0">
  <fieldset>
    <legend>{|Neuer Versuch|}</legend>
    <table>
      <tr>
        <td><label for="enotiz">{|Notiz:|}</label></td>
        <td><textarea rows="10" cols="38" name="enotiz" id="enotiz"></textarea></td>
      </tr>
    </table>
  </fieldset>
</form>
</div>








<script type="text/javascript">

$(document).ready(function() {
  $('#evon').focus();

  $("#editTelefonrueckruf").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    minWidth:500,
    maxHeight:700,
    autoOpen: false,
    buttons: {
      ABBRECHEN: function() {
        TelefonrueckrufReset();
        $(this).dialog('close');
      },
      'JETZT ANLEGEN': function() {
        TelefonrueckrufEditSave();
      }
    }
  });

    $("#editTelefonrueckruf").dialog({

  close: function( event, ui ) { TelefonrueckrufReset();}
});

});



$("#editNeuerVersuch").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    minWidth:530,
    maxHeight:700,
    autoOpen: false,
    buttons: {
      ABBRECHEN: function() {
        NeuerVersuchReset();
        $(this).dialog('close');
      },
      'SPEICHERN': function() {
        NeuerVersuchEditSave();
      }
    },
    open: function() {
      // AutoFocus auf Notizfeld setzen
      window.setTimeout(function() {
        var notizFeld = CKEDITOR.instances['enotiz'];
        if (notizFeld !== undefined) {
          notizFeld.focus();
        }
      }, 100);
    }
  });


function NeuerVersuchReset(){
  $('#editNeuerVersuch').find('#enotiz').val('');
}



function TelefonrueckrufReset()
{
  var today = new Date();
  var dd = today.getDate();
  var mm = today.getMonth()+1; //January is 0!
  var yyyy = today.getFullYear();

  var h = today.getHours();  
  var i = today.getMinutes(); 
  //var s = today.getSeconds();

  if(h < 10){
    h = '0'+h;
  }

  if(i < 10){
    i = '0'+i;
  }

  /*if(s < 10){
    s = '0'+s;
  }*/

  var time = h + ':' + i;


  if(dd<10) {
      dd = '0'+dd
  } 

  if(mm<10) {
      mm = '0'+mm
  } 

  today = dd + '.' + mm + '.' + yyyy;
  
  $('#editTelefonrueckruf').find('#e_id').val('');
  $('#editTelefonrueckruf').find('#edatum').val(today);
  $('#editTelefonrueckruf').find('#ezeit').val(time);
  $('#editTelefonrueckruf').find('#evon').val('');
  $('#editTelefonrueckruf').find('#egrund').val('');
  $('#editTelefonrueckruf').find('#ebeschreibung').val('');
  $('#editTelefonrueckruf').find('#etelefon').val('');
  $('#editTelefonrueckruf').find('#emitarbeiter').val('');
}

function TelefonrueckrufEditSave() {
	$.ajax({
    url: 'index.php?module=telefonrueckruf&action=save',
    data: {
      //Alle Felder die fürs editieren vorhanden sind
      id: $('#e_id').val(),
      datum: $('#edatum').val(),
      zeit: $('#ezeit').val(),
      von: $('#evon').val(),
      grund: $('#egrund').val(),
      beschreibung: $('#ebeschreibung').val(),
      telefon: $('#etelefon').val(),
      mitarbeiter: $('#emitarbeiter').val(),
                      
    },
    method: 'post',
    dataType: 'json',
    beforeSend: function() {
      App.loading.open();
    },
    success: function(data) {
     	App.loading.close();
      if (data.status == 1) {
        TelefonrueckrufReset();
        updateLiveTable();
        $("#editTelefonrueckruf").dialog('close');
      } else {
        alert(data.statusText);
      }
    }
  });


}

function TelefonrueckrufEdit(id) {
  if(id > 0)
  { 
    $('#zweitesfieldset').show();


    $.ajax({
      url: 'index.php?module=telefonrueckruf&action=edit&cmd=get',
      data: {
        id: id
      },
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
      	App.loading.open();
      },
      success: function(data) {
        oMoreData1telefonrueckruf_versuche = id;
        updateLiveTableVersuche();

        $("#editTelefonrueckruf").dialog({
          modal: true,
          bgiframe: true,
          closeOnEscape:false,
          minWidth:1200,
          maxHeight:1000,
          autoOpen: false,
          buttons: {
            ABBRECHEN: function() {
              TelefonrueckrufReset();
              $(this).dialog('close');
            },
            'SPEICHERN': function() {
              TelefonrueckrufEditSave();
            },
            'ALS ABGESCHLOSSEN MARKIEREN': function() {
              TelefonrueckrufAbgeschlossen(id);
            }

          }
        });

        $('#editTelefonrueckruf').find('#e_id').val(data.id);
        $('#editTelefonrueckruf').find('#edatum').val(data.datum);
        $('#editTelefonrueckruf').find('#ezeit').val(data.zeit);
        $('#editTelefonrueckruf').find('#evon').val(data.von);
        $('#editTelefonrueckruf').find('#egrund').val(data.grund);
        $('#editTelefonrueckruf').find('#ebeschreibung').val(data.kommentar);
        $('#editTelefonrueckruf').find('#etelefon').val(data.telefonnummer);
        $('#editTelefonrueckruf').find('#emitarbeiter').val(data.rueckrufvon);
        App.loading.close();
        $("#editTelefonrueckruf").dialog('open');
      }
    });
  } else {


    $('#zweitesfieldset').hide();
    TelefonrueckrufReset();

    App.loading.close();


    $("#editTelefonrueckruf").dialog({
      modal: true,
      bgiframe: true,
      closeOnEscape:false,
      maxWidth:500,
      maxHeight:700,
      autoOpen: false,
      buttons: {
        ABBRECHEN: function() {
          TelefonrueckrufReset();
          $(this).dialog('close');
        },
        'JETZT ANLEGEN': function() {
          TelefonrueckrufEditSave();
        }
      }
    });


    $("#editTelefonrueckruf").dialog('open');
   

  }

}

function updateLiveTable() {
  var oTableL = $('#telefonrueckruf_list').dataTable();
  var tmp = $('.dataTables_filter input[type=search]').val();
  oTableL.fnFilter('%');
  //oTableL.fnFilter('');
  oTableL.fnFilter(tmp);   
}

function TelefonrueckrufDelete(id) {
  var conf = confirm('Wirklich löschen?');
  if (conf) {
    $.ajax({
      url: 'index.php?module=telefonrueckruf&action=delete',
      data: {
        id: id
      },
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
        App.loading.open();
      },
      success: function(data) {
        if (data.status == 1) {
          updateLiveTable();
        } else {
          alert(data.statusText);
        }
        App.loading.close();
      }
    });
  }

  return false;

}

function TelefonrueckrufAbgeschlossen(id){
  var conf = confirm('Wirklich als abgeschlossen markieren?');
  if (conf) {
    $.ajax({
      url: 'index.php?module=telefonrueckruf&action=abgeschlossen',
      data: {
        id: id
      },
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
        App.loading.open();
      },
      success: function(data) {
        if (data.status == 1) {
          updateLiveTable();
          $("#editTelefonrueckruf").dialog('close');
        } else {
          alert(data.statusText);
        }
        App.loading.close();
      }
    });
  }

  return false;
}

function NeuerVersuchEdit(id){

  if(id > 0)
  { 

    $.ajax({
      url: 'index.php?module=telefonrueckruf&action=editversuch&cmd=get',
      data: {
        id: id
      },
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
        App.loading.open();
      },
      success: function(data) {
        $('#editNeuerVersuch').find('#eid').val(data.id);
        $('#editNeuerVersuch').find('#enotiz').val(data.beschreibung);
        //$('#editNeuerVersuch').find('#rid').val(data.telefonrueckruf);        
        App.loading.close();
        $("#editNeuerVersuch").dialog('open');
      }
    });
  } else {

    NeuerVersuchReset();

    App.loading.close();


    $("#editNeuerVersuch").dialog('open');

  }

}

function updateLiveTableVersuche(i) {
  var oTableL = $('#telefonrueckruf_versuche').dataTable();
  var tmp = $('.dataTables_filter input[type=search]').val();
  oTableL.fnFilter('%');
  //oTableL.fnFilter('');
  oTableL.fnFilter(tmp);   
}



function NeuerVersuchEditSave() {
  $.ajax({
    url: 'index.php?module=telefonrueckruf&action=saveversuch',
    data: {
      //Alle Felder die fürs editieren vorhanden sind
      id: $('#eid').val(),
      beschreibung: $('#enotiz').val(),
      telefonrueckruf: $('#e_id').val()                     
    },
    method: 'post',
    dataType: 'json',
    beforeSend: function() {
      App.loading.open();
    },
    success: function(data) {
      App.loading.close();
      if (data.status == 1) {
        NeuerVersuchReset();
        updateLiveTableVersuche();
        updateLiveTable();
        $("#editNeuerVersuch").dialog('close');
      } else {
        alert(data.statusText);
      }
    }
  });


}


function NeuerVersuchDelete(id) {
  var conf = confirm('Wirklich löschen?');
  if (conf) {
    $.ajax({
      url: 'index.php?module=telefonrueckruf&action=deleteversuch',
      data: {
        id: id
      },
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
        App.loading.open();
      },
      success: function(data) {
        if (data.status == 1) {
          updateLiveTableVersuche();
          updateLiveTable();
        } else {
          alert(data.statusText);
        }
        App.loading.close();
      }
    });
  }

  return false;

}



</script>


