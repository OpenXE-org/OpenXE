<div id="tabs-8">
<script type="text/javascript">
function testcustomfile()
{
  $.ajax({
    url: 'index.php?module=onlineshops&action=edit&cmd=testcustomfile&id=[ID]',
    type: 'POST',
    dataType: 'json',
    data: { },
    fail : function(  ) {
                alert('Parseerror');
            },
    error : function() {
                alert('Parseerror');
            },
    success: function(data) {
      if(typeof data.status !== 'undefined' && data.status == 1)
      {
        alert('Test OK');
      }else{
        alert('Fehler beim Testen');
      }
    },
    
  });

}
</script>
[MESSAGE]
[TAB8]
<form method="POST" action="index.php?module=onlineshops&action=edit&id=[ID]#tabs-8">
<fieldset><legend>[DATEINAME]</legend>
<textarea id="customdatei" name="customdatei" style="width:90%;min-height:500px;">[CUSTOMDATEI]</textarea>
</fieldset>
<div style="clear:both;"></div>
<div style="float:right;"><input type="button" onclick="testcustomfile();" value="Datei auf Parseerror Testen (zuvor abspeichern)" />&nbsp;<input type="submit" name="savefile" value="speichern" /></div>
</form>
[TAB8NEXT]
</div>


