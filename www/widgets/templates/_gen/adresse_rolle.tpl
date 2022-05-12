<script type="text/javascript">

$(document).ready(function(){

  art = document.getElementById('objekt');
  projekte = document.getElementById('parameter');
  gruppen = document.getElementById('gruppe');

  if (art) {
		if (art.options[art.selectedIndex].value =='Gruppe') {
        projekte.style.display='none';
        gruppen.style.display='';
      }
      if (art.options[art.selectedIndex].value =='Projekt') {
        projekte.style.display='';
        gruppen.style.display='none';
      }

      art.onchange=function() {
			if (art.options[art.selectedIndex].value =='Gruppe') {
        projekte.style.display='none';
        gruppen.style.display='';
	
      }
      if (art.options[art.selectedIndex].value =='Projekt') {
        projekte.style.display='';
        gruppen.style.display='none';
      }	
      }
  }

});
 </script>

<table border="0" width="100%">
<tr><td><table width="100%"><tr><td>
<form action="" method="post" name="eprooform">
[FORMHANDLEREVENT]
  <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>

      <tr valign="top" colspan="3">
        <td>
             <table border="0" width="100%">
              <tbody>
                      <tr><td>{|Rolle|}:</td><td>[SUBJEKT][MSGSUBJEKT]</td>
                      <td>{|von|}:</td><td>[OBJEKT][MSGOBJEKT]</td>
 <td>
[PROJEKTSTART][PARAMETER][MSGPARAMETER][PROJEKTENDE]
[GRUPPESTART][GRUPPE][MSGGRUPPE][GRUPPEENDE]
</td><td><input type="submit" value="Rolle speichern" name="rolleanlegen"></td></tr>

              </tbody></table>
        </td>
      </tr>

  
    </tbody>
  </table>
  </form>
</td></tr></table></td></tr>
</table>

