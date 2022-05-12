$(document).ready(function() {
  $('#e_article').focus();

  $(document).on('click', '.property-translation-edit', function(e){
    e.preventDefault();

    var labelId = $(this).data('property-translation-id');
    editPropertyTranslation(labelId);
  });

  $(document).on('click', '.property-translation-delete', function(e){
    e.preventDefault();

    var labelId = $(this).data('property-translation-id');
    deletePropertyTranslation(labelId);
  });

  $("#editPropertyTranslation").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    minWidth:550,
    maxHeight:700,
    autoOpen: false,
    buttons: {
      ABBRECHEN: function() {
        resetPropertyTranslation();
        $(this).dialog('close');
      },
      SPEICHERN: function() {
        editSavePropertyTranslation();
      }
    }
  });

  $("#editPropertyTranslation").dialog({
    close: function( event, ui ) { resetPropertyTranslation();}
  });


  languageFrom = document.getElementById('e_languageFrom');
  propertyde = document.getElementById('propertyde');
  propertyelse = document.getElementById('propertyelse');
  propertyvaluede = document.getElementById('propertyvaluede');
  propertyvalueelse = document.getElementById('propertyvalueelse');
  if(languageFrom){

    if(typeof languageFrom.options[languageFrom.selectedIndex] != 'undefined' && languageFrom.options[languageFrom.selectedIndex].value =='DE'){
      propertyde.style.display='';
      propertyelse.style.display='none';
      propertyvaluede.style.display='';
      propertyvalueelse.style.display='none';
    }else{
      propertyde.style.display='none';
      propertyelse.style.display='';
      propertyvaluede.style.display='none';
      propertyvalueelse.style.display='';
    }

    languageFrom.onchange=function(){
      if(typeof languageFrom.options[languageFrom.selectedIndex] != 'undefined' && languageFrom.options[languageFrom.selectedIndex].value == 'DE'){             
        propertyde.style.display='';
        propertyelse.style.display='none';
        propertyvaluede.style.display='';
        propertyvalueelse.style.display='none';
      }else{
        propertyde.style.display='none';
        propertyelse.style.display='';
        propertyvaluede.style.display='none';
        propertyvalueelse.style.display='';
      }
    }
  }


});


function resetPropertyTranslation()
{
  $('#editPropertyTranslation').find('#e_id').val('');
  $('#editPropertyTranslation').find('#e_article').val('');
  $('#editPropertyTranslation').find('#e_languageFrom').val('');
  $('#editPropertyTranslation').find('#e_propertyFrom').val('');
  $('#editPropertyTranslation').find('#e_propertyValueFrom').val('');
  $('#editPropertyTranslation').find('#e_languageTo').val('');
  $('#editPropertyTranslation').find('#e_propertyTo').val('');
  $('#editPropertyTranslation').find('#e_propertyValueTo').val('');
  $('#editPropertyTranslation').find('#e_shop').val('');
  $('#editUebersetzung').find('#e_propertyFromElse');
  $('#editUebersetzung').find('#e_propertyValueFromElse');

  var languageFrom = document.getElementById('e_languageFrom');
  languageFrom.selectedIndex = 0;

  var languageTo = document.getElementById('e_languageTo');
  languageTo.selectedIndex = 0;

  propertyde = document.getElementById('propertyde');
  propertyelse = document.getElementById('propertyelse');
  propertyvaluede = document.getElementById('propertyvaluede');
  propertyvalueelse = document.getElementById('propertyvalueelse');
  if(languageFrom){
    // Hide the target field if priority isn't critical
    if(languageFrom.options[languageFrom.selectedIndex].value =='DE'){
      propertyde.style.display='';
      propertyelse.style.display='none';
      propertyvaluede.style.display='';
      propertyvalueelse.style.display='none';
    }else{
      propertyde.style.display='none';
      propertyelse.style.display='';
      propertyvaluede.style.display='none';
      propertyvalueelse.style.display='';
    }

  }


}

