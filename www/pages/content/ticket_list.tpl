<div id="tabs">
    <ul>
            <li><a href="#tabs-1">[TABTEXT1]</a></li>
           [VORTABS2UEBERSCHRIFT]<li><a href="#tabs-2">[TABTEXT2]</a></li>[NACHTABS2UEBERSCHRIFT]
    </ul>
    <div id="tabs-1">
      [MESSAGE]
      <form action="#tabs-1" id="frmauto" name="frmauto" method="post">
        <div class="filter-box filter-usersave">
            <div class="filter-block filter-inline">
              <div class="filter-title">{|Filter|}</div>
              <ul class="filter-list">
                [STATUSFILTER]
                <li class="filter-item">
                  <label for="meinetickets" class="switch">
                    <input type="checkbox" id="meinetickets">
                    <span class="slider round"></span>
                  </label>
                  <label for="meinetickets">{|Meine|}</label>
                </li>           
                <li class="filter-item">
                  <label for="prio" class="switch">
                    <input type="checkbox" id="prio">
                    <span class="slider round"></span>
                  </label>
                  <label for="prio">{|Prio|}</label>
                </li>
                <li class="filter-item">
                  <label for="geschlossene" class="switch">
                    <input type="checkbox" id="geschlossene">
                    <span class="slider round"></span>
                  </label>
                  <label for="geschlossene">{|+Geschlossene|}</label>
                </li>
                <li class="filter-item">
                  <label for="archiv" class="switch">
                    <input type="checkbox" id="archiv">
                    <span class="slider round"></span>
                  </label>
                  <label for="archiv">{|+&Auml;lter als 1 Jahr|}</label>
                </li>                  
              </ul>
            </div>
          </div>
        </form>
      [TAB1]
      [TAB1NEXT]
    </div>
</div>
