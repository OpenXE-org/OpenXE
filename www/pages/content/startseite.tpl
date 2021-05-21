<script>
    $(document).ready(function () {
        $('#linkspopup').dialog({
            modal: true,
            autoOpen: false,
            minWidth: 700,
            title: '{|Links Editieren|}',
            buttons: {
                '{|SPEICHERN|}': function () {
                    $(this).find('form').submit();
                },
                '{|ABBRECHEN|}': function () {
                    $(this).dialog('close');
                }
            },
            close: function (event, ui) {}
        });
    });

    function editlinks() {
        $('#linkspopup').dialog('open');
    }
</script>
<div id="tabs">
	<ul>
		<li><a href="#tabs-1"></a></li>
	</ul>
	<div id="tabs-1">
		[MESSAGE]
		<div class="row">
			<div class="col-xs-12 col-md-6">
				<div class="inside">
					<fieldset class="home-calendar">
						<div>[KALENDER]</div>
					</fieldset>
				</div>
			</div>
			<div class="col-xs-12 col-md-6">

				<div class="row">
					<div class="col-xs-12 col-md-12">
						<div class="inside">
							<fieldset class="home-bookmarks">
								<legend>
									{|Favoriten|}
									<a href="#" class="edit"><img onclick="editlinks();" src="./themes/[THEME]/images/gear.png"></a>
								</legend>
								<div class="tabsbutton">[LINKS]</div>
							</fieldset>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="row-height">
						<div class="col-xs-12 col-md-6 col-md-height">
							<div class="inside inside-full-height">
								<fieldset class="home-wiki">
									<legend>{|Intranet|} [ACCORDIONEDIT]</legend>
									<div>[ACCORDION]</div>
								</fieldset>
							</div>
						</div>
						<div class="col-xs-12 col-md-6 col-md-height">
							<div class="inside inside-half-height">
								<fieldset class="home-events">
									<legend>{|Termine Heute|}</legend>
									<ul>[TERMINE]</ul>
								</fieldset>
							</div>
							<div class="inside inside-half-height">
								<fieldset class="home-events">
									<legend>{|Termine Morgen|}</legend>
									<ul>[TERMINEMORGEN]</ul>
								</fieldset>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="row-height">
						<div class="col-xs-12 col-md-[COLROWTASKS] col-md-height">
							<div class="inside inside-full-height">
								<fieldset class="home-tasks">
									<legend>{|Aufgaben|}</legend>
									<div>
										<table width="100%" border="0">
											[TODOFORUSER]
										</table>
										<table width="100%" border="0">
											[TODOFORMITARBEITER]
										</table>
									</div>
									<a href="index.php?module=aufgaben&action=list" class="button button-secondary">{|Alle Aufgaben|}</a>
								</fieldset>
							</div>
						</div>
						[BEFORELEARNINGDASHBOARDTILE]
						<div class="col-xs-12 col-md-6 col-md-height">
							<div class="inside inside-full-height">
								<fieldset class="home-tasks">
									<legend>{|Learning Dashboard|}</legend>
									[LEARNINGDASHBOARDTILE]
									<p>In unserem Learning Dashboard zeigen wir Euch mit unserem Klick-by-Klick Assistenten und vielen Videos wie Ihr Euch einrichten und direkt mit xentral durchstarten könnt.</p>
									<a href="index.php?module=learningdashboard&action=list"
										 class="button button-secondary"><svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M4.47287 12.0104C2.04566 9.80074 1.66708 6.11981 3.59372 3.46237C5.52036 0.804943 9.13654 0.0202146 11.9914 1.64005" stroke="#94979E" stroke-linecap="round" stroke-linejoin="round"/>
											<path d="M2.21273 11.9649C1.39377 13.3996 1.11966 14.513 1.58214 14.9761C2.2843 15.6776 4.48124 14.6858 7.02522 12.6684" stroke="#94979E" stroke-linecap="round" stroke-linejoin="round"/>
											<path fill-rule="evenodd" clip-rule="evenodd" d="M9.93719 12.1581L7.52014 9.74109L12.8923 4.3689C13.3305 3.93091 13.8797 3.62049 14.481 3.47095L15.863 3.12392C16.0571 3.07558 16.2623 3.1325 16.4037 3.27392C16.5451 3.41534 16.602 3.62054 16.5537 3.8146L16.208 5.19732C16.0578 5.7984 15.7469 6.34731 15.3087 6.78527L9.93719 12.1581Z" stroke="#94979E" stroke-linecap="round" stroke-linejoin="round"/>
											<path fill-rule="evenodd" clip-rule="evenodd" d="M7.51976 9.7409L5.54021 9.08128C5.44619 9.05019 5.37505 8.97252 5.35233 8.87613C5.32961 8.77974 5.35857 8.67847 5.42881 8.60867L6.11882 7.91866C6.7306 7.30697 7.63548 7.09343 8.45619 7.36706L9.53644 7.72625L7.51976 9.7409Z" stroke="#94979E" stroke-linecap="round" stroke-linejoin="round"/>
											<path fill-rule="evenodd" clip-rule="evenodd" d="M9.93713 12.1584L10.5968 14.1386C10.6278 14.2326 10.7055 14.3038 10.8019 14.3265C10.8983 14.3492 10.9996 14.3203 11.0694 14.25L11.7594 13.56C12.3711 12.9482 12.5846 12.0434 12.311 11.2226L11.9518 10.1424L9.93713 12.1584Z" stroke="#94979E" stroke-linecap="round" stroke-linejoin="round"/>
										</svg> {|Starte hier!|}</a>
								</fieldset>
							</div>
						</div>
						[AFTERLEARNINGDASHBOARDTILE]
					</div>
				</div>

			</div>
		</div>

		<div class="row">
			<div class="row-height">
				<div class="col-xs-12 col-md-6 col-md-height">
					<div class="inside inside-full-height">
						<fieldset class="home-news">
							<legend>{|Neues von Xentral|}</legend>
							<div>[EXTERNALNEWS]</div>
						</fieldset>
					</div>
				</div>
				<div class="col-xs-6 col-md-3 col-md-height">
					<div class="inside inside-full-height">
						<fieldset>
							<legend>{|Update Center|}</legend>
							<div>[WELCOMENEWS]</div>
						</fieldset>
					</div>
				</div>
				<div class="col-xs-6 col-md-3 col-md-height">
					<div class="inside inside-full-height">
						<fieldset class="home-appstore">
							<legend>AppStore</legend>
							<div class="home-appstore-image">
								<img src="./themes/[THEME]/images/app-icon.png" align="center" alt="AppStore">
							</div>
							<div class="home-appstore-button">
								<a href="index.php?module=appstore&action=list" class="button button-secondary" target="_blank">{|zum AppStore|}</a>
							</div>
						</fieldset>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>

