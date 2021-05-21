$(document).ready(function() {
  $('#e_module').focus();

  $(document).on('click', '.layouttemplate-attachment-edit', function(e){
    e.preventDefault();

    var labelId = $(this).data('layouttemplate-attachment-id');
    LayoutTemplateAttachmentEdit(labelId);
  });

  $(document).on('click', '.layouttemplate-attachment-delete', function(e){
    e.preventDefault();

    var labelId = $(this).data('layouttemplate-attachment-id');
    LayoutTemplateAttachmentDelete(labelId);
  });

  module = document.getElementById('e_module');
  trarticlecategory = document.getElementById('trarticlecategory');
  trgroup = document.getElementById('trgroup');

  if (module) {
    // Hide the target field if priority isn't critical
    if (module.options[module.selectedIndex].value =='artikel') {
      trarticlecategory.style.display='';
      trgroup.style.display='none';
    }else if (module.options[module.selectedIndex].value =='adresse'){
      trarticlecategory.style.display='none';
      trgroup.style.display='';
    }else{
      trarticlecategory.style.display='';
      trgroup.style.display='';
    }

    module.onchange=function() {
      if (module.options[module.selectedIndex].value == 'artikel') {
        trarticlecategory.style.display='';
        trgroup.style.display='none';
      }else if(module.options[module.selectedIndex].value =='adresse'){
        trarticlecategory.style.display='none';
        trgroup.style.display='';
      }else{
        trarticlecategory.style.display='';
        trgroup.style.display='';
      }
    }
  }


  /*module = document.getElementById('e_module');
  moduleparameter = document.getElementById('moduleparameter');
  if(module){
    // Hide the target field if priority isn't critical      
    if(typeof module.options[module.selectedIndex] != 'undefined' && module.options[module.selectedIndex].value =='adresse'){
      moduleparameter.style.display='';
    }else{
      moduleparameter.style.display='none';
    }

    module.onchange=function(){
      if(typeof module.options[module.selectedIndex] != 'undefined' && module.options[module.selectedIndex].value == 'adresse'){             
        moduleparameter.style.display='';
      }else{
        moduleparameter.style.display='none';
      }
    }
  }*/

  $("#editLayoutTemplateAttachment").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    minWidth:650,
    maxHeight:700,
    autoOpen: false,
    buttons: {
      ABBRECHEN: function() {
        LayoutTemplateAttachmentReset();
        $(this).dialog('close');
      },
      SPEICHERN: function() {
        LayoutTemplateAttachmentEditSave();
      }
    }
  });

  $("#editLayoutTemplateAttachment").dialog({
    close: function( event, ui ) { LayoutTemplateAttachmentReset();}
  });

});


function LayoutTemplateAttachmentReset()
{
  $('#editLayoutTemplateAttachment').find('#e_id').val('');
  $('#editLayoutTemplateAttachment').find('#e_module').val('adresse');
  $('#editLayoutTemplateAttachment').find('#e_articlecategory').val('');
  $('#editLayoutTemplateAttachment').find('#e_group').val('');
  $('#editLayoutTemplateAttachment').find('#e_layouttemplate').val('');
  $('#editLayoutTemplateAttachment').find('#e_language').val('');
  $('#editLayoutTemplateAttachment').find('#e_country').val('');
  //$('#editLayoutTemplateAttachment').find('#e_parameter').val('');
  $('#editLayoutTemplateAttachment').find('#e_project').val('');
  $('#editLayoutTemplateAttachment').find('#e_filename').val('');
  $('#editLayoutTemplateAttachment').find('#e_active').prop("checked", true);

  module = document.getElementById('e_module');
  trarticlecategory = document.getElementById('trarticlecategory');
  trgroup = document.getElementById('trgroup');
  if (module.options[module.selectedIndex].value =='artikel') {
    trarticlecategory.style.display='';
    trgroup.style.display='none';
  }else if(module.options[module.selectedIndex].value =='adresse'){
    trarticlecategory.style.display='none';
    trgroup.style.display='';
  }else{
    trarticlecategory.style.display='';
    trgroup.style.display='';
  }

  /*module = document.getElementById('e_module');
  moduleparameter = document.getElementById('moduleparameter');
  if(module){
    // Hide the target field if priority isn't critical      
    if(typeof module.options[module.selectedIndex] != 'undefined' && module.options[module.selectedIndex].value =='adresse'){
      moduleparameter.style.display='';
    }else{
      moduleparameter.style.display='none';
    }
  }*/

}

