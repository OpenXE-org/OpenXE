<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">

<div id="vorlageedit" style="display:none" title="Vorlageneinstellungen">
  <input type="hidden" id="vid" value='0';>
  <fieldset>
    <legend>{|Einstellungen|}</legend>
    <table>
      <tr>
        <td>
          {|Strecke|}:
        </td>
        <td>
          <input type="text" name="strecke" id="strecke" size="40" />
        </td>  
      </tr>  
      <tr>
        <td>
          {|Kilometer|}: 
        </td>
        <td>
          <input type="text" name="kilometer" id="kilometer" size="40" />
        </td>
      </tr>
      <tr>
        <td>
          {|Buchen|}:
        </td>
        <td>
          <input type="checkbox" name="buchen" id="buchen" />
        </td>
      </tr>
      <tr>
        <td>
          {|Aktiv|}:
        </td>
        <td>
          <input type="checkbox" name="akitv" id="aktiv" />
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
      [TAB1NEXT]      
    </fieldset>
    
  </div>
  </div>
  <div class="col-xs-12 col-md-2 col-md-height">
  <div class="inside inside-full-height">

    <fieldset>
      <legend>{|Aktionen|}</legend>
      <input class="btnGreenNew" type="button" name="vorlageedit" value="&#10010; Neue Vorlage eintragen" onclick="vorlageedit(0);">
    </fieldset>

  </div>
  </div>
  </div>
  </div>

</div>

<!-- tab view schließen -->
</div>



<script>
$(document).ready(function() {
  $("#vorlageedit").dialog({
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
      vorlagehinzufuegen();
    }
  }
  });

  $("#vorlageedit").dialog({
    close: function( event, ui ){}
  });
});


  function vorlageedit(nr)
  {
    if(nr == 0){
      document.getElementById('vid').value = '0';
      document.getElementById('strecke').value = '';
      document.getElementById('kilometer').value = '';
      $('#buchen').prop("checked",true);
      $('#aktiv').prop("checked",true);
      $("#vorlageedit").dialog('open');
    }else{
      $.ajax({
        url: 'index.php?module=fahrtenbuch&action=vorlagen&cmd=get&id='+nr,
        type: 'POST',
        dataType: 'json',
        data: {},
        success: function(data) {
          document.getElementById('vid').value = nr;
          document.getElementById('strecke').value = data.strecke;
          document.getElementById('kilometer').value = data.kilometer.replace(".",",");
          $('#buchen').prop("checked",data.buchen==1?true:false);
          $('#aktiv').prop("checked",data.aktiv==1?true:false);
          $('#vorlageedit').dialog('open');
        },
        beforeSend: function() {

        }
      });  
    } 
  }



  function vorlagehinzufuegen() {
    $.ajax({
      url: 'index.php?module=fahrtenbuch&action=vorlagen&cmd=save',
      type: 'POST',
      dataType: 'json',
      data: {
        id: $('#vid').val(),
        strecke: $('#strecke').val(),
        kilometer: $('#kilometer').val(),
        buchen: $('#buchen').prop("checked")?1:0,
        aktiv: $('#aktiv').prop("checked")?1:0
      },
      success: function(data) {
        if(data == 'fail'){
          alert("Fehler in angegebenen Daten");
        }else{
          $("#vorlageedit").dialog('close'); 
          updateLiveTable();
        }
       },
      beforeSend: function() {

      }
    });
  }


  function vorlagedelete(nr)
  {
    if(!confirm("Soll die Vorlage wirklich gelöscht werden?")) return false;
    $.ajax({
        url: 'index.php?module=fahrtenbuch&action=vorlagen&cmd=delete&id='+nr,
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
      var oTableL = $('#fahrtenbuch_vorlagen').dataTable();
      oTableL.fnFilter('%');
      oTableL.fnFilter('');
  }
</script>