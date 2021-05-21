<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Bestellungen</a></li>
        <li><a href="#tabs-2">Artikel</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]

  <div class="filter-box filter-usersave">
    <div class="filter-block filter-inline">
      <div class="filter-title">{|Filter|}</div>
      <ul class="filter-list">
        <li class="filter-item">
          <label for="abgeschlossen" class="switch">
            <input type="checkbox" value="1" name="abgeschlossen" id="abgeschlossen" title="abgeschlossen"/>
            <span class="slider round"></span>
          </label>
          <label for="abgeschlossen">{|auch abgeschlossene|}</label>
        </li>
      </ul>
    </div>
  </div>

[TAB1]
[TAB1NEXT]
</div>


<div id="tabs-2">
[MESSAGE]

  <div class="filter-box filter-usersave">
    <div class="filter-block filter-inline">
      <div class="filter-title">{|Filter|}</div>
      <ul class="filter-list">
        <li class="filter-item">
          <label for="abgeschlossenartikel" class="switch">
            <input type="checkbox" value="1" id="abgeschlossenartikel" title="abgeschlossene Artikel"/>
            <span class="slider round"></span>
          </label>
          <label for="abgeschlossenartikel">{|auch abgeschlossene|}</label>
        </li>
      </ul>
    </div>
  </div>

[TAB2]
[TAB2NEXT]
</div>


</div>





<script type="text/javascript">


function updateLiveTable(i){
  var oTableL = $('#adressebestellungen_artikel').dataTable();
  oTableL.fnFilter('%');
  oTableL.fnFilter('');   
}

function updateLiveTable2(i){
  var oTableL = $('#adressebestellungen').dataTable();
  oTableL.fnFilter('%');
  oTableL.fnFilter('');
}


function Geliefert(nsid, nid){
  var conf = confirm('Wirklich als geliefert markieren?');
  if(conf){
    $.ajax({
      url: 'index.php?module=adresse&action=adressebestellungmarkieren',
      data: {
        id: nid,
        sid: nsid
      },
      method: 'get',
      dataType: 'json',
      beforeSend: function(){
        App.loading.open();
      },
      success: function(data){
        if (data.status == 1){
          updateLiveTable();
        }else{
          alert(data.statusText);
        }

        App.loading.close();
      }
    });
  }

  return false;
}

function Kopieren(nid){
  var conf = confirm('Wirklich kopieren?');
  if(conf){
    $.ajax({
      url: 'index.php?module=bestellung&action=adressebestellungcopy',
      data: {
        id: nid
      },
      method: 'get',
      dataType: 'json',
      beforeSend: function(){
        App.loading.open();
      },
      success: function(data){
        if (data.status == 1){
          window.location.href='index.php?module=bestellung&action=edit&id='+data.newid;
        }else{
          alert(data.statusText);
        }

        App.loading.close();
      }
    });
  }

  return false;
}

</script>

