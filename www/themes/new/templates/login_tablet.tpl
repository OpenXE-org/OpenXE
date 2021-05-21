<style>
.loginbox {
width:80% !important;
left:10% !important;
top:10% !important;
margin: 0 !important;
font-size:25px;
}
.input_gross {
  font-size:30px !important;
  font-weight:bold;
  width:100%;
  height:40px;
  background-color:#ccc;
  padding:10px;
  text-align:center;
}

.button_gross {
  width:40% !important;
  height:100px;
  font-size:25px !important;
}


</style>
<center>
<table border="0" celpadding="0" cellspacing="4" width="100%"
height="100%" align="left">
<tr>
<td valign="top">
<form action="" method="post"><br>
<table align="center">
<tr>
<td><input type="hidden" name="isbarcode" id="isbarcode" value="0" /><input name="username" type="text" size="45" id="username" placeholder="Benutzer" class="input_gross" /></td>
</tr>
<tr>
<td><input name="password" type="password" size="45" placeholder="Passwort" class="input_gross" /></td>
</tr>
<tr>
<tr>
<td align="center">[LOGINMSG]
<span style="color:red">[LOGINERRORMSG]</span></td>
</tr>

<tr>
<td></td>
</tr>
<!--
<tr>
<td><input name="token" type="text" size="45" autocomplete="off" placeholder="optional OTP" class="input_gross"><br></td>
</tr>-->


<tr>
<td align="center"><br><br><input type="submit" value="anmelden" class="button_gross" /> <input type="reset"
name="Submit" value="zur&uuml;cksetzen" class="button_gross"></td>
</tr>
<tr>
<td><br></td>
<td></td>
</tr>

</table>
</form>
</td>
</tr>
</table>
</center>
<script type="text/javascript">
  document.getElementById("username").focus();
  $("#isbarcode").val('0');
  $(document).ready(function() {
    $( "#username" ).focus();
    $( "#username" ).on('keydown',function( event ) {
      var which = event.which;
      if ( which == 13 ) {
        event.preventDefault();
        if($( "#username" ).val().indexOf("!!!") < 1)
        {
          $('#password').focus();
        }else{
          $('#frmlogin').submit();
        }
      } else {
        var iof = $( "#username" ).val().indexOf("!!!");
        if(iof > 0)
        {
          $('#password').focus();
          $('#username').val($( "#username" ).val().substring(0,iof));
          $("#isbarcode").val('1');
        }
      }
    });
  });  
</script>

