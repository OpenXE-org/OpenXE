[MESSAGE]
<form method="post" action="">
  <fieldset>
    <legend>UI &Auml;nderung</legend>
    <table class="mkTableFormular">
      <tr>
        <td>Layout-Auswahl:</td>
        <td>
          <select name="ui_layout_mode">
            <option value="new" [UI_LAYOUT_SELECTED_NEW]>Neues Layout</option>
            <option value="standard" [UI_LAYOUT_SELECTED_STANDARD]>Standardlayout (alt)</option>
          </select>
        </td>
      </tr>
    </table>
  </fieldset>
  <p style="text-align: right;">
    <input type="submit" name="speichern" value="Speichern" />
  </p>
</form>
