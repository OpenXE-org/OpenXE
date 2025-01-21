<div id="tabs">
    <ul>
        <li><a href="#tab1">&Uuml;bersicht</a></li>
        <li><a href="#tab2">neue Version anlegen</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tab1">
<form action="" method="post" name="eprooform" enctype="multipart/form-data">
    <h2>Beschreibung</h2>
    <table border="0" width="100%">
	    <tr><td>Titel</td><td colspan="3"><input type="text" size="30" name="titel" value="[TITEL]"></td></tr>
	    <tr><td>Beschreibung</td><td colspan="3"><textarea cols="35" rows="5" name="beschreibung">[BESCHREIBUNG]</textarea></td></tr>
    </table>
    <h2>Versionen</h2>
    [VERSIONEN]
    <h2>Zuordnungen</h2>
    [STICHWOERTER]
    <table border="0" width="100%">
        <tr>
            <td width="" valign="" height="" bgcolor="" align="right" bordercolor="" classname="orange2" class="orange2">
                <input type="submit" name="titel_beschreibung_speichern" value="Speichern" />
            </td>
        </tr> 
        <tr>
            <td width="" valign="" height="" bgcolor="" align="right" bordercolor="" classname="orange2" class="orange2">
                <input type="submit" value="Abbrechen" />
            </td>
        </tr> 
    </table>
  </form>
</div>
<div id="tab2">
[NEUEVERSION]
</div>
</div>
