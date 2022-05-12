<div id="tabs">
	<ul>
		<li><a href="#tabs-1"></a></li>
	</ul>
	<div id="tabs-1">
		<div class="filter-box filter-usersave">
			<div class="filter-block filter-inline">
				<ul class="filter-list">
					<li class="filter-item">
						[JAHRSELECT]
					</li>
					<li class="filter-item">
						[GRUPPENSELECT]
					</li>
					<li class="filter-item">
						[SUMMIERENCHECK]
					</li>
				</ul>
			</div>
		</div>
		<div id="timemanagement-new-edit" style="display:none">
			<div id="timemanagement-msg"></div>
			<div id="request-title" style="display:none">{|Beantragen|}</div>
			<div id="delete-title" style="display:none">{|Entfernen|}</div>
			<form action="" method="post" id="timemanagement-form">
				<fieldset>
					<input type="hidden" name="timemanagement-edit-id" id="timemanagement-edit-id"/>
					<input type="hidden" name="status-change" id="status-change" value="1"/>
					<input type="hidden" name="status-old-type" id="status-old-type" value=""/>
					<div class="row">
						<div class="row-height">
							<div class="col-xs-12 col-md-12 col-md-height">
								<div class="inside_white inside-full-height">
									<fieldset>
										<legend>{|Info|}</legend>
										<p>{|Urlaubsanspruch|}: <span id="total-vacation"></span></p>
										<p>{|Beantragter Urlaub|}: <span id="planned-vacation"></span></p>
										<p>{|Freigegebener Urlaub|}: <span id="accepted-vacation"></span></p>
										<p><strong>{|Verfügbare Urlaubstage|}:</strong> <span id="remaining-vacation"></span></p>
										<!--p>{|Notiz|}: <span id="internal-comment"></span></p-->
									</fieldset>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="row-height">
							<div class="col-xs-12 col-md-12 col-md-height">
								<div class="inside_white inside-full-height">
									<fieldset>
										<legend>{|Eingabe|}</legend>
										<p>
											<span id="status-wish-type-box">
												<label for="status-wish-type">{|Typ|}:</label>
												<select id="status-wish-type" name="status-wish-type">
													<option value="U">{|Urlaub|}</option>
													<option value="K">{|Krankheit|}</option>
												</select>
											</span>

											<label for="from">{|von|}:</label>
											<input type="text" name="from" id="from" size="10"/>

											<label for="till">{|bis|}:</label>
											<input type="text" name="till" id="till" size="10"/>

											<label class="switch">
												<input id="halfday" name="halfday" type="checkbox">
												<span class="slider round"></span>
											</label>
											<label for="halfday">{|Halber Tag|}</label>
										</p>
										<p>
											<label for="comment">{|Nachricht|}</label>:
										</p>
										<p>
											<textarea style="width:100%" name="comment" id="comment"></textarea>
										</p>
									</fieldset>
								</div>
							</div>
						</div>
					</div>

				</fieldset>
			</form>
		</div>

		[MESSAGE]
		<div class="row">
			<div class="row-height">
				<div class="col-xs-12 col-md-10 col-md-height">
					<div class="inside_white inside-full-height">
						[TAB1]
					</div>
				</div>
				<div class="col-xs-12 col-md-2 col-md-height">
					<div class="inside inside-full-height">
						<fieldset>
							<legend>{|Aktionen|}</legend>
							<a class="neubuttonlink" id="timemanagement-new-dialog" href="#">
								<input
									type="button"
									value="&#10010; {|Antrag stellen|}"
									class="btnGreenNew"
								>
							</a>
						</fieldset>
					</div>
				</div>
			</div>
		</div>
		[TAB1NEXT]
	</div>
</div>