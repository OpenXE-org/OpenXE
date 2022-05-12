<fieldset><legend>{|Lieferadresse|}</legend>
<form action="" method="post" name="eprooform">
[FORMHANDLEREVENT]
[MESSAGE]

 <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="930">
    <tbody>

      <tr valign="top"> 
        <td  colspan="3">
<table width="930">

 <tr><td >{|Typ|}:</td><td>[TYP][MSGTYP]</td>
          <td>&nbsp;</td>
            <td></td><td></td></tr>

          <tr><td>Name:*</td><td>[NAME][MSGNAME]</td>
          <td>&nbsp;</td>
           <td>{|Telefon|}:</td><td>[TELEFON][MSGTELEFON]</td></tr>

          <tr><td>{|Abteilung|}:</td><td>[ABTEILUNG][MSGABTEILUNG]</td><td>&nbsp;</td>
          <td>{|E-Mail|}:</td><td>[EMAIL][MSGEMAIL]</td></tr>

          <tr><td>{|Unterabteilung|}:</td><td>[UNTERABTEILUNG][MSGUNTERABTEILUNG]</td><td>&nbsp;</td>
							<td>{|Standard Lieferadresse|}:</td><td>[STANDARDLIEFERADRESSE][MSGSTANDARDLIEFERADRESSE]</td>
					</tr>

          <tr><td>{|Adresszusatz|}:</td><td>[ADRESSZUSATZ][MSGADRESSZUSATZ]</td><td>&nbsp;</td>
							<td>{|GLN|}:</td><td>[GLN][MSGGLN]</td>
      		</tr>

          <tr><td>{|Stra&szlig;e|}:</td><td>[STRASSE][MSGSTRASSE]</td><td>&nbsp;</td>
            <td>USt-ID <i>(falls abweichend)</i>:</td><td>[USTID][MSGUSTID]
      </td></tr>

          <tr><td>{|PLZ/Ort|}:</td><td nowrap>[PLZ][MSGPLZ]&nbsp;[ORT][MSGORT]</td><td>&nbsp;</td>
            <td>Besteuerung <i>(falls abweichend)</i>:</td><td>[UST_BEFREIT][MSGUST_BEFREIT]</td></tr>

          <tr><td>{|Land|}:</td><td>[EPROO_SELECT_LAND_LIEFERADRESSEN]</td>
        <td>&nbsp;</td><td>{|Lieferbedingung|}:</td><td>[LIEFERBEDINGUNG][MSGLIEFERBEDINGUNG]</td>
          </tr>

</table>
<br>

	</td>
      </tr>

    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
<td></td>
    <td width="" valign="" height="" bgcolor="" align="right" colspan="2" bordercolor="" classname="orange2" class="orange2">
    <input type="submit"
    value="Lieferadresse speichern" /> 
    </td>
    </tr>


    </tbody>
  </table>

</form>
</fieldset>
