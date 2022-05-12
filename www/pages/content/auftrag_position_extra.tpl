<script>

  var drawpositionen = '[DRAWPOSITIONEN]';
  var schreibschutz = [SCHREIBSCHUTZ];
  var drawtheme = '[DRAWTHEME]';
  var anzcols = 0;
  var colourodd = '[COLOURODD]';
  var coloureven = '[COLOUREVEN]';
  var doctyp = '[DOCTYP]';
  var trtyp = null;
  var trselid = null;
  var aktel = null;
  var nu = 0;
  var eins = 1;  
  $( document ).ready(function() {

    $.ajax({
        url: 'index.php?module=[DOCTYP]&action=positionen&cmd=getelements&id=[ID]&fmodul=[FMODUL]',
        type: 'POST',
        dataType: 'json',
        data: { },
        success: function(data) {
          drawpositionen = data.result;
          if(schreibschutz)
          {
            anzcols = $('.mkTable > tbody').children('tr').first().find('td').size();
          }else{
            anzcols = $('#tableone > tbody').children('tr').first().find('td').size();
          }

          DrawExtras();


          if(!schreibschutz)
          {
            $('#tableone tr').each(function(){
              $(this).find('td:last img:last').parent().after('&nbsp;<span style="background-image: url(\'./themes/[DRAWTHEME]/images/move.svg\');background-repeat: no-repeat;width:20px;height:20px;margin:0;padding:0;display:inline-block;position:relative;" ><img src="./themes/[DRAWTHEME]/images/move.svg"  class="moveicon" /></span>');
            });
            $('#tableone tr .moveicon').draggable( {
              containment: false,
              cursor: 'move',
              snap: '#tableone > tbody tr'
            } );
            
            $('#tableone tr .moveicon').on('dragstart',function(event, ui){
              this.old_pos = ui.helper.position(this);
              if(schreibschutz)return;
              trtyp = null;
              trselid = null;
              var thistr = null;
              $(this).parent().parent().parent().each(function(){thistr = this;});
              if(typeof thistr.id != 'undefined')
              {
                var ida = thistr.id.split('_');
                if(typeof ida[eins] != 'undefined')
                {
                  var sid = parseInt(ida[eins]);
                  trtyp = ida[nu];
                  trselid = sid;
                }else{
                  var sid = parseInt(ida[nu]);
                  if(sid <=  getLastPos())
                  {
                    trtyp = 'pos';
                    trselid = sid;
                    var str = $(thistr).find('td').last().find('a').first().prop('href');
                    var stra = str.split('sid=');
                    str = stra[eins];
                    stra = str.split('&');
                    str = stra[nu];
                    trselid = parseInt(str);
                  }
                }
              }
              if(trselid !== null)
              {
                aktel = thistr;
              }
              RecolourTable();
            });
            
            $('#tableone tr .moveicon').on('dragstop',function(event, ui){
                $(this).css('left',this.old_pos.left);
                $(this).css('top',this.old_pos.top);
            });
            
            $('#spezialfeldeinfuegen').on('click',function(){
              drawedit($('#feldart').val(), 0);
              
            });
            
            $('#feldart').on('change',function()
            {
              $('#feldart2').val($('#feldart').val());
               $('#feldart2').trigger('change');
            });
            $('#feldart2').on('change',function()
            {
              changefeldart();
            });
            
            $('#tableone > tbody > tr').on('mouseover',function(event){
              if(schreibschutz)return;
              RecolourTable();
              if(aktel !== null)
              {
                var found = false;
                $(this).next('tr').each(function(){found = true;});
                if(found)
                {
                  //$(this).css('background-color','#faa');
                  $(this).children('td').css('border-bottom','2px solid green');
                }
              }
            });

            $('#tableone > tbody > tr').on('mouseup',function(){
              if(schreibschutz)return;
              if(aktel !== null)
              {
                //alert(trselid);
                //$(this).prev('tr').first().each(function(){
                  var toel = this;

                  if(toel != aktel)
                  {
                    if(typeof toel.id != 'undefined')
                    {
                      var ida2 = this.id.split('_');
                      if(typeof ida2[eins] != 'undefined')
                      {
                        var sid2 = parseInt(ida2[eins]);
                        trtyp2 = ida2[nu];
                        trselid2 = sid2;
                        if(trselid != trselid2 || trtyp2 != trtyp)
                        {
                          
                          movePos(trtyp, trselid, trtyp2, trselid2);
                        }
                      }else{
                        var sid2 = parseInt(ida2[nu]);
                        if(sid2 <=  getLastPos())
                        {
                          if(sid2 == 0)
                          {
                            movePos(trtyp, trselid, 'oben', 0);
                          }else{
                            trtyp2 = 'pos';
                            trselid2 = sid2;
                            var str2 = $(this).find('td').last().find('a').first().prop('href');
                            var str2a = str2.split('sid=');
                            str2 = str2a[eins];
                            str2a = str2.split('&');
                            str2 = str2a[nu];
                            trselid2 = parseInt(str2);
                            
                            //trselid2 = parseInt(match2[nu]);
                            if(trselid != trselid2 || trtyp2 != trtyp)
                            {
                              movePos(trtyp, trselid, trtyp2, trselid2);
                            }
                          }
                        }
                      }
                    }
                  }
                  
                //});
              }
              trtyp = null;
              trselid = null;
              aktel = null;
              RecolourTable();
              return;
            });
          }
        },
        beforeSend: function() {

        }
    });
    changefeldart();
    $("#draweditpopup").dialog({
        modal: true,
        bgiframe: true,
        closeOnEscape:false,
        autoOpen: false,
        minWidth: 940,
        buttons: {
            ABBRECHEN: function() {
                
                $(this).dialog('close');
            },
            SPEICHERN: function() {
              var sid = $('#draweditid').val();
              if($('#feldart2').val() == 'bild')
              {
                var url = '';
                if(sid == 0)
                {
                  url = 'index.php?module=[DOCTYP]&action=positionen&cmd=adddraw&fmodul=[FMODUL]&id=[ID]&pos=999999&sort=99999&postype='+$('#feldart2').val()+'&grbez='+encodeURIComponent($('#grbez').val())+'&grtext='+encodeURIComponent($('#grtext').val());
                }else{
                  url = 'index.php?module=[DOCTYP]&action=positionen&cmd=editdraw&fmodul=[FMODUL]&id=[ID]&sid='+sid+'&postype='+$('#feldart2').val()+'&grbez='+encodeURIComponent($('#grbez').val())+'&grtext='+encodeURIComponent($('#grtext').val());
                }
                url = url + '&bildhoehe='+$('#bildbreite').val()+'&bildbreite='+$('#bildbreite').val();
                var frmdata = new FormData();
                frmdata.append('bild', $('#bildform').find('#positionsbild')[0].files[0]);
                frmdata.append('postype', $('#feldart2').val());
                frmdata.append('grbez', $('#grbez').val());
                frmdata.append('grtext', $('#grtext').val());
                $.ajax({
                  url: url,
                  data: frmdata,
                  method: 'POST',
                  processData: false,
                  contentType: false,
                  success: function() {
                    window.location.href = ('index.php?module=[DOCTYP]&action=positionen&fmodul=[FMODUL]&id=[ID]');
                  }
                });
                $(this).dialog('close');

              }else{
                if (sid == 0) {
                  url = 'index.php?module=[DOCTYP]&action=positionen&cmd=adddraw&fmodul=[FMODUL]&id=[ID]&pos=999999&sort=99999';
                } else {
                  url = 'index.php?module=[DOCTYP]&action=positionen&cmd=editdraw&fmodul=[FMODUL]&id=[ID]&sid=' + sid;
                }

                $.ajax({
                  url: url,
                  data: {
                    postype: $('#feldart2').val(),
                    grbez: $('#grbez').val(),
                    grtext: $('#grtext').val()
                  },
                  method: 'post',
                  dataType: 'json',
                  beforeSend: function () {
                  },
                  success: function (data) {
                    window.location.href = ('index.php?module=[DOCTYP]&action=positionen&fmodul=[FMODUL]&id=[ID]');
                  }
                });

                $(this).dialog('close');
              }
            }
        },
        close: function(event, ui){

        }
    });

  });
  
  function RecolourTable()
  {
    var ind = 0;
    $('#tableone > tbody > tr').each(function(){
      $(this).children('td').css('border-bottom','');
      ind++;
      if(ind > 1)
      {
        if(ind % 2 == 0)
        {
          $(this).css('background-color',coloureven);
        }else{
          $(this).css('background-color',colourodd);
        }
      }
    });
    if(aktel !== null)
    {
      $(aktel).css('background-color','#cfc');
    }
  }
  
  function changefeldart()
  {
    var wert = $('#feldart2').val();
    if(wert == 'seitenumbruch')
    {
      $('#trtext').hide();
      $('#trbez').hide();
      $('.trbild').hide();
    
    }else if(wert == 'gruppe')
    {
      $('#trbez').show();
      $('#trtext').show();
      $('.trbild').hide();
    }
   else if(wert == 'gruppensumme')
    {
      $('#trbez').show();
      $('#trtext').hide();
      $('.trbild').hide();
    }
    else if(wert === 'gruppensummemitoptionalenpreisen')
    {
      $('#trbez').show();
      $('#trtext').hide();
      $('.trbild').hide();
    }
 
    else if(wert == 'zwischensumme')
    {
      $('#trbez').show();
      $('#trtext').hide();
      $('.trbild').hide();
    }
    else if(wert == 'bild')
    {
      $('#trbez').show();
      $('.trbild').show();
      $('#trtext').show();
    }
  }
  
  function DrawExtras()
  {
    //var drawobj = jQuery.parseJSON(drawpositionen);
    var drawobj = drawpositionen;
    if(drawobj)
    {
      $(drawobj).each(function(key, value){
        var auswahlcbox = '';
        if(typeof value.postype != 'undefined')
        {
          var pos = parseInt(value.pos);
          var tr = foundTrPos(pos);
          var anzcolsm1 = anzcols - 2;
          var tmpanzcolsm1 = anzcolsm1;
          var tmpd = '';
          if(tr !== null && anzcolsm1 > 0)
          {
            auswahlcbox  = '';
            if(!schreibschutz && typeof value.id != 'undefined')auswahlcbox = '<input type="checkbox" name="zwischensort[]" value="'+value.id+'" />';
            switch(value.postype)
            {
              case 'zwischensumme':
                var name = '';
                if(typeof value.wert != 'undefined' && typeof value.wert.name != 'undefined')name = value.wert.name;
                if(name==null)name='';

                tmpanzcolsm1 = 5- (('lieferschein'=='[DOCTYP]' || 'retoure'=='[DOCTYP]' || 'preisanfrage'=='[DOCTYP]' )?1:0);
                tmpd = '<td><span class="czwischensumme"></span></td><td colspan="'+(anzcolsm1-6)+'"></td>';

                $(tr).after('<tr class="zwischensumme" id="zwischensumme_'+value.id+'"><td>'+auswahlcbox+'</td><td colspan='+tmpanzcolsm1+'><b>Zwischensumme '+name+'</b></td>'+tmpd+'<td nowrap align="right">'+DrawIcons(value.postype, value.id)+'&nbsp;</td></tr>');
              break;
              case 'gruppensumme':
                var name = '';
                if(typeof value.wert != 'undefined' && typeof value.wert.name != 'undefined')name = value.wert.name;
                if(name==null)name='';
                tmpanzcolsm1 = 5- (('lieferschein'=='[DOCTYP]' || 'retoure'=='[DOCTYP]' || 'preisanfrage'=='[DOCTYP]')?1:0);
                tmpd = '<td><span class="cgruppensumme"></span></td><td colspan="'+(anzcolsm1-6)+'"></td>';
                $(tr).after('<tr class="gruppensumme" id="gruppensumme_'+value.id+'"><td>'+auswahlcbox+'</td><td colspan='+tmpanzcolsm1+'><b>Gruppensumme '+name+'</b></td>'+tmpd+'<td nowrap align="right">'+DrawIcons(value.postype, value.id)+'&nbsp;</td></tr>');
              break;
              case 'gruppensummemitoptionalenpreisen':
                    var name = '';
                    if(typeof value.wert != 'undefined' && typeof value.wert.name != 'undefined')name = value.wert.name;
                    if(name==null)name='';
                    tmpanzcolsm1 = 5- (('lieferschein'=='[DOCTYP]' || 'retoure'=='[DOCTYP]' || 'preisanfrage'=='[DOCTYP]')?1:0);
                    tmpd = '<td><span class="cgruppensummemitoptionalenpreisen"></span></td><td colspan="'+(anzcolsm1-6)+'"></td>';
                    $(tr).after('<tr class="gruppensummemitoptionalenpreisen" id="gruppensummemitoptionalenpreisen_'+value.id+'"><td>'+auswahlcbox+'</td><td colspan='+tmpanzcolsm1+'><b>Gruppensumme (mit optionalen Preisen) '+name+'</b></td>'+tmpd+'<td nowrap align="right">'+DrawIcons(value.postype, value.id)+'&nbsp;</td></tr>');
                    break;
              case 'gruppe':
                var name = '';
                if(typeof value.wert != 'undefined' && typeof value.wert.name != 'undefined')name = value.wert.name;
                if(name==null)name='';
                extname = '';
                if(typeof value.kurztext != 'undefined' && value.kurztext != null && value.kurztext != '' && typeof value.erweiterte_positionsansicht != 'undefined' && value.erweiterte_positionsansicht == 1)
                {
                  extname = '<br>'+value.kurztext;
                }
                $(tr).after('<tr class="gruppe" id="gruppe_'+value.id+'"><td>'+auswahlcbox+'</td><td colspan='+anzcolsm1+'><b>Gruppe '+name+'</b>'+extname+'</td><td nowrap align="right">'+DrawIcons(value.postype, value.id)+'&nbsp;</td></tr>');
              break;

              case 'seitenumbruch':
                $(tr).after('<tr class="seitenumbruch" id="seitenumbruch_'+value.id+'"><td>'+auswahlcbox+'</td><td colspan='+anzcolsm1+'><b>Seitenumbruch</b></td><td nowrap align="right">'+DrawIcons(value.postype, value.id)+'&nbsp;</td></tr>');
              break;
              case 'bild':
                var name = '';
                if(typeof value.wert != 'undefined' && typeof value.wert.name != 'undefined')name = value.wert.name;
                if(name==null)name='';
                extname = '';
                if(typeof value.kurztext != 'undefined' && value.kurztext != null && value.kurztext != '' && typeof value.erweiterte_positionsansicht != 'undefined' && value.erweiterte_positionsansicht == 1)
                {
                  extname = '<br>'+value.kurztext;
                }
                $(tr).after('<tr class="gruppe" id="bild_'+value.id+'"><td>'+auswahlcbox+'</td><td colspan='+anzcolsm1+'><b>Bild</b></td><td nowrap align="right">'+DrawIcons(value.postype, value.id)+'&nbsp;</td></tr>');
              break;
            }
          }
        }
      });
      if('[DOCTYP]'!== 'bestellung' && ('[DOCTYP]' !== 'lieferschein' || 'retoure' === '[DOCTYP]') && '[DOCTYP]' !== 'preisanfrage') {
              if(typeof aktuallisierePreise !== "function"){
                   return;
              }

              aktuallisierePreise();
      }
    }
    RecolourTable();
  }
  
  function DrawIcons(typ, id)
  {
    html = '';
    if(schreibschutz)return html;
    html = html + '<a href="#" onclick="window.location.href=\'index.php?module='+doctyp+'&action=positionen&cmd=downdrawitem&sid='+id+'&id=[ID]&fmodul=[FMODUL]\';"><img src="./themes/'+drawtheme+'/images/up.png" border="0" /></a> ';
    html = html + '<a href="#" onclick="window.location.href=\'index.php?module='+doctyp+'&action=positionen&cmd=updrawitem&sid='+id+'&id=[ID]&fmodul=[FMODUL]\';"><img src="./themes/'+drawtheme+'/images/down.png" border="0" /></a> ';
    html = html + '<a href="#" title="&auml;ndern" onclick=drawedit("'+typ+'",'+id+')><img src="./themes/'+drawtheme+'/images/edit.svg" border="0" /></a> ';
    html = html + '<a href="#" title="kopieren" onclick="window.location.href=\'index.php?module='+doctyp+'&action=positionen&cmd=copydrawitem&sid='+id+'&id=[ID]&fmodul=[FMODUL]\';"><img src="./themes/'+drawtheme+'/images/copy.svg" border="0" /></a> ';
    html = html + '<a href="#" title="l&ouml;schen" onclick="if(!confirm(\'Wirklich löschen?\')) return false; else window.location.href=\'index.php?module='+doctyp+'&action=positionen&cmd=deldrawitem&sid='+id+'&id=[ID]&fmodul=[FMODUL]\';" ><img src="./themes/'+drawtheme+'/images/delete.svg" border="0" /></a>';
    return html;
  }
  
  function drawdown(typ, id)
  {
    alert('nicht implementiert');
  }
  
  function drawup(typ, id)
  {
    alert('nicht implementiert');
  }
  
  function drawedit(typ, id)
  {
    if(id == 0)
    {
      $('#feldart2').val($('#feldart').val());
      $('#grbez').val('');
      $('#grtext').val('');
      $('#bildbreite').val('');
      $('#bildhoehe').val('');
    }else{
      $('#draweditid').val(id);
      $(drawpositionen).each(function(key, value){
        if(typeof value.id != 'undefined')
        {
          if(value.id == id)
          {
            $('#feldart2').val(value.postype);
            $('#grbez').val(value.name);
            $('#grtext').val(value.kurztext);
            if(typeof value.bildbreite != 'undefined')$('#bildbreite').val(value.bildbreite);
            if(typeof value.bildhoehe != 'undefined')$('#bildhoehe').val(value.bildhoehe);
          }
        }
      
      });
      
      
    }
    $('#feldart2').trigger('change');
    $('#draweditpopup').dialog('open');

  }
  
  function getLastPos()
  {
    var last = 0;
    if(schreibschutz)
    {
      $('.mkTable > tbody > tr').each(function(){
        $(this).children('td').first().each(function(){
          if($(this).html() != '')
          {
            var nr = parseInt($(this).html());
            if(!isNaN(nr))last = nr;
          }
        });
      });
    }else{
      $('#tableone tr').each(function(){
        if(typeof this.id != 'undefined')last = parseInt(this.id);
      });
    }

    if(schreibschutz)
    {
      
    }else{
      last = last - (('lieferschein'=='[DOCTYP]' || 'bestellung' == '[DOCTYP]' || 'preisanfrage'=='[DOCTYP]')?1:2);
    }
    if(last < 0)last = 0;
    return last;
  }
  
  function foundTrPos(pos)
  {
    var el = null;
    var max = getLastPos();
    if(pos > max)pos = max;
    if(schreibschutz)
    {
      $('.mkTable > tbody').children('tr').each(function(){
        var akt = this;
        if(pos == 0 && el == null)
        {
          el = akt;
          return el;
        }
        $(this).children('td').first().each(function(){
          if($(this).html() != '')
          {
            if(pos == parseInt($(this).html()))
            {
              el = akt;
            }
          }
        });
      });
      return el;
    }else{
      $('#'+pos).each(function(){el = this});
      if(el !== null)
      {
        var foundpos = pos + ('lieferschein'=='[DOCTYP]' || 'bestellung'=='[DOCTYP]' || 'preisanfrage'=='[DOCTYP]')?1:2;
        if(schreibschutz)
        {
          foundpos--;
        }
        var found = false;
        $('#'+foundpos).each(function(){found = true});
        if(found)return el;
        //find last
        while(foundpos > 0 || !found)
        {
          foundpos--;
          $('#'+foundpos).each(function(){found = true});
        }
        foundpos -= ('lieferschein'=='[DOCTYP]' || 'bestellung'=='[DOCTYP]' || 'preisanfrage'=='[DOCTYP]')?1:2;
        if(schreibschutz)foundpos++;
        if(foundpos < 0)foundpos = 0;
        el = null;
        $('#'+foundpos).each(function(){el = this});
        return el;
      }
      return null;
    }
  }
  
  function movePos(trtyp, trselid, trtyp2, trselid2)
  {
    $('body').append('<div class="ui-front ui-widget-overlay"></div>');
    $('.ui-widget-overlay').css('display','');
    window.location.href =  'index.php?module='+doctyp+'&action=positionen&cmd=drawmove&fmodul=[FMODUL]&sid='+trselid+'&id=[ID]'+'&styp='+trtyp+'&styp2='+trtyp2+'&sid2='+trselid2;
  }
  
