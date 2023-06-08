<div id="tabs">
    <ul>
            <li><a href="#tabs-1">[TABTEXT1]</a></li>
    </ul>
    <div id="tabs-1">

        <form method="post" action="#">

            <div class="filter-box filter-usersave">
                <div class="filter-block filter-inline">
                  <div class="filter-title">{|Filter|}</div>
                  <ul class="filter-list">
                    <li class="filter-item">
                      <label for="inkl_bezahlte" class="switch">
                        <input type="checkbox" id="inkl_bezahlte">
                        <span class="slider round"></span>
                      </label>
                      <label for="inkl_bezahlte">{|Inkl. bezahlte|}</label>
                    </li>  
                   <li class="filter-item">
                      <label for="inkl_gesperrte" class="switch">
                        <input type="checkbox" id="inkl_gesperrte">
                        <span class="slider round"></span>
                      </label>
                      <label for="inkl_gesperrte">{|Inkl. gesperrte|}</label>
                    </li>               
                  </ul>
                  <input type="submit" class="btnBlue" name="zahlungsstatus_berechnen" value="{|Zahlungsstatus berechnen|}" />
                </div>    
              </div>     
            [MESSAGE]
            [TAB1]
            [TAB1NEXT]
            <fieldset>
                <legend>{|Stapelverarbeitung|}</legend>
                <input type="checkbox" id="auswahlalle" onchange="alleauswaehlen();" />&nbsp;{|alle markieren|}&nbsp;
                <select id="sel_aktion" name="sel_aktion">
                    <option value="">{|bitte w&auml;hlen|} ...</option>
                    [ALSBEZAHLTMARKIEREN]
                    <option value="offen">{|als offen markieren|}</option>
                    <option value="mahnen">{|Mahnenstufe erh&ouml;hen|}</option>
                    <option value="email">{|Mahnungen versenden (E-Mail)|}</option>
                    <option value="drucken">{|Mahnungen drucken|}</option>
                </select>&nbsp;{|Drucker|}: <select name="seldrucker">[SELDRUCKER]</select>&nbsp;<input type="submit" class="btnBlue" name="ausfuehren" value="{|ausf&uuml;hren|}" />
            </fieldset>   
        </div> 
    </form>
</div>

<script>
function alleauswaehlen()
{
  var wert = $('#auswahlalle').prop('checked');
  $('#mahnwesen_list').find(':checkbox').prop('checked',wert);
}
</script>
