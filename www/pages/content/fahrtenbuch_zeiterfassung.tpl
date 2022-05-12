
<div id="fahrtenbuch_accordion">
  <h2>{|Fahrtenbuch|}</h2>
  <div>
      <table class="mkTableFormular" style="width:100%">
        <tr>
          <td>{|Buchen|}:</td>
          <td colspan="3">
            <input type="checkbox" id="fahrtenbuch_buchen" name="fahrtenbuch_buchen" value="1" [FAHRTENBUCH_BUCHEN]>&nbsp;<i>Bitte auswählen wenn Fahrt gebucht werden soll.</i>
          </td>
        </tr>
        <tr>
          <td>{|Fahrzeug|}:</td>
          <td colspan="3">
            <input type="text" id="fahrtenbuch_fahrzeug" name="fahrtenbuch_fahrzeug" size="50" value="[FAHRTENBUCH_FAHRZEUG]">
          </td>
        </tr>
        <tr>
          <td>{|Strecke|}:</td>
          <td colspan="3">
            <input type="text" id="fahrtenbuch_strecke" name="fahrtenbuch_strecke" size="50" value="[FAHRTENBUCH_STRECKE]">&nbsp;[OPENSTREETMAPBUTTON]
          </td>
        </tr>
        <tr>
          <td>{|Kilometer|}:</td>
            <td colspan="3">
              <input type="text" id="fahrtenbuch_kilometer" name="fahrtenbuch_kilometer" size="50" value="[FAHRTENBUCH_KILOMETER]">
            </td>
        </tr>

      </table>
  </div>
</div>

<script>

  $(document).ready(function() {
    $("input#fahrtenbuch_strecke").autocomplete({
      source: "index.php?module=ajax&action=filter&filtername=fahrtenbuch_strecke",
      select: function( event, ui ) {
        if(ui.item){
          var ergebnis = encodeURIComponent(ui.item.value);
          $.ajax({
              url: 'index.php?module=fahrtenbuch&action=vorlagen&cmd=getstrecke&strecke='+ergebnis,
              data: {
              },
              method: 'post',
              dataType: 'json',
              success: function(data) {
                $('#fahrtenbuch_kilometer').val(data.kilometer);
                $('#fahrtenbuch_buchen').prop("checked",data.buchen==1?true:false);
              }
          });
        }
      }
    });
  });



  $( function() {
    $( "#fahrtenbuch_accordion" ).accordion({ heightStyle: "content"});
  } );

  function FahrtenbuchStreckKilometerladen()
  {
    var tmp_adresse_abrechnung = $('#adresse_abrechnung').val();
    var tmp_projekt_manuell = $('#projekt_manuell').val();

    $.ajax({
        url: 'index.php?module=fahrtenbuch&action=kilometerstrecke',
        data: {
            //Alle Felder die fürs editieren vorhanden sind
            adresse_abrechnung: tmp_adresse_abrechnung,
            projekt_manuell: tmp_projekt_manuell
            //editbetreff: $('#editbetreff').val(),
            //edittext: $('#edittext').val(),
            //editbearbeiter: $('#editbearbeiter')
        },
        method: 'post',
        dataType: 'json',
        success: function(data) {
            App.loading.close();
            if (data.status == 1) {
                $('#fahrtenbuch_strecke').val(data.strecke);
                $('#fahrtenbuch_kilometer').val(data.kilometer);
                $('#fahrtenbuch_buchen').prop("checked",true);
            } else {
                alert(data.statusText);
            }
        }
    });

  }
</script>

