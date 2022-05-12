<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1"><!--[TABTEXT]--></a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]
[TAB1]
[TAB1NEXT]
</div>

<!-- tab view schließen -->
</div>

<div id="seriennummernassistent">
<table>
<tr><td>Artikel:</td><td><input type="text" id="artikel" value="" /></td></tr>
<tr><td>Startnummer:</td><td><input type="text" id="startnummer" value="" /></td></tr>
<tr><td>Menge:</td><td><input type="text" id="menge" value="" /></td></tr>
</table>
[SERIENNUMERNASSISTENTTAB]
</div>

<script>
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
              url: 'index.php?module=lieferschein&action=eingabeseriennummern&cmd=uebernehmeseriennummern&id=[ID]',
              type: 'POST',
              dataType: 'json',
              data: { artikelnummer: $('#artikel').val(), startnummer: $('#startnummer').val(),menge: $('#menge').val()},
              success: function(data) {
                if(data != null)
                {
                  $.each(data, function (k,v){
                    var fertig = false;
                    var artikelnummera = $('#artikel').val();
                    artikelnummera = artikelnummera.split(' ');
                    artikelnummera = artikelnummera[ 0 ];
                    $('#sntabelle').find('tr').each(function(){
                      if(!fertig)
                      {
                        var artnr = $(this).children('td').first().html();

                        if(artnr == artikelnummera)
                        {

                          $(this).find('input.inpseriennumern').each(function(){
                            if($(this).val() == v)
                            {
                              fertig = true;
                            }
                          });
                        }
                      }
                    });


                    $('#sntabelle').find('tr').each(function(){
                      if(!fertig)
                      {
                        var artnr = $(this).children('td').first().html();
                        var artikelnummera = $('#artikel').val();
                        artikelnummera = artikelnummera.split(' ');
                        artikelnummera = artikelnummera[ 0 ];
                        if(artnr == artikelnummera)
                        {

                          $(this).find('input.inpseriennumern').each(function(){
                            if($(this).val() == '')
                            {
                              fertig = true;
                              $(this).val(v);
                            }
                          });
                        }
                      }
                    });

                  });


                }
                $('#seriennummernassistent').dialog('close');
              },
              beforeSend: function() {

              }
            });

          },
          ABBRECHEN: function() {
            $(this).dialog('close');
          }
        },
        close: function(event, ui){

        }
      });
  });
  function openseriennummernassistent()
  {
    $('#seriennummernassistent').dialog('open');
  }
  function uebernehme(el,menge)
  {
    var tr = $(el).parent().parent();
    var td = $(tr).children('td');
    $('#artikel').val($(td[ 0 ]).html());
    $('#startnummer').val($(td[ 3 ]).html());
    $('#menge').val(menge);

  }


  $("#artikel").autocomplete({
      source: "index.php?module=ajax&action=filter&filtername=artikelnummermitseriennummern",
      select: function( event, ui ) {
          $.ajax({
              url: 'index.php?module=ajax&action=filter&filtername=artikelmengeinbeleg&beleg=lieferschein&id=[ID]',
              data: {
                  vorlage: ui.item.label
              },
              method: 'post',
              dataType: 'json',
              success: function(data) {
                  $("#menge").val(data);
              }
          });
      }
  });

</script>