<!--<table border="0" width="100%">
<tr><td><table width="100%"><tr><td>[USER_CREATE]</td></tr></table></td></tr>
</table>-->
<style>
	ul.ui-autocomplete {
		/*padding-top:100px;*/
	}
	#trdummy {
		height:0;
		width:0;
		overflow: hidden;
		display:none;
	}
</style>
<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">{|E-Mailaccount|}</a></li>
        <!-- <li><a href="#tabs-3">{|Rechte|}</a></li> -->
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]
<form enctype="multipart/form-data" action="" method="post" name="eprooform" id="usereditform">
[FORMHANDLEREVENT]

  <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>
      <tr valign="top" colspan="3">
        <td >
<fieldset><legend>{|E-Mailaccount|}</legend>
    <table width="100%" border="0">
    <tr><td>{|E-Mailadresse|}:*</td><td><input type="text" name="email" value="[EMAIL]" size="40"></td></tr>
    <tr><td width="200">{|Angezeigter Name|}:</td><td><input type="text" name="angezeigtername" value="[ANGEZEIGTERNAME]" size="40"><i>{|Wird als Absendername angezeigt.|}</i></td></tr>


</table></fieldset>

</td></tr>

    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
    <input type="submit" id="submit" name="submitemailbackup" value="Speichern" />
    </tr>

    </tbody>
  </table>
</form>

</div>

