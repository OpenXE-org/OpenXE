
<div id="tabs" class="report">
	<ul>
		<li><a href="#tabs-1">[TABTEXT]</a></li>
	</ul>
	<!-- ende gehort zu tabview -->

	<!-- erstes tab -->
	<div id="tabs-1" data-id="[REPORT_ID]">
		[MESSAGE]

		<div class="row">
			<div class="row-height">
				[INPUT_PARAMETERS]
				<!--<div class="col-xs-12 col-sm-2 col-sm-height">
					<div class="inside inside-full-height">
						<fieldset id="view-input-parameters">
							<legend>{|Parameter|}</legend>
								[INPUT_PARAMETERS]
						</fieldset>
					</div>
				</div>-->
				<div class="col-xs-12 col-sm-10 col-sm-height">
					<div class="inside inside-full-height">
						[TABLEVIEW]
					</div>
				</div>
			</div>
		</div>

		[LOADSCRIPT]

		[TAB1NEXT]
	</div>

	<!-- tab view schließen -->
</div>
