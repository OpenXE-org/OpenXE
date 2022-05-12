<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1"><!--[TABTEXT]--></a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">


<div class="row">
  <div class="row-height">
    <div class="col-xs-12 col-sm-4 col-sm-height">      
      <div class="inside inside-full-height">
<fieldset><legend>Empf&auml;nger</legend>
[ADRESSE]
</fieldset>
     </div>
    </div>

    <div class="col-xs-12 col-sm-4 col-sm-height">      
      <div class="inside inside-full-height">
<fieldset><legend>{|Lieferung|}</legend>
[LIEFERUNG]
</fieldset>
     </div>
    </div>
    <div class="col-xs-12 col-sm-4 col-sm-height">      
      <div class="inside inside-full-height">
<fieldset><legend>{|Information|}</legend>
[INFORMATION]
</fieldset>
     </div>
    </div>



  </div>
</div>

[FREITEXTMESSAGE]

<div class="row">
  <div class="row-height">
    <div class="col-xs-12 col-sm-height">      
      <div class="inside_white inside-full-height">

[MESSAGE]
[SCANNEN]
     </div>
    </div>
  </div>
</div>



<div class="row">
  <div class="row-height">
    <div class="col-xs-12 col-sm-height">      
      <div class="inside inside-full-height">
[TAB1]
     </div>
    </div>
  </div>
</div>



[TAB1NEXT]
</div>

  <div id="seriennummernassistent" style="display:none">
    <table>
      <tr><td>Artikel:</td><td>[VERSANDSERIENARTIKELNUMMER]</td></tr>
      <tr><td>Startnummer:</td><td><input type="text" id="startnummer" value="" /></td></tr>
      <tr><td>Menge:</td><td><input type="text" id="serienartikelmenge" value="" /></td></tr>
    </table>
    [SERIENNUMERNASSISTENTTAB]
  </div>

<!-- tab view schließen -->
</div>

<script type="application/javascript">
    oMoreData1versanderzeugen_seriennummern_assistent = '[VERSANDSERIENARTIKELNUMMER]';
    oMoreData2versanderzeugen_seriennummern_assistent = '[VERSANDSERIENARTIKELMENGE]';

    $(document).ready(function(){
    if($('input.autoclick').length) {
      $('input.autoclick').parents('div').first().find('#weiter').trigger('click');
    }
  });

  function openseriennummernassistent()
  {
      $('#seriennummernassistent').dialog('open');
  }

  function uebernehme(el,menge)
  {
      var tr = $(el).parent().parent();
      var td = $(tr).children('td');
      $('#startnummer').val($(td[ 3 ]).html());
      $('#serienartikelmenge').val(menge);
  }

  $(document).ready(function() {
      $('#seriennummernassistent').dialog(
          {
              modal: true,
              autoOpen: false,
              minWidth: 300,
              width: '90%',
              minHeight:300,
              buttons: {
                  "SERIENNUMMERN ÜBERNEHMEN": function()
                  {

                      $.ajax({
                          url: 'index.php?module=versanderzeugen&action=einzel&cmd=holeseriennummern',
                          type: 'POST',
                          dataType: 'json',
                          data: { maximalmenge: '[VERSANDSERIENARTIKELMENGE]',
                              artikelnummer: '[VERSANDSERIENARTIKELNUMMER]',
                              startnummer: $('#startnummer').val(),
                              menge: $('#serienartikelmenge').val()},
                          success: function(data) {
                              elements = $('[name^="seriennummer"]');
                              for (i=0;i<data.length;i++){
                                  elements[i].value=data[i];
                              }
                          },
                          beforeSend: function() {

                          }
                      });

                      $(this).dialog('close');
                  },
                  ABBRECHEN: function() {
                      $(this).dialog('close');
                  }
              },
              close: function(event, ui){

              }
          });
  });
</script>