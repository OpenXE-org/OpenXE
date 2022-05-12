<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">{|Offene Rechnungen|}</a></li>
        <li><a href="#tabs-2">{|Offene Gutschriften|}</a></li>
        <li><a href="#tabs-3">{|Abgebuchte Rechnungen|}</a></li>
        <li><a href="#tabs-4">{|Zahlungsavis|}</a></li>
        <li><a href="#tabs-5">{|SEPA Sammellastschriften|}</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">

<form method="post">
<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-10 col-md-height">
<div class="inside_white inside-full-height">
	<div class="filter-box filter-usersave">
		<div class="filter-block filter-inline">
			<div class="filter-title">{|Filter|}</div>
			<ul class="filter-list">
				<li class="filter-item">
					<label for="faellig" class="switch">
						<input type="checkbox" id="faellig">
						<span class="slider round"></span>
					</label>
					<label for="faellig">{|in min. 10 Tagen f&auml;llig|}</label>
				</li>
				<!--  <td><input type="checkbox" id="abfallenden">&nbsp;nur Abstiege</td>-->
			</ul>
		</div>
	</div>
  <fieldset class="white">
     <legend> </legend>
	  [TAB1]
  </fieldset>
</div>
</div>
<div class="col-xs-12 col-md-2 col-md-height">
<div class="inside inside-full-height">
	<fieldset>
                <legend>{|&Uuml;bersicht|}</legend>
		<table width="100%>">
      <tr>
        <td>{|Gesamt offen (Lastschrift)|}</td>
      </tr>
      <tr>
        <td class="greybox" width="20%">[GESAMTOFFENRECHNUNG]</td>
      </tr>
     </table>
	</fieldset>
</div>
</div>
</div>
</div>

<fieldset>
	<legend>{|Stapelverarbeitung|}</legend>
	[SAMMELDRUCK]
</fieldset>

</form>
</div>


<div id="tabs-2">

<form method="post">

<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-10 col-md-height">
<div class="inside_white inside-full-height">
  <fieldset class="white">
    <legend> </legend>
	  [TAB2]
  </fieldset>
</div>
</div>
<div class="col-xs-12 col-md-2 col-md-height">
<div class="inside inside-full-height">
	<fieldset>
                <legend>{|&Uuml;bersicht|}</legend>
		<table width="100%>">
      <tr>
        <td>{|Gesamt offen (Lastschrift)|}</td>
      </tr>
      <tr>
        <td class="greybox" width="20%">[GESAMTOFFENGUTSCHRIFT]</td>
      </tr>
     </table>
	</fieldset>
</div>
</div>
</div>
</div>

</form>

</div>


<div id="tabs-3">
[TAB3]
</div>


<div id="tabs-4">
[TAB4]

<form method="post">

<fieldset>
	<legend>{|Stapelverarbeitung|}</legend>
  <table>
    <tr>
      <td><input type="checkbox" id="auswahlalleavis" onchange="alleauswaehlenavis();" />&nbsp;{|alle markieren|}</td>
      <td><select id="sammelavistyp" onchange="sammelavistypselect(this);"><option value="viaprozessstarter">über Prozessstarter versenden</option><option value="druck">Drucken</option></select></td>
      <td id="sammelmailavistd"><input type="button" class="btnBlue" name="versandavismailseneden" value="Offene Zahlungsavis senden" onclick="ausfuehren()"></td>
      <td id="sammeldruckavistd" style="display: none;">{|Auswahl Drucker|}:
        <select name="drucker">[SAMMELDRUCKAVISDRUCKER]</select>
        <input type="submit" class="btnBlue" name="versandavis" value="Offene Zahlungsavis senden" onclick="this.form.action += 'tabs-4';"></td>
    </tr>
  </table>

	[SAMMELDRUCKAVIS]
</fieldset>

</form>

</div>


<div id="tabs-5">
[MESSAGE]
[TAB5]
</div>




<!-- tab view schließen -->
</div>
<!-- ende tab view schließen -->


<script>
  function sammelavistypselect(obj){
      if(obj.value == 'druck'){
          document.getElementById("sammelmailavistd").style.display="none";
          document.getElementById("sammeldruckavistd").style.display="";
      }else{
          document.getElementById("sammelmailavistd").style.display="";
          document.getElementById("sammeldruckavistd").style.display="none";
      }
  }

  function ausfuehren(){

      var checkboxen = [];
      $("#zahlungsavis input:checked").each(function() {
          if($(this).attr('name') == 'avis[]'){
              checkboxen.push($(this).val());
          }
      });

      if(checkboxen != ''){
          var $dialog = $('#page_container');
          $dialog.loadingOverlay();
          $.ajax({
              url: 'index.php?module=zahlungsverkehr&action=lastschrift&cmd=prozessstarter',
              data: {
                  avisids: checkboxen
              },
              method: 'post',
              dataType: 'json',
              beforeSend: function() {
              },
              success: function(data) {
                  if(!data.success){
                      alert(data.message);
                  }
                  $dialog.loadingOverlay('remove');
              }
          });
      }
  }

	function alleauswaehlen()
	{
  	var wert = $('#auswahlalle').prop('checked');
  	$('#lastschriften').find(':checkbox').prop('checked',wert);
	}


	function alleauswaehlenavis()
	{
  	var wert = $('#auswahlalleavis').prop('checked');
  	$('#zahlungsavis').find(':checkbox').prop('checked',wert);
	}


</script>
