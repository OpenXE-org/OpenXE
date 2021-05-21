<div class="row">
  <div class="row-height">
    <div class="col-xs-12 col-sm-6 col-sm-height">
      <div class="inside inside-full-height">
        <h2 class="greyh2">{|[BESCHRIFTUNG1]|}</h2>
        <div>[DIAGRAMMWOCHE]</div>
      </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-sm-height">
      <div class="inside inside-full-height">
        <h2 class="greyh2">{|Statistik|} {|[BELEGTYP]|} {|Heute|} {|[APIHINWEIS]|}</h2>
        [STATISTIKHEUTE]
        <h2 class="greyh2">{|Statistik|} {|[BELEGTYP]|} {|Gestern|} {|[APIHINWEIS]|}</h2>
        [STATISTIKGESTERN]
        <h2 class="greyh2">{|&Uuml;bersicht Auftr&auml;ge|} {|[APIHINWEIS]|}</h2>
        [STATISTIKAUFTRAEGE]
        <div id="aktiondiv"><input type="button" value="{|Details|}" onclick="openchart([ID]);"  />&nbsp;<input type="button" value="{|+ weiteres Diagaramm|}" onclick="openchart(0);" /></div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="row-height">
    [VORMONAT]
    <div class="col-xs-12 col-sm-12 col-sm-height">
      <div class="inside inside-full-height">
        <h2 class="greyh2">{|[BESCHRIFTUNG1]|}</h2>
        <div>[DIAGRAMMMONAT]</div>
      </div>
    </div>
    [NACHMONAT]
  </div>
</div>

