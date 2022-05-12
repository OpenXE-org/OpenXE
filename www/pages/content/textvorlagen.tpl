<div id="textvorlagenModal">
  [TVFILTERHEADER]
  [TABTEXTVORLAGEN]
	<br><br>
	<div class="inside">
  <fieldset>
	<legend>{|Neue Textvorlage anlegen / bearbeiten|}</legend>
	<table>
      <tr id="textvorlageneingabe">
	    <td>
		  <table>
		    <tr>
			  <td>
			    <input type="hidden" id="textvorlageid" value=""/>Name:<br/>
				<input type="text" id="textvorlagename" value=""/>
			  </td>
			</tr>
			<tr>
			  <td>Stichw&ouml;rter:<br/><input type="text" id="textvorlagestichwoerter"/></td>
			</tr>
			<tr>
			  <td>Projekt:<br/>[PROJEKTSTART]<input type="text" id="textvorlageprojekt" name="textvorlageprojekt" value=""/>[MSGPROJEKT][PROJEKTENDE]</td>
			</tr>
		  </table>
		</td>
		<td>Text:<br/><textarea id="textvorlagetext"></textarea></td>
		<td><br/><input type="button" id="textvorlagespeichern" value="Speichern"/></td>
	  </tr>
	</table>
  </fieldset>
	</div>
</div>
