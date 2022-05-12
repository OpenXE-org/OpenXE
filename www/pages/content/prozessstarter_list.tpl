<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1"><!--[TABTEXT]--></a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]
<div class="row">
  <div class="row-height">
    <div class="col-xs-12 col-sm-10 col-sm-height">
      <div class="inside-full-height">


          <div class="filter-box filter-usersave">
            <div class="filter-block filter-inline">
              <div class="filter-title">{|Filter|}</div>
              <ul class="filter-list">
                <li class="filter-item">
                  <label for="nuraktiv" class="switch">
                    <input type="checkbox" id="nuraktiv" title="nur aktive">
                    <span class="slider round"></span>
                  </label>
                  <label for="nuraktiv">{|nur aktive|}</label>
                </li>
              </ul>
            </div>
          </div>

          [TAB1]

      </div>
    </div>
    <div class="col-xs-12 col-sm-2 col-sm-height">
      <div class="inside-full-height">
        <fieldset style="height:100%;">
        <legend>{|Aktion|}</legend>
        <form method="POST">
        [CRONJOBBUTTON]
        </form>
        </fieldset>
      </div>
    </div>
  </div>
</div>
[TAB1NEXT]
</div>

<!-- tab view schließen -->
</div>






<div id="editProzessstarter" style="display:none;" title="Bearbeiten">
<form method="post">
  <input type="hidden" id="e_id" value="0">
  <fieldset>
    <legend>{|Prozess|}</legend>
    
    <table width="100%">
      <tr>
        <td>{|Bezeichnung|}:</td>
        <td><input type="text" name="bezeichnung" id="e_bezeichnung" size="40" rule="notempty" msg="Pflichfeld!" tabindex="2"></td>
      </tr>
      <tr>
        <td>{|Art|}:</td>
        <td><select name="art" id="e_art">
              <option value="uhrzeit">{|Uhrzeit|}</option>
              <option value="periodisch">{|Periodisch|}</option>
            </select></td>
      </tr>
      <tr>
        <td>{|Wochentag|}:</td>
        <td><select name="art_filter" id="e_art_filter">
              <option value="">{|Jeden Tag|}</option>
              <option value="1">{|Montag|}</option>
              <option value="2">{|Dienstag|}</option>
              <option value="3">{|Mittwoch|}</option>
              <option value="4">{|Donnerstag|}</option>
              <option value="5">{|Freitag|}</option>
              <option value="6">{|Samstag|}</option>
              <option value="7">{|Sonntag|}</option>
            </select></td>
      </tr>
      <tr>
        <td>{|Startzeit|}:</td>
        <td><input type="text" name="startzeit" id="e_startzeit" size="40"></td>
        <td>
      </tr>
      <tr>
        <td width="130">{|Letzte Ausf&uuml;hrung|}:</td>
        <td><input type="text" name="letzteausfuerhung" id="e_letzteausfuerhung" size="40"></td>
        <td>
      </tr>
      <tr id="trperiode">
        <td>{|Periode|}:</td>
        <td><input type="text" name="periode" id="e_periode" size="40"> {|(in Minuten)|}</td>
        <td>
      </tr>
      <tr>
        <td>{|Typ|}:</td>
        <td><select name="typ" id="e_typ">
              <option value="cronjob">{|Cronjob|}</option>
              <option value="url">{|URL|}</option>
            </select></td>
      </tr>
      <tr>
        <td>{|Parameter|}:</td>
        <td><input type="text" name="parameter" id="e_parameter" size="40" tabindex="2" rule="notempty" msg="Pflichfeld!"></td>
      </tr>
      <tr>
        <td>{|Aktiv|}:</td>
        <td><input type="checkbox" name="aktiv" id="e_aktiv" value="1"></td>
      </tr>
      <!--<tr><td>{|Mutex|}:</td><td><input type="checkbox" name="mutex" value="1"></td></tr>
      <tr><td>{|Mutex-Counter|}:</td><td><input type="text" size="10" name="mutexcounter" value=""></td></tr>-->
      [MUTEXTBUTTON]
    </table>

  </fieldset>
  
</form>
</div>



<script type="text/javascript">

$(document).ready(function() {
  
  art = document.getElementById('e_art');
  trperiode = document.getElementById('trperiode');
  
  if(art){
    // Hide the target field if priority isn't critical
    if (art.options[art.selectedIndex].value =='uhrzeit') {
      trperiode.style.display='none';
    }
    if (art.options[art.selectedIndex].value =='periodisch') {
      trperiode.style.display='';
    }

    art.onchange=function() {
      if (art.options[art.selectedIndex].value == 'uhrzeit') {             
        trperiode.style.display='none';
      } else if(art.options[art.selectedIndex].value == 'periodisch') {
        trperiode.style.display='';
      }
    }
  }


  $('#e_bezeichnung').focus();

  $("#editProzessstarter").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    minWidth:600,
    maxHeight:700,
    autoOpen: false,
    buttons: {
      ABBRECHEN: function() {
        ProzessstarterReset();
        $(this).dialog('close');
      },
      'SPEICHERN': function() {
        ProzessstarterEditSave();
      }
    }
  });

  $("#editProzessstarter").dialog({
    close: function( event, ui ) { ProzessstarterReset();}
  });

});


