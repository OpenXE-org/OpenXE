

<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]
<h1><center>[OVBEVOR]Dieses Modul ist erst ab Version [VERS] verf&uuml;gbar[OVNACH][OVALTMESSAGE]</center></h1>

<table width="100%">
<tr><td width="50%" valign="top" align="center">
<img src="./themes/new/images/wawibox1-300x230.png">
<br>
<ul>
<li><a href="http://www.wawision.de" target="_blank">Zur Homepage</a></li>
<li><a href="http://shop.wawision.de" target="_blank">AppStore, Hardware und Zubeh&ouml;r</a></li>
</ul>

</td><td width="50%" valign="top">

<table width="100%">
<tr><td width="140">Firma:*</td><td><input type="text" id="firma" style="width:100%;max-width:300px;" /></td></tr>
<tr><td>Anprechpartner:</td><td><input type="text" id="ansprechpartner" style="width:100%;max-width:300px;" /></td></tr>
<tr><td>Telefon:</td><td><input type="text" id="telefon" style="width:100%;max-width:300px;" /></td></tr>
<tr><td>Email:</td><td><input type="text" id="email" style="width:100%;max-width:300px;" /></td></tr>
<tr><td>Nachricht:*</td><td><textarea id="nachricht" style="width:100%;max-width:300px;height:150px;"></textarea></td></tr>
<tr><td>Teamview Termin</td><td><input type="checkbox" id="teamview" value="1" /></td></tr>
<tr><td>R&uuml;ckruf:</td><td><input type="checkbox" id="ruf" value="1" /></td></tr>
<tr><td>Angebot:</td><td><input type="checkbox" id="angebot" value="1" /></td></tr>
<tr><td></td><td><input type="button" id="abschicken" name="abschicken" value="Anfrage senden" /></td></tr>
</table>
</td></tr>
</table>
<script type="text/javascript">

  $(document).ready(function() {
    $('#abschicken').on('click',function(){
      var fehler = false;
      
      if($('#ruf').prop('checked') && !$('#telefon').val())fehler = 'Bitte geben Sie eine Telefonnummer ein';
      if(!$('#nachricht').val())fehler = 'Bitte geben Sie eine Nachricht ein';
      if(!$('#firma').val())fehler = 'Bitte geben Sie einen Firmennamen ein';
      if(!fehler)
      {
        var subject = 'Anfrage Version/Modul: [MODUL]';
        var body = "Firma: "+$('#firma').val();
        body += "\r\nAnprechpartner: "+$('#ansprechpartner').val();
        body += "\r\nTelefon: "+$('#telefon').val();
        body += "\r\nTeamview Termin: "+($('#teamview').prop('checked')?'ja':'nein');
        body += "\r\nRückruf: "+($('#ruf').prop('checked')?'ja':'nein');
        body += "\r\nAngebot: "+($('#angebot').prop('checked')?'ja':'nein');
        body += "\r\nNachricht:\r\n"+$('#nachricht').val();
        
      
        window.location.href = "mailto:info@embedded-projects.net?subject="+encodeURIComponent(subject)+"&body="+encodeURIComponent(body);
      }else{
      alert(fehler);
      }
    })
  
  });
</script>


[TAB1NEXT]
</div>

<!-- tab view schließen -->
</div>
