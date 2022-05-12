<script type="text/javascript">

  function GrZahlFormatieren(x) {
    x = parseFloat(x);
    var k = (x.toFixed(2)).toString().replace('.',',');
    var anzstellen = k.length;
    var vorzeichen = 0;           
    if(k.substring(0,1) == '-')
    {
      vorzeichen = 1;
    }
    if(anzstellen - 1 <= 6)return k;
    var vorzeichenstring = '';
    if(vorzeichen)vorzeichenstring = k.substring(0, 1);
    var vorkomma = k.substring(vorzeichen, anzstellen - 3);
    var ret = vorzeichenstring;
    var modstellen = vorkomma.length % 3;
    if(modstellen > 0)ret = ret + vorkomma.substring(0, modstellen)+'.';
    var nachkomma = k.substring(anzstellen - 3, anzstellen);
    
    var i = 0;
    for(i = 0; i < Math.floor(vorkomma.length / 3); i++)
    {
      if(i > 0)ret = ret + '.';
      ret = ret + vorkomma.substring(i*3+modstellen, (i+1)*3+modstellen);
    }
    ret = ret+nachkomma;
    return ret;
  }


  function grchangebetrag(nr)
  {
    var gesamtsumme = parseFloat($('#grgesamtsumme').val().replace(',','.'));
    var betrag = parseFloat($('#grbetrag_'+nr).val().replace(',','.'));
    $('#grprozent_'+nr).val('');
    ReplaceBezeichnung(nr);
    CalcEndbetrag();
  }
  
  function CalcEndbetrag()
  {
    var gesamtsumme = parseFloat($('#grgesamtsumme').val().replace(',','.'));
    var endbetrag = gesamtsumme;
    var prozent = 0;
    var betrag = 0;
    var gesamtrabattanz = $('#gesamtrabattanz').val();
    for(i = 1; i <= gesamtrabattanz; i++)
    {
      prozent = parseFloat($('#grprozent_'+i).val().replace(',','.'));
      betrag = parseFloat($('#grbetrag_'+i).val().replace(',','.'));
      if(!isNaN(prozent) && prozent != 0)
      {
        endbetrag = endbetrag - (Math.abs(prozent) / 100 * gesamtsumme);
      }else{
        if(!isNaN(betrag) && betrag != 0)
        {
          endbetrag = endbetrag - Math.abs(betrag);
        }
      }
    }
    $('#grendbetrag').val(GrZahlFormatieren(endbetrag));
  }
  
  function ReplaceBezeichnung(nr)
  {
    var prozent = parseFloat($('#grprozent_'+nr).val().replace(',','.'));
    var bezeichnung = $('#grrabatt_'+nr).val()+'';
    
    var patt = /([0-9\-\,\.]*)\%/i;
    var bezeichnung = $('#grrabatt_'+nr).val()+'';
    var result = patt.exec(bezeichnung);
    if(result != null)
    {
      bezeichnung = bezeichnung.replace(result[ 0 ] + '', '');
    }
    bezeichnung = trim(bezeichnung);
    $('#grrabatt_'+nr).val(bezeichnung);
    if(isNaN(prozent))return;
    if(prozent == 0)return;
    prozent = prozent + '';
    bezeichnung = bezeichnung + ' '+prozent.replace('.',',')+'%';
    $('#grrabatt_'+nr).val(bezeichnung);
    
  }

  function grchangeprozent(nr)
  {
    var gesamtsumme = parseFloat($('#grgesamtsumme').val().replace(',','.'));
    var prozent = parseFloat($('#grprozent_'+nr).val().replace(',','.'));
    $('#grbetrag_'+nr).val('');
    ReplaceBezeichnung(nr);
    CalcEndbetrag();
  }
  
  $(document).ready(function() {

    $('#gesamtrabattediv').dialog(
    {
      modal: true,
      autoOpen: false,
      minWidth: 940,
      title:'Rabatt',
      buttons: {
        SPEICHERN: function()
        {
          var dataarr = {gesamtrabattanz:$('#gesamtrabattanz').val()};
          $('#gesamtrabattedivinhalt input').each(function(){
          if(typeof this.name != 'undefined')
            {
              if(this.name)dataarr[this.name] = $(this).val();
            }
          });
        
          $.ajax({
              url: 'index.php?module=[MODULE]&action=positionen&cmd=updategesamtrabatte&id=[ID]',
              type: 'POST',
              dataType: 'text',
              data:  dataarr,
              success: function(data) {
                var urla = window.location.href.split('#');
                window.location.href=urla[ 0 ];
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
    $('#gesamtrabatte').on('click',function(){
      $.ajax({
        url: 'index.php?module=[MODULE]&action=positionen&cmd=getegesamtrabatte&id=[ID]',
        type: 'POST',
        dataType: 'text',
        data: { },
        success: function(data) {
          $('#gesamtrabattedivinhalt').html(data);
          $('#gesamtrabattediv').dialog('open');
          var gesamtrabattanz = $('#gesamtrabattanz').val();
          for(i = 1; i <= gesamtrabattanz; i++)
          {
            ReplaceBezeichnung(i);
          }
        },
        beforeSend: function() {

        }
      });
    });
  });
</script>
<div id="gesamtrabattediv" style="display:none">
<div id="gesamtrabattedivinhalt"></div>
</div>