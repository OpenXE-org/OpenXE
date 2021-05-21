<style>
  span.green {
    color:green;
  }
  span.red {
    color:red;
  }
  span.blue {
    color:blue;
  }
  span.ftest {
    font-weight: bold;
    font-size:13px;
    border: 1px lightgrey solid;
    border-radius: 3px;
    width:18px;
    height:16px;
    display:inline-block;
    padding-top:2px;
    margin-top:0;
    text-align:center;
    top:-4px;
    position: relative;
  }

</style>
<div id="tabs">
  <ul>
    <li><a href="#tabs-1"></a></li>
  </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">

<table width="100%">
<tr>

<td width="20%">
<fieldset style="height:125px"><legend>{|Funktionstest|}</legend>
<br>
<center>{|Seriennummer scannen|}:&nbsp;<form id="frmproduktionfunktionstest" action="" method="post"><input type="hidden" id="produktionfunktionstestid" name="produktionfunktionstestid" /><input type="text" [FUNKTIONSTESTDEAKTIVIERT] id="produktionfunktionstest" name="produktionfunktionstest" size="30"></form></center>
</fieldset>
</td>


<td width="20%">
</td>

<td width="20%">
<fieldset style="height:125px"><legend>{|Neue Seriennummer|}</legend>
<br>
<center>{|Seriennummer scannen|}:&nbsp;<form action="" method="post"><input type="hidden" id="produktionseriennummerid" name="produktionseriennummerid" /><input type="text" id="produktionseriennummer" name="produktionseriennummer" size="30"></form><br></center>
<br><center><input type="button" id="seriennummerngenerieren" value="Optional Generator starten" /></center>
</fieldset>

[MESSAGENEUESERIENNUMMER]
</td>
<td width="20%">
</td>



<td width="20%">
<fieldset style="height:125px"><legend>{|Drucken|}</legend>
<input type="button" onclick="window.location.href='index.php?module=produktion&action=pdf&id=[ID]'" value="Produktionsanweisung als PDF" style="width:300px"><br>
<input type="button" onclick="window.location.href='index.php?module=produktion&action=pdfanhang&id=[ID]'" value="Anh&auml;nge als PDF" style="width:300px"><br>
<input type="button" onclick="window.location.href='index.php?module=produktion&action=etikettendrucken&id=[ID]&cmd=sets'" value="Etikettensets f&uuml;r alle Baugruppen" style="width:300px">
<input type="button" onclick="window.location.href='index.php?module=produktion&action=etikettendrucken&id=[ID]&cmd=seriennummern'" value="Seriennummern f&uuml;r alle Baugruppen" style="width:300px">
</td>



</tr></table>
<table height="80" width="100%"><tr><td>

 <div class="filter-box filter-usersave">
   <div class="filter-block filter-inline">
     <div class="filter-title">{|Filter|}</div>
     <ul class="filter-list">
       <li class="filter-item">
         <label for="fehlendehauptseriennummern" class="switch">
           <input type="checkbox" id="fehlendehauptseriennummern">
           <span class="slider round"></span>
         </label>
         <label for="fehlendehauptseriennummern">{|fehlende Hauptseriennummern|}</label>
       </li>
       <li class="filter-item">
         <label for="fehlendeunterseriennummern" class="switch">
           <input type="checkbox" id="fehlendeunterseriennummern">
           <span class="slider round"></span>
         </label>
         <label for="fehlendeunterseriennummern">{|fehlende Unterseriennummern|}</label>
       </li>
       <li class="filter-item">
         <label for="ohnefunktionstest" class="switch">
           <input type="checkbox" id="ohnefunktionstest">
           <span class="slider round"></span>
         </label>
         <label for="ohnefunktionstest">{|ohne Funktionstest|}</label>
       </li>
       <li class="filter-item">
         <label for="negativerfuntkionstest" class="switch">
           <input type="checkbox" id="negativerfuntkionstest">
           <span class="slider round"></span>
         </label>
         <label for="negativerfuntkionstest">{|negativer Funktionstest|}</label>
       </li>
       <li class="filter-item">
         <label for="positiverfunktionstest" class="switch">
           <input type="checkbox" id="positiverfunktionstest">
           <span class="slider round"></span>
         </label>
         <label for="positiverfunktionstest">{|positiver Funktionstest|}</label>
       </li>
       <li class="filter-item">
         <label for="laufenderfunktionstest" class="switch">
           <input type="checkbox" id="laufenderfunktionstest">
           <span class="slider round"></span>
         </label>
         <label for="laufenderfunktionstest">{|laufender Funktionstest|}</label>
       </li>
     </ul>
   </div>
 </div>

