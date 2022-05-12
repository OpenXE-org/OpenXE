<!-- WAWICORE -->
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


  <div class="row">
  <div class="row-height">
  <div class="col-xs-12 col-md-10 col-md-height">
  <div class="inside-white inside-full-height">
    [TAB1]    
  </div>
  </div>
  <div class="col-xs-12 col-md-2 col-md-height">
  <div class="inside inside-full-height">
    <fieldset>
      <legend>{|Aktionen|}</legend>
      <input type="submit" class="btnBlueNew" name="nachladen" id="nachladen" value="Fehlende Spracheintr&auml;ge nachladen">
    </fieldset>
  </div>
  </div>
  </div>
  </div>  


  
  
  [TAB1NEXT]
</form>
</div>

<!-- tab view schließen -->
</div>

<div id="editArtikelfreifelder" style="display:none;" title="Bearbeiten">
<form method="post">
  <input type="hidden" id="e_id">
  <input type="hidden" id="e_sprache">
  <input type="hidden" id="e_nummer">
  <fieldset>
  	<legend>{|Freifeld|}</legend>
  	<table>
      <tr>
        <td width="150">{|Sprache|}:</td><td id="sprache"></td>
      </tr>
      <tr>
        <td width="150" id="freifeldinhalt"></td><td id="inputtyp"></td>
      </tr>
    </table>
  </fieldset>    
</div>
</form>


<script type="text/javascript">

$(document).ready(function() {
  $('#e_freifeldinhalttext').focus();

  $("#editArtikelfreifelder").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    minWidth:580,
    maxHeight:700,
    autoOpen: false,
    buttons: {
      ABBRECHEN: function() {
        ArtikelfreifelderReset();
        $(this).dialog('close');
      },
      SPEICHERN: function() {
        ArtikelfreifelderEditSave();
      }
    }
  });

  $("#editArtikelfreifelder").dialog({

    close: function( event, ui ) { ArtikelfreifelderReset();}
  });

});


function ArtikelfreifelderReset()
{
  $('#editArtikelfreifelder').find('#e_id').val('');
  document.getElementById("inputtyp").innerHTML = '<input type="text" name="e_freifeldinhalttext" id="e_freifeldinhalttext">';
  $('#editArtikelfreifelder').find('#e_freifeldinhalttext').val('');

   
}

function ArtikelfreifelderEditSave() {
	$.ajax({
    url: 'index.php?module=artikel&action=artikelfreifeldersave',
    data: {
      //Alle Felder die fürs editieren vorhanden sind
      id: $('#e_id').val(),
      freifeldinhalttext: $('#e_freifeldinhalttext').val(),
      sprache: $('#e_sprache').val(),
      nummer: $('#e_nummer').val()                       
    },
    method: 'post',
    dataType: 'json',
    beforeSend: function() {
      App.loading.open();
    },
    success: function(data) {
     	App.loading.close();
      if (data.status == 1) {
        ArtikelfreifelderReset();
        updateLiveTable();
        $("#editArtikelfreifelder").dialog('close');
      } else {
        alert(data.statusText);
      }
    }
  });


}

function ArtikelfreifelderEdit(id) {
  if(id != ""){
    var arr = id.split(":");
    var id = arr[0];
    var sprache = arr[1];
    var nummer = arr[2];

    if(id > 0)
    { 
      $.ajax({
        url: 'index.php?module=artikel&action=artikelfreifelderedit&cmd=get',
        data: {
          id: id,
          sprache: sprache,
          nummer: nummer
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
          App.loading.open();
        },
        success: function(data) {
          if(data.inputtyp == 'mehrzeilig'){
            document.getElementById("inputtyp").innerHTML = '<textarea cols="40" rows="5" name="e_freifeldinhalttext" id="e_freifeldinhalttext"></textarea>';
          }else{
            document.getElementById("inputtyp").innerHTML = '<input type="text" name="e_freifeldinhalttext" id="e_freifeldinhalttext">';
          }

          $('#editArtikelfreifelder').find('#e_id').val(data.id);
          $('#editArtikelfreifelder').find('#e_freifeldinhalttext').val(data.wert);
          $('#editArtikelfreifelder').find('#e_sprache').val(data.sprache);
          $('#editArtikelfreifelder').find('#e_nummer').val(data.nummer);
          document.getElementById("sprache").innerHTML = data.sprache;
          document.getElementById("freifeldinhalt").innerHTML = data.bezeichnung+':';


                          
          App.loading.close();
          $("#editArtikelfreifelder").dialog('open');
        }
      });
    } else {
      ArtikelfreifelderReset(); 
      $("#editArtikelfreifelder").dialog('open');
    }

  }
  

}





function updateLiveTable(i) {
  var oTableL = $('#artikelfreifelder_list').dataTable();
  var tmp = $('.dataTables_filter input[type=search]').val();
  oTableL.fnFilter('%');
  //oTableL.fnFilter('');
  oTableL.fnFilter(tmp);   
}

function ArtikelfreifelderDelete(id) {
  var conf = confirm('Wirklich löschen?');
  if (conf) {
    $.ajax({
      url: 'index.php?module=artikel&action=artikelfreifelderdelete',
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


