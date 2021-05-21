$(document).ready(function() {
  $('#e_article').focus();

  $(document).on('click', '.frameagreement-edit', function(e){
    e.preventDefault();

    var labelId = $(this).data('frameagreement-id');
    FrameagreementEdit(labelId);
  });

  $(document).on('click', '.frameagreement-delete', function(e){
    e.preventDefault();

    var labelId = $(this).data('frameagreement-id');
    FrameagreementDelete(labelId);
  });


  $("#editFrameagreement").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    minWidth:650,
    maxHeight:700,
    autoOpen: false,
    buttons: {
      ABBRECHEN: function() {
        FrameagreementReset();
        $(this).dialog('close');
      },
      SPEICHERN: function() {
        FrameagreementEditSave();
      }
    }
  });

  $("#editFrameagreement").dialog({
    close: function( event, ui ) { FrameagreementReset();}
  });

});


function FrameagreementReset()
{
  $('#editFrameagreement').find('#e_id').val('');
  $('#editFrameagreement').find('#e_article').val('');
  $('#editFrameagreement').find('#e_customer').val('');
  $('#editFrameagreement').find('#e_label').val('');
  $('#editFrameagreement').find('#e_amount').val('');
  $('#editFrameagreement').find('#e_from').val('');
  $('#editFrameagreement').find('#e_to').val('');
}

function FrameagreementEditSave() {
	$.ajax({
    url: 'index.php?module=frameagreement&action=save',
    data: {
      //Alle Felder die fürs editieren vorhanden sind
      id: $('#e_id').val(),
      article: $('#e_article').val(),
      customer: $('#e_customer').val(),
      label: $('#e_label').val(),
      amount: $('#e_amount').val(),
      from: $('#e_from').val(),
      to: $('#e_to').val()
                      
    },
    method: 'post',
    dataType: 'json',
    beforeSend: function() {
      App.loading.open();
    },
    success: function(data) {
     	App.loading.close();
      if (data.status == 1) {
        FrameagreementReset();
        updateLiveTable();
        $("#editFrameagreement").dialog('close');
      } else {
        alert(data.statusText);
      }
    }
  });

}

function FrameagreementEdit(id) {
  if(id > 0)
  { 
    $.ajax({
      url: 'index.php?module=frameagreement&action=edit&cmd=get',
      data: {
        id: id
      },
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
      	App.loading.open();
      },
      success: function(data) {
        $('#editFrameagreement').find('#e_id').val(data.id);
        $('#editFrameagreement').find('#e_article').val(data.article);
        $('#editFrameagreement').find('#e_customer').val(data.customer);
        $('#editFrameagreement').find('#e_label').val(data.label);
        $('#editFrameagreement').find('#e_amount').val(data.frame_agreement_amount);
        $('#editFrameagreement').find('#e_from').val(data.date_from);
        $('#editFrameagreement').find('#e_to').val(data.date_to);
                
        App.loading.close();
        $("#editFrameagreement").dialog('open');
      }
    });
  } else {
    FrameagreementReset(); 
    $("#editFrameagreement").dialog('open');
  }

}

function updateLiveTable(i) {
  var oTableL = $('#frameagreement_articlelist').dataTable();
  var tmp = $('.dataTables_filter input[type=search]').val();
  oTableL.fnFilter('%');
  //oTableL.fnFilter('');
  oTableL.fnFilter(tmp);   
}

function FrameagreementDelete(id) {
  var conf = confirm('Wirklich löschen?');
  if (conf) {
    $.ajax({
      url: 'index.php?module=frameagreement&action=delete',
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
        }
        App.loading.close();
      }
    });
  }

  return false;
}