</td></tr></table>
[MESSAGE]
<div id="ajaxmessage"></div>
[TAB1]
</div>


<!-- tab view schließen -->
</div>
<!-- ende tab view schließen -->

<div id="functionlogdialog">
<input type="hidden" id="functionlogbg"><input type="hidden" id="functionlogfunction" />
  <div id="functionlogcontent">
  </div>
</div>

<script type="text/javascript">
function ProduktionEtikettenDrucken(produktion, cmd, id)
{
  if(confirm('Etiketten wirklich drucken?')) {
    $.ajax({
      url: "index.php?module=produktion&action=etikettendrucken&id="+produktion+"&cmd="+cmd+"&lid="+id+"&json=1",
      type: 'POST',
      dataType: 'json',
      data: {
      }}).done( function(data) {

      }).fail( function( jqXHR, textStatus ) {
    });
  }
}
function refreshpzlivetable()
{
  $('#produktionszentrum_erfassen_filter').find('input').each(function(){
    var old = $(this).val();
    $(this).val(old+' ');
    $(this).trigger('keyup');
  });
}

function editUnterseriennummer(id)
{
    alert('Keine Unterseriennummern vorhanden');
}
function doFunktionstest(id)
{
  if(!$('#produktionfunktionstest').hasClass('disabled'))
  {
    $('#produktionfunktionstest').val('');
    $('#produktionfunktionstestid').val(id);
    $('#frmproduktionfunktionstest').submit();

  } else {
    alert('Kein Funktionstest vorhanden');
  }
}

function setzeAusschuss(id)
{
  $.ajax({
    url: "index.php?module=produktionszentrum&action=setzeausschuss&id="+id,
    type: 'POST',
    dataType: 'json',
    data: {
    }}).done( function(data) {
      if (typeof data == 'undefined' || data == null || typeof data.status == 'undefined' || data.status == 0)
      {
        $('#ajaxmessage').html('<div class="error">{|Fehler beim Laden|}. '+(typeof data != 'undefined' && data != null && typeof data.fehler != 'undefined'?data.fehler:'')+'</div>');
      } else {
        refreshpzlivetable();
      }
    }).fail( function( jqXHR, textStatus ) {
      $('#ajaxmessage').html('<div class="error">{|Fehler beim Laden|}.'+ textStatus+'</div>');
    
   });
}

function editSeriennummer(id)
{
  $.ajax({
    url: "index.php?module=produktionszentrum&action=getseriennummer&id="+id,
    type: 'POST',
    dataType: 'json',
    data: {
    }}).done( function(data) {
      if (typeof data == 'undefined' || data == null || typeof data.status == 'undefined' || data.status == 0)
      {
        $('#ajaxmessage').html('<div class="error">{|Fehler beim Laden|}. '+(typeof data != 'undefined' && data != null && typeof data.fehler != 'undefined'?data.fehler:'')+'</div>');
      } else {
        $('#baugruppeid').val(id);
        $('#diaseriennumer').val(data.seriennummer);
        jQuery('#seriennummeraendern').dialog({
				title: 'Seriennummer ändern',
				width: 600
			});
      }
    }).fail( function( jqXHR, textStatus ) {
      $('#ajaxmessage').html('<div class="error">{|Fehler beim Laden|}.'+ textStatus+'</div>');
   });
}

function editKommentar(id)
{
  $.ajax({
    url: "index.php?module=produktionszentrum&action=getkommentar&id="+id,
    type: 'POST',
    dataType: 'json',
    data: {
    }}).done( function(data) {
      if (typeof data == 'undefined' || data == null || typeof data.status == 'undefined' || data.status == 0)
      {
        $('#ajaxmessage').html('<div class="error">{|Fehler beim Laden|}. '+(typeof data != 'undefined' && data != null && typeof data.fehler != 'undefined'?data.fehler:'')+'</div>');
      } else {
        $('#kommentarbaugruppeid').val(id);
        $('#diakommentar').val(data.kommentar);
        jQuery('#kommentaraendern').dialog({
				title: 'Kommentar ändern',
				width: 600
			});
      }
    }).fail( function( jqXHR, textStatus ) {
      $('#ajaxmessage').html('<div class="error">{|Fehler beim Laden|}.'+ textStatus+'</div>');
   });
}

