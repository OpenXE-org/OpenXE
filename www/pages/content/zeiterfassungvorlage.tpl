<!-- gehort zu tabview -->
<div id="tabs">
  <ul>
    <li><a href="#tabs-1"></a></li>
  </ul>
    <!-- ende gehort zu tabview -->

    <!-- erstes tab -->
  <div id="tabs-1">
    [MESSAGE]
    [TAB1]
    [TABNEXT]
  </div>
    <!-- tab view schließen -->
</div>

<div id="editzeiterfassungsvorlage" style="display:none;" title="Bearbeiten">
  <fieldset>
    <legend>{|Zeiterfassung Vorlage|}</legend>
    <input type="hidden" name="vorlageid" id="vorlageid" size="40">
    <table>
      <tr>
        <td>{|Art|}:</td>
        <td><select name="vorlageart" id="vorlageart">
              <option value=""></option>
              [ART]
            </select></td>
      </tr>
      <tr>
        <td width="120">{|Beschreibung|}:</td>
        <td><input type="text" name="vorlage" id="vorlage" size="40"></td>
      </tr>
      <tr>
        <td>{|Details|}:</td>
        <td>
          <textarea name="vorlagedetail" id="vorlagedetail" cols="39" rows="5"></textarea>
        </td>
      </tr>
      <tr>
        <td>{|Projekt|}:</td>
        <td><input type="text" name="vorlageprojekt" id="vorlageprojekt" size="40"></td>
      </tr>
      <tr>
        <td>{|Teilprojekt|}:</td>
        <td><input type="text" name="vorlageteilprojekt" id="vorlageteilprojekt" size="40"></td>
      </tr>
      <tr>
        <td>{|Kunde|}:</td>
        <td><input type="text" name="vorlagekunde" id="vorlagekunde" size="40"></td>
      </tr>
      <tr>
        <td>{|Abrechnen|}:</td>
        <td><input type="checkbox" name="vorlageabrechnen" id="vorlageabrechnen" value="1"></td>
      </tr>
      <tr>
        <td>{|ausblenden|}:</td>
        <td><input type="checkbox" id="ausblenden" name="ausblenden" value="1" /></td>
      </tr>
    </table>
  </fieldset>
</div>

<script type="text/javascript">

	$(document).ready(function() {
		$("#editzeiterfassungsvorlage").dialog({
      modal: true,
			bgiframe: true,
			closeOnEscape:false,
			minWidth:700,
			autoOpen: false,
			buttons: {
				ABBRECHEN: function() {
					ZeiterfassungReset();
				  $("#editzeiterfassungsvorlage").dialog('close');
				},
				SPEICHERN: function() {
          ZeiterfassungsvorlageSave($('#editzeiterfassungsvorlage').find('#vorlageid').val());
				},
			}
		});
		$("#editzeiterfassungsvorlage").dialog({
      close: function( event, ui ) { ZeiterfassungReset();}
    });
	});


	function ZeiterfassungReset(){
	  $('#editzeiterfassungsvorlage').find('#vorlageid').val('');
	  $('#editzeiterfassungsvorlage').find('#vorlageart').val('');
	  $('#editzeiterfassungsvorlage').find('#vorlage').val('');
	  $('#editzeiterfassungsvorlage').find('#vorlagedetail').val('');
	  $('#editzeiterfassungsvorlage').find('#vorlageprojekt').val('');
	  $('#editzeiterfassungsvorlage').find('#vorlageteilprojekt').val('');
	  $('#editzeiterfassungsvorlage').find('#vorlagekunde').val('');
	  $('#editzeiterfassungsvorlage').find('#vorlageabrechnen').prop("checked", false);
	  $('#editzeiterfassungsvorlage').find('#ausblenden').prop("checked", false);
	}

  function ZeiterfassungsvorlageEdit(id) {
   	if(id > 0){
		  $.ajax({
			  url: 'index.php?module=zeiterfassungvorlage&action=data&cmd=get&id=' + id,
			  type: 'POST',
			  dataType: 'json',
			  data: {},
        beforeSend: function() {
          App.loading.open();
        },
			  success: function (data) {
			    $('#editzeiterfassungsvorlage').find('#vorlageid').val(data.id);
			    $('#editzeiterfassungsvorlage').find('#vorlageart').val(data.vorlageart);
			    $('#editzeiterfassungsvorlage').find('#vorlage').val(data.vorlage);
			    $('#editzeiterfassungsvorlage').find('#vorlagedetail').val(data.vorlagedetail);
			    $('#editzeiterfassungsvorlage').find('#vorlageprojekt').val(data.vorlageprojekt);
			    $('#editzeiterfassungsvorlage').find('#vorlageteilprojekt').val(data.vorlageteilprojekt);
			    $('#editzeiterfassungsvorlage').find('#vorlagekunde').val(data.vorlagekunde);
			    $('#editzeiterfassungsvorlage').find('#vorlageabrechnen').prop("checked", data.vorlageabrechnen==1?true:false);
				  $('#editzeiterfassungsvorlage').find('#ausblenden').prop("checked", data.ausblenden==1?true:false);

				  App.loading.close();
				  $("#editzeiterfassungsvorlage").dialog('open');
			  },
			  beforeSend: function () {
			  }
		  });
    }else{
   	  ZeiterfassungReset();
      $("#editzeiterfassungsvorlage").dialog('open');
    }
  }

  function ZeiterfassungsvorlageSave(id) {
    $.ajax({
      url: 'index.php?module=zeiterfassungvorlage&action=data&cmd=save&id='+id,
      type: 'POST',
      dataType: 'json',
      data: {
        vorlageart: $('#vorlageart').val(),
        vorlage: $('#vorlage').val(),
        vorlagedetail: $('#vorlagedetail').val(),
        vorlageprojekt: $('#vorlageprojekt').val(),
        vorlageteilprojekt: $('#vorlageteilprojekt').val(),
        vorlagekunde: $('#vorlagekunde').val(),
        vorlageabrechnen: $('#vorlageabrechnen').prop("checked")?1:0,
        ausblenden: $('#ausblenden').prop("checked")?1:0
      },
      success: function(data) {
        App.loading.close();
        if(data.status == 1){
          ZeiterfassungReset();
          updateLiveTable();
          $("#editzeiterfassungsvorlage").dialog('close');
        }else{
          alert(data.statusText);
        }
      }
    });
  }

  function ZeiterfassungsvorlageDelete(id) {
    var conf = confirm('Wirklich löschen?');
    if (conf) {
      $.ajax({
        url: 'index.php?module=zeiterfassungvorlage&action=data&cmd=delete&id=' + id,
        data: {},
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

  function updateLiveTable() {
    var oTableL = $('#zeiterfassungvorlagelist').dataTable();
    var tmp = $('.dataTables_filter input[type=search]').val();
    oTableL.fnFilter('%');
    oTableL.fnFilter(tmp);
  }

</script>