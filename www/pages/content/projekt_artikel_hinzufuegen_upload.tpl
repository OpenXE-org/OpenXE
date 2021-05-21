[MESSAGE]
<form id="frmupload" method="post" enctype="multipart/form-data">
<script>
function serlvorlage()
{
  var vorlageid = parseInt($('#vorlagesel').val());
  if(vorlageid > 0)
  {
    $.ajax({
        url: 'index.php?module=projekt&action=dashboard&cmd=getimportvorlage&id=[ID]',
        type: 'POST',
        dataType: 'json',
        data: { vorlage:vorlageid},
        success: function(data) {
          $('#importerstezeilenummer').val(data.importerstezeilenummer);
          $('#charset').val(data.charset);
          $('#importdatenmaskierung').val(data.importdatenmaskierung);
          $('#fields').val(data.fields);
          $('#importtrennzeichen').val(data.importtrennzeichen);
        },
        beforeSend: function() {

        }
    });
  }
}
$(document).ready(function() {serlvorlage();});
</script>
<input type="hidden" name="uploadteilprojekt" value="[UPLOADTEILPROJEKT]" id="uploadteilprojekt" />
<fieldset><legend>{|CSV Upload|}</legend>
<table class="mkTableFormular">
<tr><td>Datei:</td><td><input type="file" name="userfile">&nbsp;<input type="submit" value="hochladen" name="upload" /></td></tr>
<tr><td>Vorlage</td><td><select id="vorlagesel" name="vorlagesel" onchange="serlvorlage();"><option value="0"> - Auswahl - </option>[VORLAGESEL]</select></td></tr>
<tr><td>CSV Daten ab Zeile:</td><td><input type="text" readonly name="importerstezeilenummer" id="importerstezeilenummer" value="[IMPORTZEILENNUMMER]" /><i>Erste Zeile = 1 (Falls Daten in CSV nicht ab Zeile 1 starten, da Feldbezeichnungen o.ä. in Dokument vorhanden sind.)</i></td></tr>	
<tr><td>CSV Trennzeichen:	</td><td><select id="importtrennzeichen" readonly name="importtrennzeichen"><option value="semikolon">;</option><option value="komma" [SELKOMMA]>,</option></select></td></tr>
<tr><td>CSV Maskierung:	</td><td><select id="importdatenmaskierung" readonly name="importdatenmaskierung"><option value="keine">keine</option><option value="gaensefuesschen" [SELGAENSEFUESSCHEN]>"</option></select></td></tr>
<tr><td>Auswahl Charset:</td><td><select id="charset" readonly name="charset"><option>UTF8</option><option [SELISO88591]>ISO-8859-1</option><option [SELCP850]>CP850</option></select></td></tr>
<tr><td>CSV-Felder:</td><td><textarea name="fields" readonly id="fields" rows="15" cols="60">[FIELDS]</textarea></td></tr>
<!--<tr><td></td><td>[IMPORTBUTTON]</td></tr>-->
</table>
</fieldset>
<style type="text/css">
 table.importstyle
{
    border-width: 0 0 1px 1px;
    border-spacing: 0;
    border-collapse: collapse;
    border-style: solid;
}

.importstyle td, .importstyle th
{
    margin: 0;
    padding: 4px;
    border-width: 1px 1px 0 0;
    border-style: solid;
}
</style>
<form action="" method="post">
<div style="width:100%; overflow: auto">
<center>[IMPORTBUTTON]</center>
<br>
<table border="0" class="importstyle">
[ERGEBNIS]
</table>
<br>
</div>
<br><br>
<!--<center>[IMPORTBUTTON]</center>-->
</form>




</form>

