<!-- gehort zu tabview -->
<div id="tabs">
  <ul>
    <li><a href="#tabs-1"></a></li>
  </ul>
<!-- ende gehort zu tabview -->

  <!-- erstes tab -->
  <div id="tabs-1">
    <form action="" method="post" name="eprooform">
      <div class="row">
        <div class="row-height">
          <div class="col-xs-12 col-md-8 col-md-height">
            <div class="inside inside-full-height">

              <div class="filter-box filter-usersave">
                <div class="filter-block filter-inline">
                  <fieldset>
                    <div class="filter-title">{|Filter|}</div>
                    <ul class="filter-list">
                      <li class="filter-item">
                        <label for="lieferungfehlt" class="switch">
                          <input type="checkbox" id="lieferungfehlt" value="1" />
                          <span class="slider round"></span>
                        </label>
                        <label for="lieferungfehlt">{|Lieferung fehlt|}</label>
                      </li>
                      <li class="filter-item">
                        <label for="nurkunden" class="switch">
                          <input type="checkbox" id="nurkunden" value="1" />
                          <span class="slider round"></span>
                        </label>
                        <label for="nurkunden">{|nur Kunden|}</label>
                      </li>
                      <li class="filter-item">
                        <label for="nurlieferanten" class="switch">
                          <input type="checkbox" id="nurlieferanten" value="1" />
                          <span class="slider round"></span>
                        </label>
                        <label for="nurlieferanten">{|nur Lieferanten|}</label>
                      </li>
                      <li class="filter-item">
                        <label for="kundenmitrma" class="switch">
                          <input type="checkbox" id="kundenmitrma" value="1" />
                          <span class="slider round"></span>
                        </label>
                        <label for="kundenmitrma">{|Retoure eingegangen|}</label>
                      </li>
                      <li class="filter-item">
                        <label for="zeitvon">{|Lieferung von|}:</label>
                        <input type="text" id="zeitvon" size="10"/>
                      </li>
                      <li class="filter-item">
                        <label for="zeitbis">{|bis|}:</label>
                        <input type="text" id="zeitbis" size="10">
                      </li>
                    </ul>
                  </fieldset>
                </div>
              </div>
            </div>
          </div>
          <div class="col-xs-12 col-md-4 col-md-height">
            <div class="inside inside-full-height">
              <fieldset>
                <legend>{|Scan|}</legend>
                <div class="usersave-box clearfix inside">
                  <table>
                    <tr>
                      <td><label for="articlescan">{|Eingabe|}:</label></td>
                      <td><input type="hidden" id="ismobile" name="ismobile" /><input type="text" size="30" id="articlescan" name="articlescan" /></td>
                    </tr>
                  </table>
                </div>
              </fieldset>
            </div>
          </div>
        </div>
      </div>

      [INFO]
      [LETZTESPAKET]
      [SUCHE]
      [TAB1]
    </form>
  </div>
<script type="text/javascript">
let el = document.getElementById("adresssuche");
if(el != null)el.focus();

document.getElementById('articlescan').focus();

$(document).ready(function () {

    // "nur Kunden" + "nur Lieferanten" > Jeweils anderen Haken entfernen
    // Beide zur gleichen Zeit machen keinen Sinn.
    $('#nurkunden, #nurlieferanten').change(function() {
        var elementId = $(this).prop('id');
        var isChecked = $(this).prop('checked');

        var otherId = elementId === 'nurkunden' ? 'nurlieferanten' : 'nurkunden';
        var otherChecked = $('#' + otherId).prop('checked');

        // Wenn beide Haken gesetzt > Klick auf den nicht geklickten Haken auslösen
        if (isChecked === true && otherChecked === true) {
            window.setTimeout(function () {
                $('#' + otherId).trigger('click');
            }, 50);
        }
    });

  $('#articlescan').on('keydown', function(event) {
    if(event.which == 13) {
      $('#ismobile').val($('div.menu-opener:visible').length);
      $('form[name="eprooform"]').submit();
    }
  });
  $('#articlescan').on('change',function (){
    $('#ismobile').val($('div.menu-opener:visible').length);
  });
});
</script>
<!-- tab view schließen -->
</div>
