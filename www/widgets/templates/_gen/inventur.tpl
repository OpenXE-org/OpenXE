<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-2">Inventur</a></li>
        <li><a href="#tabs-4" onclick="callCursor();">Positionen</a></li>
        <li><a href="index.php?module=inventur&action=inlinepdf&id=[ID]&frame=true#tabs-3">Vorschau</a></li>
        [FURTHERTABS]
    </ul>


<div id="tabs-2">
[MESSAGE]
<form action="" method="post" name="eprooform" id="eprooform">
[FORMHANDLEREVENT]

<!-- // rate anfang -->

<div class="row">
  <div class="row-height">
    <div class="col-xs-12 col-sm-height">
      <div class="inside inside-full-height">
      <table width="100%" align="center">
      <tr>
      <td>&nbsp;<b style="font-size: 14pt">Inventur <font color="blue">[NUMMER]</font></b>[KUNDE][RABATTANZEIGE]</td>
      <td><!--[STATUSICONS]--></td>
      <td width="" align="right">[ICONMENU]&nbsp;[SAVEBUTTON]</td>
      </tr>
      </table>
      </div>
    </div>
  </div>
</div>


<div class="row">
  <div class="row-height">
    <div class="col-xs-12 col-sm-6 col-sm-height">
      <div class="inside inside-full-height">

      <fieldset><legend>{|Allgemein|}</legend>
      <table width="100%">
        <tr><td>{|Datum|}:</td><td>[DATUM][MSGDATUM]</td></tr>
        <tr><td>{|Name|}:</td><td>[NAME][MSGNAME]</td></tr>
        <tr><td>{|Projekt|}:</td><td>[PROJEKTAUTOSTART][PROJEKT][MSGPROJEKT][PROJEKTAUTOEND]</td></tr>
        <tr><td>{|Status|}:</td><td>[STATUS]</td></tr>
        <tr><td>{|Schreibschutz|}:</td><td>[SCHREIBSCHUTZ][MSGSCHREIBSCHUTZ]&nbsp;</td></tr>
        <tr><td>{|Bearbeiter|}:</td><td>[BEARBEITER][MSGBEARBEITER]</td></tr>
        <tr><td>{|Preise ausblenden|}:</td><td>[NOPRICE][MSGNOPRICE]&nbsp;</td></tr>
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
    <div class="col-xs-12 col-sm-12 col-sm-height">
      <div class="inside inside-full-height">
        <fieldset><legend>{|Bemerkung / Hinweise|}</legend>
          <table width="100%"><tr><td>
            [FREITEXT][MSGFREITEXT]
            </td></tr></table>
        </fieldset>
      </div>
    </div>
  </div>
</div>


<div class="row">
  <div class="row-height">
    <div class="col-xs-12 col-sm-12 col-sm-height">
      <div class="inside inside-full-height">
        <fieldset><legend>{|Interne Bemerkung|}</legend>
          <table width="100%"><tr><td>
            [INTERNEBEMERKUNG][MSGINTERNEBEMERKUNG]
            </td></tr></table>
        </fieldset>
      </div>
    </div>
  </div>
</div>


<br><br>
<table width="100%">
<tr><td align="right">
    <input type="submit" name="speichern" class="btnGreen"
    value="Speichern" />
</td></tr></table>
</div>


</form>

<div id="tabs-4">

<div class="row">
  <div class="row-height">
    <div class="col-xs-12 col-sm-height">
      <div class="inside inside-full-height">
      <table width="100%" align="center">
      <tr>
      <td>&nbsp;<b style="font-size: 14pt">Inventur <font color="blue">[NUMMER]</font></b>[KUNDE][RABATTANZEIGE]</td>
      <td><!--[STATUSICONS]--></td>
      <td width="" align="right">[ICONMENU]&nbsp;[SAVEBUTTON]</td>
      </tr>
      </table>
      </div>
    </div>
  </div>
</div>



<div class="row">
  <div class="row-height">
    <div class="col-xs-12 col-sm-12 col-sm-height">
      <div class="inside inside-full-height">
        [POS]
      </div>
    </div>
  </div>
</div>






</div>

<div id="tabs-3">
</div>

      [FURTHERTABSDIV]


 <!-- tab view schließen -->
</div>

