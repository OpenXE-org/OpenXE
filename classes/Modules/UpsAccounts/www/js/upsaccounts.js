$(document).ready(function() {
  $('#e_adresse').focus();


  $(document).on('click', '.ups-accounts-edit', function(e){
    e.preventDefault();

    var labelId = $(this).data('ups-accounts-id');
    UpsEdit(labelId);
  });

  $(document).on('click', '.ups-accounts-delete', function(e){
    e.preventDefault();

    var labelId = $(this).data('ups-accounts-id');
    UpsDelete(labelId);
  });



  $("#editUps").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    minWidth:650,
    maxHeight:700,
    autoOpen: false,
    buttons: {
      ABBRECHEN: function() {
        UpsReset();
        $(this).dialog('close');
      },
      SPEICHERN: function() {
        UpsEditSave();
      }
    }
  });

  $("#editUps").dialog({
    close: function( event, ui ) { UpsReset();}
  });

});


function UpsReset()
{
  $('#editUps').find('#e_id').val('');
  $('#editUps').find('#e_adresse').val('');
  $('#editUps').find('#e_account_nummer').val('');
  $('#editUps').find('#e_bemerkung').val('');
  $('#editUps').find('#e_auswahl').val('0');
  $('#editUps').find('#e_aktiv').prop("checked", true);
}

function UpsEditSave() {
	$.ajax({
    url: 'index.php?module=ups&action=save',
    data: {
      //Alle Felder die fürs editieren vorhanden sind
      id: $('#e_id').val(),
      adresse: $('#e_adresse').val(),
      account_nummer: $('#e_account_nummer').val(),
      bemerkung: $('#e_bemerkung').val(),
      auswahl: $('#e_auswahl').val(),
      aktiv: $('#e_aktiv').prop("checked")?1:0                      
    },
    method: 'post',
    dataType: 'json',
    beforeSend: function() {
      App.loading.open();
    },
    success: function(data) {
     	App.loading.close();
      if (data.status == 1) {
        UpsReset();
        updateLiveTable();
        $("#editUps").dialog('close');
      } else {
        alert(data.statusText);
      }
    }
  });
}

function UpsEdit(id) {
  if(id > 0)
  { 
    $.ajax({
      url: 'index.php?module=ups&action=edit&cmd=get',
      data: {
        id: id
      },
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
      	App.loading.open();
      },
      success: function(data) {
        $('#editUps').find('#e_id').val(data.id);
        $('#editUps').find('#e_adresse').val(data.adresse);
        $('#editUps').find('#e_account_nummer').val(data.account_nummer);
        $('#editUps').find('#e_bemerkung').val(data.bemerkung);
        $('#editUps').find('#e_auswahl').val(data.auswahl);
        $('#editUps').find('#e_aktiv').prop("checked",data.aktiv==1?true:false);
                        
        App.loading.close();
        $("#editUps").dialog('open');
      }
    });
  } else {
    UpsReset(); 
    $("#editUps").dialog('open');
  }

}

function updateLiveTable(i) {
  var oTableL = $('#ups_list').dataTable();
  var tmp = $('.dataTables_filter input[type=search]').val();
  oTableL.fnFilter('%');
  //oTableL.fnFilter('');
  oTableL.fnFilter(tmp);   
}

function UpsDelete(id) {
  var conf = confirm('Wirklich löschen?');
  if (conf) {
    $.ajax({
      url: 'index.php?module=ups&action=delete',
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