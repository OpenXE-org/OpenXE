$(document).ready(function() {
    $('#maximum_discount_employee').focus();

    $(document).on('click', '.maximumdiscount-edit', function(e){
        e.preventDefault();

        var labelId = $(this).data('maximumdiscount-id');
        MaximumDiscountEdit(labelId);
    });

    $(document).on('click', '.maximumdiscount-delete', function(e){
        e.preventDefault();

        var labelId = $(this).data('maximumdiscount-id');
        MaximumDiscountDelete(labelId);
    });


    $("#editMaximumDiscount").dialog({
        modal: true,
        bgiframe: true,
        closeOnEscape:false,
        minWidth:580,
        maxHeight:700,
        autoOpen: false,
        buttons: {
            ABBRECHEN: function() {
                MaximumDiscountReset();
                $(this).dialog('close');
            },
            SPEICHERN: function() {
                MaximumDiscountEditSave();
            }
        }
    });

    $("#editMaximumDiscount").dialog({
        close: function( event, ui ) { MaximumDiscountReset();}
    });

});


function MaximumDiscountReset()
{
    $('#editMaximumDiscount').find('#maximum_discount_entry_id').val('');
    $('#editMaximumDiscount').find('#maximum_discount_employee').val('');
    $('#editMaximumDiscount').find('#maximum_discount_employee_discount').val('');
}

function MaximumDiscountEditSave() {
    $.ajax({
        url: 'index.php?module=maximumdiscount&action=save',
        data: {
            //Alle Felder die fürs editieren vorhanden sind
            id: $('#maximum_discount_entry_id').val(),
            employee: $('#maximum_discount_employee').val(),
            discount: $('#maximum_discount_employee_discount').val()
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {
            App.loading.close();
            if (data.status == 1) {
                MaximumDiscountReset();
                updateLiveTable();
                $("#editMaximumDiscount").dialog('close');
            } else {
                alert(data.statusText);
            }
        }
    });
}

function MaximumDiscountEdit(id) {
    if(id > 0)
    {
        $.ajax({
            url: 'index.php?module=maximumdiscount&action=edit',
            data: {
                id: id
            },
            method: 'post',
            dataType: 'json',
            beforeSend: function() {
                App.loading.open();
            },
            success: function(data) {
                $('#editMaximumDiscount').find('#maximum_discount_entry_id').val(data.id);
                $('#editMaximumDiscount').find('#maximum_discount_employee').val(data.employee);
                $('#editMaximumDiscount').find('#maximum_discount_employee_discount').val(data.discount);

                App.loading.close();
                $("#editMaximumDiscount").dialog('open');
            }
        });
    } else {
        MaximumDiscountReset();
        $("#editMaximumDiscount").dialog('open');
    }

}

function updateLiveTable(i) {
    var oTableL = $('#maximumdiscount_list').dataTable();
    var tmp = $('.dataTables_filter input[type=search]').val();
    oTableL.fnFilter('%');
    //oTableL.fnFilter('');
    oTableL.fnFilter(tmp);
}

function MaximumDiscountDelete(id) {
    var conf = confirm('Wirklich löschen?');
    if (conf) {
        $.ajax({
            url: 'index.php?module=maximumdiscount&action=delete',
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