<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs1"></a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs1">
[MESSAGE]
<form action="" method="post" name="eprooform">
[FORMHANDLEREVENT]

  <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>
      <tr valign="top" colspan="3">
        <td >
<fieldset><legend>{|Einstellung|}</legend>
    <table width="100%" class="mkTableFormular">
   	<tr><td>{|Kunde|}:</td><td>[ADRESSE][MSGADRESSE]</td><td><input type="button" class="btnGreenNew" onclick="aufgabenanlegen([ID]);" value="&#10010; Aufgaben anlegen"></td></tr>
   	<tr><td>{|Mitarbeiter|}:</td><td colspan="2">[MITARBEITER][MSGMITARBEITER]</td></tr>
   	<tr><td>{|Version|}:</td><td colspan="2">[VERSION][MSGVERSION]</td></tr>
   	<tr><td>{|Status|}:</td><td colspan="2">[STATUS][MSGSTATUS]&nbsp;Intervall:&nbsp;[INTERVALL][MSGINTERVALL]</td></tr>
   	<tr><td>{|Startdatum|}:</td><td colspan="2">[STARTDATUM][MSGSTARTDATUM]&nbsp;Zeit geplant (in h):&nbsp;[ZEITGEPLANT][MSGZEITGEPLANT]</td></tr>
        <tr><td>{|Bemerkung|}:</td><td colspan="2">[BEMERKUNG][MSGBEMERKUNG]</td><td></tr>

   	<tr><td>{|Phase|}:</td><td>[PHASE][MSGPHASE]</td></tr>
</table></fieldset>

</td></tr>

    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
    <input type="submit" value="Speichern" name="submit"/>
    </tr>

    </tbody>
  </table>
</form>

</div>

<!-- tab view schließen -->
</div>

<script>
    function aufgabenanlegen(id) {
        $.ajax({
            url: 'index.php?module=wawisioneinrichtung&action=edit&cmd=getaufgabeninfo&id='+id,
            data: {
            },
            method: 'post',
            dataType: 'json',
            beforeSend: function() {
            },
            success: function(data) {
                if(!confirm(data)) return false;
                doaufgabenalegen(id);
            }
        });
    }

    function doaufgabenalegen(id){
        $.ajax({
            url: 'index.php?module=wawisioneinrichtung&action=edit&cmd=createaufgaben&id='+id,
            data: {
            },
            method: 'post',
            dataType: 'json',
            beforeSend: function() {
            },
            success: function(data) {
                alert('Aufgaben angelegt.');
            }
        });
    }

</script>