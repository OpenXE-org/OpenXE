
<style type="text/css">
  #placeholder {
    width: 100%;
    height: 300px;
  }

  #placeholder2 {
    width: 100%;
    height: 300px;
  }

  #aktiondiv {
    text-align: center;
    margin: 5px;
  }
</style>

[DIAGRAMME]

<!--
<div class="row">
  <div class="row-height">
    <div class="col-xs-12 col-sm-6 col-sm-height">
      <div class="inside inside-full-height">
        <h2 class="greyh2">Gesamt&uuml;bersicht Auftragseingang[BESCHRIFTUNG1]</h2>
        <div id="placeholder" width="100%" height="400"></div>
      </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-sm-height">
      <div class="inside inside-full-height">
        <h2 class="greyh2">Statistik Auftr&auml;ge Heute [APIHINWEIS]</h2>
        [STATISTIKHEUTE]
        <h2 class="greyh2">Statistik Auftr&auml;ge Gestern [APIHINWEIS]</h2>
        [STATISTIKGESTERN]
        
        <div id="aktiondiv"><input type="button" value="{|Details|}" />&nbsp;<input type="button" value="{|+ weiteres Diagaramm|}" /></div>
      </div>
    </div>
  </div>
</div>
-->
<div class="row">
  <div class="row-height">
    <div class="col-xs-12 col-sm-6 col-sm-height">
      <div class="inside inside-full-height">
        <h2 class="greyh2">{|Tages&uuml;bersicht Auftragseingang (nur versendet und freigegebene)|}</h2>
        <div class="geteasytablelist" id="tagesuebersicht">[TAGESUEBERSICHT]</div>
      </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-sm-height">
      <div class="inside inside-full-height">
        <h2 class="greyh2">{|Top Artikel (Lager) letzten 90 Tage|}</h2>
        <div class="geteasytablelist" id="topartikel">[TOPARTIKEL]</div>
        
        [EXTEND]
      </div>
    </div>
  </div>
</div>


<div id="chartpopup" style="display:none;">
  <form id="chartfrm">
    <fieldset><legend>{|Einstellungen|}</legend>
      <table>
        <tr><td>{|Beschriftung|}:</td><td><input type="text" id="bezeichnung" name="bezeichnung" /><input type="hidden" name="sid" id="sid" value="" /></td></tr>
        
        <tr><td colspan="2"><input type="checkbox" value="1" id="regs" name="regs" />&nbsp;{|basierend auf Rechnungen und Gutschriften|}</td></tr>
        <tr><td colspan="2"><input type="checkbox" value="1" id="monat" name="monat" />&nbsp;{|12 Monate Statistik anzeigen|}</td></tr>
      </table>
    </fieldset>
    <fieldset><legend>{|Projekte|}</legend>
      <table>
        [POPUPPROJEKTE]
      </table>
    </fieldset>
  </form>
</div>

<style type="text/css">

  div.graph {
    width: 400px;
    height: 350px;
    float: right;
  }

  #placeholder2 {
    width: 450px;
    height: 300px;
  }

</style>

<script type="text/javascript" language="javascript">
$(document).ready(function() {

    $('#chartpopup').dialog(
    {
      modal: true,
      autoOpen: false,
      minWidth: 940,
      title:'{|Statistik|}',
      buttons: {
        '{|SPEICHERN|}': function()
        {

            $.ajax({
                url: 'index.php?module=verkaufszahlen&action=list&cmd=savechart',
                type: 'POST',
                dataType: 'json',
                data:  $('#chartfrm').serialize(),
                success: function(data) {
                  window.location.href=window.location.href;
                },
                beforeSend: function() {

                }
            });

        },
        
        [VORDEAKTIVIEREN]
        'DEAKTIVEREN': function() {
          if(parseFloat($('#sid').val()) > 0 && confirm('Wirklich deaktivieren?'))
          {
            $.ajax({
                url: 'index.php?module=verkaufszahlen&action=list&cmd=deletechart',
                type: 'POST',
                dataType: 'json',
                data:  $('#chartfrm').serialize(),
                success: function(data) {
                  window.location.href=window.location.href;
                },
                beforeSend: function() {

                }
            });
          }
        },
        
        [NACHDEAKTIVIEREN]
        
        'ABBRECHEN': function() {
          $(this).dialog('close');
        }

      },
      close: function(event, ui){
        
      }
    });
});
  
function openchart(id) {
  $.ajax({
      url: 'index.php?module=verkaufszahlen&action=list&cmd=getchart',
      type: 'POST',
      dataType: 'json',
      data: { el:id},
      success: function(data) {
        $('#chartpopup').dialog('open');
        $('#sid').val(data.id);
        $('#bezeichnung').val(data.bezeichnung);
        $('#monat').prop('checked', data.monat==1?true:false);
        $('#regs').prop('checked', data.regs==1?true:false);
        $.each(data.projekte,function (k,v)
        {
          window.console.log(v);
          $('#projekt_'+k).prop('checked', v==1?true:false);
        });
      },
      beforeSend: function() {

      }
  });
}
</script>