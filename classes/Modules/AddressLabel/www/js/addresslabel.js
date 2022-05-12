$(document).ready(function() {
  $('#e_adresse').focus();

  $(document).on('click', '.address-label-edit', function(e){
    e.preventDefault();

    var labelId = $(this).data('address-label-id');
    AdressetikettenEdit(labelId);
  });

  $(document).on('click', '.address-label-delete', function(e){
    e.preventDefault();

    var labelId = $(this).data('address-label-id');
    AdressetikettenDelete(labelId);
  });

  $("#editAdressetiketten").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    minWidth:650,
    maxHeight:700,
    autoOpen: false,
    buttons: {
      ABBRECHEN: function() {
        AdressetikettenReset();
        $(this).dialog('close');
      },
      SPEICHERN: function() {
        AdressetikettenEditSave();
      }
    }
  });

  $("#editAdressetiketten").dialog({
    close: function( event, ui ) { AdressetikettenReset();}
  });

});


function AdressetikettenReset()
{
  $('#editAdressetiketten').find('#e_id').val('');
  $('#editAdressetiketten').find('#e_adresse').val('');
  $('#editAdressetiketten').find('#e_etikett').val('');
  $('#editAdressetiketten').find('#e_verwenden_als').val('');  
}

function AdressetikettenEditSave() {
	$.ajax({
    url: 'index.php?module=adressabhaengigesetikett&action=save',
    data: {
      //Alle Felder die fürs editieren vorhanden sind
      id: $('#e_id').val(),
      adresse: $('#e_adresse').val(),
      etikett: $('#e_etikett').val(),
      verwenden_als: $('#e_verwenden_als').val()                      
    },
    method: 'post',
    dataType: 'json',
    beforeSend: function() {
      App.loading.open();
    },
    success: function(data) {
     	App.loading.close();
      if (data.status == 1) {
        AdressetikettenReset();
        updateLiveTable();
        $("#editAdressetiketten").dialog('close');
      } else {
        alert(data.statusText);
      }
    }
  });
}

function AdressetikettenEdit(id) {
  if(id > 0)
  { 
    $.ajax({
      url: 'index.php?module=adressabhaengigesetikett&action=edit&cmd=get',
      data: {
        id: id
      },
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
      	App.loading.open();
      },
      success: function(data) {
        $('#editAdressetiketten').find('#e_id').val(data.id);
        $('#editAdressetiketten').find('#e_adresse').val(data.adresse);
        $('#editAdressetiketten').find('#e_etikett').val(data.etikett);
        $('#editAdressetiketten').find('#e_verwenden_als').val(data.verwenden_als);
                        
        App.loading.close();
        $("#editAdressetiketten").dialog('open');
      }
    });
  } else {
    AdressetikettenReset(); 
    $("#editAdressetiketten").dialog('open');
  }

}

function updateLiveTable(i) {
  var oTableL = $('#adressetiketten_list').dataTable();
  var tmp = $('.dataTables_filter input[type=search]').val();
  oTableL.fnFilter('%');
  //oTableL.fnFilter('');
  oTableL.fnFilter(tmp);   
}

function AdressetikettenDelete(id) {
  var conf = confirm('Wirklich löschen?');
  if (conf) {
    $.ajax({
      url: 'index.php?module=adressabhaengigesetikett&action=delete',
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