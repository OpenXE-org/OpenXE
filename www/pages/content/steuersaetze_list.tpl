<!-- gehort zu tabview -->
<div id="tabs">
  <ul>
    <li><a href="#tabs-1">[TABTEXT]</a></li>
  </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab --> 
  <form method="post">
    <div id="tabs-1">
      [MESSAGE]
      [TAB1]
      [TAB1NEXT]
    </div>
<!-- tab view schließen -->

  </form>
</div>

<div id="editSteuersaetze" style="display:none;" title="Bearbeiten">
  <fieldset>
    <legend>{|Steuersatz|}</legend>
    <input type="hidden" id="id" name="id">
    <table>
      <tr>
        <td width="220"><label for="satz">{|Steuersatz als Zahl eintragen|}:</label></td>
        <td><input type="text" id="satz" name="satz"></td>        
      </tr>
      <tr>
        <td><label for="bezeichnung">{|Bezeichnung z.B. "Sonderregel"|}:</label></td>
        <td><input type="text" id="bezeichnung" name="bezeichnung"></td>
      </tr>
      <tr>
        <td><label for="type">{|Typ|}:</label></td>
        <td>
          <select id="type" name="type">
            <option value="">{|manuell|}</option>
            <option value="ermaessigt">{|erm&auml;&szlig;igt|}</option>
            <option value="normal">{|normal|}</option>
          </select>
        </td>
      </tr>
      <tr>
        <td><label for="country_code">{|Land|}:</label></td>
        <td>
          <select id="country_code" name="country_code">
            <option value=""></option>
            [SELCOUNTRYCODE]
          </select>
        </td>
      </tr>
      <tr>
        <td><label for="project">{|Projekt|}:</label></td>
        <td><input type="text" id="project" /></td>
      </tr>
      <tr>
        <td><label for="valid_from">{|g&uuml;ltig von|}:</label></td>
        <td><input type="text" id="valid_from" /></td>
      </tr>
      <tr>
        <td><label for="valid_to">{|g&uuml;ltig bis|}:</label></td>
        <td><input type="text" id="valid_to" /></td>
      </tr>
      <tr>
        <td><label for="aktiv">{|Aktiv|}:</label></td>
        <td><input type="checkbox" id="aktiv" name="aktiv" value="1"></td>
       </tr>
      <tr>
        <td><label for="set_data">{|Als Standardsteuersatz setzen|}:</label></td>
        <td><input type="checkbox" id="set_data" name="set_data" value="1"></td>
       </tr>
    </table>
  </fieldset>
  
  
</div>



<script type="text/javascript">

$(document).ready(function() {
    $('#steuersatz').focus();

    $("#editSteuersaetze").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    minWidth:440,
    autoOpen: false,
    buttons: {
      ABBRECHEN: function() {
        $(this).dialog('close');
      },
      SPEICHERN: function() {
        steuersaetzeEditSave();
      }
    }
  });

});

function steuersaetzeEditSave() {

    $.ajax({
        url: 'index.php?module=steuersaetze&action=save',
        data: {
            //Alle Felder die fürs editieren vorhanden sind
            id: $('#id').val(),
            satz: $('#satz').val(),
            bezeichnung: $('#bezeichnung').val(),
            project: $('#project').val(),
            type: $('#type').val(),
            valid_from: $('#valid_from').val(),
            valid_to: $('#valid_to').val(),
            country_code: $('#country_code').val(),
            aktiv: $('#aktiv').prop("checked")?1:0,
            set_data: $('#set_data').prop("checked")?1:0,
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {
            App.loading.close();
            if (data.status == 1) {
                $('#editSteuersaetze').find('#id').val('');
                $('#editSteuersaetze').find('#satz').val('');
                $('#editSteuersaetze').find('#bezeichnung').val('');
                $('#editSteuersaetze').find('#type').val('');
                $('#editSteuersaetze').find('#project').val('');
                $('#editSteuersaetze').find('#valid_from').val('');
                $('#editSteuersaetze').find('#valid_to').val('');
                $('#editSteuersaetze').find('#country_code').val('');
                $('#editSteuersaetze').find('#aktiv').prop('checked', false);
                $('#editSteuersaetze').find('#set_data').prop('checked', false);
                updateLiveTable();
                $("#editSteuersaetze").dialog('close');
            } else {
                alert(data.statusText);
            }
        }
    });

}

function SteuersaetzeEdit(id) {
  if(id === 0) {
    $('#editSteuersaetze').find('#id').val(0);
    $('#editSteuersaetze').find('#satz').val('');
    $('#editSteuersaetze').find('#bezeichnung').val('');
    $('#editSteuersaetze').find('#type').val('');
    $('#editSteuersaetze').find('#project').val('');
    $('#editSteuersaetze').find('#valid_from').val('');
    $('#editSteuersaetze').find('#valid_to').val('');
    $('#editSteuersaetze').find('#country_code').val('');
    $('#editSteuersaetze').find('#aktiv').prop("checked",true);
    $('#editSteuersaetze').find('#set_data').prop("checked",false);
    $("#editSteuersaetze").dialog('open');
    return;
  }
    $.ajax({
        url: 'index.php?module=steuersaetze&action=edit&cmd=get',
        data: {
            id: id
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {
            $('#editSteuersaetze').find('#id').val(data.id);
            $('#editSteuersaetze').find('#satz').val(data.satz);
            $('#editSteuersaetze').find('#bezeichnung').val(data.bezeichnung);
            $('#editSteuersaetze').find('#type').val(data.type);
            $('#editSteuersaetze').find('#project').val(data.project);
            $('#editSteuersaetze').find('#valid_from').val(data.valid_from);
            $('#editSteuersaetze').find('#valid_to').val(data.valid_to);
            $('#editSteuersaetze').find('#country_code').val(data.country_code);
            $('#editSteuersaetze').find('#aktiv').prop("checked",data.aktiv==1?true:false);
            $('#editSteuersaetze').find('#set_data').prop("checked",data.set_data==1?true:false);
            App.loading.close();
            $("#editSteuersaetze").dialog('open');
        }
    });

}

function updateLiveTable(i) {
    var oTableL = $('#steuersaetze_list').dataTable();
    oTableL.fnFilter('%');
    oTableL.fnFilter('');   
}

function SteuersaetzeDelete(id) {

    var conf = confirm('Wirklich löschen?');
    if (conf) {
        $.ajax({ 
            url: 'index.php?module=steuersaetze&action=delete',
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

