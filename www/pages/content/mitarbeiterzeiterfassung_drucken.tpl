<!-- gehort zu tabview -->
<div>

  <div>
  [MESSAGE]
    <form method="POST" id="frmdrucken">
      <table width="100%">
        <tr>
          <td width="100%">
            <fieldset style="height:30px"><legend>{|Auswahl|}</legend>
              <center>{|Jahr|}: <select name="jahr">[JAHR]</select> {|Monat von|}: <select name="monatvon">[MONATVON]</select> {|Monat bis|}: <select name="monatbis">[MONATBIS]</select></center>
            </fieldset>
          </td>
        </tr>
      </table>
      [TABELLE]
      <table width="100%"><tr><td width="100%" align="center">{|Drucker|}: <select name="drucker">[DRUCKER]</select> <input type="submit" name="drucken"  value="{|drucken|}" onclick="checkdrucken();" /></td></tr></table>
    </form>
  </div>
</div>

<script type="text/javascript">
function checkdrucken()
{


}
</script>
