<script>
$( document ).ready(function() {

if($('#steuersatz').val()=="")
{ 
  $('#anderersteuersatz').prop('checked', false);
  $('.steuersatz').hide();
  $("#umsatzsteuer").prop('disabled', false);
}
else
{ 
  $('.steuersatz').show();
  $('#anderersteuersatz').prop('checked', true);
  $("#umsatzsteuer").prop('disabled', 'disabled');
}


$('#anderersteuersatz').click(function() {        if (!$(this).is(':checked')) {            $('.steuersatz').hide();
            $('#steuersatz').val('');
            $("#umsatzsteuer").prop('disabled', false);
        } else {
            $('.steuersatz').show();
            $("#umsatzsteuer").prop('disabled', 'disabled');        }
    });
});
</script>
<form action="" method="post" name="eprooform">
[FORMHANDLEREVENT]
 <table border="0" style="padding-right:10px;" class="mkTableFormular">
            <tbody>
              <tr><td nowrap>{|Artikel-Nr|}:</td><td>[ARTIKEL][MSGARTIKEL]</td></tr>
	      <tr><td nowrap>{|Beschreibung|}:</td><td>[BEZEICHNUNG][MSGBEZEICHNUNG]</td></tr>
	      <tr><td nowrap>{|Artikel-Nr|}:</td><td>[NUMMER][MSGNUMMER]</td></tr>
	      <tr><td>{|Beschreibung|}:</td><td>[BESCHREIBUNG][MSGBESCHREIBUNG]</td></tr>
	      <tr><td>{|Menge|}:</td><td>[MENGE][MSGMENGE]</td></tr>
	      <tr><td>{|Preis|}:</td><td>[PREIS][MSGPREIS]</td></tr>
	      <tr><td>{|W&auml;hrung|}:</td><td>[WAEHRUNG][MSGWAEHRUNG]&nbsp;[WAEHRUNGSBUTTON]</td></tr>
                <tr><td>{|Steuersatz|}:</td><td>[UMSATZSTEUER][MSGUMSATZSTEUER]&nbsp;

[ANDERERSTEUERSATZ][MSGANDERERSTEUERSATZ]&nbsp;individuellen Steuersatz verwenden
</td></tr>
        <tr style="display:none" class="steuersatz"><td>{|Individueller Steuersatz|}:</td><td>[STEUERSATZ][MSGSTEUERSATZ]&nbsp;in Prozent</td></tr>
        <tr><td>{|Rechtlicher Steuerhinweis|}:</td><td>
                [STEUERTEXT][MSGSTEUERTEXT]
        </td></tr>
        <tr><td>{|Einheit|}:</td><td>[EINHEIT][MSGEINHEIT]</td></tr>
	      <tr><td>{|VPE|}:</td><td>[VPE][MSGVPE]</td></tr>
	      <tr><td>{|Projekt|}:</td><td>[PROJEKT][MSGPROJEKT]</td></tr>
        <tr><td>{|Kostenstelle|}:</td><td>[KOSTENSTELLE][MSGKOSTENSTELLE]</td></tr>
	      <tr><td>{|Lieferdatum|}:</td><td>[LIEFERDATUM][MSGLIEFERDATUM]</td></tr>
                    <tr><td></td><td><input type="submit" value="Speichern" ></td></tr>

</tbody></table>
</form>
[WAEHRUNGSTABELLE]