function resetDialog(nr)
{
  //if(!confirm("Soll der Eitnrag wirklich zurückgesetzt werden?")) return false;
  $.ajax({
    url: 'index.php?module=prozessstarter&action=reset&id='+nr,
    data: {},
    method: 'post',
    dataType: 'json',
    beforeSend: function() {
    },
    success: function(data) {
      updateLiveTable();
    }
  });
}


function ProzessstarterReset()
{
  $('#editProzessstarter').find('#e_id').val('');
  $('#editProzessstarter').find('#e_bezeichnung').val('');
  $('#editProzessstarter').find('#e_art').val('uhrzeit');
  $('#editProzessstarter').find('#e_art_filter').val('');
  $('#editProzessstarter').find('#e_startzeit').val('0000-00-00 00:00:00');
  $('#editProzessstarter').find('#e_letzteausfuerhung').val('0000-00-00 00:00:00');
  $('#editProzessstarter').find('#e_periode').val('1440');
  $('#editProzessstarter').find('#e_typ').val('cronjob');
  $('#editProzessstarter').find('#e_parameter').val('');
  $('#editProzessstarter').find('#e_aktiv').prop("checked",true);


  art = document.getElementById('e_art');
  trperiode = document.getElementById('trperiode');
  if(art){
    // Hide the target field if priority isn't critical
    if (art.options[art.selectedIndex].value =='uhrzeit') {
      trperiode.style.display='none';
    }
    if (art.options[art.selectedIndex].value =='periodisch') {
      trperiode.style.display='';
    }

  }

}

function ProzessstarterEditSave() {
  $.ajax({
    url: 'index.php?module=prozessstarter&action=edit&cmd=save',
    data: {
      //Alle Felder die fürs editieren vorhanden sind
      id: $('#e_id').val(),
      bezeichnung: $('#e_bezeichnung').val(),
      art: $('#e_art').val(),
      art_filter: $('#e_art_filter').val(),
      startzeit: $('#e_startzeit').val(),
      letzteausfuerhung: $('#e_letzteausfuerhung').val(),
      periode: $('#e_periode').val(),
      typ: $('#e_typ').val(),
      parameter: $('#e_parameter').val(),
      aktiv: $('#e_aktiv').prop("checked")?1:0,
                      
    },
    method: 'post',
    dataType: 'json',
    beforeSend: function() {
      App.loading.open();
    },
    success: function(data) {
      App.loading.close();
      if (data.status == 1) {
        ProzessstarterReset();
        updateLiveTable();
        $("#editProzessstarter").dialog('close');
      } else {
        alert(data.statusText);
      }
    }
  });


}

function ProzessstarterEdit(id) {
  if($('#e_art').val() == 'uhrzeit'){
    $('#trperiode').hide();
  }else{
    $('#trperiode').show();
  }
  if(id > 0)
  { 
    $.ajax({
      url: 'index.php?module=prozessstarter&action=edit&cmd=get',
      data: {
        id: id
      },
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
        App.loading.open();
      },
      success: function(data) {
        $('#editProzessstarter').find('#e_id').val(data.id);
        $('#editProzessstarter').find('#e_bezeichnung').val(data.bezeichnung);
        $('#editProzessstarter').find('#e_art').val(data.art);
        $('#editProzessstarter').find('#e_art_filter').val(data.art_filter);
        $('#editProzessstarter').find('#e_startzeit').val(data.startzeit);
        $('#editProzessstarter').find('#e_letzteausfuerhung').val(data.letzteausfuerhung);
        $('#editProzessstarter').find('#e_periode').val(data.periode);
        $('#editProzessstarter').find('#e_typ').val(data.typ);
        $('#editProzessstarter').find('#e_parameter').val(data.parameter);
        $('#editProzessstarter').find('#e_aktiv').prop("checked", data.aktiv==1?true:false);


        art = document.getElementById('e_art');
        trperiode = document.getElementById('trperiode');
        
        if(art){
          // Hide the target field if priority isn't critical
          if (art.options[art.selectedIndex].value =='uhrzeit') {
            trperiode.style.display='none';
          }
          if (art.options[art.selectedIndex].value =='periodisch') {
            trperiode.style.display='';
          }

          art.onchange=function() {
            if (art.options[art.selectedIndex].value == 'uhrzeit') {             
              trperiode.style.display='none';
            } else if(art.options[art.selectedIndex].value == 'periodisch') {
              trperiode.style.display='';
            }
          }
        }


                
        App.loading.close();
        $("#editProzessstarter").dialog('open');
      }
    });
  } else {
    ProzessstarterReset(); 
    $("#editProzessstarter").dialog('open');
  }

}

function updateLiveTable(i) {
  var oTableL = $('#prozessstarterlist').dataTable();
  var tmp = $('.dataTables_filter input[type=search]').val();
  oTableL.fnFilter('%');
  //oTableL.fnFilter('');
  oTableL.fnFilter(tmp);   
}

function ProzessstarterDelete(id) {
  var conf = confirm('Wirklich löschen?');
  if (conf) {
    $.ajax({
      url: 'index.php?module=prozessstarter&action=delete',
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




</script>

