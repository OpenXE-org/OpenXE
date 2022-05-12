<!-- gehort zu tabview -->


<style>

::-webkit-input-placeholder { /* WebKit, Blink, Edge */
    color:    #999;
    line-height:13px;
    font-size: 0.9em;
}
:-moz-placeholder { /* Mozilla Firefox 4 to 18 */
   color:    #999;
   opacity:  1;
   line-height:13px;
   font-size: 0.9em;
}
::-moz-placeholder { /* Mozilla Firefox 19+ */
   color:    #999;
   opacity:  1;
   line-height:13px;
   font-size: 0.9em;
}
:-ms-input-placeholder { /* Internet Explorer 10-11 */
   color:    #999;
   line-height:13px;
   font-size: 0.9em;
}
::-ms-input-placeholder { /* Microsoft Edge */
   color:    #999;
   line-height:13px;
   font-size: 0.9em;
}

</style>

<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
        <!--<li><a href="#tabs-2">Auftr&auml;ge</a></li>
        <li><a href="#tabs-3">Rechnungen</a></li>
        <li><a href="#tabs-4">Gutschriften</a></li>
        <li><a href="#tabs-5">Lieferscheine</a></li>-->
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]

  <div class="filter-box filter-usersave">
    <div class="filter-block filter-inline">
      <div class="filter-title">{|Filter|}</div>
      <ul class="filter-list">
        [VORANGEBOT]
        <li class="filter-item">
          <label for="angebot" class="switch">
            <input type="checkbox" value="1" id="angebot" title="angebot" />
            <span class="slider round"></span>
          </label>
          <label for="angebot">{|Angebot|}</label>
        </li>
        [NACHANGEBOT]
        [VORAUFTRAG]
        <li class="filter-item">
          <label for="auftrag" class="switch">
            <input type="checkbox" value="1" id="auftrag" title="auftrag" />
            <span class="slider round"></span>
          </label>
          <label for="auftrag">{|Auftrag|}</label>
        </li>
        [NACHAUFTRAG]
        [VORRECHNUNG]
        <li class="filter-item">
          <label for="rechnung" class="switch">
            <input type="checkbox" value="1" id="rechnung" title="rechnung" />
            <span class="slider round"></span>
          </label>
          <label for="rechnung">{|Rechnung|}</label>
        </li>
        [NACHRECHNUNG]
        [VORGUTSCHRIFT]
        <li class="filter-item">
          <label for="gutschrift" class="switch">
            <input type="checkbox" value="1" id="gutschrift" title="gutschrift" />
            <span class="slider round"></span>
          </label>
          <label for="gutschrift">{|Gutschrift|}</label>
        </li>
        [NACHGUTSCHRIFT]
        [VORLIEFERSCHEIN]
        <li class="filter-item">
          <label for="lieferschein" class="switch">
            <input type="checkbox" value="1" id="lieferschein" title="lieferschein" />
            <span class="slider round"></span>
          </label>
          <label for="lieferschein">{|Lieferschein|}</label>
        </li>
        [NACHLIEFERSCHEIN]
        [VORBESTELLUNG]
        [MITTEBESTELLUNG]
        [NACHBESTELLUNG]
        [VORVERBINDLICHKEIT]
        [MITTEVERBINDLICHKEIT]
        [NACHVERBINDLICHKEIT]
        <li class="filter-item">
          [VORVON]
          <label for="von">{|Von|}:</label>
          <input type="text" name="von" id="von" placeholder="Von" title="von" onchange="holesaldodatumsrelevant()">
          [NACHVON]
        </li>
        <li class="filter-item">
          [VORBIS]
          <label for="bis">{|Bis|}:</label>
          <input type="text" name="bis" id="bis" placeholder="Bis" title="bis" onchange="holesaldodatumsrelevant()">
          [NACHBIS]
        </li>
      </ul>
    </div>
  </div>

  <div class="row">
  <div class="row-height">
  <div class="col-xs-12 col-md-10 col-md-height">
  <div class="inside_white inside-full-height">
    <fieldset class="white">
      <legend> </legend>
      [TAB1]
    </fieldset>
  </div>
  </div>
  <div class="col-xs-12 col-md-2 col-md-height">
  <div class="inside inside-full-height">
    <fieldset>
      <legend>{|Kundenstatistik|}</legend>
      <table width="100%>">
        <tr>
          <td>Kreditlimit</td>
        </tr>
        <tr>
          <td class="greybox" width="20%">[KREDITLIMIT]</td>
        </tr>
        <tr>
          <td>Kreditlimit frei</td>
        </tr>
        <tr>
          <td class="greybox" width="20%">[KREDITLIMITFREI]</td>
        </tr>
        <tr>
          <td>Umsatz netto Rechnung aktuelles Jahr</td>
        </tr>
        <tr>
          <td class="greybox" width="20%">[UMSATZ]</td>
        </tr>
<!--        <tr>
          <td>Nicht bezahlte Rechnungen</td>
        </tr>
        <tr>
          <td class="greybox" width="20%">[SALDO]</td>
        </tr>
-->
        <tr>
          <td>Saldo aktuell</td>
        </tr>
        <tr>
          <td class="greybox" width="20%">[KUNDENSALDO]</td>
        </tr>
        <tr>
        <tr>
          <td>Saldo für Datum</td>
        </tr>
          <td class="greybox" id="saldodatumsrelevant" width="20%">[KUNDENSALDODATUMSRELEVANT]</td>
        </tr>
      </table>
    </fieldset>
  </div>
  </div>
  </div>
  </div>




  

[TAB1NEXT]
</div>

<!--<div id="tabs-2">
[MESSAGE]
[TAB2]
[TAB1NEXT]
</div>

-->

<!-- erstes tab -->
<!--<div id="tabs-3">
<table width="100%>">
<tr><td>Kreditlimit</td><td>Kreditlimit frei</td><td>Umsatz netto Rechnung aktuelles Jahr</td><td>Nicht bezahlte Rechnungen</td></tr>
<tr>
  <td class="greybox" width="25%">[KREDITLIMIT]</td>
  <td class="greybox" width="25%">[KREDITLIMITFREI]</td>
  <td class="greybox" width="25%">[UMSATZ]</td>
  <td class="greybox" width="25%">[SALDO]</td>

</tr>
</table>
[MESSAGE]
[TAB3]
</div>
-->

<!-- erstes tab -->
<!--<div id="tabs-4">
[MESSAGE]
[TAB4]
</div>
-->

<!-- erstes tab -->
<!--
<div id="tabs-5">
[MESSAGE]
[TAB5]
</div>

-->


<!-- tab view schließen -->
</div>

<script>
function holesaldodatumsrelevant(){
  var von = $('#von').val();
  var bis = $('#bis').val();
  $.ajax({
    url: 'index.php?module=adresse&action=belege&cmd=kundensaldodatumsrelevant&von='+von+'&bis='+bis+'&id=[ID]',
    type: 'POST',
    dataType: 'json',
    data: {},
    success: function(data) {
      document.getElementById('saldodatumsrelevant').innerHTML = data;
    }
  });

}

</script>
