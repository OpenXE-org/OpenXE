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
<div id="editvpepopup" style="display:none;">
<table>
<tr><td>{|Vorlage|}:</td><td><input type="hidden" id="vpe_lpiid" /><select id="vpe_vorlage" onchange="getvorlage();"><option value="">{|- bitte w&auml;hlen -|}</option>[SELECTVPE]</select></td></tr>
</table>

<table width="100%">
<tr><td width="100%">
<fieldset><legend>Abmessungen VPE</legend>
<table>
<tr><td>{|Menge in VPE|}:</td><td><input type="text" size="6" id="vpe_menge" />&nbsp;<i>Stück in VPE</i></td></tr>
<tr><td>{|Gewicht von VPE|}:</td><td><input type="text" size="6" id="vpe_gewicht" />&nbsp;<i>Gesamt in Kg</i></td></tr>
<tr><td>{|L&auml;nge von VPE|}:</td><td><input type="text" size="6" id="vpe_laenge" />&nbsp;<i>in cm</i></td></tr>
  <tr><td>{|Breite von VPE|}:</td><td><input type="text" size="6" id="vpe_breite" />&nbsp;<i>in cm</i></td></tr>
<tr><td>{|H&ouml;he von VPE|}:</td><td><input type="text" size="6" id="vpe_hoehe" />&nbsp;<i>in cm</i></td></tr>
</table>
</fieldset>
</td><!--<td>
<fieldset><legend>Abmessungen VPE von VPE</legend>
<table>
<tr><td>{|Menge VPE in VPE|}:</td><td><input type="text" size="6" id="vpe_menge2" />&nbsp;<i>Stück VPE in VPE</i></td></tr>
<tr><td>{|Gewicht VPE von VPE|}:</td><td><input type="text" size="6" id="vpe_gewicht2" />&nbsp;<i>Gesamt in Kg</i></td></tr>
<tr><td>{|L&auml;nge VPE von VPE|}:</td><td><input type="text" size="6" id="vpe_laenge2" />&nbsp;<i>in cm</i></td></tr>
<tr><td>{|Breite VPE von VPE|}:</td><td><input type="text" size="6" id="vpe_breite2" />&nbsp;<i>in cm</i></td></tr>
<tr><td>{|H&ouml;he in VPE von VPE|}:</td><td><input type="text" size="6" id="vpe_hoehe2" />&nbsp;<i>in cm</i></td></tr>
</table>
</fieldset>
</td>--></tr></table>
</div>
<script>
  $(document).ready(function() {
    $('#editvpepopup').dialog(
    {
      modal: true,
      autoOpen: false,
      minWidth: 440,
      title:'{|VPEs ändern|}',
      buttons: {
        '{|SPEICHERN|}': function()
        {

            $.ajax({
                url: 'index.php?module=artikel&action=lager&cmd=savevpe&id=[ID]',
                type: 'POST',
                dataType: 'json',
                data: { lpiid: $('#vpe_lpiid').val()
                ,gewicht:$('#vpe_gewicht').val()
                ,gewicht2:$('#vpe_gewicht').val()
                ,menge:$('#vpe_menge').val()
                ,menge2:$('#vpe_menge2').val()
                ,breite:$('#vpe_breite').val()
                ,breite2:$('#vpe_breite2').val()
                ,hoehe:$('#vpe_hoehe').val()
                ,hoehe2:$('#vpe_hoehe2').val()
                ,laenge:$('#vpe_laenge').val()
                ,laenge2:$('#vpe_laenge2').val()
                },
                success: function(data) {
                  window.location.href=window.location.href;
                },
                beforeSend: function() {

                }
            });

        },
        '{|ABBRECHEN|}': function() {
          $(this).dialog('close');
        }
      },
      close: function(event, ui){
        
      }
    });
 });
  
  
  function getvorlage()
  {
    let wert = $('#vpe_vorlage').val();
    $.ajax({
        url: 'index.php?module=artikel&action=lager&cmd=getvpevorlage&id=[ID]',
        type: 'POST',
        dataType: 'json',
        data: { vpeid: wert},
        success: function(data) {
          if(data != null && typeof data.menge != 'undefined')
          {
            $('#vpe_menge').val(data.menge);
            $('#vpe_breite').val(data.breite);
            $('#vpe_gewicht').val(data.gewicht);
            $('#vpe_hoehe').val(data.hoehe);
            $('#vpe_laenge').val(data.laenge);
            $('#vpe_menge2').val(data.menge2);
            $('#vpe_breite2').val(data.breite2);
            $('#vpe_gewicht2').val(data.gewicht2);
            $('#vpe_hoehe2').val(data.hoehe2);
            $('#vpe_laenge2').val(data.laenge2);
          }
        },
        beforeSend: function() {

        }
    });
    
  }
  
  function editvpe(id)
  {
    $('#vpe_lpiid').val(id);
    $.ajax({
        url: 'index.php?module=artikel&action=lager&cmd=getvpe&id=[ID]',
        type: 'POST',
        dataType: 'json',
        data: { lpiid: id},
        success: function(data) {
          $('#vpe_vorlage').val(data.id);
          $('#vpe_menge').val(data.menge);
          $('#vpe_breite').val(data.breite);
          $('#vpe_gewicht').val(data.gewicht);
          $('#vpe_hoehe').val(data.hoehe);
          $('#vpe_laenge').val(data.laenge);
          $('#vpe_menge2').val(data.menge2);
          $('#vpe_breite2').val(data.breite2);
          $('#vpe_gewicht2').val(data.gewicht2);
          $('#vpe_hoehe2').val(data.hoehe2);
          $('#vpe_laenge2').val(data.laenge2);
          $('#editvpepopup').dialog('open');
        },
        beforeSend: function() {

        }
    });
  }
</script>
