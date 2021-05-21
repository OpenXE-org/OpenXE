<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
<form method="post">
  [MESSAGE]
</form>

  <div class="filter-box filter-usersave">
    <div class="filter-block filter-inline">
      <div class="filter-title">{|Filter|}</div>
      <ul class="filter-list">
        <li class="filter-item">
          <label for="inaktiv" class="switch">
            <input type="checkbox" name="inaktiv" id="inaktiv">
            <span class="slider round"></span>
          </label>
          <label for="inaktiv">{|auch inaktive|}</label>
        </li>
      </ul>
    </div>
  </div>

  [TAB1]
  
  [TAB1NEXT]
</div>

<!-- tab view schließen -->
</div>

<div id="editBundesstaaten" style="display:none;" title="Bearbeiten">
  <div class="row">
    <div class="row-height">
      <div class="col-xs-12 col-md-10 col-md-height">
        <div class="inside inside-full-height">
          <form method="post">
            <input type="hidden" id="e_id">
            <fieldset>
              <legend>{|Bundesstaaten|}</legend>
              <table>
                <tr>
                  <td width="150">{|Land|}:</td><td><select name="e_land" id="e_land">
                                                      [LAENDER]
                                                    </select>
                                              </td>
                </tr>
                <tr>
                  <td>{|ISO-Code Bundesstaat|}:</td><td><input type="text" name="e_iso" id="e_iso" size="5"><i>&nbsp;2-stellig</i></td>
                </tr>
                <tr>
                  <td>{|Bundesstaat|}:</td><td><input type="text" name="e_bundesstaat" id="e_bundesstaat" size="40"></td>
                </tr>
                <tr>
                  <td>{|Aktiv|}:</td><td><input type="checkbox" name="e_aktiv" id="e_aktiv"></td>
                </tr>
              </table>
            </fieldset>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>



<script type="text/javascript">

$(document).ready(function() {
  $('#e_name').focus();

  $("#editBundesstaaten").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    minWidth:650,
    maxHeight:700,
    autoOpen: false,
    buttons: {
      ABBRECHEN: function() {
        bundesstaatenReset();
        $(this).dialog('close');
      },
      SPEICHERN: function() {
        BundesstaatenEditSave();
      }
    }
  });

    $("#editBundesstaaten").dialog({

  close: function( event, ui ) { bundesstaatenReset();}
});

});


function bundesstaatenReset()
{
  $('#editBundesstaaten').find('#e_id').val('');
  $('#editBundesstaaten').find('#e_land').val('');
  $('#editBundesstaaten').find('#e_iso').val('');
  $('#editBundesstaaten').find('#e_bundesstaat').val('');
  $('#editBundesstaaten').find('#e_aktiv').prop("checked",true);  
}

function BundesstaatenEditSave() {
	$.ajax({
    url: 'index.php?module=bundesstaaten&action=save',
    data: {
      //Alle Felder die fürs editieren vorhanden sind
      id: $('#e_id').val(),
      land: $('#e_land').val(),
      iso: $('#e_iso').val(),
      bundesstaat: $('#e_bundesstaat').val(),
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
        bundesstaatenReset();
        updateLiveTable();
        $("#editBundesstaaten").dialog('close');
      } else {
        alert(data.statusText);
      }
    }
  });


}

function BundesstaatenEdit(id) {
  if(id > 0)
  { 
    $.ajax({
      url: 'index.php?module=bundesstaaten&action=edit&cmd=get',
      data: {
        id: id
      },
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
      	App.loading.open();
      },
      success: function(data) {
        $('#editBundesstaaten').find('#e_id').val(data.id);
        $('#editBundesstaaten').find('#e_land').val(data.land);
        $('#editBundesstaaten').find('#e_iso').val(data.iso);
        $('#editBundesstaaten').find('#e_bundesstaat').val(data.bundesstaat);
        $('#editBundesstaaten').find('#e_aktiv').prop("checked", data.aktiv==1?true:false);
                
        App.loading.close();
        $("#editBundesstaaten").dialog('open');
      }
    });
  } else {
    bundesstaatenReset(); 
    $("#editBundesstaaten").dialog('open');
  }

}

function updateLiveTable(i) {
  var oTableL = $('#bundesstaaten_list').dataTable();
  var tmp = $('.dataTables_filter input[type=search]').val();
  oTableL.fnFilter('%');
  //oTableL.fnFilter('');
  oTableL.fnFilter(tmp);   
}

function BundesstaatenDelete(id) {
  var conf = confirm('Wirklich löschen?');
  if (conf) {
    $.ajax({
      url: 'index.php?module=bundesstaaten&action=delete',
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
          window.location.replace("index.php?module=bundesstaaten&action=list");
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


