<!-- gehort zu tabview -->
<div id="tabs">
	<ul>
		<li><a href="#tabs-1"></a></li>
	</ul>
	<div id="tabs-1">
		<div id="bmdledgernewedit" style="display:none" title="Bearbeiten">
			<div id="ledgermsg"></div>
			<form action="" method="post">
				<div class="row">
					<div class="row-height">
						<div class="col-xs-12 col-md-12 col-md-height">
							<div class="inside inside-full-height">
								<fieldset>
									<legend>{|Erlöskonto|}</legend>
									<table>
										<tr>
											<td width="120">
												{|Konto|}:
											</td>
											<td>
												<input type="text" name="revenueledger" id="revenueledger" size="40" />
											</td>
										</tr>
										<tr>
											<td>
												{|Bezeichnung|}:
											</td>
											<td>
												<input type="text" name="label" id="label" size="40" />
											</td>
										</tr>
										<tr>
											<td>
												{|USt-Steuercode|}:
											</td>
											<td>
												<input type="text" name="taxcode" id="taxcode" size="40" />
											</td>
										</tr>
										<tr>
											<td>
												{|USt-Prozentsatz|} %:
											</td>
											<td>
												<input type="text" name="salestaxpercent" id="salestaxpercent" size="40" />
											</td>
										</tr>
										<tr>
											<td></td>
											<td>
												<input type="hidden" name="revenueledgereditid" id="revenueledgereditid"/>
											</td>
										</tr>
									</table>
								</fieldset>
							</div>
						</div>
					</div>
				</div>
				<input type="submit" value="{|Speichern|}" name="bmdledgerSave" class="btnGreen pull-right" />
			</form>
		</div>

		<div id="bmdledgerdelete" style="display:none" title="{|Erlöskonto löschen|}">
			<form action="" method="post">
				<table>
					<tr>
						<td>
                            {|Konto wirklich löschen|}?
							<input type="hidden" name="revenueledgerdeleteid" id="revenueledgerdeleteid"/>
						</td>
						<td><input type="submit" value="{|Löschen|}" name="bmdledgerDelete" class="btnGreen pull-right" /></td>
					</tr>
				</table>
			</form>
		</div>


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

						<fieldset>
							<legend>{|Aktionen|}</legend>
							<!--input class="btnGreenNew" type="button" name="neuedit" value="&#10010; Neues Erlöskonto eintragen" onclick="neuedit(0);"-->
							<a class="neubuttonlink" id="ledgernewdialog" href="#"><input type="button" value="&#10010; {|Neues Erlöskonto eintragen|}" class="btnGreenNew"></a>
						</fieldset>

					</div>
				</div>
			</div>
		</div>

		[TAB1NEXT]



	</div>


	<!-- tab view schließen -->
</div>
