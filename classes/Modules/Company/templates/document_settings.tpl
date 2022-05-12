{namespace key='company_data'}
{block name="document_settings_list"}
		<div class="row">
				<div class="row-height">
						<div class="col-sm-12 col-md-10 col-md-height">
								<div class="inside-white inside-full-height">
										{$datatable}
									  <fieldset>
											  <legend>{translate key='infobox_stack'}Stapelverarbeitung{/translate}</legend>
											  <table>
														<tr>
																<td>
											  						<input type="checkbox" id="selectall" /> <label for="selectall">{translate key='select_all'}Alle markieren{/translate}</label>
																</td>
															  <td>
																	  <label for="actionselection">Aktion:</label>
																</td>
																<td>
																		<select id="actionselection">
																				<option value="">bitte w&auml;hlen...</option>
																				<option value="activate">aktivieren</option>
																				<option value="deactivate">deaktiveren</option>
																		</select>
																</td>
															  <td>
																	  <input type="button" id="doaction" value="ausf&uuml;hren" />
																</td>
														</tr>
												</table>
										</fieldset>
								</div>
						</div>
						<div class="col-sm-12 col-md-2 col-md-height">
								<div class="inside inside-full-height">
									  <fieldset>
											  <legend>{translate key='action'}Aktionen{/translate}</legend>
											  <input type="button" class="edit btnGreenNew" data-id="0" value="{translate key='newbutton'}&#10010; Neuer Eintrag{/translate}" />
										</fieldset>
								</div>
						</div>
				</div>
		</div>
{/block}

{block name="document_popup"}
	<div id="document_popup">
	<fieldset>
		<legend>{translate key='infobox'}Infobox{/translate}</legend>
		<table>
			<tr>
				<td><label for="document_doctype">{translate key='format'}Dokument{/translate}:</label></td>
				<td>
					<select id="document_doctype">
						{foreach from=$doctypearr key=doctypekey item=doctypeval}
							<option value="{$doctypekey}">{$doctypeval}</option>
						{/foreach}
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="document_project">{translate key='format'}Projekt{/translate}:</label></td>
				<td><input type="text" id="document_project" name="document_project" /></td>
			</tr>
			<tr>
				<td><label for="document_alignment">{translate key='format'}Ausrichtung{/translate}:</label></td>
				<td>
					<select name="document_alignment" id="document_alignment">
						{foreach from=$alignments key=alignkey item=alignoption}
							<option value="{$alignkey}">{$alignoption}</option>
						{/foreach}
					</select>
				</td>
				<td>
				</td>
			</tr>
			<tr>
				<td><label for="document_fontstyle">{translate key='format'}Format{/translate}:</label></td>
				<td>
					<select name="document_fontstyle" id="document_fontstyle">
						{foreach from=$fontoptions key=fontkey item=fontoption}
							<option value="{$fontkey}" {if $fontkey==$documentarr.fontstyle} selected="selected" {/if}>{$fontoption}</option>
						{/foreach}
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="document_active">{translate key='format'}Aktiv{/translate}:</label></td>
				<td>
					<input type="hidden" id="document_id" />
					<input type="checkbox" value="1" name="document_active" id="document_active" />
				</td>
			</tr>
			<tr>
				<td><label for="document_content">{translate key='elements'}Elemente{/translate}:</label></td>
				<td><textarea cols="35" rows="15" name="document_content" id="document_content">{$documentarr.content}</textarea></td>
				<td><button class="button button-primary getelements" alt="{translate key='getelement'}&Uuml;bernehmen{/translate}"><</button></td>
				<td rowspan="3">
					<select multiple size="15" id="document_preview">
						{foreach from=$documentarr.preview key=prevkey item=prevval}
							<option class="{foreach from=$prevval.doc_types key=dockey item=docval}doctype-{$docval} {/foreach}" value="{$prevval.value}">{$prevval.label}</option>
						{/foreach}
					</select>
				</td>
				<td style="vertical-align:top"><img src="./themes/new/images/globe.svg" id="opentranslation" class="translate" alt="globe" /></td>
		</table>
		<br />
		Bitte beachten Sie, dass nicht alle Variablen in allen Belegen verfügbar sind!
	</fieldset>
	</div>
{/block}
{block name="translation_popup"}
	  <div id="translation_popup">
			  <fieldset>
						<legend>{translate key='settings'}Infobox &Uuml;bersetzung{/translate}</legend>
					  <table>
							<tr>
								<td><label for="language">{translate key='language'}Sprache{/translate}:</label></td>
								<td>
									<select id="language">
										{foreach from=$languages key=langkey item=langoption}
											<option value="{$langkey}">{$langoption}</option>
										{/foreach}
									</select>
								</td>
								<td></td>
							</tr>
							<tr>
								<td><label for="document_translation_alignment">{translate key='format'}Ausrichtung{/translate}:</label></td>
								<td>
									<select name="document_translation_alignment" id="document_translation_alignment">
										{foreach from=$alignments key=alignkey item=alignoption}
											<option value="{$alignkey}">{$alignoption}</option>
										{/foreach}
									</select>
								</td>
								<td></td>
							</tr>
							<tr>
								<td><label for="document_translation_fontstyle">{translate key='format'}Format{/translate}:</label></td>
								<td>
									<select name="document_translation_fontstyle" id="document_translation_fontstyle">
										{foreach from=$fontoptions key=fontkey item=fontoption}
											<option value="{$fontkey}" {if $fontkey==$documentarr.fontstyle} selected="selected" {/if}>{$fontoption}</option>
										{/foreach}
									</select>
								</td>
								<td></td>
							</tr>
							<tr>
								<td><label for="active">{translate key='active'}Aktiv{/translate}:</label></td>
								<td><input type="hidden" id="doctype" /><input type="checkbox" value="1" id="active" /></td>
								<td></td>
							</tr>
							<tr>
								<td><label for="translationcontent">{translate key='translation'}&Uuml;bersetzung{/translate}:</label></td>
							  <td><textarea id="translationcontent" cols="35" rows="15" ></textarea></td>
								<td><button class="button button-primary gettranslationelements" alt="{translate key='getelement'}&Uuml;bernehmen{/translate}"><</button></td>
								<td>
									<select multiple size="15" id="document_translation_preview">
										{foreach from=$documentarr.preview key=prevkey item=prevval}
											<option class="{foreach from=$prevval.doc_types key=dockey item=docval}doctype-{$docval} {/foreach}" value="{$prevval.value}">{$prevval.label}</option>
										{/foreach}
									</select>
								</td>
							</tr>
						</table>
					<br />
					Bitte beachten Sie, dass nicht alle Variablen in allen Belegen verfügbar sind!
				</fieldset>
		</div>
{/block}