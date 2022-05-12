<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]

<table height="80" width="100%"><tr><td>
<fieldset><legend>&nbsp;Filter</legend>
<center>
<table width="100%" cellspacing="5">
<tr>
  <td><input type="checkbox" id="auchohneauswahl">nur ausgew&auml;hlte</td><td>&nbsp;<form method="POST"><input type="submit" value="zur&uuml;cksetzen" name="zuruecksetzen" /></form></td>
</tr></table>
</center>
</fieldset>
</td></tr></table>
<!--//<div id="adresstab" style="height:500px;overflow-y: scroll;">-->
[TAB1]
<!--//</div>-->
<form method="POST">Layout: [LAYOUTS] Drucker: <select name="drucker" id="drucker">[DRUCKER]</select><input type="submit" value="drucken" name="drucken" id="drucken" /></form>
<input type="button" value="vorschau" name="vorschau" id="vorschau" onclick="vorschau();" />
[TAB1NEXT]
</div>

<!-- tab view schließen -->
</div>


<script type="text/javascript">



function vorschau()
{
  window.open('index.php?module=multilabelprint&action=vorschau&layout='+$('#layout').val(), '_blank');
}

  $(document).ready(function() {
  $('#serienbriefe .filter_column').first().css('display','none');
  $('#serienbriefe .filter_column').last().css('display','none');
    $('#drucken').on('click',function(){
      if($('#drucker').val())
      {
        var layout = 0;
        var layoutex = false;
        $('#layout').each(function(){
          layoutex = true;
          layout = $(this).val();
        });
        if(layoutex)
        {
          if(layout)
          {
            
          } else {
            alert('Kein Layout gewählt');
          }
        
        } else {
          alert('Kein Layout erstellt!');
        }
      
      } else {
        alert('Kein Drucker gewählt!');
      }
    
    });
  
  });
  
      function chmultilabelprint(aid)
      {
        var status = 0;
        var el = '#multilabelprint_'+aid;
        var elm = '#menge_'+aid;
        var menge = $(elm).val();
        menge = parseInt(menge);
        if(isNaN(menge) || menge == 0){
          menge = 1;
        }
        status = $(el).prop('checked');
        if(status)status = 1;
        
        if(aid)
        {
            $.ajax({
                url: 'index.php?module=multilabelprint&action=chmultilabelprint',
                type: 'POST',
                dataType: 'json',
                data: {artikel :aid, wert : status, menge: menge},
                success: function(data) {
                  
                },
                beforeSend: function() {

                }
            });
        
        }   
            
      }


      function chmenge(aid){
        var el = '#menge_'+aid;
        var menge = $(el).val();
        menge = parseInt(menge);
        
        if(menge > 0){
          var el2 = '#multilabelprint_'+aid;
          $(el2).prop('checked', true);

          $.ajax({
            url: 'index.php?module=multilabelprint&action=chmenge',
            type: 'POST',
            dataType: 'json',
            data: {artikel: aid, menge: menge},
            success: function(data){

            },
            beforeSend: function(){

            }
          });
        } 
      }
</script>
