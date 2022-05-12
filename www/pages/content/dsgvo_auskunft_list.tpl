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
          <label for="nurloeschauftrag" class="switch">
            <input type="checkbox" name="nurloeschauftrag" id="nurloeschauftrag" title="nur mit loeschauftrag">
            <span class="slider round"></span>
          </label>
          <label for="nurloeschauftrag">{|nur mit L&ouml;schauftrag|}</label>
        </li>
      </ul>
    </div>
  </div>

  [TAB1]
  
  [TAB1NEXT]
</div>

<!-- tab view schließen -->
</div>

<div id="editLoeschauftrag" style="display:none;" title="Bearbeiten">
<form method="post">
  <input type="hidden" id="e_id">
  <fieldset>
  	<legend>{|L&ouml;schauftrag|}</legend>
  	<table>
      <tr>
        <td>{|Grund|}:</td><td><input type="text" name="e_kommentar" id="e_kommentar" size="40"></td>
      </tr>
      <tr>
        <td width="120">{|L&ouml;schauftrag vom|}:</td><td><input type="text" name="e_loeschauftrag_vom" id="e_loeschauftrag_vom"></td>
      </tr>      
    </table>
  </fieldset>    
</div>
</form>


<script type="text/javascript">

$(document).ready(function(){
  
  $("#editLoeschauftrag").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    minWidth:470,
    maxHeight:700,
    autoOpen: false,
    buttons: {
      ABBRECHEN: function(){
        DSGVOReset();
        $(this).dialog('close');
      },
      SPEICHERN: function(){
        DSGVOEditSave();
      }
    }
  });

  $("#editLoeschauftrag").dialog({
    close: function( event, ui ) { DSGVOReset();}
  });

});


function DSGVOReset()
{
  $('#editLoeschauftrag').find('#e_id').val('');
  $('#editLoeschauftrag').find('#e_loeschauftrag_vom').val('');
  $('#editLoeschauftrag').find('#e_kommentar').val('');  
}

function DSGVOEditSave() {
	$.ajax({
    url: 'index.php?module=dsgvo&action=save',
    data: {
      //Alle Felder die fürs editieren vorhanden sind
      id: $('#e_id').val(),
      loeschauftrag_vom: $('#e_loeschauftrag_vom').val(),
      kommentar: $('#e_kommentar').val()                      
    },
    method: 'post',
    dataType: 'json',
    beforeSend: function() {
      App.loading.open();
    },
    success: function(data) {
     	App.loading.close();
      if (data.status == 1) {
        DSGVOReset();
        updateLiveTable();
        $("#editLoeschauftrag").dialog('close');
      } else {
        alert(data.statusText);
      }
    }
  });


}

function DSGVOEdit(id) {
  if(id > 0)
  { 
    $.ajax({
      url: 'index.php?module=dsgvo&action=edit&cmd=get',
      data: {
        id: id
      },
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
        App.loading.open();
      },
      success: function(data) {
        $('#editLoeschauftrag').find('#e_id').val(id);  
        $('#editLoeschauftrag').find('#e_loeschauftrag_vom').val(data.loeschauftrag_vom);
        $('#editLoeschauftrag').find('#e_kommentar').val(data.kommentar);
                
        App.loading.close();
        $("#editLoeschauftrag").dialog('open');
      }
    });
  } else {
    DSGVOReset(); 
    $("#editLoeschauftrag").dialog('open');
  }

}

function updateLiveTable(i) {
  var oTableL = $('#dsgvo_auskunft_list').dataTable();
  var tmp = $('.dataTables_filter input[type=search]').val();
  oTableL.fnFilter('%');
  //oTableL.fnFilter('');
  oTableL.fnFilter(tmp);   
}

</script>


