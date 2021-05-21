
<div id="tabs">
<ul>
        <li><a href="#tabs-2">[TABTEXT2]</a></li>
       [VORTABS3UEBERSCHRIFT]<li><a href="#tabs-3">[TABTEXT3]</a></li>[NACHTABS3UEBERSCHRIFT]
 </ul>


<div id="tabs-2">
[AUTOVERSANDBERECHNEN]

<form action="#tabs-2" id="frmauto" name="frmauto" method="post">

  <div class="filter-box filter-usersave">
    <div class="filter-block filter-inline">
      <div class="filter-title">{|Filter|}</div>
      <ul class="filter-list">
        <li class="filter-item">
          <label for="fastlane" class="switch">
            <input type="checkbox" id="fastlane">
            <span class="slider round"></span>
          </label>
          <label for="fastlane">{|Fast-Lane|}</label>
        </li>
        <li class="filter-item">
          <label for="auftrag_kundemehrereauftraege" class="switch">
            <input type="checkbox" id="auftrag_kundemehrereauftraege">
            <span class="slider round"></span>
          </label>
          <label for="auftrag_kundemehrereauftraege">{|nur Kunden mit mehreren Auftr&auml;gen|}</label>
        </li>
        <li class="filter-item">
          <label for="auftrag_lieferdatum" class="switch">
            <input type="checkbox" id="auftrag_lieferdatum">
            <span class="slider round"></span>
          </label>
          <label for="auftrag_lieferdatum">{|nur Auftr&auml;ge mit zukünftigem Lieferdatum|}</label>
        </li>
      </ul>
    </div>
  </div>

[TAB2]
<fieldset>
  <legend>Stapelverarbeitung</legend>
  <input type="checkbox" value="1" id="autoalle" checked="checked" />&nbsp;alle markieren&nbsp;
  <input type="hidden" id="bezeichnung" name="bezeichnung" value="" />
  <!--<input type="submit" class="btnBlue" value="Auto-Versand starten" id="submit" name="submit">&nbsp;-->
  <!--<input type="button" class="btnBlue" value="Auto-Versand starten (mit Kommissionierbezeichnung)" onclick="kommissionierfrage();" name="submit2" />&nbsp;-->
  <select name="auftrag_versandauswahl" id="auftrag_versandauswahl">
    <option value="">{|bitte w&auml;hlen|} ...</option>
    <option value="versandstarten">Auto-Versand</option>
    <option value="versandstartenmit">Auto-Versand (mit Kommissionierbez.)</option>
    <option value="drucken">drucken</option>
  </select>
  <span id="druckerauswahl" style="display: none;">{|Drucker|}: <select name="seldruckerversand" id="seldruckerversand">[SELDRUCKERVERSAND]</select></span><input type="submit" class="btnBlue" name="ausfuehren" value="{|ausf&uuml;hren|}" />
  <input type="button" value="Anzahl x markieren" onclick="var anzahl = prompt('Anzahl zu markierender Aufträge:', ''); if( anzahl > 0) anzahlxmarkieren(anzahl);" class="btnBlue">&nbsp;
  [AUTOBERECHNEN]
</fieldset>
</form>
</div>
<div id="tabs-3">
[VORTABS3UEBERSCHRIFT]
<form action="#tabs-3" method="post">

  <div class="filter-box filter-usersave">
    <div class="filter-block filter-inline">
      <div class="filter-title">{|Filter|}</div>
      <ul class="filter-list">
        <li class="filter-item">
          <label for="autoallewartend" class="switch">
            <input type="checkbox" value="1" id="autoallewartend" checked="checked" />
            <span class="slider round"></span>
          </label>
          <label for="autoallewartend">{|Alle|}</label>
        </li>
      </ul>
    </div>
  </div>

[TAB3]
<table width="100%"><tr><td><input type="submit" value="Auftr&auml;ge aus Liste entfernen" name="entfernen"></td>
</tr></table>
</form>
[NACHTABS3UEBERSCHRIFT]
</div>

</div>
<script>

  function kommissionierfrage()
  {
    var bezeichnung = prompt('Bitte Kommissionierbezeichnung eingeben');
    $('#bezeichnung').val(bezeichnung);
    $('#auftrag_versandauswahl').val('versandstarten');
    $('#submit').trigger('click');
  }

  function anzahlxmarkieren(anzahl)
  {
    
    $('#auftraegeoffeneauto').find('input[type="checkbox"]').prop('checked',false);
    $('#auftraegeoffeneauto').find('input[type="checkbox"]').each( function(index,el) {
      //$('#auftraegeoffeneauto').find('input[type="checkbox"]').prop('checked',true);
      if(index < anzahl)
        $(el).prop('checked',true);
    });

  }

  $(document).ready(function() {
    $('#bezeichnung').val('');
    $("#auftraegeoffeneauto > tfoot:nth-child(3) > tr:nth-child(1) > th:nth-child(1) > span:nth-child(1) > input:nth-child(1)").hide();
    $("#auftraegeoffeneauto > tfoot:nth-child(3) > tr:nth-child(1) > th:nth-child(2) > span:nth-child(1) > input:nth-child(1)").hide();
    $("#auftraegeoffeneauto > tfoot:nth-child(3) > tr:nth-child(1) > th:nth-child(14) > span:nth-child(1) > input:nth-child(1)").hide();
    $("#auftraegeoffeneauto > tfoot:nth-child(3) > tr:nth-child(1) > th:nth-child(15) > span:nth-child(1) > input:nth-child(1)").hide();


    $('#autoalle').on('change',function(){
      var wert = $(this).prop('checked');
      $('#auftraegeoffeneauto').find('input[type="checkbox"]').prop('checked',wert);
      $('#auftraegeoffeneauto').find('input[type="checkbox"]').first().trigger('change');
    });
    $('#autoallewartend').on('change',function(){
      var wert = $(this).prop('checked');
      $('#auftraegeoffeneautowartend').find('input[type="checkbox"]').prop('checked',wert);
      $('#auftraegeoffeneautowartend').find('input[type="checkbox"]').first().trigger('change');
    });

    $('#auftrag_versandauswahl').on('change',function(){
      if($('#auftrag_versandauswahl').val() == 'drucken'){
        $('#druckerauswahl').show();
      }else{
        $('#druckerauswahl').hide();
      }
    });

    document.getElementById('frmauto').onsubmit = function (evt) {
      if($('#auftrag_versandauswahl').val() == 'versandstartenmit'){
        kommissionierfrage();
      }
    }
  });
  
</script>

