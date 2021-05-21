<script type="text/javascript">
function updateLiveTable(i) {
    var oTableL = $('#verbindlichkeit_kontierungen').dataTable();
    var tmp = $('.dataTables_filter input[type=search]').val();
    oTableL.fnFilter('%');
    //oTableL.fnFilter('');
    oTableL.fnFilter(tmp);  
}


function SaveBelegfeld(element,id)
{
  SaveOnlinebanking(id,"belegfeld1",element.value);
}


function SaveBuchungstext(element,id)
{
  SaveOnlinebanking(id,"buchungstext",element.value);
}

function SaveOnlinebanking(id,feld,wert)
{
 $.ajax({
        url: 'index.php?module=zahlungseingang&action=onlinebanking&cmd=save',        
	data: {
            //Alle Felder die fürs editieren vorhanden sind
            kid: id,
            kfeld: feld,
            kwert: wert
        },
        method: 'post',        
	dataType: 'json',
        beforeSend: function() { 
            App.loading.open();
        },
        success: function(data) {
            App.loading.close();
            if (data.status == 1) {
	    } else {                
		alert('Fehler beim Speichern!');            
	    }        
	}    
	});
}       
</script>
<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1"></a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
<table height="80" width="100%"><tr><td>
  <fieldset class="usersave"><legend>{|Filter|}</legend>
    <center>
      <table width="100%" cellspacing="5" height="50">
        <tr>
          <td><input type="checkbox" id="klaerfaelle">&nbsp;{|Kl&auml;rf&auml;lle|}</td>
          <td><input type="checkbox" id="importfehler">&nbsp;{|Importfehler|}</td>
          <td><input type="checkbox" id="ohneimportfehler">&nbsp;{|Ohne Importfehler|}</td>
          <td><input type="checkbox" id="pruefen">&nbsp;{|Status "pr&uuml;fen"|}</td>
          <td><input type="checkbox" id="buchungstext">&nbsp;{|Buchungstext statt Kontoauszugstext|}</td>
          <td><input type="checkbox" id="nursoll">&nbsp;{|SOLL|}</td>
          <td><input type="checkbox" id="nurhaben">&nbsp;{|HABEN|}</td>
        </tr>
      </table>
    </center>
  </fieldset>
</td><td>
  <fieldset><legend>{|Datum|}</legend>
  <table cellspacing="5" width="100%" height="50"><tr>
    <td>{|Von|}:</td><td><input type="text" id="von" name="von" value="[VON]" size="12" onchange="updateLiveTable()" />&nbsp;</td>
    <td>{|Bis|}:</td><td><input type="text" id="bis" name="bis" value="[BIS]" size="12" onchange="updateLiveTable()" />&nbsp;</td>
  </tr></table>
</fieldset>
</td></tr></table>
[MESSAGE]
[TAB1]
[TAB1NEXT]
</div>

<!-- tab view schließen -->
</div>

