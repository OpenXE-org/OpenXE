<div>
	<div class="row">
		<div class="row-height">
			<div class="col-md-12 col-md-height">
				<div class="inside-full-height">
					<div>
						<div class="clearfix">
							[MESSAGE]
							<fieldset>
								<legend>{|Filter|}</legend>
								<div>
									<label>&nbsp;&nbsp;
										<input type="checkbox" id="nicht_gedruckt" name="nicht_gedruckt">{|Nur nicht gedruckte|}
									</label>
								</div>
							</fieldset>
						</div>
						<form action="" method="post">
							[SPOOLERDIALOGCONTENT]
							<fieldset>
								<legend>{|Stapelverarbeitung|}</legend>
								<label><input type="checkbox" id="markall_trigger">&nbsp;{|alle markieren|}&nbsp;</label>
								<input type="submit" class="btnBlue" value="{|ZIP erstellen|}" name="makezip">
								<input type="submit" class="btnBlue" value="{|Sammel-PDF &ouml;ffnen|}" name="makepdf">
								<input type="hidden" name="markall_selection" id="markall_selection">
							</fieldset>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
