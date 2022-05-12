<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1"></a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
	
	[MESSAGE]

	<div class="row">
  <div class="row-height">
  <div class="col-xs-12 col-md-10 col-md-height">
  <div class="inside_white inside-full-height">

    <fieldset class="white">
      <legend>&nbsp;</legend>
        [TAB1]
    </fieldset>

  </div>
  </div>
  <div class="col-xs-12 col-md-2 col-md-height">
  <div class="inside inside-full-height">

  	<div class="tabsbutton" align="center">
			<fieldset>
				<legend>{|Aktionen|}</legend>
				<a href="#" onclick="if(!confirm('Soll eine neue Anfrage angelegt werden?')) return false; else window.location.href='index.php?module=service&action=createadresse&id=[ID]';">
					<input type="button" class="btnGreenNew" value="&#10010; Neue Anfrage anlegen">
				</a>
			</fieldset>
		</div>

  </div>
  </div>
  </div>
  </div>

	[TAB1NEXT]
</div>

<!-- tab view schließen -->
</div>

