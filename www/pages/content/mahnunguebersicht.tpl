<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        [BEFORETABFAELLIGE]<li><a href="#tabs-1">[FAELLIGE]</a></li>[AFTERTABFAELLIGE]
        [BEFORETABZAHLUNGSERINNERUNGEN]<li><a href="#tabs-2">[ZAHLUNGSERINNERUNGEN]</a></li>[AFTERTABZAHLUNGSERINNERUNGEN]
        [BEFORETABMAHNUNGEN]<li><a href="#tabs-3">[MAHNUNGEN]</a></li>[AFTERTABMAHNUNGEN]
        [BEFORETABINKASSO]<li><a href="#tabs-4">[INKASSO]</a></li>[AFTERTABINKASSO]
        [BEFORETABGESPERRT]<li><a href="#tabs-5">[GESPERRT]</a></li>[AFTERTABGESPERRT]
        [BEFORETABFORDERUNGSVERLUSTE]<li><a href="#tabs-6">[FORDERUNGSVERLUSTE]</a></li>[AFTERTABFORDERUNGSVERLUSTE]
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
  [BEFORETABFAELLIGE]
<div id="tabs-1">
[SUB1MESSAGE]
[MELDUNGMAHNWESEN]
<form action="" onsubmit="return confirm('Mahnwesen wirklich starten?');" method="post">
  <table width="100%" style="background-color: #CFCFD1;" align="center">
    <tr>
      <td align="center">
        <br><b style="font-size: 14pt">{|Mahnlauf starten|}:</b>
        <br>
        <br>
        [MANUELLCHECKBOX]<label for="">{|alle markieren|}</label>
        &nbsp;{|Drucker|}:&nbsp;<select name="drucker">[DRUCKER]</select>
        <span style="text-align: left;">
        &nbsp;<input type="submit" [SUBMITDISABLED] value="{|Mahnwesen starten (E-Mail und Belegdruck)|}" name="starten" id="starten">&nbsp;
        </span>
        <br>
        <br>
      </td>
    </tr>
  </table>



  <div class="filter-box filter-usersave">
    <div class="filter-block filter-inline">
      <div class="filter-title">{|Filter|}</div>
      <ul class="filter-list">
        <li class="filter-item">
          <label for="rechnung" class="switch">
            <input type="checkbox" id="rechnung">
            <span class="slider round"></span>
          </label>
          <label for="rechnung">{|Rechnung|}</label>
        </li>
        <li class="filter-item">
          <label for="lastschrift" class="switch">
            <input type="checkbox" id="lastschrift">
            <span class="slider round"></span>
          </label>
          <label for="lastschrift">{|Lastschrift|}</label>
        </li>
        <li class="filter-item">
          <label for="nachnahme" class="switch">
            <input type="checkbox" id="nachnahme">
            <span class="slider round"></span>
          </label>
          <label for="nachnahme">{|Nachnahme|}</label>
        </li>
        <li class="filter-item">
          <label for="bar" class="switch">
            <input type="checkbox" id="bar">
            <span class="slider round"></span>
          </label>
          <label for="bar">{|Bar|}</label>
        </li>
        <li class="filter-item">
          <label for="vorkasse" class="switch">
            <input type="checkbox" id="vorkasse">
            <span class="slider round"></span>
          </label>
          <label for="vorkasse">{|Vorkasse|}</label>
        </li>
      </ul>
    </div>
  </div>

  <table width="100%">
    <tr>
      <td width="100%">
        [TAB1]
      </td>
    </tr>
  </table>
  <br>
</form>
</div>
  [AFTERTABFAELLIGE]

  [BEFORETABZAHLUNGSERINNERUNGEN]
<div id="tabs-2">
[SUB2MESSAGE]
  <table width="100%" style="background-color: #CFCFD1;" align="center">
    <tr>
      <td align="center">
        <br><b style="font-size: 14pt">{|Zahlungserinnerungen|}</b>
        <br>
        <br>
      </td>
    </tr>
  </table>
[TAB2]
</div>
  [AFTERTABZAHLUNGSERINNERUNGEN]

  [BEFORETABMAHNUNGEN]
<div id="tabs-3">
[SUB3MESSAGE]
  <table width="100%" style="background-color: #CFCFD1;" align="center">
    <tr>
      <td align="center">
        <br><b style="font-size: 14pt">{|Mahnungen|}</b>
        <br>
        <br>
      </td>
    </tr>
  </table>

[TAB3]
</div>
  [AFTERTABMAHNUNGEN]

  [BEFORETABINKASSO]
<div id="tabs-4">
[SUB4MESSAGE]
  <table width="100%" style="background-color: #CFCFD1;" align="center">
    <tr>
      <td align="center">
        <br><b style="font-size: 14pt">{|Inkasso|}</b>
        <br>
        <br>
      </td>
    </tr>
  </table>
[TAB4]
</div>
  [AFTERTABINKASSO]

  [BEFORETABGESPERRT]
<div id="tabs-5">
[SUB5MESSAGE]
  <table width="100%" style="background-color: #CFCFD1;" align="center">
    <tr>
      <td align="center">
        <br><b style="font-size: 14pt">{|Gesperrt (nicht im Mahnungslauf)|}</b>
        <br>
        <br>
      </td>
    </tr>
  </table>
[TAB5]
</div>
  [AFTERTABGESPERRT]

  [BEFORETABFORDERUNGSVERLUSTE]
<div id="tabs-6">
[SUB6MESSAGE]
  <table width="100%" style="background-color: #CFCFD1;" align="center">
    <tr>
      <td align="center">
        <br><b style="font-size: 14pt">{|Forderungsverluste|}</b>
        <br>
        <br>
      </td>
    </tr>
  </table>

[TAB6]
</div>
  [AFTERTABFORDERUNGSVERLUSTE]




<!-- tab view schließen -->
</div>
<!-- ende tab view schließen -->
