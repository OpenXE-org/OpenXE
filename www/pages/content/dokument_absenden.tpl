<fieldset><legend>{|Versand|}</legend>
<form action="" method="post" name="eprooform">
<table width="100%" border="0">
	<tr id="selectrow" style="[SELECTUNSICHTBAR]"><td width="70">An:</td><td><select name="ansprechpartner">[ANSPRECHPARTNER]</select></td></tr>
  <tr id="manuellrow" style="[MANUELLUNSICHTBAR]"><td width="70">An:</td><td><input type="text" name="ansprechpartnermanuell" id="ansprechpartnermanuell" value="[ANSPRECHPARTNERMANUELL]" [ANSPRECHPARTNERMANUELLDISABLED] size="90"></td></tr>
  <tr><td></td><td><input type="checkbox" id="ansprechpartnermanuellverwenden" name="ansprechpartnermanuellverwenden" onchange="manuellchange(this)" [ANSPRECHPARTNERMANUELLVERWENDEN]>&nbsp;<label for="ansprechpartnermanuellverwenden"><i>E-Mail Adresse manuell angeben</i></td></label></tr>
	<tr><td width="70">CC:</td><td><input type="text" size="90" name="cc"> <i>(weitere E-Mails per Komma getrennt)</i></td></tr>
	<tr><td width="70">BCC:</td><td><input type="text" size="90" name="bcc"> <i>(weitere E-Mails per Komma getrennt)</i></td></tr>
	<tr><td width="70"></td><td>&nbsp;</td></tr>
<!--	<tr valign="top"><td width="70">Projekt:</td><td>[PROJEKTSTART]<input type="text" name="projekt" id="projekt" value="[PROJEKT]">[PROJEKTENDE]</select></td></tr>-->
	<tr><td>Betreff:</td><td><input type="text" name="betreff" value="[BETREFF]" size="70" id="betreff">&nbsp;<input type="button" value="Vorlage laden" name="vorlage" onclick="LoadGeschaeftsbriefvorlage('[SID]','[TYP]');"></td></tr>
	<tr valign="top"><td>Text:</td><td>
<table width="100%">
<tr valign="top"><td>
<textarea name="text" id="text" cols="60" rows="16">[TEXT]</textarea><br><i>(Signatur f&uuml;r E-Mail wird automatisch angeh&auml;ngt</i>)</td>
<td>
<fieldset><legend>{|Anhang|}</legend>
<table style="width:400px" class="mkTable" cellpadding=0 cellspacing=0><tr><th width="20"></th><th colspan="2">Dateiname</th></tr>[DATEIANHAENGE]

<tr><td colspan="3"><input type="checkbox" name="alleartikel" id="alleartikel" value="1" [ALLEARTIKEL]>&nbsp;Anhänge von Artikel anzeigen&nbsp;
<input type="checkbox" name="sammelpdf" id="sammelpdf" value="1" [SAMMELPDF]>&nbsp;Sammelpdf</td></tr>
</table>

</fieldset>
<fieldset><legend>{|Abschicken|}</legend>

			<table>
				<tr valign="top"><td><input type="checkbox" [BRIEFCHECKED] name="senden[]" value="brief"> per Drucker</td><td><select name="drucker_brief">[DRUCKER]</select></td></tr>
				<tr valign="top"><td><input type="checkbox" [FAXCHECKED] name="senden[]" value="fax" [ABPERFAX]> per Fax</td><td><select name="drucker_fax">[FAX]</select></td></tr>
        <tr valign="top"><td>Faxnummer:</td><td><input type="text" name="faxnummer" value="[FAXNUMMER]"></td></tr>
				<tr valign="top"><td><input type="checkbox" id="emailchecked" [EMAILCHECKED] name="senden[]" value="email"> per E-Mail</td><td><select name="email_from">[EMAILEMPFAENGER]</select>[EMAILHOOK]</td></tr>
				<tr valign="top"><td><input type="checkbox" [TELEFONCHECKED] name="senden[]" value="telefon"> Telefongespr&auml;ch</td><td></td></tr>
				<tr valign="top"><td><input type="checkbox" [SONSTIGESCHECKED] name="senden[]" value="sonstiges"> Sonstiges</td><td><i>(markieren als versendet)</i></td></tr>
			</table>
</fieldset>
<br>
<input type="submit" value="[KURZUEBERSCHRIFTFIRSTUPPER] abschicken oder als versendet markieren" name="submit">
<!--&nbsp;<input type="submit" value="Anschreiben nur speichern" name="speichern">-->

</td></tr></table>


	<tr><td></td><td align="center"><br></td></tr>


	<tr valign="top"><td>Versand:</td><td>[HISTORIE]</td><td></td></tr>


	<tr valign="top"><td>Protokoll:</td><td>[PROTOKOLL]</td><td></td></tr>
<!--<tr><td>Status:</td><td>TABELLE</td><td></td></tr>-->
</table>
<br><br>
<input type="hidden" name="sid" value="[SID]">
<input type="hidden" name="typ" value="[TYP]">
<input type="hidden" id="pran_adressids" name="pran_adressids" value="[PRANADRESSIDS]">
</form>
</fieldset>

<div id="geschaeftsbriefvorlage-confirm" title="Gesch&auml;ftsbriefvorlage neu laden?" style="display: none;">
  <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>Soll der Text neu geladen werden?</p>
</div>

<script>
function daup(daid)
{
  $.ajax({
    url: "index.php?module=[TYP]&action=edit&cmd=daup&id=[ID]",
    timeout:60000,
    type: 'POST',
    dataType: 'json',
    data: {
        da_id: daid
    }}).done( function(data) {
      if(typeof data.status != 'undefined' && data.status == 1 && typeof data.from != 'undefined')
      {
        var tdfirst = $('#td_'+daid).parent();
        var tmphtml = $(tdfirst).html();
        var tdsecond = $('#td_'+data.from).parent();
        $(tdfirst).html($(tdsecond).html());
        $(tdsecond).html(tmphtml);
      }
    }).fail( function( jqXHR, textStatus ) {
    
   });
}

function dadown(daid)
{
  $.ajax({
    url: "index.php?module=[TYP]&action=edit&cmd=dadown&id=[ID]",
    timeout:60000,
    type: 'POST',
    dataType: 'json',
    data: {
        da_id: daid
    }}).done( function(data) {
      if(typeof data.status != 'undefined' && data.status == 1 && typeof data.from != 'undefined')
      {
        var tdfirst = $('#td_'+daid).parent();
        var tmphtml = $(tdfirst).html();
        var tdsecond = $('#td_'+data.from).parent();
        $(tdfirst).html($(tdsecond).html());
        $(tdsecond).html(tmphtml);
      }
    }).fail( function( jqXHR, textStatus ) {

   });
}

function manuellchange(cb){
  if(cb.checked){
    document.getElementById('manuellrow').style.display = '';
    document.getElementById('selectrow').style.display = 'none';
  }else{
    document.getElementById('manuellrow').style.display = 'none';
    document.getElementById('selectrow').style.display = '';
  }
}

</script>