<div id="linkspopup">
	<form method="POST">
		<table>
			<tr>
				<td><b>{|Name|}</b></td>
				<td><b>{|Link|}</b></td>
				<td><b>{|nicht in neuem Tab|}</b></td>
			</tr>
			<tr>
				<td><input type="text" name="linkname1" value="[LINKNAME1]"/></td>
				<td><input type="text" size="50" name="linklink1" value="[LINKLINK1]"/></td>
				<td><input type="checkbox" value="1" name="linkintern1" [LINKINTERN1]/></td>
			</tr>
			<tr>
				<td><input type="text" name="linkname2" value="[LINKNAME2]"/></td>
				<td><input type="text" size="50" name="linklink2" value="[LINKLINK2]"/></td>
				<td><input type="checkbox" value="1" name="linkintern2" [LINKINTERN2]/></td>
			</tr>
			<tr>
				<td><input type="text" name="linkname3" value="[LINKNAME3]"/></td>
				<td><input type="text" size="50" name="linklink3" value="[LINKLINK3]"/></td>
				<td><input type="checkbox" value="1" name="linkintern3" [LINKINTERN3]/></td>
			</tr>
			<tr>
				<td><input type="text" name="linkname4" value="[LINKNAME4]"/></td>
				<td><input type="text" size="50" name="linklink4" value="[LINKLINK4]"/></td>
				<td><input type="checkbox" value="1" name="linkintern4" [LINKINTERN4]/></td>
			</tr>
			<tr>
				<td><input type="text" name="linkname5" value="[LINKNAME5]"/></td>
				<td><input type="text" size="50" name="linklink5" value="[LINKLINK5]"/></td>
				<td><input type="checkbox" value="1" name="linkintern5" [LINKINTERN5]/></td>
			</tr>
			<tr>
				<td><input type="text" name="linkname6" value="[LINKNAME6]"/></td>
				<td><input type="text" size="50" name="linklink6" value="[LINKLINK6]"/></td>
				<td><input type="checkbox" value="1" name="linkintern6" [LINKINTERN6]/></td>
			</tr>
			<tr>
				<td><input type="text" name="linkname7" value="[LINKNAME7]"/></td>
				<td><input type="text" size="50" name="linklink7" value="[LINKLINK7]"/></td>
				<td><input type="checkbox" value="1" name="linkintern7" [LINKINTERN7]/></td>
			</tr>
			<tr>
				<td><input type="text" name="linkname8" value="[LINKNAME8]"/></td>
				<td><input type="text" size="50" name="linklink8" value="[LINKLINK8]"/></td>
				<td><input type="checkbox" value="1" name="linkintern8" [LINKINTERN8]/></td>
			</tr>
		</table>
		<input type="hidden" name="savelinks" value="1"/>
	</form>
</div>

[AUFGABENPOPUP]
