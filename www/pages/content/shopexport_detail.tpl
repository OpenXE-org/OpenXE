[MESSAGE2]
<style>
#erstezeile > td  {
padding-top:8px;
}
</style>
<form method="post">
<div class="row">
  <div class="row-height">
    <div class="col-xs-12 col-sm-10 col-sm-height">
      <div class="inside inside-full-height">
        <fieldset><legend>{|Einstellungen|}</legend>
          <table style="width:100%;" id="einstellungentab">
            <tr><td style="min-width:130px;">{|Bezeichnung|}:</td><td><input type="text" id="frm_bezeichnung" name="frm_bezeichnung" value="[FRM_BEZEICHNUNG]" size="40"/></td><td >{|Import-Modus|}:</td><td><select id="modus" name="modus">[SELMODUS]</select></td></tr>
            <tr><td>{|Aktiv|}:</td><td><input type="checkbox" value="1" [AKTIVHAKEN] name="aktivhaken" id="aktivhaken" /></td><td>{|Nur 1 Auftrag pro Anfrage|}:</td><td><input type="checkbox" name="einzelnabholen" value="1" [EINZELNABHOLEN] /></td></tr>
            <tr><td>{|Projekt|}:</td><td><input type="text" name="frm_projekt" id="frm_projekt" value="[FRM_PROJEKT]" size="40"/></td><td>{|Aufträge nach abholen in Zwischentabelle|}:</td><td><input type="checkbox" name="inwarteschlange" value="1" [INWARTESCHLANGE] /> <i>{|Freigabe erfolgt manuell|}</i>
</td></tr>
            <tr><td>{|Abholmodus|}:</td><td><select id="abholmodus" name="abholmodus">[SELABHOLMODUS]</select></td></tr>
            <tr class="ab_nummerzeitraum zeitraum"><td>{|Datum von|}:</td><td><input type="text" name="datumvon" value="[DATUMVON]" size="40">&nbsp;</td><td></tr>
            <tr class="ab_nummerzeitraum zeitraum"><td>{|Datum bis|}:</td><td><input type="text" name="datumbis" value="[DATUMBIS]" size="40">&nbsp;<i>{|Leer = bis heute|}</i></td><td></tr>
            <tr class="ab_nummerzeitraum ab_nummer"><td>{|Holen jeden Status|}:</td><td><input type="checkbox" name="holeallestati" [HOLEALLESTATI] value="1" size="40">&nbsp;<i>{|Es werden alle Auftr&auml;ge &uuml;bertragen von Shop auf Xentral unabh&auml;ngig vom Status.|}</i></td><td></tr>
            <tr class="ab_nummerzeitraum ab_nummer"><td>{|ab Nummer|}:</td><td><input type="text" name="ab_nummer" value="[AB_NUMMER]" size="40">&nbsp;<i>{|Es werden alle Auftr&auml;ge ab dieser Nummer &uuml;bertragen.|}</i></td><td></tr>
            <tr class="ab_nummerzeitraum ab_nummer"><td>{|Status &auml;ndern|}:</td><td><input type="checkbox" name="nummersyncstatusaendern" value="1" [NUMMERSYNCSTATUSAENDERN]>&nbsp;<i>{|Es wird der Status nach dem Abholen ge&auml;ndert.|}</i></td><td></tr>
            [VOREXTRA]<tr><td colspan="4" style="font-weight:bold;padding-top:40px;">{|Einstellungen f&uuml;r Shop oder Marktplatz|}:</td></tr>[NACHEXTRA]
            [EXTRAEINSTELLUNGEN]
            <tr><td colspan="3"></td><td><input type="submit" value="{|Speichern|}" name="speichern" /></td></tr>
          </table>
        </fieldset>
      </div>
    </div>
    <div class="col-xs-12 col-sm-2 col-sm-height">
      <div class="inside_turkey inside-full-height">
        <fieldset class="turkey"><legend>{|Aktion|}</legend>
          <table width="100%">
            <tr><td><input type="submit" name="pruefen" value="{|Verbindung pr&uuml;fen|}" style="min-width:150px;height:50px;width:99%;"></td></tr>
            <tr><td width="50%"><input type="submit" name="auftragabholen" value="Auftr&auml;ge abholen" style="min-width:150px;height:50px;width:99%;"></td></tr>
          </table>
        </fieldset>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="row-height">
    <div class="col-xs-12 col-sm-12 col-sm-height">
      <div class="inside-full-height">
        <fieldset><legend>{|Filter|}</legend>
        <table><tr><td>{|nur Fehler|}</td><td><input type="checkbox" id="nurfehler" name="nurfehler" value="1" /></td></tr></table>
        </fieldset>
        [LOGTABELLE]
      </div>
    </div>
  </div>
</div>
</form>
