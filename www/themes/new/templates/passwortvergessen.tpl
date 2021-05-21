<form action="" id="frmlogin" method="post" autocomplete="off">
  <table>
    [VORZURUECKSETZEN]
    <tr>
      <td>
        <label for="username">Benutzername</label>
        <input name="vergessenusername" type="text" size="45" value="[USERNAME]" id="username" autocomplete="off">
      </td>
    </tr>
    [NACHZURUECKSETZEN]
    [VORPASSWORT]
    <tr>
      <td>
        <label for="passwort">Passwort</label>
        <input name="passwort" type="password" size="45" value="" id="passwort">
      </td>
    </tr>
    <tr>
      <td>
        <label for="passwortwiederholen">Passwort-Wiederholung</label>
        <input name="passwortwiederholen" type="password" size="45" value="" id="passwortwiederholen">
      </td>
    </tr>
    [NACHPASSWORT]
    <tr>
      <td align="center">
        <span id="loginmsg">[LOGINMSG]</span>
        <span id="loginerrormsg">[LOGINERRORMSG]</span>
      </td>
    </tr>
    <tr>
      <td align="center">
        [VORPASSWORT]<input type="submit" class="btn btn-primary" name="aendern" value="Passwort &auml;ndern">[NACHPASSWORT]
        [VORZURUECKSETZEN]<input type="submit" class="btn btn-primary" value="Passwort zur&uuml;cksetzen">[NACHZURUECKSETZEN]
      </td>
    </tr>
    <tr>
      <td align="center">
        <a href="index.php" class="btn btn-secondary">zur&uuml;ck zum Login</a>
      </td>
    </tr>
  </table>
</form>
