<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
  [MESSAGE]
  [MESSAGE2]
  
  [TAB1]
  
  [TAB1NEXT]
</div>

<!-- tab view schließen -->
</div>

<div id="editKassierer" style="display:none;" title="Bearbeiten">
  <div class="row">
    <div class="row-height">
      <div class="col-xs-12 col-md-10 col-md-height">
        <div class="inside inside-full-height">
          <form method="post">
            <input type="hidden" id="e_id">
            <fieldset>
              <legend>{|Kassierer|}</legend>
              <table>
                <tr>
                  <td width="150">{|Mitarbeiter|}:</td>
                  <td><input type="text" name="emitarbeiter" id="emitarbeiter" size="40"></td>
                </tr>
                <tr>
                  <td>{|Kasse / Filliale / Projekt|}:</td>
                  <td><input type="text" name="ekasse" id="ekasse" size="40"></td>
                </tr>
                <tr>
                  <td>{|Kassierernummer|}:</td>
                  <td><input type="text" name="ekassierernr" id="ekassierernr" size="40"></td>
                </tr>
                <tr>
                  <td>{|Inaktiv|}:</td>
                  <td><input type="checkbox" name="einaktiv" id="einaktiv"></td>
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
  $('#emitarbeiter').focus();

  $("#editKassierer").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    minWidth:600,
    maxWidth:600,
    maxHeight:700,
    autoOpen: false,
    buttons: {
      ABBRECHEN: function() {
        KassiererReset();
        $(this).dialog('close');
      },
      SPEICHERN: function() {
        KassiererEditSave();
      }
    }
  });

    $("#editKassierer").dialog({

  close: function( event, ui ) { KassiererReset();}
});

});


function KassiererReset()
{
  $('#editKassierer').find('#e_id').val('');
  $('#editKassierer').find('#emitarbeiter').val('');
  $('#editKassierer').find('#ekasse').val('');
  $('#editKassierer').find('#ekassierernr').val('');
  $('#editKassierer').find('#einaktiv').prop("checked",false);  
}

function KassiererEditSave() {
	$.ajax({
    url: 'index.php?module=pos_kassierer&action=edit&cmd=save',
    data: {
      //Alle Felder die fürs editieren vorhanden sind
      id: $('#e_id').val(),
      mitarbeiter: $('#emitarbeiter').val(),
      kasse: $('#ekasse').val(),
      kassierernr: $('#ekassierernr').val(),
      inaktiv: $('#einaktiv').prop("checked")?1:0,
                      
    },
    method: 'post',
    dataType: 'json',
    beforeSend: function() {
      App.loading.open();
    },
    success: function(data) {
     	App.loading.close();
      if (data.status == 1) {
        KassiererReset();
        updateLiveTable();
        $("#editKassierer").dialog('close');
        window.location = "index.php?module=pos_kassierer&action=list";
      } else {
        alert(data.statusText);
      }
    }
  });


}

function KassiererEdit(id) {
  if(id > 0)
  { 
    $.ajax({
      url: 'index.php?module=pos_kassierer&action=edit&cmd=get',
      data: {
        id: id
      },
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
      	App.loading.open();
      },
      success: function(data) {
        $('#editKassierer').find('#e_id').val(data.id);
        $('#editKassierer').find('#emitarbeiter').val(data.mitarbeiter);
        $('#editKassierer').find('#ekasse').val(data.kasse);
        $('#editKassierer').find('#ekassierernr').val(data.kassierernr);
        $('#editKassierer').find('#einaktiv').prop("checked", data.inaktiv==1?true:false);
                
        App.loading.close();
        $("#editKassierer").dialog('open');
      }
    });
  } else {
    KassiererReset(); 
    $("#editKassierer").dialog('open');
  }

}

function updateLiveTable(i) {
  var oTableL = $('#pos_kassierer_list').dataTable();
  var tmp = $('.dataTables_filter input[type=search]').val();
  oTableL.fnFilter('%');
  //oTableL.fnFilter('');
  oTableL.fnFilter(tmp);   
}

function KassiererDelete(id) {
  var conf = confirm('Wirklich löschen?');
  if (conf) {
    $.ajax({
      url: 'index.php?module=pos_kassierer&action=delete',
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