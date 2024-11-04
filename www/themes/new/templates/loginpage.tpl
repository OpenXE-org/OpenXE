<!doctype html>
<html lang="de">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>OpenXE - Login</title>
	<link id="shortcuticon" rel="shortcut icon" href="./themes/new/images/favicon/favicon.ico" type="image/x-icon">
	<link id="favicon" rel="icon" href="./themes/new/images/favicon/favicon.ico" type="image/x-icon">
	<link rel="icon" type="image/png" href="./themes/new/images/favicon/favicon-16x16.png" sizes="16x16">
	<link rel="icon" type="image/png" href="./themes/new/images/favicon/favicon-32x32.png" sizes="32x32">
	<script type="text/javascript" src="./js/jquery/jquery-3.5.0.min.js"></script>
	<script type="text/javascript" src="[JQUERYMIGRATESRC]"></script>
	<script src="themes/new/js/scripts_login.js"></script>
	<link rel="stylesheet" href="themes/new/css/normalize.min.css?v=5">
	<link rel="stylesheet" href="themes/new/css/login_styles.css?v=3">
	<link rel="stylesheet" href="themes/new/css/custom.css?v=3">
</head>

<body>
<div id="login-container">
	<div id="login-slider-wrapper">
		<div id="login-slider">
			[LOGINSLIDER]
		</div>
	</div>
	<div id="login-wrapper">

	<img src="[TPLLOGOFIRMA]" heigth="72">
	</img>

		<div class="intro">
			Willkommen beim ERP im Kölner Keller.<br/>
			Bitte gib Deinen Benutzernamen und Passwort ein!
		</div>
		<div [LOGINWARNING_VISIBLE] class="warning"><p>[LOGINWARNING_TEXT]</p></div>

		[SPERRMELDUNGNACHRICHT]
		[PAGE]
		<div id="login-footer">
			<div class="copyright">
				&copy; [YEAR] by OpenXE-org & Xentral&nbsp;ERP&nbsp;Software&nbsp;GmbH.<br>
                OpenXE is free open source software under AGPL/EGPL license, based on <a href="https://xentral.com" target="_blank">Xentral®</a>.<br>
				<a href="https://github.com/OpenXE-org/OpenXE/commits/master">[XENTRALVERSION]</a>
			</div>
		</div>

	</div>
</div>

</body>
</html>
