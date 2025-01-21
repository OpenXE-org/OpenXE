<div id="tabs">
    <ul>
            <li><a href="#tabs-1">[TABTEXT1]</a></li>
    </ul>
    <div id="tabs-1">
        <form method="post" action="#">        
            <div class="filter-box filter-usersave" [ZU_MAHNEN_HIDDEN]>
                <div class="filter-block filter-inline">
                  <div class="filter-title">{|Filter|}</div>
                  <ul class="filter-list">
                    <li class="filter-item">
                      <label for="zu_mahnen" class="switch">
                        <input type="checkbox" id="zu_mahnen">
                        <span class="slider round"></span>
                      </label>
                      <label for="zu_mahnen">{|Nur zu mahnende|}</label>
                    </li>  
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
                  <input type="submit" class="btnBlue" name="mahnstufe_berechnen" value="{|Mahnstufe berechnen|}" />
                </div>    
            </div>                 
        </form>
        <form method="post" action="#">
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
                    <option value="mahnung_reset">{|Mahnstatus zur&uuml;cksetzen|}</option>
                    <option value="mahnen">{|Mahnung durchf&uuml;hren|}</option>
                <!--    <option value="email">{|Mahnung durchf&uuml;hren (nur E-Mail)|}</option>
                    <option value="email">{|Mahnung durchf&uuml;hren (nur Drucken)|}</option> -->
                </select>&nbsp;{|Drucker|}: <select name="seldrucker">[SELDRUCKER]</select>&nbsp;<input type="submit" class="btnBlue" name="ausfuehren" value="{|ausf&uuml;hren|}" />
            </fieldset>   
        </form>
    </div> 
</div>

<script>
function alleauswaehlen()
{
  var wert = $('#auswahlalle').prop('checked');
  $('#mahnwesen_list').find(':checkbox').prop('checked',wert);
}
</script>
