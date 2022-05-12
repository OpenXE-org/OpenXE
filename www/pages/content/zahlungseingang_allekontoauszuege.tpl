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

  <div class="filter-box filter-usersave">
    <div class="filter-block filter-inline">
      <div class="filter-title">{|Filter|}</div>
      <ul class="filter-list">
        <li class="filter-item">
          <label for="klaerfaelle" class="switch">
            <input type="checkbox" id="klaerfaelle">
            <span class="slider round"></span>
          </label>
          <label for="klaerfaelle">{|Kl&auml;rf&auml;lle|}</label>
        </li>
        <li class="filter-item">
          <label for="importfehler" class="switch">
            <input type="checkbox" id="importfehler">
            <span class="slider round"></span>
          </label>
          <label for="importfehler">{|Importfehler|}</label>
        </li>
        <li class="filter-item">
          <label for="ohneimportfehler" class="switch">
            <input type="checkbox" id="ohneimportfehler">
            <span class="slider round"></span>
          </label>
          <label for="ohneimportfehler">{|Ohne Importfehler|}</label>
        </li>
        <li class="filter-item">
          <label for="pruefen" class="switch">
            <input type="checkbox" id="pruefen">
            <span class="slider round"></span>
          </label>
          <label for="pruefen">{|Status "pr&uuml;fen"|}</label>
        </li>
        <li class="filter-item">
          <label for="buchungstext" class="switch">
            <input type="checkbox" id="buchungstext">
            <span class="slider round"></span>
          </label>
          <label for="buchungstext">{|Buchungstext statt Kontoauszugstext|}</label>
        </li>
        <li class="filter-item">
          <label for="nursoll" class="switch">
            <input type="checkbox" id="nursoll">
            <span class="slider round"></span>
          </label>
          <label for="nursoll">{|SOLL|}</label>
        </li>
        <li class="filter-item">
          <label for="nurhaben" class="switch">
            <input type="checkbox" id="nurhaben">
            <span class="slider round"></span>
          </label>
          <label for="nurhaben">{|HABEN|}</label>
        </li>
        <li class="filter-item">
          <label for="von">{|Von|}:</label>
          <input type="text" id="von" name="von" value="[VON]" size="12" onchange="updateLiveTable()">
        </li>
        <li class="filter-item">
          <label for="bis">{|Bis|}:</label>
          <input type="text" id="bis" name="bis" value="[BIS]" size="12" onchange="updateLiveTable()">
        </li>
      </ul>
    </div>
  </div>

[MESSAGE]
[TAB1]
[TAB1NEXT]
</div>

<!-- tab view schließen -->
</div>

