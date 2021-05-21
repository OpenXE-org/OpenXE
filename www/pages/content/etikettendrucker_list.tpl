
<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">{|Etiketten Drucker|}</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]
[TAB1]
[TAB1NEXT]

<form action="" method="post">
<table class="mkTableFormular">
<tr class="trmenge"><td colspan="2"><strong>{|Bitte ausf&uuml;llen|}</strong><br></td></tr>
[TRCUSTOM]
<!--<tr><td><label for="artikel">{|Artikel|}:</label></td><td><input type="text" size="30" value="[ARTIKEL]" name="artikel" id="artikel"></td></tr>
<tr><td><label for="lagerplatz">{|Lagerplatz|}:</label></td><td><input type="text" size="30" value="[LAGERPLATZ]" id="lagerplatz" name="lagerplatz"></td></tr>
<tr class="trmenge"><td><label for="menge">{|Menge|}:</label></td><td><input type="text" size="30" value="[MENGE]" id="menge" name="menge"></td></tr>
-->
<tr><td colspan="2"><strong>{|Anzahl Etiketten|}</strong><br></td></tr>
<tr><td><label for="amountof">{|Anzahl|}:</label></td><td><input type="text" size="10" value="1" id="amountof" name="amountof"></td></tr>
<tr><td colspan="2"><strong>{|Auswahl|}</strong><br></td></tr>
<tr><td><label for="printer">{|Drucker|}:</label></td><td><select id="printer" name="printer">[DRUCKER]</select></td></tr>
<tr><td><label for="label">{|Etikett|}:</label></td><td><select id="label" name="label">[LABEL]</select></td></tr>

<tr><td colspan="2" align="center">
  <input type="button" value="Abbrechen" class="btnBlueBig" name="abbrechen" onclick="window.location.href='[ABBRECHENURL]'">
  <input type="submit" value="Drucken" class="btnGreenBig" name="drucken">
</td></tr>
</table>
</form>
</div>

<!-- tab view schließen -->
</div>

<script>
  $(document).on('ready',function() {
    document.getElementById("artikel").focus();
    $('input').on("keypress", function (e) {
      /* ENTER PRESSED*/
      if (e.keyCode == 13) {
        /* FOCUS ELEMENT */
        var inputs = $(this).parents("form").eq(0).find(":input");
        var idx = inputs.index(this);

        if (idx == inputs.length - 1) {
          inputs[0].select()
        } else {
          inputs[idx + 1].focus(); //  handles submit buttons
          inputs[idx + 1].select();
        }
        return false;
      }
    });
  });
  $('#label').on('change',function() {
    $.ajax({
      url: 'index.php?module=etikettendrucker&action=list&cmd=getlabelvars',
      type: 'POST',
      dataType: 'json',
      data: {label: $('#label').val()},
      success: function(data) {
        if(data && typeof data.html != 'undefined') {
          $('tr.vars').remove();
          $('tr.trmenge').after(data.html);
          checkautocomplete();
        }
      },
      beforeSend: function() {

      }
    });
  });
</script>

