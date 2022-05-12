<!-- gehort zu tabview -->
<div id="tabs">
  <ul>
    <li><a href="#tabs-1">[TABTEXT]</a></li>
  </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
	<div id="tabs-1">
		[MESSAGE]
		[TAB1]

		<div class="row">
		<div class="row-height">
		<div class="col-xs-12 col-sm-6 col-sm-height">
		<div class="inside inside-full-height">

			<fieldset>
				<legend>{|Auswahl Etiketten|}</legend>
				[FORMULAR]
			</fieldset>

		</div>
		</div>
		<div class="col-xs-12 col-sm-6 col-sm-height">
		<div class="inside inside-full-height">

			<div id="abweichendelieferadressestyle">
				<fieldset>
					<legend>&nbsp;</legend>
					[BILD]
				</fieldset>
			</div>

		</div>
		</div>
		</div>
		</div> <!-- spalte 2 zu -->

		[TAB1NEXT]
	</div>

<!-- tab view schließen -->
</div>


<script type="text/javascript">

function Etikettendrucken(id, anzahl) {
  anzahl = 0;
  $.ajax({
    url: 'index.php?module=lager&action=etikettenlist&cmd=holeanzahl',
    data: {
      id: id,
      von: $('#von').val(),
      bis: $('#bis').val(),
      etikettenauswahl: $('#etikettenauswahl').val(),
      etikettendrucker: $('#etikettendrucker').val()
    },
    method: 'post',
    dataType: 'json',
    beforeSend: function() {
    },
    success: function(data) {
      if (data.status == 1) {
        anzahl = data.anzahl;
        daten = data.daten;
			  var conf = confirm(anzahl+' Etiketten drucken?');
			  if (conf) {
			    $.ajax({
			      url: 'index.php?module=lager&action=etikettenlist&cmd=print&data='+daten,
			      data: {
			        id: id
			      },
			      method: 'post',
			      dataType: 'json',
			      beforeSend: function() {
			        App.loading.open();
			      },
			      success: function(data) {
			        if (data.status == 1) {
			          window.location.replace("index.php?module=lager&action=etikettenlist");
			        } else {
			          alert(data.statusText);
			        }
			        App.loading.close();
			      }
			    });
			  }
			  return false;
      } else {
        alert(data.statusText);
      }
    }
  });


}

</script>