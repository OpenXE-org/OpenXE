<div id="tabs">
	<ul>
		<li><a href="#tabs-1">Offene Anträge</a></li>
		<li><a href="#tabs-2">Abgeschlossene Anträge</a></li>
	</ul>
	<div id="tabs-1">

		<div id="timemanagement-handle-dialog" style="display:none" title="Urlaub freigeben">
			<div id="timemanagement-handle-msg"></div>
			<div id="request-title" style="display:none">{|Freigeben|}</div>
			<div id="delete-title" style="display:none">{|Löschen|}</div>
			<div id="default-note-vacation" style="display:none">{|Schönen Urlaub!|}</div>
			<div id="default-note-sick" style="display:none">{|Gute Genesung!|}</div>
			<form action="" method="post" id="timemanagement-handle-form">
				<input type="hidden" name="request-token" id="request-token"/>
				<input type="hidden" name="request-address-id" id="request-address-id"/>
				<input type="hidden" name="request-reject" id="request-reject" value="0"/>
				<div class="row-height">
					<div class="col-xs-12 col-md-3 col-md-height">
						<div class="inside inside-full-height">
							<fieldset>
								<legend>{|Info|}</legend>
								<p>{|Mitarbeiternummer|}: <span id="employee-number"></span></p>
								<p>{|Name|}: <span id="employee-name"></span></p>
								<p>{|Von|}: <span id="from"></span></p>
								<p>{|Bis|}: <span id="till"></span></p>
								<p>{|Anzahl der Tage|}: <span id="amount"></span></p>
								<p>{|Kommentar|}: <span id="comment"></span></p>
							</fieldset>
						</div>
					</div>
					<div class="col-xs-12 col-md-3 col-md-height">
						<div class="inside inside-full-height">
							<fieldset>
								<p>
									<label for="internal-comment">{|Notiz an Mitarbeiter|}:</label>
								</p>
								<div>
									<textarea id="internal-comment" style="width:100%" name="internal-comment"></textarea>
								</div>
							</fieldset>
						</div>
					</div>
				</div>
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
			</div>
		</div>
		[TAB1NEXT]
	</div>
	<div id="tabs-2">
		<div class="row">
			<div class="row-height">
				<div class="col-xs-12 col-md-10 col-md-height">
					<div class="inside_white inside-full-height">
						[TAB2]
					</div>
				</div>
			</div>
		</div>
		[TAB2NEXT]
	</div>
</div>