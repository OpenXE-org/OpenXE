<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT1]</a></li>
    </ul>
    <div id="tabs-1">
        [MESSAGE]
        <form action="" method="post">            
            [FORMHANDLEREVENT]
            <div class="filter-box filter-usersave">
                <div class="filter-block filter-inline">
                    <div class="filter-title">{|Filter|}
                    </div>
                    <ul class="filter-list">
                        <li class="filter-item">
                            <label for="geschlossenewareneingaenge" class="switch">
                                <input type="checkbox" id="geschlossenewareneingaenge" />
                                <span class="slider round">
                                </span>
                            </label>
                            <label for="geschlossenewareneingaenge">
                                {|Zzgl. geschlossene Wareneing&auml;nge|}
                            </label>
                        </li>
                    </ul>
                </div>
            </div>
        </form>
        <div class="row" [ARTIKEL_HIDDEN]>
        [TAB1]
        [TAB1NEXT]
        </div>
    </div>
</div>
