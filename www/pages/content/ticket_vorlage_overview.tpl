<!-- gehort zu tabview -->
<script type="text/javascript" src="js/aciTree/js/jquery.aciPlugin.min.js"></script>
<script type="text/javascript" src="js/aciTree/js/jquery.aciTree.min.js"></script>
<link rel="stylesheet" type="text/css" href="js/aciTree/css/aciTree.css">
<div id="tabs">
	<ul>
		<li><a href="#tabs-1"><!--[TABTEXT]--></a></li>
	</ul>
	<!-- ende gehort zu tabview -->

	<!-- erstes tab -->
	<div id="tabs-1">
		[MESSAGE]

		<div class="row">
			<div class="row-height">
				<div class="col-xs-12 col-sm-4 col-sm-height">
					<div class="inside inside-full-height">
						<div class="mlmTreeContainerLeft">
							<fieldset>
								<legend>{|Suche|}</legend>
								<div class="mlmTreeSuche"><label for="search">{|Bezeichnung|}:</label>
									<input id="search" type="text" value="">
									<hr>
								</div>
							</fieldset>
							<br><br>
							<div id="mlmTree" class="aciTree"></div>
						</div>
					</div>
				</div>
				<div class="col-xs-12 col-sm-8 col-sm-height">
					<div class="inside-full-height">
						<div class="mlmTreeContainerRight">
							<div class="row">
								<div class="row-height">
									<div class="col-xs-12 col-sm-4 col-sm-height">
										<div class="inside inside-full-height">
											<fieldset>
												<legend id="categorielegend">{|Neue Kategorie|}</legend>
												<table>
													<tr>
														<td colspan="2" nowrap="">
															<label for="categoriename">{|Name|}:</label>
															<input type="text" id="categoriename" /><input type="hidden" id="categoryid" />
														</td>
													</tr>
													<tr>
														<td>
															<input type="button" class="button button-primary" value="Speichern" id="savecategory" />
														</td>
														<td>
															<input type="button" class="button button-secondary" value="Löschen" id="deletecategory" />
														</td>
													</tr>
												</table>
											</fieldset>
										</div>
									</div>
									<div class="col-xs-12 col-sm-4 col-sm-height">
										<div class="inside inside-full-height">
											<fieldset>
												<legend id="categorielegend">{|Neue Unterkategorie|}</legend>
												<table>
													<tr>
														<td nowrap="">
															<label for="subcategoriename">{|Name|}:</label>
															<input type="text" id="subcategoriename" />
															<input type="hidden" id="subcategoryid" />
														</td>
													</tr>
													<tr>
														<td>
															<input type="button" class="button button-primary" value="Speichern" id="savesubcategory" />
														</td>
													</tr>
												</table>
											</fieldset>
										</div>
									</div>
									<div class="col-xs-12 col-sm-4 col-sm-height">
										<div class="inside inside-full-height">
											<fieldset>
												<legend id="categorielegend">{|Neue Vorlage|}</legend>
												<table>
													<tr>
														<td><label for="newtemplatename">{|Name|}:</label></td>
														<td class="placeholeder">
															<input type="text" id="newtemplatename" />
														</td>
													</tr>
													<tr>
														<td colspan="2">
															<input type="button" class="button button-primary" value="Speichern" id="newtemplate" />
														</td>
													</tr>
												</table>
											</fieldset>
										</div>
									</div>
								</div>
							</div>
							[TAB1]
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="mlmClear"></div>

		[TAB1NEXT]
	</div>

	<!-- tab view schließen -->
</div>

<div id="tickettemplatepopup" class="hidden">
	<fieldset>
		<legend></legend>
		<table>
			<tr>
				<td>
					<label for="itemname">{|Vorlage|}:</label>
				</td>
				<td>
					<input type="text" id="itemname" size="40" /><input type="hidden" id="itemid" />
				</td>
			</tr>
			<tr>
				<td>
					<label for="itemtext">{|Text|}:</label>
				</td>
				<td>
					<textarea id="itemtext" name="itemtext"></textarea>
				</td>
			</tr>
			<tr>
				<td>
					<label for="itemproject">{|Projekt|}:</label>
				</td>
				<td>
					<input type="text" id="itemproject" size="40" />
				</td>
			</tr>
			<tr>
				<td>
					<label for="itemvisible">{|In-Aktiv|}:</label>
				</td>
				<td>
					<input type="checkbox" id="itemvisible" value="1" />
				</td>
			</tr>
			<tr>
				<td>
					<label for="itemcategory">{|Kategorie|}:</label>
				</td>
				<td>
					<input type="text" id="itemcategory" size="40" />
				</td>
			</tr>
			<tr>
				<td>
					<label for="itemsort">{|Sortierung|}:</label>
				</td>
				<td>
					<input type="text" id="itemsort" size="10" />
				</td>
			</tr>
		</table>
	</fieldset>
</div>