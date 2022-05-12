<div id="tabs" class="report">
	<ul>
		<li><a href="#tabs-1">[TABTEXT]</a></li>
	</ul>
	<!-- ende gehort zu tabview -->

	<!-- erstes tab -->
	<div id="tabs-1">
		[MESSAGE]

		<div class="row" id="report_list_main">
			<div class="row-height">
				<div class="col-xs-12 col-sm-10 col-sm-height">

					<div class="inside">
						<fieldset>
							<form>
								<table width="100%">
									<tr>
										<div class="filter-box">
											<div class="filter-block filter-inline">
												<!--<div class="filter-title">{|Filter|}</div>-->
												<ul class="filter-list">
													<li class="filter-item">
														<label for="report-list-filter-own" class="switch">
															<input type="checkbox" id="report-list-filter-own">
															<span class="slider round"></span>
														</label>
														<label for="report-list-filter-own">{|Nur eigene|}</label>
													</li>
													<li class="filter-item">
														<label for="report-list-filter-favorites" class="switch">
															<input type="checkbox" value="foo" id="report-list-filter-favorites">
															<span class="slider round"></span>
														</label>
														<label for="report-list-filter-favorites">{|Nur Favoriten|}</label>
													</li>
												</ul>
											</div>
										</div>
									</tr>
									<tr>
										<td><legend>{|Kategorie|}</legend></td>
										<td><legend>{|Suche|}</legend></td>
										<td></td>
									</tr>
									<tr>
										<td width="20%"><input type="text" name="filter_category" value="" id="reportListFilterCategory"></td>
										<td width="65%"><input type="text" name="filter_term" value="" id="reportListFilterTerm"></td>
										<td width="40%"><input type="submit" name="filter_apply" value="{|Anwenden|}" id="reportListFilterApply"></td>
									</tr>
								</table>
							</form>
						</fieldset>
					</div>

					<div>
							[TAB1]
					</div>
				</div>
				<div class="col-xs-12 col-sm-2 col-sm-height">
					<div class="inside inside-full-height">
						<fieldset><legend>{|Aktion|}</legend>
							[NEWBTN]
						</fieldset>
					</div>
				</div>
			</div>
		</div>

		[TAB1NEXT]
	</div>

	<!-- tab view schließen -->
</div>


<div id="dialogInputReportParameter" style="display:none;" title="PARAMETER">
	<form method="post">
		<div>
			<fieldset>
				<table id="inputControlPanel" width="100%">
				</table>
			</fieldset>
		</div>
	</form>
</div>
<div id="dialogReportChart">
	<div id="dialogReportChartContent">

	</div>
</div>
