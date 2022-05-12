<div id="tabs">
  <ul>
    <li><a href="#tabs-1"></a></li>
  </ul>

<div id="tabs-1">
  <form method="post" action="" enctype="multipart/form-data">
    <table height="80" width="100%">
      <tr>
        <td width="70%">
          <fieldset>
            <legend>&nbsp;{|Datei importieren|}</legend>
            <center>
              <table width="100%" cellspacing="5">
                <tr>
                  <td>
                    <input type="file" id="datei" name="datei">&nbsp;
                    Beleg auswählen: <select id="art" name="art" onchange="selart();">
                      <option value="">{|- bitte w&auml;hlen -</option>
                      <option value="lieferschein">{|Lieferschein|}</option>
                      <option value="angebot">{|Angebot|}</option>
                      <option value="auftrag">{|Auftrag|}</option>
                      <option value="rechnung">{|Rechnung|}</option>
                      <option value="gutschrift">{|Gutschrift|}</option>
                      <option value="preisanfrage">{|Preisanfrage|}</option>
                      <option value="bestellung">{|Bestellung|}</option>
                      <option value="produktion">{|Produktion|}</option>
                      <option value="proformarechnung">{|Proformarechnung|}</option>
                      <option value="adresse">{|Adresse|}</option>
                      </select>

                      {|als Status|}: <select id="status" name="status">
                      <option value="freigegeben">{|freigegeben|}</option>
                      <!--<option value="angelegt">angelegt (Entwurf)</option>-->
                      <option value="versendet">{|versendet|}</option>
                      </select>
                    <input type="submit" id="datei" value="Datei hochladen">
                  </td>
                </tr>
              </table>
            </center>
          </fieldset>
        </td>
        <td>
          <fieldset>
            <legend>&nbsp;{|Aktionen|}</legend>
            <center>
              <table width="100%" cellspacing="5">
                <tr>
                  <td>
                    <!--input type="button" value="{|Belege jetzt importieren|}" onclick="if(!confirm('Belege jetzt importieren?')) return false; else window.location.href='index.php?module=belegeimport&action=all';">&nbsp;-->
                    <input type="button" value="{|Zwischenspeicher leeren|}" onclick="if(!confirm('Zwischenspeicher wirklich leeren?')) return false; else window.location.href='index.php?module=belegeimport&action=deleteall';">&nbsp;
                  </td>
                </tr>
              </table>
            </center>
          </fieldset>
        </td>

      </tr>
    </table>
  </form>

[MESSAGE]
[TAB1]
</div>

</div>



<div id="editBelegeimport" style="display:none;" title="Bearbeiten">
  <form method="post">
    <input type="hidden" id="e_id">
    <fieldset>
      <legend>{|Bitte w&auml;hlen|}</legend>
      <table>
        <tr>
          <td width="150">{|Adresse|}:</td><td><input type="text" name="e_adresse" id="e_adresse" size="40"></td>
        </tr>
      </table>
    </fieldset>
  </form>
</div>



<script>
function selart()
{
  var wert = $('#art').val();
  var html = '';
  switch(wert)
  {
    case 'lieferschein':
      html = '<option value="freigegeben">freigegeben</option><option value="versendet">versendet</option>';
    break;
    default:
      html = '<option value="angelegt">angelegt</option><option value="freigegeben">freigegeben</option><option value="abgeschlossen">abgeschlossen</option>';
    break;
  }
  $('#status').html(html);
}




$(document).ready(function() {
  $('#e_adresse').focus();

  $("#editBelegeimport").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    minWidth:450,
    maxHeight:700,
    autoOpen: false,
    buttons: {
      ABBRECHEN: function() {
        BelegeimportReset();
        $(this).dialog('close');
      },
      SPEICHERN: function() {
        BelegeimportEditSave();
      }
    }
  });

    $("#editBelegeimport").dialog({

  close: function( event, ui ) { BelegeimportReset();}
});

});


function BelegeimportReset()
{
  $('#editBelegeimport').find('#e_adresse').val('');
}

function BelegeimportEditSave() {
  $.ajax({
    url: 'index.php?module=belegeimport&action=save',
    data: {
      //Alle Felder die fürs editieren vorhanden sind
      id: $('#e_id').val(),
      adresse: $('#e_adresse').val()
                      
    },
    method: 'post',
    dataType: 'json',
    beforeSend: function() {
      App.loading.open();
    },
    success: function(data) {
      App.loading.close();
      if (data.status == 1) {
        BelegeimportReset();
        updateLiveTable();
        $("#editBelegeimport").dialog('close');
      } else {
        alert(data.statusText);
      }
    }
  });


}

function BelegeimportEdit(id) {
  if(id > 0)
  { 
    $.ajax({
      url: 'index.php?module=belegeimport&action=edit&cmd=get',
      data: {
        id: id
      },
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
        App.loading.open();
      },
      success: function(data) {
        $('#editBelegeimport').find('#e_id').val(data.id);
        $('#editBelegeimport').find('#e_adresse').val(data.adresse);
                
        App.loading.close();
        $("#editBelegeimport").dialog('open');
      }
    });
  } else {
    BelegeimportReset(); 
    $("#editBelegeimport").dialog('open');
  }

}

function updateLiveTable(i) {
  var oTableL = $('#belegeimport_list').dataTable();
  var tmp = $('.dataTables_filter input[type=search]').val();
  oTableL.fnFilter('%');
  //oTableL.fnFilter('');
  oTableL.fnFilter(tmp);   
}



</script>


