$(document).ready(function() {
  
    $(document).on('click', '.packagedeliverylist-edit', function(e){
      e.preventDefault();

      var labelId = $(this).data('packagedeliverylist-id');
      PackageDeliveryListEdit(labelId);
    });

    $("#editPackageDeliveryList").dialog({
      modal: true,
      bgiframe: true,
      closeOnEscape:false,
      minWidth:650,
      maxHeight:700,
      autoOpen: false,
      buttons: {
        ABBRECHEN: function() {
          $(this).dialog('close');
        }
      }
    });
  });

function PackageDeliveryListEdit(id)
{
  if(id > 0){
    $("#editPackageDeliveryList").dialog('open');
    oMoreData1packagedeliverylist_pdflist = id;
    updateLiveTable();
  }  
}

function updateLiveTable(i)
{
  var oTableL = $('#packagedeliverylist_pdflist').dataTable();
  var tmp = $('.dataTables_filter input[type=search]').val();
  oTableL.fnFilter('%');
  //oTableL.fnFilter('');
  oTableL.fnFilter(tmp);
}