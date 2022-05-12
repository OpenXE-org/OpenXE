<form action="" method="post" name="eprooform" id="eprooform">
<br> 
<table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>
      <tr classname="orange1" class="orange1" bordercolor="" align="" bgcolor="" height="" valign="">
        <td colspan="3" bordercolor="" class="" align="" bgcolor="" height="" valign="">Artikel zuordnen<br></td>
      </tr>

      <tr valign="top" colspan="3">
        <td>

<div class="info">Der Artikel ist ein Produktionsartikel.</div>
<br><br>
<table width="60%" style="background-color: #fff; border: solid 1px #000;" align="center">
<tr>
<td align="center">
<br>

<table height="200" border="0" width="400">
<tr valign="top"><td><b>Artikel:</b></td><td align="center"><u>[NAME]</u></td></tr> 
<tr valign="top"><td><br></td><td align="center"></td></tr> 
<tr valign="top"><td><b>Bemerkung:</b></td><td><textarea cols="35" rows="2" name="bemerkung">[BEMERKUNG]</textarea>
</td></tr>
<tr valign="top"><td><br></td><td align="center"></td></tr>
<tr valign="top"><td><b>1. Schritt:</b></td><td align="center"><img src="http://www.handy-foto-pc.de/pics/Sonstiges/kisterot.jpg" width="100"><br>Produktionskiste(n) holen</td></tr> 
<tr valign="top"><td><br></td><td align="center"></td></tr> 
<tr valign="top"><td><b>2. Schritt:</b></td><td align="center">Kiste mit Artikel f&uuml;llen</td></tr> 
<tr valign="top"><td><br></td><td align="center"></td></tr> 
<tr valign="top"><td><b>3. Schritt:</b></td><td align="center">Anzahl:&nbsp;<select name="anzahl">
  <option value="1">1</option><option value="2">2</option><option value="3">3</option>
  <option value="4">4</option><option value="5">5</option></select>&nbsp;<br>
  Etiketten f&uuml;r Produktionskiste drucken.<br><i style="font-size: 8pt">(Achtung: Jede Koste bekommt ein Etikett!)</i></td></tr> 
</table>

<br>
<br>
</td>
</tr>
</table>
<br><br>


</td>
      </tr>

    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
    <input type="submit" name="submit"
    value="Etiketten drucken" /></td>
    </tr>

    </tbody>
  </table>
</form>

<script type="text/javascript">
 var firstsubmit = false;

  $(document).ready(function() {
    $( "#eprooform" ).submit(function( event ) {
      if(firstsubmit)
      {
        event.preventDefault();
        return false;
      }
      firstsubmit = true;
      return true;
    });

  });
</script>