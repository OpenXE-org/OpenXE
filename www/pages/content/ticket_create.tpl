<script type="text/javascript" src="./js/einfuegen.js" ></script>
<form method="POST" id="eprooform" enctype="multipart/form-data">

<table width=100%"><tr valign="top"><td>
<fieldset><legend>{|Nachricht|}</legend>
<table>
<tr><td width="200">Von:</td><td><select name="quelle" id="quelle"/>[QUELLE]</select></td></tr>
<tr><td>Kunde:</td><td><input type="text" name="adresse" onchange="changeemail()" id="adresse" size="80" /></td></tr>
<tr><td>E-Mail:</td><td><input type="text" name="email" id="email" size="80" /></td></tr>
<tr><td>E-Mail CC:</td><td><input type="text" name="emailcc" id="emailcc" size="80" /></td></tr>
<tr><td>Betreff:</td><td><input type="text" name="betreff" id="betreff" size="80" /></td></tr>
<tr><td>Nachricht:</td><td><textarea id="eingabetext" name="eingabetext" cols="90" style="min-height:200px;">[TEXT]</textarea></td></tr>
<tr><td>Anhang 1:</td><td><input type="file" name="datei[]" /></td></tr>
<tr><td>Anhang 2:</td><td><input type="file" name="datei[]" /></td></tr>
<tr><td>Anhang 3:</td><td><input type="file" name="datei[]" /></td></tr>
<tr><td>Anhang 4:</td><td><input type="file" name="datei[]" /></td></tr>
<tr><td>Anhang 5:</td><td><input type="file" name="datei[]" /></td></tr>
<tr><td></td><td><input type="submit" name="speichern" id="speichern" value="Speichern" />&nbsp;<input type="submit" name="senden" id="senden" value="Speichern und Senden" /></td></tr>
</table>
</fieldset>
</td><td width="30%">

Textvorlagen:<br>
<select name="vorlage" id="vorlage"  onchange="changevorlage(this)"><option value=""> - </option>[VORLAGE]</select>


</td></tr>
</table>
</form>
<script>
  var vorlage = [];
  [VORLAGEARRAY]
  var kundennummer = '';
  



function changeemail()
{
      if($('#adresse').val() != kundennummer)
      {
      	kundennummer = $('#adresse').val();
        var kunde = kundennummer.split(' ');
        var kundenid = kunde[0];
        kunde[kunde.length - 1].replace(')', '');
        if(kunde[kunde.length - 1].replace(')', '') != '') {
	        $.ajax({
		        url: "index.php?module=ajax&action=filter&filtername=emailadresse&limit=1&kd_id="+kundenid,
		        type: 'GET',
		        dataType: 'json',
		        success: function (data) {
			        $("input#email").val(data[0]);
		        },
		        errror: function (data) {
			        $("input#email").val('');
		        },


	        });
        }

        $( "input#email" ).autocomplete({
          source: "index.php?module=ajax&action=filter&filtername=emailadresse&kd_id="+kundenid,
        });
      }
}
 

function changevorlage(id)
{
  if(typeof id == "undefined")id = $('#vorlage');
  	$.ajax({
	  url: 'index.php',
	  type: 'GET',
    dataType: 'json',
	  data: 'module=ticket&action=create&getvorlagen=1&kunde='+$('#adresse').val(),
      success: function(data) {
        if(typeof data.v  != "undefined")
        {
          $.each(data.v, function (k,va)
          {
            vorlage[k] = va;
          }
          );
          id = $(id).val();

          if(typeof (vorlage[id]) !== "undefined" )
          {
            einfuegenticket(vorlage[id]);
          }
        }        
      }
    });
}
  
$(document).ready(function() {
/*
  setInterval(function(){
      if($('#adresse').val() != kundennummer)
      {
      	kundennummer = $('#adresse').val();
        var kunde = kundennummer.split(' ');
        var kundenid = kunde[0];
        kunde[kunde.length - 1].replace(')', '');
        if(kunde[kunde.length - 1].replace(')', '') != '') {
	        $.ajax({
		        url: "index.php?module=ajax&action=filter&filtername=emailadresse&limit=1&kd_id="+kundenid,
		        type: 'GET',
		        dataType: 'json',
		        success: function (data) {
			        $("input#email").val(data[0]);
		        }
	        });
        }

        $( "input#email" ).autocomplete({
          source: "index.php?module=ajax&action=filter&filtername=emailadresse&kd_id="+kundenid,
        });
        changevorlage();
      }
  },100);
*/

  $('#eprooform').submit(function(){
    var fehler = '';
    if(!$('#adresse').val())fehler = fehler + ' Kein Kunde ausgewählt!';
    if(!$('#betreff').val())fehler = fehler + ' Kein Betreff eingegeben!';
    if(!$('#email').val())fehler = fehler + ' Keine E-Mail-Adresse ausgewählt!';
    if(!$('#eingabetext').val())fehler = fehler + ' Kein Text eingegben!';
    if(fehler == '')
    {
        return true;
    }
    else
    {
      alert(fehler);
      return false;
    }
  });
});
</script>
