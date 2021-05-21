<!-- gehort zu tabview -->
<style>
#payment label {
    width: 24.3%;
    font-size: 12px;
}
</style>
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Tagesabschluss</a></li>
        <li><a href="#tabs-2">Bargeld zählen</a></li>
        <li><a href="#tabs-3">Monatsabschluss</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[TAB2]
</div>
<div id="tabs-2">
<form action="" method="post" id="posabschlussfrm">
<div class="row">
<div class="row-height">
<div class="col-xs-12 col-sm-3 col-sm-height">
<div class="inside inside-full-height">
  <div id="kashin" style="display:none;" title="Kein Kassierer eingeloggt">
    <p>Bitte als Kassierer anmelden.</p>
    <form>
      <input type="text" name="kanr2" value="" id="kanr2" style="width:60px;" /><input type="hidden" name="kassiererId" id="kassiererId" value="" />
    </form>
  </div>
<fieldset><legend>{|Scheine|}</legend>
<table>
<tr><td width="100"></td><td>Anzahl Scheine</td><td>Summe</td></tr>
[SCHEINE]
</table>
</fielset>

</div>
</div>

<div class="col-xs-12 col-sm-3 col-sm-height">
<div class="inside inside-full-height">
<fieldset><legend>Münzen</legend>
<table>
<tr><td width="100"></td><td>Stück</td><td>Summe</td></tr>
[MUENZEN]
</table>
</fielset>

</div>
</div>

<div class="col-xs-12 col-sm-6 col-sm-height">
<div class="inside inside-full-height">
<fieldset><legend>{|Gesamt|}</legend><span style="display:none;" id="waehrung">[WAEHRUNGRABATT]</span>
<table>
<tr><td width="200">Anfangsbestand:</td><td id="anfang">[ANFANGSBESTAND]</td></tr>
<tr><td>Gesamt Scheine:</td><td id="gesamtscheine">0,00 EUR</td></tr>
<tr><td>Gesamt Münzen:</td><td id="gesamtmuenzen">0,00 EUR</td></tr>
<tr><td><b>Gesamt:</b></td><td id="gesamt">0,00 EUR</td></tr>
<tr><td><b>Soll:</b></td><td id="soll">[SOLLBESTAND]</td></tr>
<tr><td><b>Differenz:</b></td><td id="differenz"></td></tr>
<!--<tr><td>Korrektur:</td><td><div id="payment"><input type="radio" id="einlage" style="display:none;" name="entnahmeeinlage" /><label for="einlage" class="button">Einlage</label>&nbsp;<input type="radio" id="entnahme" style="display:none;" name="entnahmeeinlage" /><label for="entnahme">Entnahme</label></div></td></tr>-->
[VORKORREKTUR]<tr><td>Korrektur:</td><td id="korrekturtext"></td></tr>[NACHKORREKTUR]
<tr><td><br></td><td></td></tr>
<tr><td>Kommentar:</td><td><textarea rows="5" cols="50" name="kommentar">[KOMMENTAR]</textarea></td></tr>
<tr><td>Bearbeiter:</td><td id="loggedinkas">[BEARBEITER]</td></tr>
<tr><td><br></td><td></td></tr>
<tr><td>Datum:</td><td><input type="text" name="datum" size="10" id="datum" value="[DATUM]">&nbsp;<input type="submit" value="Gezählte Werte jetzt festschreiben" name="speichern">&nbsp;<input type="submit" value="Gezählte Werte speichern ohne Korrekturbuchung" class="buttonsave" name="speichernohnekorrektur"></td></tr>
<tr><td colspan="2">[DOPPELWARNUNG]</td></tr>
</table>
</fielset>
</div>
</div>
</div>
</div>

</form>
</div>




<div id="tabs-3">
[TAB3]
</div>



<!-- tab view schließen -->
</div>

<script>
  $(document).ready(function() {
    $('#posabschlussfrm').on('keyup keypress', function(e) {
      var keyCode = e.keyCode || e.which;
      if (keyCode === 13) { 
        e.preventDefault();
        if(confirm('Wirklich abschließen?'))return true;
        return false;
      }
    });
    PosAbschlussCalcall();

    $('#zvt').on('click',function(){
      $.ajax({
        url: 'index.php?module=pos&action=list&cmd=endofdate',
        type: 'POST',
        dataType: 'json',
        data: {uid:Math.floor((Math.random() * 90000000) + 10000000)},
        success: function(data) {
          if(typeof data.error != 'undefined') {
            alert(data.error);
          }
        }
      });
    });
  });
  function reloadsite()
  {
    $.ajax({
        url: 'index.php?module=pos&action=abschluss&cmd=getsumme',
        type: 'POST',
        dataType: 'json'}).done( function(data) {
          $('#anfang').html(data.anfang);
          $('#soll').html(data.soll);
        });

  }
</script>
