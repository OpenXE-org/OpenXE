<!-- gehort zu tabview -->
<div id="tabs">
	<ul>
		<li><a href="#tabs-1">[TABTEXT]</a></li>
	</ul>
	<div id="tabs-1" class="[CHAT_STANDALONE]">
		<div id="chat-wrapper">
			<div id="sidebar-wrapper">
				<div id="sidebar-scroller">
					<div>
						<h3>{|Benutzer|}</h3>
						<div id="userlist"></div>
					</div>
					<div>
						<h3>{|Räume|}</h3>
						<div id="roomlist"></div>
					</div>
				</div>
			</div>
			<div id="message-wrapper">
				<fieldset class="white">
					<legend id="message-header">[NAME]</legend>
					<div id="message-scroller">
						<div id="message-area"></div>
					</div>
				</fieldset>
				<form id="chatform">
					<fieldset class="white">
						<legend></legend>
						<div class="input-wrapper">
							<div class="input-message">
								<input type="text" placeholder="Nachricht" name="nachricht" id="nachricht" autocomplete="off" tabindex="1">
							</div>
							<div class="input-prio">
								<label><input type="checkbox" name="prio" id="prio" value="1" tabindex="2">&nbsp;{|Prio-Nachricht|}&nbsp;</label>
							</div>
							<div class="input-button">
								<input type="submit" name="submit" class="btnGreen" value="{|absenden|}" tabindex="3">
							</div>
						</div>
					</fieldset>
				</form>
			</div>
		</div>
	</div>
</div>
