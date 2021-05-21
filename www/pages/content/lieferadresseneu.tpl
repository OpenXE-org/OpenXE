<table>
          <tr>
	    <td>Typ:</td><td><select name="typ">
            <option value="person">Person</option><option value="firma">Firma</option></select></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
	    </tr>
          <tr><td>Name/Firma:</td><td><input type="text" name="name" size="20" rule="notempty" msg="Pflichtfeld!" value="[NAME]"></td>
          <td>&nbsp;</td>
            <td></td><td></td></tr>
          <tr><td>Abteilung:</td><td><input type="text" name="abteilung" size="20" value="[ABTEILUNG]"></td><td>&nbsp;</td>
            <td>Unterabteilung:</td><td><input type="text" name="unterabteilung" size="20"></td></tr>
          <tr><td>Strasse:</td><td><input type="text" name="strasse" size="20" rule="notempty" msg="Pflichtfeld!" value="[STRASSE]"></td>
          <td>&nbsp;</td>
            <td>Adresszusatz:</td><td><input type="text" name="adresszusatz" size="20" value="[ADRESSZUSATZ]"></td></tr>
          <tr><td>PLZ:</td><td><input type="text" name="plz" size="20" value="[PLZ]"></td><td>&nbsp;</td>
            <td>Ort:</td><td><input type="text" name="ort" size="20" value="[ORT]"></td></tr>
          <tr><td>Land:</td><td colspan="3">[EPROO_SELECT_LAND]</td>
            </tr>
          <tr><td>Telefon:</td><td><input type="text" name="telefon" size="20" value="[TELEFON]"></td><td>&nbsp;</td>
            <td>Telefax:</td><td><input type="text" name="telefax" size="20" value="[TELEFAX]"></td></tr>
        </table>