</script>
<div id="draweditpopup"><input type="hidden" id="draweditid" value="">
<table class="mkTableFormular">
<tr><td>Spezialfeld:</td><td><select name="feldart2" id="feldart2">
        <option value="gruppe">Gruppen&uuml;berschrift</option>
        <option value="zwischensumme">Zwischensumme</option>
        <option value="gruppensumme">Gruppensumme</option>
        [GRUPPENSUMMEMITOPTIONALENPREISEN]
        <option value="seitenumbruch">Seitenumbruch</option>
        <option value="bild">Bild</option>
        </select></td></tr>
<tr id="trbez"><td>{|Bezeichnung|}:</td><td><input type="text" name="grbez" value="" id="grbez" size="80"/></td></tr>
<tr id="trtext"><td>{|Beschreibung|}:</td><td><textarea id="grtext"  name="grtext" rows="5" cols="80"></textarea></td></tr>
<tr class="trbild"><td>{|Bild|}:</td><td>
<form method="POST" enctype="multipart/form-data" id="bildform" action="#">
<input type="hidden" name="postype" value="bild">
<input type="file" id="positionsbild" name="bild" /><input type="submit" style="display:none;" />
<br />
{|Breite|}: <input type="text" name="bildbreite" id="bildbreite" size="6" /> {|H&ouml;he|}: <input type="text" size="6"  id="bildhoehe" name="bildhoehe" /> <i>{|in mm (falls leer: 30 x 30)|}</i>
</form></td></tr>
</table>
</div>
