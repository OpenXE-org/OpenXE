{extends file='layout.tpl'}
{namespace key='chat'}

{block name='page'}
	<div id="tabs">
		<ul>
			<li><a href="#tabs-1">{translate key='overview'}{$tabText|default:'Übersicht'}{/translate}</a></li>
		</ul>
		<div id="tabs-1" class="{$chatStandaloneClass}">
			<div id="chat-wrapper">
				<div id="sidebar-wrapper">
					<div id="sidebar-scroller">
						<div>
							<h3>{translate key='users'}Benutzer{/translate}</h3>
							<div id="userlist"></div>
						</div>
						<div>
							<h3>{translate key='rooms'}Räume{/translate}</h3>
							<div id="roomlist"></div>
						</div>
					</div>
				</div>
				<div id="message-wrapper">
					<fieldset class="white">
						<legend id="message-header"></legend>
						<div id="message-scroller">
							<div id="message-area"></div>
						</div>
					</fieldset>
					<form id="chatform">
						<fieldset class="white">
							<legend></legend>
							<div class="input-wrapper">
								<div class="input-message">
									<input type="text" placeholder="{translate key='message_placeholder'}Nachricht{/translate}"
												 name="nachricht" id="nachricht" autocomplete="off" tabindex="1">
								</div>
								<div class="input-prio">
									<label>
										<input type="checkbox" name="prio" id="prio" value="1" tabindex="2">
										{translate key='priority_message'}Prio-Nachricht{/translate}
									</label>
								</div>
								{block name='module_chat_list_form_button'}
								<div class="input-button">
									<input type="submit" name="submit" class="btnGreen"
												 value="{translate key='submit_button_text'}absenden{/translate}" tabindex="3">
								</div>
								{/block}
							</div>
						</fieldset>
					</form>
				</div>
			</div>
		</div>
	</div>
{/block}
