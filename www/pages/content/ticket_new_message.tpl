<div class="row">
	<div class="row-height">
        <div class="col-xs-14 col-md-12 col-md-height">
			<div class="inside inside-full-height">
				<fieldset>
                    <legend>{|Neue E-Mail|}</legend>
                    <input type="hidden" name="type" value="email">
                    <input type="hidden" name="eintragId" value="[EINTRAGID]">
                    <div style="position: absolute; margin-top:27em; margin-left:30%; min-width: 90px; background: url(./themes/new/images/loading.gif) no-repeat; background-position: 50% 0px; background-size: 150px; padding-top: 90px; display:none; " id="mailworking">
                    </div>
                    <table width="100%" border="0" class="mkTableFormular">
                        <tr>
                            <td>An:</td>
                            <td><input type="text" name="email_an" id="email_an" value="[EMAIL_AN]" id="an" style="width: 500px;"></td>
                          <td align="right"  valign="bottom">
                        </tr>
                        <tr>
                            <td>CC:</td>
                            <td><input type="text" id="email_cc" name="email_cc" value="[EMAIL_CC]" id="cc" style="width: 500px;"></td>
                        </tr>
                        <tr>
                            <td colspan="3"><br></td>
                        </tr>
                        <tr>
                            <td>Betreff:</td>
                            <td><input type="text" name="email_betreff" value="[EMAIL_BETREFF]" style="width: 500px;"></td>
                        </tr>
                        <tr>
                            <td>Text:</td>
                            <td colspan=2><textarea name="email_text" id="email_text" style="min-height: 180px;">[EMAIL_TEXT]</textarea><br><i>(Signatur für E-Mail wird automatisch angehängt)</i></td>
                        </tr>
                        [ANHAENGEHERAUFLADEN]
                        <tr>
                            <td colspan="3"><br></td>
                        </tr>
                        <tr valign="top">
                            <td>Anh&auml;nge:</td>
                            <td>                                
                                    [ANHAENGE]                                
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td><input type="file" name="upload[]" id="file" multiple/></td>
                        </tr>
                        <tr>
                            <td align="left" valign="bottom">
                                <button name="submit" value="addfile" id="addfile" class="ui-button-icon" style="display:none">Hinzufügen</button>
                            </td>
                            <script type="text/javascript">
                                document.getElementById("file").onchange = function(e) {
                                    document.getElementById("addfile").click();
                                }
                             </script>
                           </tr>
                    </table>
                </fieldset>            
            </div>
   		</div>
		<div class="col-xs-14 col-md-2 col-md-height">
			<div class="inside inside-full-height">
				<fieldset>
                    <legend>{|Neue E-Mail|}</legend>
                    <input type="hidden" name="type" value="email">
                    <input type="hidden" name="eintragId" value="[EINTRAGID]">
                    <div style="position: absolute; margin-top:27em; margin-left:30%; min-width: 90px; background: url(./themes/new/images/loading.gif) no-repeat; background-position: 50% 0px; background-size: 150px; padding-top: 90px; display:none; " id="mailworking">
                    </div>
                    <table width="100%" border="0" class="mkTableFormular">
                        <tr>
                          <td align="right"  valign="bottom">
                             <button name="submit" value="entwurfloeschen" id="entwurfloeschen" class="ui-button-icon" style="width:100%;">Entwurf l&ouml;schen</button>
                                [DATEIENBUTTON]
                             </td>
                        </tr>
                        <tr>
                            <td align="right" ><button name="submit" value="zitat" id="zitat" class="ui-button-icon" style="width:100%;">Original-Nachricht einf&uuml;gen</button></td>
                        </tr>
                        <tr>
                            <td colspan="3"><br></td>
                        </tr>
                        <tr>
                            <td align="right" valign="bottom">
                              <button name="submit" value="absenden" id="absenden" class="ui-button-icon" style="width:100%;">Absenden</button>                                           
                            </td>
                        </tr>
                    </table>
                </fieldset>            
            </div>
   		</div>
   	</div>	
</div>