function editSavePropertyTranslation() {
  var propertyfromfield = '';
  var propertyvaluefromfield = '';
  if($('#e_languageFrom').val() == 'DE'){
    propertyfromfield = $('#e_propertyFrom').val();
    propertyvaluefromfield = $('#e_propertyValueFrom').val();
  }else{
    propertyfromfield = $('#e_propertyFromElse').val();
    propertyvaluefromfield = $('#e_propertyValueFromElse').val();
  }


	$.ajax({
    url: 'index.php?module=propertytranslation&action=save',
    data: {
      //Alle Felder die fürs editieren vorhanden sind
      id: $('#e_id').val(),
      article: $('#e_article').val(),
      languageFrom: $('#e_languageFrom').val(),
      propertyFrom: propertyfromfield,
      propertyValueFrom: propertyvaluefromfield,
      languageTo: $('#e_languageTo').val(),
      propertyTo: $('#e_propertyTo').val(),
      propertyValueTo: $('#e_propertyValueTo').val(),
      shop: $('#e_shop').val()                      
    },
    method: 'post',
    dataType: 'json',
    beforeSend: function() {
      App.loading.open();
    },
    success: function(data) {
     	App.loading.close();
      if (data.status == 1) {
        resetPropertyTranslation();
        updateLiveTable();
        $("#editPropertyTranslation").dialog('close');
      } else {
        alert(data.statusText);
      }
    }
  });
}

function editPropertyTranslation(id) {
  if(id > 0)
  { 
    $.ajax({
      url: 'index.php?module=propertytranslation&action=edit&cmd=get',
      data: {
        id: id
      },
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
      	App.loading.open();
      },
      success: function(data) {
        $('#editPropertyTranslation').find('#e_id').val(data.id);
        $('#editPropertyTranslation').find('#e_article').val(data.article);
        $('#editPropertyTranslation').find('#e_languageFrom').val(data.language_from);

        if(data.language_from == 'DE'){
          $('#editPropertyTranslation').find('#e_propertyFrom').val(data.property_from);
          $('#editPropertyTranslation').find('#e_propertyValueFrom').val(data.property_value_from);
        }else{
          $('#editPropertyTranslation').find('#e_propertyFromElse').val(data.property_from);
          $('#editPropertyTranslation').find('#e_propertyValueFromElse').val(data.property_value_from);
        }

        $('#editPropertyTranslation').find('#e_propertyFrom').val(data.property_from);
        $('#editPropertyTranslation').find('#e_propertyValueFrom').val(data.property_value_from);
        $('#editPropertyTranslation').find('#e_languageTo').val(data.language_to);
        $('#editPropertyTranslation').find('#e_propertyTo').val(data.property_to);
        $('#editPropertyTranslation').find('#e_propertyValueTo').val(data.property_value_to);
        $('#editPropertyTranslation').find('#e_shop').val(data.shop);       


        languageFrom = document.getElementById('e_languageFrom');
        propertyde = document.getElementById('propertyde');
        propertyelse = document.getElementById('propertyelse');
        propertyvaluede = document.getElementById('propertyvaluede');
        propertyvalueelse = document.getElementById('propertyvalueelse');
        if(languageFrom){
          // Hide the target field if priority isn't critical
          if(typeof languageFrom.options[languageFrom.selectedIndex] != 'undefined' && languageFrom.options[languageFrom.selectedIndex].value =='DE'){
            propertyde.style.display='';
            propertyelse.style.display='none';
            propertyvaluede.style.display='';
            propertyvalueelse.style.display='none';
          }else{
            propertyde.style.display='none';
            propertyelse.style.display='';
            propertyvaluede.style.display='none';
            propertyvalueelse.style.display='';
          }
        }



        App.loading.close();
        $("#editPropertyTranslation").dialog('open');
      }
    });
  } else {
    resetPropertyTranslation(); 
    $("#editPropertyTranslation").dialog('open');
  }
}

function updateLiveTable(i) {
  var oTableL = $('#propertytranslation_list').dataTable();
  var tmp = $('.dataTables_filter input[type=search]').val();
  oTableL.fnFilter('%');
  //oTableL.fnFilter('');
  oTableL.fnFilter(tmp);   
}

function deletePropertyTranslation(id) {
  var conf = confirm('Wirklich löschen?');
  if (conf) {
    $.ajax({
      url: 'index.php?module=propertytranslation&action=delete',
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