<script type="text/javascript">

$(document).ready(function(){

  art = document.getElementById('objekt');
  projekte = document.getElementById('parameter');
  gruppen = document.getElementById('gruppe');

  if (art) {
		if (art.options[art.selectedIndex].value =='Gruppe') {
      projekte.style.display='none';
      gruppen.style.display='';
    }
    if (art.options[art.selectedIndex].value =='Projekt') {
      projekte.style.display='';
      gruppen.style.display='none';
    }
    if(typeof(aktualisiereLupe) == 'function')
    {
      aktualisiereLupe();
    }
    art.onchange=function() {
			if (art.options[art.selectedIndex].value =='Gruppe') {
        projekte.style.display='none';
        gruppen.style.display='';
	
      }
      if (art.options[art.selectedIndex].value =='Projekt') {
        projekte.style.display='';
        gruppen.style.display='none';
      }
      if(typeof(aktualisiereLupe) == 'function')
      {
        aktualisiereLupe();
      }
    }
  }

});


$(document).ready(function() {
    $('#von_datum').focus();

    $("#editRolle").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    minWidth:300,
    autoOpen: false,
    buttons: {
      ABBRECHEN: function() {
        $(this).dialog('close');
      },
      SPEICHERN: function() {
        AdresserolleSave();
      }
    }
  });

});

function AdresserolleSave() {

    $.ajax({
        url: 'index.php?module=adresse&action=saverolle',
        data: {
            //Alle Felder die fürs editieren vorhanden sind
            id: $('#id').val(),
            von_datum: $('#von_datum').val(),
            bis_datum: $('#bis_datum').val(),
            
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {
            App.loading.close();
            if (data.status == 1) {
                $('#editRolle').find('#id').val('');
                $('#editRolle').find('#von_datum').val('');
                $('#editRolle').find('#bis_datum').val('');
                window.location.href='index.php?module=adresse&action=rolledatum&id=[ID]';
            } else {
                alert(data.statusText);
            }
        }
    });

}

function AdresseRolleEdit(id) {

    $.ajax({
        url: 'index.php?module=adresse&action=rolledatum&cmd=get',
        data: {
            id: id
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {
          $("#editRolle").dialog('open');
          console.log(data);
          $('#editRolle').find('#id').val(data.id);
          $('#editRolle').find('#von_datum').val(data.von_datum);
          $('#editRolle').find('#bis_datum').val(data.bis_datum);
          App.loading.close();
          $("#editRolle").dialog('open');
        }
    });

}

</script>


<table border="0" width="100%">
<tr><td><table width="100%"><tr><td>
<form action="" method="post" name="eprooform">
[FORMHANDLEREVENT]
  <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>

      <tr valign="top" colspan="3">
        <td>
             <table border="0" width="100%">
              <tbody>
                      <tr><td>Rolle:</td><td><select name="subjekt">[ROLLE_SELECT]</select></td>
                      <td>von:</td><td><select name="objekt" id="objekt"><option value="Projekt">Projekt</option><option value="Gruppe">Gruppe</option></select></td>
 <td>
[PROJEKTSTART]<input name="parameter" id="parameter" type="text" size="20">[PROJEKTENDE]
[GRUPPESTART]<input name="gruppe" id="gruppe" type="text" size="20">[GRUPPEENDE]
</td><td><input type="submit" value="als neue Rolle anlegen" name="rolleanlegen"></td></tr>

              </tbody></table>
        </td>
      </tr>

  
    </tbody>
  </table>
  </form>
</td></tr></table></td></tr>
</table>

<form method="post">

  <div id="editRolle" style="display:none;" title="Bearbeiten">
    <input type="hidden" id="id"><!--adresse-->
    <table>
      <tr>
        <td>Von:</td>
        <td><input type="text" id="von_datum"></td>        
      </tr>
      <tr>
        <td>Bis:</td>
        <td><input type="text" id="bis_datum"></td>
      </tr>
    </table>
      
  </div>

</form>