<style>
    #app-suche {
        display: table;
        width: 320px;
        margin: 10px auto;
        text-align: center;
    }
    #app-suche .suche {
        display: table-row;
        margin: 0 auto;
    }
    #app-suche label {
        display: table-cell;
        width: 15%;
    }
    #app-suche input {
        display: table-cell;
        width: 85%;
    }
    #meineapps {
        font-family: 'Inter', 'Helvetica', 'Arial', sans-serif;
        clear: both;
        max-width: 1024px;
        margin: 0 auto;
    }
    #meineapps .app {
        float: left;
        width: 23%;
        margin: 1%;
    }
    #meineapps .app .icon {
        background-repeat: no-repeat;
        background-position: center;
        display: block;
        width: 66px;
        max-width: 74px;
        height: 66px;
        text-align: center;
        margin: 0 auto;
        border-radius: 10px;
    }
    #meineapps .app span {
        font-size: 13px;
        display: block;
        margin: 10px auto;
        text-align: center;
        height: 28px;
        overflow: hidden;
    }
    #meineapps #keineappsgefunden {
        display: none;
        text-align: center;
        padding: 20px;
    }
</style>
<script>
    $(document).ready(function() {
        $('#suche')
            .val('')
            .on('keyup', function (e) {
            if (e.which !== 0) {
                suche($(this).val());
            }
        });
    });

    function suche(begriff) {
        $.ajax({
            url: 'index.php?module=welcome&action=meineapps&cmd=suche',
            type: 'POST',
            dataType: 'json',
            data: {val: begriff}
        })
            .done(function (data) {
                if (typeof data === 'undefined' || data === null) {
                    return;
                }
                // Apps ausblenden
                if (typeof data.ausblenden !== 'undefined' && data.ausblenden !== null) {
                    $.each(data.ausblenden, function (modulKey, v) {
                        if (modulKey !== '') $('#' + modulKey).hide();
                    });
                }
                // Apps einblenden
                if (typeof data.anzeigen !== 'undefined' && data.anzeigen !== null) {
                    $.each(data.anzeigen, function (modulKey, v) {
                        if (modulKey !== '') $('#' + modulKey).show();
                    });
                }
                // Meldung anzeigen wenn keine Apps gefunden wurden
                if (typeof data.gefunden !== 'undefined' && data.gefunden !== null) {
                    if (parseInt(data.gefunden) === 0) {
                        $('#keineappsgefunden').show();
                    } else  {
                        $('#keineappsgefunden').hide();
                    }
                }
            });
    }
</script>

<!-- gehort zu tabview -->
<div id="tabs">
  <ul>
    <li><a href="#tabs-1"><!--[TABTEXT]--></a></li>
  </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
  <div id="tabs-1">
    [MESSAGE]
    <fieldset><legend>{|Meine Apps|}</legend>
      <div id="app-suche">
        <div class="suche">
          <label for="suche">{|Suche|}</label>
          <input type="text" id="suche" value="">
        </div>
      </div>
      <div id="meineapps">
        [APPLIST]
        <div id="keineappsgefunden">
          <strong>{|Keine Apps mit diesen Suchkriterien gefunden|}</strong>
        </div>
      </div>
    </fieldset>
    [TAB1NEXT]
  </div>

<!-- tab view schließen -->
</div>

