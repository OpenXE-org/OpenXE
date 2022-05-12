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

        <div class="col-xs-12 col-md-9 col-md-height">
					<div class="inside inside-full-height">
						<form action="index.php?module=supersearch&action=settings&cmd=save" method="post">
							<fieldset>
								<legend>{|Index-Statistik|}</legend>
								[INDEXSTATSTABLE]
							</fieldset>
						</form>
					</div>
				</div>

        <div class="col-xs-12 col-md-3 col-md-height">
					<div class="inside inside-full-height">
						<fieldset>
							<legend>{|Aktionen|}</legend>
							<table class="mkTableFormular" width="100%">
								<tr>
									<td>
										<div id="supersearch-fullindex-task-wrapper">
											<button id="supersearch-fullindex-task-trigger" class="button button-primary"
															type="button" name="cmd" value="run-full-index-task">
												{|Such-Index neuaufbauen|}
											</button>
										</div>
									</td>
								</tr>
							</table>
						</fieldset>
					</div>
				</div>

			</div>
		</div>

	</div>
</div>
