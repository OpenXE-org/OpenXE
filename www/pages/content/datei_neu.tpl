<table border="0" width="100%">
<tr><td><table width="100%"><tr><td>
<form action="" method="post" name="eprooform" enctype="multipart/form-data" >

<table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="70%">
      <tr classname="orange1" class="orange1" bordercolor="" align="" bgcolor="" height="" valign="">
        <td colspan="4" bordercolor="" class="" align="" bgcolor="" height="" valign="">Datei Eigenschaften<br></td>
      </tr>
            <tr><td colspan="4">[ERROR]</td></tr>
            <tr><td><b>Eigenschaften</b></td><td></td><td>&nbsp;</td><td></td></tr>

	    <tr><td>Datei:</td><td colspan="3"><input type="file" name="upload"></td></tr>
[STARTDISABLE]
	    <tr><td>Titel</td><td colspan="3"><input type="text" size="30" name="titel" value="[TITEL]"></td></tr>
	    <tr><td>Beschreibung</td><td colspan="3"><textarea cols="35" rows="5" value="beschreibung">[BESCHREIBUNG]</textarea></td></tr>


            <tr><td><br></td><td></td><td></td><td></td></tr>
	    <tr valign="middle"><td>Stichwort:</td><td colspan="3">
	      <table>
	      <tr> 
	      <td><select name="huhu" onchange="document.getElementById('subjekt').value=document.eprooform.huhu.options[document.eprooform.huhu.selectedIndex].value">
		<option value=""></option>
		<option value="Bild">Bild</option>
		<option value="Schaltplan">Schaltplan</option>
		<option value="Studentennachweis">Studentennachweis</option>
		<option value="Datenblatt">Datenblatt</option>
		<option value="Software">Software</option>
		<option value="Datenblatt">Fax</option>
		<option value="Datenblatt">Brief</option>
		<option value="Datenblatt">Konstruktionsdaten</option>
		<option value="Schaltplan">UST-Pr&uuml;fung</option>
	      </select></td>
	      <td>von</td>
	      <td><select> 
		<option value="Projekt">Projekt %</option>
		<option value="Kunde">Kunde %</option>
		<option value="Artikel">Artikel %</option>
		<option value="Auftrag">Auftrag %</option>
		</select></td>
	      </tr>
	      <tr>
	      <td><input type="text" size="15" name="subjekt" id="subjekt" value="[SUBJEKT]"></td>
	      <td>&nbsp;</td>
	      <td><input type="text" size="15" name="objekt" value="[OBJEKT]"></td>
	      </tr>
	      </table>
	    </td></tr>
	    <tr><td><br><br></td><td></td><td></td><td></td></tr>
<!--
	    <tr><td>ISO9001:</td><td colspan="3"> <select name="gruppe">      
                  <option>keine</option>
                  <option>100 Organigramme</option>
                  <option>105 Pl&auml;ne</option>
                  <option>110 Festlegungen</option>
                  <option>115 Protokolle</option>
                  <option>125 Stellenbeschreibung</option>
                  <option>610 Schaltpl&auml;ne</option>
                  <option>611 BOM</option>
              </select></td></tr>
-->
<!--
	    <tr><td>Dokumentenummer:</td><td colspan="3">
	      <table>
	      <tr><td>Gruppe</td><td><b>L</b>aufindex</td><td><b>V</b>ersion</td></tr>
	      <tr valign="top"><td>
              <select name="gruppe">	  
		  <option>keine</option>
		  <option>100 Organigramme</option>
		  <option>105 Pl&auml;ne</option>
		  <option>110 Festlegungen</option>
		  <option>115 Protokolle</option>
		  <option>125 Stellenbeschreibung</option>
		  <option>610 Schaltpl&auml;ne</option>
		  <option>611 BOM</option>
	      </select></td><td>
	      <input type="text" size="10" name="nummer" value="[NUMMER]"><br>
	      <input type="checkbox" name="automatisch">L + V automatisch
	      </td><td>
              <select name="version"><option value=""></option><option value="A">A</option><option value="B">B</option></select></td></tr></table>
	      </td></tr>
-->
[ENDEDISABLE]
    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="4" bordercolor="" classname="orange2" class="orange2">
    <input type="submit" name="speichern"
    value="Speichern" /> <input type="button" value="Abbrechen" /></td>
    </tr>
  </table>

  </form>
</td></tr></table></td></tr>
</table>
