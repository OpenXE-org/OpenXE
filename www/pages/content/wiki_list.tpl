<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1"></a></li>
    </ul>
    <!-- ende gehort zu tabview -->

    <!-- erstes tab -->
    <div id="tabs-1">
			[WIKISUBMENU]
			<div class="row">
				<div class="row-height">
					<div class="col-xs-12 col-sm-12 col-sm-height">
						<div class="inside-full-height">
							<fieldset class="white">
								[MESSAGE]
								<div id="wikicontent" class="wikilistcontent" data-site="[WIKISITE]">
									[TAB1]
								</div>
							</fieldset>
						</div>
					</div>
					<!--<div class="col-xs-12 col-sm-2 col-sm-height">
						<div class="inside inside-full-height">
								<fieldset>
										<legend>{|Aktionen|}</legend>
										<table class="wikiright" id="tabwikilist">-->
											<!--<tr>
												<td colspan="3">
													<a href="index.php?module=wiki&action=new">
														<input type="button" value="+ neue Seite" class="btnGreenNew" />
													</a>
												</td>
											</tr>-->
											<!--<tr>
												<td colspan="3">
													<a href="index.php?module=wiki&action=edit&cmd=[WIKISITE]">
														<input type="button" value="Seite bearbeiten" class="btnBlueNew" />
													</a>
												</td>
											</tr>-->
												<!--<tr class="labeltr">
													<td><label for="labels">{|Labels|}:</label></td>
													<td></td>
													<td><a href="#" class="label-manager" data-label-column-number="2" data-label-reference-id="[ID]" data-label-reference-table="wiki"><img src="./themes/new/images/label.svg"></a></td>
												</tr>-->
												<!--<tr>
													<td><label for="language">{|Sprache|}:</label></td>
														<td colspan="2">
																<select id="language" name="language">
																		<option value="">{|Default|}</option>
																		[SELLANGUAGE]
																</select>
														</td>
												</tr>-->


												<!--<tr>
														<td colspan="2"><input type="button" id="save" class="btnGreenNew" value="{|Speichern|}" /></td>
												</tr>-->
										<!--</table>
								</fieldset>
						</div>
					</div>-->
				</div>
			</div>
    </div>
</div>
<div id="changepopup">
    <fieldset>
        <legend>{|Was haben Sie ge&auml;ndert?|}</legend>
        <table>
            <tr>
                <td><label for="comment">{|Grund|}</label></td>
                <td><input type="text" id="comment" size="50" /></td>
            </tr>
            <tr>
                <td></td>
                <td><input type="checkbox" value="1" id="notify" />
                    <label for="notify">{|Beobachter benachrichtigen|}</label>
                </td>
            </tr>
        </table>
    </fieldset>
</div>