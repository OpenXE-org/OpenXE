<div id="tabs">
    <ul>
            <li><a href="#tabs-1">[TABTEXT1]</a></li>
    </ul>
    <div id="tabs-1">
        [MESSAGE]
        <form id="eprooform" method="post">
            <div class="row">
            	<div class="row-height">
            		<div class="col-xs-14 col-md-6 col-md-height">
            			<div class="inside inside-full-height">
            				<fieldset>
                                <legend>
                                    {|Lieferschein scannen|}</span>
                                </legend>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr>
                                        <td>
                                            {|Neues Paket erstellen oder Pakete anzeigen|}:
                                        </td>
                                        <td>
                                            <input type="text" name="lieferschein" id="lieferschein" value="[LIEFERSCHEIN]" autofocus size="40">
                                            <input type="submit" name="submit" value="lieferscheinscan" hidden>
                                        </td>
                                    </tr>
                                </table>
                            </fieldset>
                        </div>
               		</div>
                </div>
            </div>
            <div class="row">
                	<div class="row-height">
                		<div class="col-xs-14 col-md-6 col-md-height">
                			<div class="inside inside-full-height">
                				<div class="filter-box filter-usersave">
                                    <div class="filter-block filter-inline">
                                        <div class="filter-title">{|Filter|}</div>
                                        <ul class="filter-list">
                                            [STATUSFILTER]
                                            <li class="filter-item">
                                                <label for="unterwegs" class="switch">
                                                    <input type="checkbox" id="unterwegs">
                                                    <span class="slider round"></span>
                                                </label>
                                                <label for="unterwegs">{|Unterwegs|}</label>
                                            </li>
                                            <li class="filter-item">
                                                <label for="geschlossene" class="switch">
                                                    <input type="checkbox" id="geschlossene">
                                                    <span class="slider round"></span>
                                                </label>
                                                <label for="geschlossene">{|Zzgl. abgeschlossen|}</label>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                [TAB1]
                                [TAB1NEXT]
                            </div>
                   		</div>
                    </div>
           		</div>
            </div>
        </form>
    </div>
</div>
