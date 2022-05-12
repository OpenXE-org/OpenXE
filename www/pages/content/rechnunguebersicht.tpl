<div id="tabs">
    <ul>
        <li><a href="#tabs-1">{|Rechnungen|}</a></li>
        <li><a href="#tabs-2">{|nicht versendete Rechnungen|}</a></li>
        <li><a href="#tabs-3">{|in Bearbeitung|}</a></li>
    </ul>
<div id="tabs-1">

  <div class="filter-box filter-usersave">
    <div class="filter-block filter-inline">
      <div class="filter-title">{|Filter|}</div>
      <ul class="filter-list">
        <li class="filter-item">
          <label for="zahlungseingang" class="switch">
            <input type="checkbox" id="zahlungseingang">
            <span class="slider round"></span>
          </label>
          <label for="zahlungseingang">{|Zahlungsstatus offen|}</label>
        </li>
        <li class="filter-item">
          <label for="zahlungseingangfehlt" class="switch">
            <input type="checkbox" id="zahlungseingangfehlt">
            <span class="slider round"></span>
          </label>
          <label for="zahlungseingangfehlt">{|Zahlungsstatus nicht bezahlt / teilbezahlt|}</label>
        </li>
        <li class="filter-item">
          <label for="rechnungenheute" class="switch">
            <input type="checkbox" id="rechnungenheute">
            <span class="slider round"></span>
          </label>
          <label for="rechnungenheute">{|Alle Rechnungen von heute|}</label>
        </li>
        <li class="filter-item">
          <label for="rechnungenstorniert" class="switch">
            <input type="checkbox" id="rechnungenstorniert">
            <span class="slider round"></span>
          </label>
          <label for="rechnungenstorniert">{|Storniert|}</label>
        </li>
      </ul>
    </div>
  </div>

[MESSAGE]
<form method="post" action="#">
[TAB1]
<fieldset><legend>{|Stapelverarbeitung|}</legend>
<input type="checkbox" id="auswahlalle" onchange="alleauswaehlen();" />&nbsp;{|alle markieren|}
&nbsp;<select id="sel_aktion" name="sel_aktion">
<option value="">{|bitte w&auml;hlen|} ...</option>
[ALSBEZAHLTMARKIEREN]
<option value="offen">{|als offen markieren|}</option>
<option value="mail">{|per Mail versenden|}</option>
<option value="versendet">{|als versendet markieren|}</option>
<option value="pdf">{|Sammel-PDF|}</option>
<option value="drucken">{|drucken|}</option>
</select>&nbsp;{|Drucker|}: <select name="seldrucker">[SELDRUCKER]</select>&nbsp;<input type="submit" class="btnBlue" name="ausfuehren" value="{|ausf&uuml;hren|}" />
</fieldset>
</form>
</div>

<div id="tabs-2">
[TAB2]
</div>

<div id="tabs-3">
[TAB3]
</div>

</div>

<script>
function alleauswaehlen()
{
  var wert = $('#auswahlalle').prop('checked');
  $('#rechnungen').find(':checkbox').prop('checked',wert);
}
</script>