function LayoutTemplateAttachmentEditSave()
{
  $.ajax({
    url: 'index.php?module=layouttemplateattachment&action=save',
    data: {
      //Alle Felder die fürs editieren vorhanden sind
      id: $('#e_id').val(),
      module: $('#e_module').val(),
      articlecategory: $('#e_articlecategory').val(),
      group: $('#e_group').val(),
      layouttemplate: $('#e_layouttemplate').val(),
      language: $('#e_language').val(),
      country: $('#e_country').val(),
      //parameter: $('#e_parameter').val(),
      project: $('#e_project').val(),
      active: $('#e_active').prop("checked")?1:0,
      filename: $('#e_filename').val()
    },
    method: 'post',
    dataType: 'json',
    beforeSend: function() {
      App.loading.open();
    },
    error : function() {
      alert('Speichern fehlgeschlagen: Fehlende Rechte');
    },
    success: function(data) {
      App.loading.close();
      if(data.status == 1){
        LayoutTemplateAttachmentReset();
        updateLiveTable();
        $("#editLayoutTemplateAttachment").dialog('close');
      }else{
        alert(data.statusText);
      }
    }
  });
}

function LayoutTemplateAttachmentEdit(id)
{
  if(id > 0){
    $.ajax({
      url: 'index.php?module=layouttemplateattachment&action=edit&cmd=get',
      data: {
        id: id
      },
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
        App.loading.open();
      },
      success: function(data) {
        $('#editLayoutTemplateAttachment').find('#e_id').val(data.id);
        $('#editLayoutTemplateAttachment').find('#e_module').val(data.module);
        $('#editLayoutTemplateAttachment').find('#e_articlecategory').val(data.articlecategory);
        $('#editLayoutTemplateAttachment').find('#e_group').val(data.gruppe);
        $('#editLayoutTemplateAttachment').find('#e_layouttemplate').val(data.layouttemplate);
        $('#editLayoutTemplateAttachment').find('#e_language').val(data.language);
        $('#editLayoutTemplateAttachment').find('#e_country').val(data.country);
        //$('#editLayoutTemplateAttachment').find('#e_parameter').val(data.parameter);
        $('#editLayoutTemplateAttachment').find('#e_project').val(data.project);
        $('#editLayoutTemplateAttachment').find('#e_active').prop("checked",data.active==1?true:false);
        $('#editLayoutTemplateAttachment').find('#e_filename').val(data.filename);

        trarticlecategory = document.getElementById('trarticlecategory');
        trgroup = document.getElementById('trgroup');

        if (data.module == 'artikel') {
          trarticlecategory.style.display='';
          trgroup.style.display='none';
        }else if (data.module == 'adresse'){
          trarticlecategory.style.display='none';
          trgroup.style.display='';
        }else{
          trarticlecategory.style.display='';
          trgroup.style.display='';
        }

        /*module = document.getElementById('e_module');
        moduleparameter = document.getElementById('moduleparameter');
        if(module){
          // Hide the target field if priority isn't critical      
          if(typeof module.options[module.selectedIndex] != 'undefined' && module.options[module.selectedIndex].value =='adresse'){
            moduleparameter.style.display='';
          }else{
            moduleparameter.style.display='none';
          }
        }*/

        App.loading.close();
        $("#editLayoutTemplateAttachment").dialog('open');
      }
    });
  }else{
    LayoutTemplateAttachmentReset();
    $("#editLayoutTemplateAttachment").dialog('open');
  }
}

function updateLiveTable(i)
{
  var oTableL = $('#layouttemplateattachment_list').dataTable();
  var tmp = $('.dataTables_filter input[type=search]').val();
  oTableL.fnFilter('%');
  //oTableL.fnFilter('');
  oTableL.fnFilter(tmp);
}

function LayoutTemplateAttachmentDelete(id)
{
  var conf = confirm('Wirklich löschen?');
  if (conf) {
    $.ajax({
      url: 'index.php?module=layouttemplateattachment&action=delete',
      data: {
        id: id
      },
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
        App.loading.open();
      },
      error : function() {
        alert('Löschen fehlgeschlagen: Fehlende Rechte');
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