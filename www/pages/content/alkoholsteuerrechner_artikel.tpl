<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1"></a></li>
    </ul>
<div id="tabs-1">

<div id="artikeledit" style="display:none" title="Artikeleinstellungen">
  <fieldset>
    <legend>{|Einstellungen|}</legend>
    <table>
      <tr>
        <td>
          {|Artikel|}:
        </td>
        <td>
          <input type="text" name="article-number-name" id="article-number-name" size="40"/>
        </td>  
      </tr>  
      <tr>
        <td>
          {|Inhalt|}: 
        </td>
        <td>
          <input type="text" name="inhalt" id="inhalt" size="40" />
        </td>
      </tr>
      <tr>
        <td width="80">
          {|Alkohol|} %:
        </td>
        <td>
          <input type="text" name="prozent" id="prozent" size="40" />
        </td>
      </tr>
      <tr>
        <td>
          {|Art|}:
        </td>
        <td>
          <select name="art" id="art"><option value="wein">Wein</option><option value="schaumwein">Schaumwein</option><option value="spiritus" selected>Spirituosen</select>
        </td>
      </tr>
    </table>
  </fieldset>
</div>


[MESSAGE]

  <div class="row">
  <div class="row-height">
  <div class="col-xs-12 col-md-10 col-md-height">
  <div class="inside_white inside-full-height">

    <fieldset class="white">
      <legend>&nbsp;</legend>
      [TAB1]
    </fieldset>
    
  </div>
  </div>
  <div class="col-xs-12 col-md-2 col-md-height">
  <div class="inside inside-full-height">

    <fieldset>
      <legend>{|Aktionen|}</legend>
      <input class="btnGreenNew" type="button" name="neuedit" value="&#10010; Neuen Artikel eintragen" onclick="neuedit(0);">
    </fieldset>

  </div>
  </div>
  </div>
  </div>

[TAB1NEXT]



</div>


<!-- tab view schließen -->
</div>


<script>
$(document).ready(function() {
  $("#artikeledit").dialog({
  modal: true,
  bgiframe: true,
  closeOnEscape:false,
  minWidth:420,
  autoOpen: false,
  buttons: {
    ABBRECHEN: function() {
      $(this).dialog('close');
    },
    SPEICHERN: function() {
      artikelhinzufuegen();
    }
  }
  });

  $("#artikeledit").dialog({
    close: function( event, ui ){}
  });
});


  function neuedit(nr)
  {
    if(nr == 0){
      document.getElementById('artikel').value = '';
      document.getElementById('inhalt').value = '';
      document.getElementById('prozent').value = '';
      document.getElementById('art').value = 'spiritus';
      $("#artikeledit").dialog('open');
    }else{
      $.ajax({
        url: 'index.php?module=alkoholsteuerrechner&action=artikel&cmd=editartikel&id='+nr,
        type: 'POST',
        dataType: 'json',
        data: {},
        success: function(data) {
          document.getElementById('artikel').value = data.artikel;
          document.getElementById('inhalt').value = data.inhalt.replace(".",",");
          document.getElementById('prozent').value = data.prozent.replace(".",",");
          document.getElementById('art').value = data.art;
          $('#artikeledit').dialog('open');
        },
        beforeSend: function() {

        }
      });
      fnFilterColumn1(nr);  
    } 
  }



  function artikelhinzufuegen() {
    $.ajax({
      url: 'index.php?module=alkoholsteuerrechner&action=artikel&cmd=artikelspeichern',
      type: 'POST',
      dataType: 'json',
      data: {
        artikelid: $('#article-number-name').val(),
        inhalt: $('#inhalt').val(),
        prozent: $('#prozent').val(),
        art: $('#art').val(),
      },
      success: function(data) {
        if(data === 'fail'){
          alert("Fehler in angegebenen Daten");
        }else{
          $("#artikeledit").dialog('close'); 
          updateLiveTable();
        }
       },
      beforeSend: function() {

      }
    });
  }


  function deleteeintrag(nr)
  {
    if(!confirm("Soll der Artikel wirklich aus der Auflistung entfernt werden?")) return false;
    $.ajax({
        url: 'index.php?module=alkoholsteuerrechner&action=artikel&cmd=delete&id='+nr,
        data: { 
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
        },
        success: function(data) {
          updateLiveTable();
        }
    });
  }

  function updateLiveTable() {
      var oTableL = $('#artikeluebersicht').dataTable();
      oTableL.fnFilter('%');
      oTableL.fnFilter('');
  }
</script>
