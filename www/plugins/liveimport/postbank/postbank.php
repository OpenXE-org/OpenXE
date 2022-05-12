<?php

//header("Content-Type: text/html; charset=utf-8");

class postbank
{
	function __construct()
	{

	}

	function Import($zugangsdaten)
	{
		//user login information
		$username = $zugangsdaten["username"];
		$password = $zugangsdaten["password"];
		$submit = "Anmelden";
		//server link and variables
		$url ="https://banking.postbank.de/rai/login/wicket:interface/:0:login:loginForm::IFormSubmitListener::";
		$nameField ="nutzername";
		$passField ="kennwort";
		$subField ="loginButton";

		$cookie_file = "/tmp/cookie.txt";

		$page = curl_init($url);

		curl_setopt($page , CURLOPT_POST, 1);
		$postData = "jsDisabled=false&".$nameField."=".$username."&".$passField."=".$password."&".$subField."=".$submit;
		curl_setopt($page, CURLOPT_POSTFIELDS, $postData);
		curl_setopt($page, CURLOPT_RETURNTRANSFER, 1);

		curl_setopt($page, CURLOPT_SSL_VERIFYPEER, FALSE);
		//curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 2); 

		curl_setopt($page, CURLOPT_COOKIEJAR, $cookie_file);

		curl_setopt($page, CURLOPT_FOLLOWLOCATION, true);

		$out = curl_exec($page);

		$url = "https://banking.postbank.de/rai/?wicket:bookmarkablePage=:de.postbank.ucp.application.rai.fs.umsatzauskunft.UmsatzauskunftPage";

		curl_setopt($page, CURLOPT_URL, $url);
		$pagedata = curl_exec($page);

//		$url = "https://banking.postbank.de/rai/?wicket:interface=:3:umsatzauskunftContainer:umsatzauskunftpanel:panel:form:umsatzanzeigeGiro:umsatzaktionen:umsatzanzeigeUndFilterungDownloadlinksPanel:csvHerunterladen::IResourceListener::";

		$url = "https://banking.postbank.de/rai/?wicket:interface=:4:umsatzauskunftpanel:form:umsatzanzeigeGiro:umsatzaktionen:umsatzanzeigeUndFilterungDownloadlinksPanel:csvHerunterladen::IResourceListener::";

		curl_setopt($page, CURLOPT_URL, $url);
		$outstr = curl_exec($page);

		curl_close($page);
		return $outstr;
	}

}
?>
