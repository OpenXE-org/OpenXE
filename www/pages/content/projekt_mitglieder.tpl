<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">

<script>
function provisionartikelProvisionSave()
{
  if($('#adresse').val() == '')return false;
}

</script>
[MESSAGE]
<fieldset>
        <legend>Hinzufügen</legend>
        <form action="index.php?module=projekt&action=mitglieder&id=[ID]" method="POST" onsubmit="return provisionartikelProvisionSave(this);">
            <table width="" cellspacing="0" cellpadding="0">
                <tr>
                    <td>Adresse:&nbsp;</td>
                    <td><input type="text" name="adresse" id="adresse"></td>
                    <td>&nbsp;</td>
                    <td>als Rolle:&nbsp;</td>
                    <td><select id="rolle" name="rolle"><option value="Kunde">Kunde</option><option value="Lieferant">Lieferant</option><option value="Mitarbeiter" selected>Mitarbeiter</option><option value="Mitglied">Mitglied</option></select></td>
                    <td>&nbsp;</td>
                    <td><input type="submit" name="hinzufuegen" value="hinzufügen" [BUTTONDISABLED]></td>
                </tr>
            </table>
        </form>
    </fieldset>


[TAB1]
[TAB1NEXT]
</div>

<!-- tab view schließen -->
</div>

