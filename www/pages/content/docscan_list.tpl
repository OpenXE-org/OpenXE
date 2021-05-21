<div id="tabs">
	<ul>
		<li><a href="#tabs-1">{|Dateien|}</a></li>
		<li><a href="#tabs-2">{|Upload|}</a></li>
	</ul>
	<div id="tabs-1">
		[MESSAGE]
		<div class="row" id="docscan-module">
			<div class="row-height">
				<div class="col-xs-12 col-md-8 col-md-height">
					<div class="inside-full-height">
						<div>
							<div class="filter-box">
								<div class="filter-block filter-inline">
									<div class="filter-title">{|Filter|}</div>
									<ul class="filter-list">
										<li class="filter-item">
											<label for="docscan_alle" class="switch">
												<input type="checkbox" id="docscan_alle" name="docscan_alle"
															 class="datatable-filter"
															 data-datatable-filter="show-all"
															 data-datatable-target="docscan_files">
												<span class="slider round"></span>
											</label>
											<label for="docscan_alle">{|Archiv|} ({|alle anzeigen|})</label>
										</li>
									</ul>
								</div>
							</div>
							[TAB1]
						</div>
					</div>
				</div>
				<div class="col-xs-12 col-md-4 col-md-height">
					<div class="inside inside-full-height">
						<fieldset>
							<legend>{|Vorschau + Upload|}</legend>
							<div id="dropzone-wrapper">
								<div id="dropzone" class="active"><h3>{|Dateien hier einfügen|}</h3></div>
								<iframe id="preview-iframe" width="100%" height="100%" src=""></iframe>
							</div>
						</fieldset>
					</div>
				</div>
			</div>
		</div>
		[TAB1NEXT]
	</div>
	<div id="tabs-2">
		[DOCSCANUPLOAD]
	</div>
</div>

<div id="docscan-files-dialog" class="hide">
	<div class="row">
		<div class="col-md-12">
			<div id="docscan-files-content">[FILEDIALOGCONTENT]</div><br>
		</div>
	</div>
</div>
