$(document).ready(function() {
  $('#e_label').focus();

  $(document).on('click', '.file-link-edit', function(e){
    e.preventDefault();

    var labelId = $(this).data('file-link-id');
    FileLinkEdit(labelId);
  });

  $(document).on('click', '.file-link-delete', function(e){
    e.preventDefault();

    var labelId = $(this).data('file-link-id');
    FileLinkDelete(labelId);
  });

  $("#editFileLink").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    minWidth:630,
    maxHeight:700,
    autoOpen: false,
    buttons: {
      ABBRECHEN: function() {
        FileLinkReset();
        $(this).dialog('close');
      },
      SPEICHERN: function() {
        FileLinkEditSave();
      }
    }
  });

  $("#editFileLink").dialog({
    close: function( event, ui ) { FileLinkReset();}
  });

});


function FileLinkReset()
{
  $('#editFileLink').find('#e_id').val('');
  $('#editFileLink').find('#e_label').val('');
  $('#editFileLink').find('#e_file_link').val('');
  $('#editFileLink').find('#e_internal_note').val('');
}

function FileLinkEditSave() {
	$.ajax({
    url: 'index.php?module=filelink&action=save',
    data: {
      //Alle Felder die fürs editieren vorhanden sind
      id: $('#e_id').val(),
      article_id: $('#e_article_id').val(),
      label: $('#e_label').val(),
      file_link: $('#e_file_link').val(),
      internal_note: $('#e_internal_note').val()                      
    },
    method: 'post',
    dataType: 'json',
    beforeSend: function() {
      App.loading.open();
    },
    success: function(data) {
     	App.loading.close();
      if (data.status == 1) {
        FileLinkReset();
        updateLiveTable();
        $("#editFileLink").dialog('close');
      } else {
        alert(data.statusText);
      }
    }
  });
}

function FileLinkEdit(id) {
  if(id > 0)
  { 
    $.ajax({
      url: 'index.php?module=filelink&action=edit&cmd=get',
      data: {
        id: id
      },
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
      	App.loading.open();
      },
      success: function(data) {
        $('#editFileLink').find('#e_id').val(data.id);
        $('#editFileLink').find('#e_article_id').val(data.article_id);
        $('#editFileLink').find('#e_label').val(data.label);
        $('#editFileLink').find('#e_file_link').val(data.file_link);
        $('#editFileLink').find('#e_internal_note').val(data.internal_note);
                        
        App.loading.close();
        $("#editFileLink").dialog('open');
      }
    });
  } else {
    FileLinkReset(); 
    $("#editFileLink").dialog('open');
  }

}

function updateLiveTable(i) {
  var oTableL = $('#filelink_list').dataTable();
  var tmp = $('.dataTables_filter input[type=search]').val();
  oTableL.fnFilter('%');
  //oTableL.fnFilter('');
  oTableL.fnFilter(tmp);   
}

function FileLinkDelete(id) {
  var conf = confirm('Wirklich löschen?');
  if (conf) {
    $.ajax({
      url: 'index.php?module=filelink&action=delete',
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