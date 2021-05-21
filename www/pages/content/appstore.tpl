<div id="appstore" class="[PAGETYPE]">

	<div class="appstore-header" style="[APPSTOREUEBERSICHTAUSBLENDEN]">
		<div class="appstore-tabs">
			<span class="appstore-tab [ACTIVEALL]" data-filter="all">{| App store |}</span>
			<span class="appstore-tab [ACTIVEINSTALLED]" data-filter="installed">{| My Apps |}</span>
			<a class="appstore-tab" href="update.php"><span class="tab-counter">[COUNTUPDATES]</span>{| Updates |}</a>
		</div>

		<h1 class="appstore-headline">{| Every business is unique.|}<br>{| Find the app that is right for yours. |}</h1>

		<div class="appstore-search">
			<input type="text" class="search-input" id="appstore-search" placeholder="{|Search by name or category (e.g. Accounting, Marketing)|}"/>
		</div>
	</div>

	<div class="category-page-nav">
		<div class="category-page-back">
			<svg width="8" height="12" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M0.902682 5.67084L6.44381 0.129715C6.62725 -0.0474495 6.91956 -0.0423629 7.09673 0.141079C7.26956 0.320029 7.26956 0.603714 7.09673 0.782637L1.88206 5.9973L7.09673 11.212C7.277 11.3923 7.277 11.6846 7.09673 11.8649C6.9164 12.0452 6.62411 12.0452 6.44381 11.8649L0.902682 6.32376C0.722406 6.14343 0.722406 5.85114 0.902682 5.67084Z" fill="#374051" fill-opacity="0.6"/>
			</svg>
		</div>
		{|Kategorien|} / <b>[CATEGORYFILTER]</b>
	</div>
	<h2>{|Kategorien|}</h2>
	<div class="appstore-categories">
		[SUCHFILTER]
	</div>

	<div class="detail" style="[APPSTOREDETAILSEITEAUSBLENDEN]">
		<div class="information clearfix">
			[MODULINFORMATION]
		</div>
		<div class="screenshots clearfix">
			[MODULSCREENSHOTS]
		</div>
	</div>

	<div class="overview" style="[APPSTOREUEBERSICHTAUSBLENDEN]">

		<div class="popular" style="[POPULARVISIBILITY]">
			<h2>{|Populäre Apps|}</h2>
			<div class="modules">
				[HIGHLIGHTS]
			</div>
		</div>

		<div class="available-apps" style="[AVAILABLEVISIBILITY]">
			<h2>{|Verfügbare Apps|}</h2>
			<div class="modules">
				[MODULEVERFUEGBAR]
			</div>
		</div>

		<div class="purchases" style="[INSTALLIERTEAUSBLENDEN]">
			<h2>{|Meine Apps|}</h2>
			<div class="modules">
				[MODULEINSTALLIERT]
			</div>
		</div>
	</div>

	<div id="anfragepopup">
		<input type="hidden" value="" id="anfragemd5">
		<p>{|Bitte best&auml;tigen Sie die Aktivierung der Testphase.|}</p>
	</div>
	<div id="anfrageokpopup">
		<p id="anfrageoknachricht"></p>
	</div>
</div>
<div id="modalbuy" data-updatekey="0">
	<fieldset><legend>{|Freischalten|}</legend>
		<label id="modulbuytext" for="newvalue">

		</label>
		<input type="text" value="0" id="newvalue" />
	</fieldset>
</div>
