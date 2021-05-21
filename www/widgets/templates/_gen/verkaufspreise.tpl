<script type="text/javascript">

$(document).ready(function(){

  art = document.getElementById('art');
  adressediv = document.getElementById('adressediv');
  gruppediv = document.getElementById('gruppediv');

  if (art) {
      // Hide the target field if priority isn't critical
      if (art.options[art.selectedIndex].value =='Kunde') {
        adressediv.style.display='';
        gruppediv.style.display='none';
      }
      if (art.options[art.selectedIndex].value =='Gruppe') {
            adressediv.style.display='none';
        gruppediv.style.display='';
      }

      art.onchange=function() {
          if (art.options[art.selectedIndex].value == 'Kunde') {             
            adressediv.style.display='';
            gruppediv.style.display='none';
          } else if(art.options[art.selectedIndex].value == 'Gruppe') {
            adressediv.style.display='none';
            gruppediv.style.display='';
          } else {
            adressediv.style.display='';
            gruppediv.style.display='';
          }
      }
  }
});
 </script>
[MESSAGE]
<form action="" method="post" name="eprooform" >
[FORMHANDLEREVENT]
  <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>

      <tr valign="top" colspan="3">
        <td colspan="3">


<fieldset><legend>&nbsp;Verkaufspreis&nbsp;</legend>

<table align="center" cellspacing="5" border="0">
<tr><td width="170">{|Konditionen|}:</td><td width="180" colspan="3">

[ART][MSGART]&nbsp;

</td><td width="170"></td></tr>

<tr id="adressediv"><td width="170">{|Kunde|}:</td><td colspan="4"><i>F&uuml;r Standardpreis leer lassen</i>[ADRESSESTART][ADRESSE][MSGADRESSE][ADRESSEENDE]<br></td></tr>
<tr id="gruppediv"><td width="170">{|Gruppe|}:</td><td colspan="4">[GRUPPESTART][GRUPPE][MSGGRUPPE][GRUPPEENDE]</td></tr>
<tr><td width="170">{|Artikelnummer bei Kunde|}:</td><td colspan="4">[KUNDENARTIKELNUMMER][MSGKUNDENARTIKELNUMMER]&nbsp;<i>(wenn vorhanden)</i></td></tr>
<!--<tr><td width="170">{|Konditionen|}:</td><td width="180">
[OBJEKT][MSGOBJEKT]
</td><td width="20">&nbsp;</td><td width="150"></td><td width="170"></td></tr>-->
<tr><td width="170"><b>Ab Menge:</b></td><td width="180">[AB_MENGE][MSGAB_MENGE]&nbsp;</td><td width="20">&nbsp;</td><td width="150">Menge in VPE</td><td>[VPESTART][VPE][MSGVPE][VPEENDE]</td></tr>

<tr><td width="170" rowspan="3" valign="top"><b>Preis:</b></td><td width="180" rowspan="3">[PREIS][MSGPREIS]&nbsp;<br>[PREISRECHNER]</td><td width="20">&nbsp;</td><td width="150" valign="top">W&auml;hrung</td><td>[WAEHRUNG][MSGWAEHRUNG]</td></tr>
<tr><td width="20">&nbsp;</td><td width="150" valign="top">Preis nicht berechnet aus W&auml;hrungstabelle</td><td>[NICHTBERECHNET][MSGNICHTBERECHNET]</td></tr>
<tr><td width="20">&nbsp;</td><td colspan="2" align="right">[PREISTABELLE]</td></tr>
<tr><td width="170">{|G&uuml;ltig ab|}:</td><td width="180">[GUELTIG_AB][MSGGUELTIG_AB]&nbsp;</td><td width="20">&nbsp;</td><td width="150"></td><td></td></tr>
<tr><td width="170">{|G&uuml;ltig bis|}:</td><td width="180">[GUELTIG_BIS][MSGGUELTIG_BIS]&nbsp;</td><td width="20">&nbsp;</td><td width="150"></td><td></td></tr>

<tr><td>{|Interner Kommentar|}:</td><td colspan="4">[BEMERKUNG][MSGBEMERKUNG]</td></tr>

</table>

</fieldset>
       </td>
      </tr>
    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
    <input type="submit" name="submit"
    value="Speichern" /> [ABBRECHEN]</td>
    </tr>

    </tbody>
</table>
</form>
[PREISTABELLEPOPUP]
