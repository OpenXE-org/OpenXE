  <script>
  $(function() {
    $( "#accordion" ).accordion();
  });
  </script>

<div class="demo">

<div id="accordion">
  <h3><a href="#">Artikelsuche</a></h3>
  <div>


<form action="" method="post" name="eprooform">
        
<table width="100%">
  <tr><td>Name:</td><td>[NAMESTART]<input type="text" name="name" id="name" size="20" value="[SUCHENAME]">[NAMEENDE]</td>
        <td>&nbsp;</td>
        <td>Projekt:</td><td width="300">[PROJEKTSTART]<input type="text" name="projekt" id="projekt" value="[SUCHEPROJEKT]">[PROJEKTENDE]</td></tr>
        <tr><td>Nummer:</td><td>[NUMMERSTART]<input type="text" name="nummer" size="20" id="nummer" value="[SUCHENUMMER]">[NUMMERENDE]</td>
        <td>&nbsp;</td>
        <td></td><td> <input type="submit" name="suche"value="Suchen" /></td></tr>
</table>
  </form>

</div>

</div><!-- End demo -->


