<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT1]</a></li>
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
                      <label for="angelegte" class="switch">
                        <input type="checkbox" id="angelegte">
                        <span class="slider round"></span>
                      </label>
                      <label for="angelegte">{|Angelegt|}</label>
                    </li>           
                    <li class="filter-item">
                      <label for="offene" class="switch">
                        <input type="checkbox" id="offene">
                        <span class="slider round"></span>
                      </label>
                      <label for="offene">{|Offen|}</label>
                    </li>
                    <li class="filter-item">
                      <label for="geschlossene" class="switch">
                        <input type="checkbox" id="geschlossene">
                        <span class="slider round"></span>
                      </label>
                      <label for="geschlossene">{|Abgeschlossen|}</label>
                    </li>
                    <li class="filter-item">
                      <label for="stornierte" class="switch">
                        <input type="checkbox" id="stornierte">
                        <span class="slider round"></span>
                      </label>
                      <label for="stornierte">{|Papierkorb|}</label>
                    </li>
                  </ul>
                </div>
            </div>
        </form>
        [TAB1]
        [TAB1NEXT]
    </div>
</div>
