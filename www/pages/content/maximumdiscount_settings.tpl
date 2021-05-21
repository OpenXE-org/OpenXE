<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1"><!--[TABTEXT]--></a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]
    <form method="POST">
        <div class="row">
            <div class="row-height">
                <div class="col-xs-12 col-md-6 col-md-height">
                    <div class="inside inside-full-height">
                        <fieldset><legend>{|Maximal Rabatt pro Artikel|}</legend>
                            <table class="mkTableFormular">
                                <tr><td>{|aktivieren|}:</td><td><input type="checkbox" value="1" [MAXDISCOUNTARTICLE] name="maxdiscountarticle" id="maxdiscountarticle"/></td></tr>
                                <tr><td>{|Freifeld f&uuml;r Rabatt in %|}:</td><td><select name="maxdiscountfield">[MAXDISCOUNTFIELD]</select></td></tr>
                            </table>
                        </fieldset>
                    </div>
                </div>
                <div class="col-xs-12 col-md-6 col-md-height">
                    <div class="inside inside-full-height">
                        <fieldset></fieldset>
                    </div>
                </div>
            </div>
        </div>
        <input type="submit" value="Speichern" name="speichern" style="float:right"/>
    </form>
[TAB1NEXT]
</div>

<!-- tab view schließen -->
</div>

