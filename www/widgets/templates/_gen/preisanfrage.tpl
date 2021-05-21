[SAVEPAGEREALLY]

<!-- gehort zu tabview -->
<div id="tabs">
  <ul>
    <li><a href="#tabs-1">{|Preisanfrage|}</a></li>
    <li><a href="#tabs-2" onclick="callCursor();">{|Positionen|}</a></li>
    <li><a href="index.php?module=preisanfrage&action=inlinepdf&id=[ID]&frame=true#tabs-3">{|Vorschau|}</a></li>
    [FURTHERTABS]
  </ul>


<div id="tabs-1">
[MESSAGE]
<form action="" method="post" name="eprooform" id="eprooform">
[FORMHANDLEREVENT]





<div class="row">
<div class="row-height">
<div class="col-xs-12 col-sm-height">
<div class="inside_dark inside-full-height">

<table width="100%" align="center">
<tr>
<td>&nbsp;<b style="font-size: 14pt">{|Preisanfrage|} <font color="blue">[NUMMER]</font></b>[LIEFERANT][RABATTANZEIGE]</td>
<td>[STATUSICONS]</td>
<td align="right">[ICONMENU]&nbsp;[SAVEBUTTON]</td>
</tr>
</table>

</div>
</div>
</div>
</div>


<div class="row">
<div class="row-height">
<div class="col-xs-12 col-sm-height">
<div class="inside inside-full-height">

<fieldset><legend>{|Allgemein|}</legend>
<table width="100%">
 <tr><td>{|Lieferant|}:</td><td>[ADRESSE][MSGADRESSE]&nbsp;
[BUTTON_UEBERNEHMEN]
</td></tr>
  <tr><td>{|Datum|}:</td><td>[DATUM][MSGDATUM]</td></tr>
  <tr><td>{|Interne Bezeichnung|}:</td><td>[INTERNEBEZEICHNUNG][MSGINTERNEBEZEICHNUNG]</td></tr>
  <tr><td width="200">{|Projekt|}:</td><td>[PROJEKTAUTOSTART][PROJEKT][MSGPROJEKT][PROJEKTAUTOEND]</td></tr>
  <tr><td>{|Status|}:</td><td>[STATUS]</td></tr>
  <tr><td>{|Schreibschutz|}:</td><td>[SCHREIBSCHUTZ][MSGSCHREIBSCHUTZ]&nbsp;</td></tr>
</table>
</fieldset>


</div>
</div>
</div>
</div>

<div class="row">
  <div class="row-height">
   <div class="col-xs-12 col-md-6 col-sm-height">
      <div class="inside inside-full-height">

        <fieldset><legend>{|Freitext|}</legend>
        [FREITEXT][MSGFREITEXT]
        </fieldset>

      </div>
    </div>


    <div class="col-xs-12 col-md-6 col-sm-height">
      <div class="inside inside-full-height">

        <fieldset><legend>{|Kopftext|}</legend>
        [BODYZUSATZ][MSGBODYZUSATZ]
        </fieldset>

      </div>
    </div>  </div>
</div>







<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-9 col-md-height">
<div class="inside inside-full-height">

  <fieldset><legend>{|Stammdaten|}</legend>
    <table border="0" class="mkTableFormular">
            <tr><td width="200">{|Typ|}:</td><td width="200">[TYP][MSGTYP]</td></tr>
            <tr><td>{|Name|}:</td><td>[NAME][MSGNAME]</td></tr>
                  <tr><td>{|Titel|}:</td><td>[TITEL][MSGTITEL]</td></tr>
            <tr><td>{|Ansprechpartner|}:</td><td>[ANSPRECHPARTNER][MSGANSPRECHPARTNER]</td></tr>
            <tr><td>{|Abteilung|}:</td><td>[ABTEILUNG][MSGABTEILUNG]</td></tr>
            <tr><td>{|Unterabteilung|}:</td><td>[UNTERABTEILUNG][MSGUNTERABTEILUNG]</td></tr>
            <tr><td>{|Adresszusatz|}:</td><td>[ADRESSZUSATZ][MSGADRESSZUSATZ]</td></tr>
            <tr><td>{|Stra&szlig;e|}:</td><td>[STRASSE][MSGSTRASSE]</td></tr>
            <tr><td>{|PLZ/Ort|}:</td><td>[PLZ][MSGPLZ]&nbsp;[ORT][MSGORT]</td></tr>
            [VORBUNDESSTAAT]<tr valign="top"><td><label for="bundesstaat">{|Bundesstaat|}:</label></td><td colspan="2">[EPROO_SELECT_BUNDESSTAAT]</td></tr>[NACHBUNDESSTAAT]
            <tr><td>{|Land|}:</td><td>[EPROO_SELECT_LAND]</td></tr>
  </table>

    <table class="mkTableFormular">
              <tr><td>{|Telefon|}:</td><td>[TELEFON][MSGTELEFON]</td></tr>
              <tr><td>{|Telefax|}:</td><td>[TELEFAX][MSGTELEFAX]</td></tr>
            <tr><td>{|E-Mail|}:</td><td>[EMAIL][MSGEMAIL]</td></tr>
             <tr><td>{|Anschreiben|}</td><td>[ANSCHREIBEN][MSGANSCHREIBEN]</td></tr>
              <tr><td></td><td>[ANSPRECHPARTNERPOPUP]</td></tr>
  </table>


  </fieldset>

</div>
</div>
</div>
</div>

<div class="row">
<div class="row-height">
<div class="col-xs-12 col-sm-6 col-sm-height">
<div class="inside inside-full-height">

<fieldset><legend>{|Preisanfrage|}</legend>
<table width="100%">
<tr><td width="200">{|Artikel-Nr. gruppieren|}:</td><td>[ZUSAMMENFASSEN][MSGZUSAMMENFASSEN]</td></tr>


</table>
</fieldset>

</div>
</div>
<div class="col-xs-12 col-sm-6 col-sm-height">
<div class="inside inside-full-height">

<fieldset><legend>&nbsp;</legend></fieldset>
</div>
</div>
</div>
</div>


<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-height">
<div class="inside inside-full-height">

<fieldset><legend>{|Interne Bemerkung|}</legend>
[INTERNEBEMERKUNG][MSGINTERNEBEMERKUNG]
</fieldset>

</div>
</div>
</div>
</div>



<br><br>
<table width="100%">
<tr><td align="center">
    <input type="submit" name="speichern"
    value="Speichern" />
</td></tr></table>
  </form>
</div>



<div id="tabs-2">
<div class="overflow-scroll">
<!-- // rate anfang -->


<!-- // ende anfang -->
<table width="100%" style="" align="center">
<tr>
<td width="33%"></td>
<td align="center"><b style="font-size: 14pt">{|Preisanfrage|} <font color="blue">[NUMMER]</font></b>[LIEFERANT][RABATTANZEIGE]</td>
<td width="33%" align="right">[ICONMENU2]</td>
</tr>

</table>


[POS]


</div>
</div>


      [FURTHERTABSDIV]




 <!-- tab view schließen -->
</div>


