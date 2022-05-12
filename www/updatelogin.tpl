<center>
<table border="0" celpadding="0" cellspacing="4" width="100%"
height="100%" align="left">
<tr>
<td valign="top">
<form action="" id="frmlogin" method="post"><br>
  <table align="center">
    [MULTIDB]
    <tr>
    <td style="width:100%;text-align:center;"><input style="display:none;width:200px;" id="chtype" type="button" value="Login mit Username / PW" /></td>
    </tr>
    <tr>
    <td align="center"><input type="hidden" name="isbarcode" id="isbarcode" value="0" /><input name="username" type="text" size="45" id="username" placeholder="Benutzer"></td>
    </tr>
    <tr>
    <td align="center"><input name="password" id="password" type="password" size="45" placeholder="Passwort"></td>
    </tr>
    
    <tr>
    <td align="center"><span id="loginmsg">[LOGINMSG]</span>
    <span style="color:red">[LOGINERRORMSG]</span></td>
    </tr>

    <tr>
    <td align="center">[STECHUHRDEVICE]</td>
    </tr>

    <tr>
    <td align="center"><input name="token" id="token" type="text" size="45" autocomplete="off" placeholder="optional OTP"><br></td>
    </tr>


    <tr>
    <td align="center"><br><br><input type="submit" value="anmelden"> <input type="reset"
    name="Submit" value="zur&uuml;cksetzen"></td>
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
  var siv = null;
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
    if(typeof(Storage) !== "undefined") {
      [RESETSTORAGE]
      var devicecode = localStorage.getItem("devicecode"); 
      if(devicecode)
      {
        $('#stechuhrdevice').each(function(){
          $('#token').hide();
          $('#password').hide();
          $('#username').hide();
          $('#loginmsg').hide();
          $('#chtype').show();
          $('#chtype').on('click',function()
          {
            $('#token').show();
            $('#password').show();
            $('#username').show();
            $('#loginmsg').show();
            $(this).hide();
            clearInterval(siv);
          });
          $('#code').val(devicecode);
          $('#stechuhrdevice').focus();
          $( "#stechuhrdevice" ).on('keydown',function( event ) {
            setTimeout(function(){
              if($('#stechuhrdevice').val().length > 205)
                setTimeout(function(){$('#frmlogin').submit();},100);          
            }, 500);

          });
          siv = setInterval(function(){$('#stechuhrdevice').focus(),200});
        });
      } else {
        $('#stechuhrdevice').hide();
      }
    } else {
      $('#stechuhrdevice').hide();
      
      
      
    }
    
    
  });

  
</script>

