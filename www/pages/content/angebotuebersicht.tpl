<div id="tabs">
    <ul>
        <li><a href="#tabs-1">{|Angebote|}</a></li>
        <li><a href="#tabs-3">{|in Bearbeitung|}</a></li>
    </ul>
<div id="tabs-1">

  <div class="filter-box filter-usersave">
    <div class="filter-block filter-inline">
      <div class="filter-title">{|Filter|}</div>
      <ul class="filter-list">
        <li class="filter-item">
          <label for="angeboteoffen" class="switch">
            <input type="checkbox" id="angeboteoffen" title="alle nicht versendeten Angebote">
            <span class="slider round"></span>
          </label>
          <label for="angeboteoffen">{|Alle nicht versendeten Angebote|}</label>
        </li>
        <li class="filter-item">
          <label for="angeboteheute" class="switch">
            <input type="checkbox" id="angeboteheute">
            <span class="slider round"></span>
          </label>
          <label for="angeboteheute">{|Alle Angebote von heute|}</label>
        </li>
        <li class="filter-item">
          <label for="angeboteohneab" class="switch">
            <input type="checkbox" id="angeboteohneab">
            <span class="slider round"></span>
          </label>
          <label for="angeboteohneab">{|Alle Angebote ohne Auftrag|}</label>
        </li>
        <li class="filter-item">
          <label for="angeboteabgelehnt" class="switch">
            <input type="checkbox" id="angeboteabgelehnt">
            <span class="slider round"></span>
          </label>
          <label for="angeboteabgelehnt">{|Alle abgelehnten Angebote|}</label>
        </li>
      </ul>
    </div>
  </div>

[MESSAGE]

<form method="post" action="#">
  [TAB1]

  <div class="clear"></div>
  <fieldset>
    <legend>{|Stapelverarbeitung|}</legend>
    <input type="checkbox" id="auswahlalle" onchange="alleauswaehlen();" />&nbsp;{|alle markieren|}
    <select id="sel_aktion" name="sel_aktion">
      <option value="">{|bitte w&auml;hlen|} ...</option>
      <option value="freigeben">{|als freigegeben markieren|}</option>
      <option value="storniert">{|als storniert markieren|}</option>
      <option value="versendet">{|als versendet markieren|}</option>
      <option value="beauftragt">{|als beauftragt markieren|}</option>
      <option value="abgelehnt">{|als abgelehnt markieren|}</option>
      <option value="pdf">{|Sammel-PDF|}</option>
      <option value="drucken">{|drucken|}</option>
      [HOOK_SEL_ACTION]
    </select>&nbsp;{|Drucker|}: <select name="seldrucker">[SELDRUCKER]</select>&nbsp;<input type="submit" class="btnBlue" name="ausfuehren" id="ausfuehren" value="ausf&uuml;hren" />
  </fieldset>
</form>
</div>

<div id="tabs-3">
[TAB3]
</div>



</div>


<script>
  function alleauswaehlen()
  {
    var wert = $('#auswahlalle').prop('checked');
    $('#angebote').find(':checkbox').prop('checked',wert);
  }
</script>
