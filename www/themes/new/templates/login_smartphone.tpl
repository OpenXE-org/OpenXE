<style>
.loginbox {
    background-color: #ECECEC;
position:relative;
top:50px;
height:40%;
}

.input_gross {
}

.button_gross {
  width:40% !important;
  height:100px;
  font-size:25px !important;
}

</style>


<form action="" method="post"><br>
<table align="center" border="0">
<tr>
<td><input name="username" type="text" size="25" id="username" placeholder="Benutzer" class="input_gross"></td>
</tr>
<tr>
<td><input name="password" type="password" size="25" placeholder="Passwort" class="input_gross"></td>
</tr>
<tr>
<tr>
<td align="center">[LOGINMSG]
<span style="color:red">[LOGINERRORMSG]</span></td>
</tr>

<tr>
<td></td>
</tr>

<tr>
<td><input name="token" type="text" size="25" autocomplete="off" placeholder="optional OTP"><br></td>
</tr>


<tr>
<td align="center"><br><br><input type="submit" value="anmelden"><br><input type="reset"
name="Submit" value="zur&uuml;cksetzen"></td>
</tr>
<tr>
<td><br></td>
<td></td>
</tr>
</table>

<script type="text/javascript">document.getElementById("username").focus();</script>