$(document).ready(function() {
  $('#functionlogdialog').dialog(
          {
            modal: true,
            autoOpen: false,
            minWidth: 940,
            title:'Funktionsprotokoll',
            buttons: {
              SPEICHERN: function()
              {
                var klasse = $('input[name="klasse"]:checked');
                if(klasse) {
                  klasse = $(klasse).val();
                }
                var textfeld1 = $('input[name="textfeld1"]');
                if(textfeld1) {
                  textfeld1 = $(textfeld1).val();
                }
                var textfeld2 = $('input[name="textfeld2"]');
                if(textfeld2) {
                  textfeld2 = $(textfeld2).val();
                }
                $.ajax({
                  url: 'index.php?module=produktionszentrum&action=erfassen&cmd=savefunctionlog',
                  type: 'POST',
                  dataType: 'json',
                  data: {
                    bgid:$('#functionlogbg').val(),
                    logid:$('#functionlogfunction').val(),
                    kommentar:$('#kommentar').val(),
                    status:$('input[name="status"]:checked').val(),
                    klasse:klasse,
                    textfeld1:textfeld1,
                    textfeld2:textfeld2
                  }
                }).done(function (data) {
                  $('#functionlogdialog').dialog('close');
                  $('#functionlogcontent').html('');
                  $('#functionlogbg').val('');
                  $('#functionlogfunction').val('');
                  var oTable = $('#produktionszentrum_erfassen').DataTable( );
                  oTable.ajax.reload();
                });
                $('#functionlogdialog').dialog('close');
              },
              ABBRECHEN: function() {
                $(this).dialog('close');
              }
            },
            close: function(event, ui){

            }
          });

  $('#produktionszentrum_erfassen').on('afterreload', function (){
    $('span.ftest').off('click');
    $('span.ftest').on('click',function() {
      $.ajax({
        url: 'index.php?module=produktionszentrum&action=erfassen&cmd=getfunctionlog',
        type: 'POST',
        dataType: 'json',
        data: {
          bgid:$(this).data('bg'),
          logid:$(this).data('flog')
        }
      }).done(function (data) {
        if(typeof data.status != 'undefined' && data.status == '1') {
          $('#functionlogbg').val(data.bg);
          $('#functionlogfunction').val(data.log);
          $('#functionlogcontent').html(data.html);
          $('#functionlogdialog').dialog('open');
          $('#functionlogcontent #ftsubmit').remove();
          $('#functionlogcontent #kommentar').val(data.comment);
        } else {
          alert(data.error);
        }
      });
    });
  });
});
    
  
  /*
  $('div.dataTables_filter input').focus();
  $('div.dataTables_filter input').val("");
  $('div.dataTables_filter input').submit();
});
  */
  //$('#produktion').focus();
</script>

<!--<div id="chargendiaglog" style="display:none;">
  <form method="POST">
    <table>
      <tr><th>Artikel</th><th>Charge</th></tr>
      [CHARGENFORM]
      <tr><td><input type="text" id="chargenartikelneu_1" name="chargenartikelneu_1" /></td><td><input type="text" id="chargennummerneu_1" name="chargennummerneu_1" /></td></tr>
      <tr><td><input type="text" id="chargenartikelneu_2" name="chargenartikelneu_2" /></td><td><input type="text" id="chargennummerneu_2" name="chargennummerneu_2" /></td></tr>
      <tr><td></td><td><input type="submit" name="chargensubmit" value="Erfassen" /></td></tr>
    </table>
  </form>
</div>-->

[GENERATORDIALOGE]
<div id="seriennummeraendern" style="display:none;">
  <form method="POST">
    <table>
      <tr><td>{|Seriennummer|}</td><td><input type="hidden" name="baugruppeid" value="" id="baugruppeid" /><input type="text" id="diaseriennumer" name="seriennummer" value="1" size="40"/>
      &nbsp;<input type="submit" name="seriennummereditsubmit" value="&Auml;ndern" /></td></tr>
    </table>
  </form>
</div>
<div id="kommentaraendern" style="display:none;">
  <form method="POST">
    <table>
      <tr><td>{|Kommentar|}</td><td><input type="hidden" name="baugruppeid" value="" id="kommentarbaugruppeid" /><input type="text" size="40" id="diakommentar" name="kommentar" value="1" />&nbsp;
      <input type="submit" name="kommentareditsubmit" value="&Auml;ndern" /></td></tr>
    </table>
  </form>
</div>
[DIVFUNKTIONSTEST]
[DIVUNTERSERIENNUMMERN]
