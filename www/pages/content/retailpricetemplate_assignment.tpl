<div id="tabs">
	<ul>
		<li><a href="#tabs-1"></a></li>
	</ul>
	<div id="tabs-1">

		[MESSAGE]

		[TAB1]
		[TAB1NEXT]
		<fieldset>
			<legend>Stapelverarbeitung</legend>
			<input type="checkbox" id="alle" onchange="toggleall()"> <label for="alle">{|Alle auswählen|}</label>
			<br /><br />
			<label for="templates">{|Vorlagen|}: </label><select id="templates">[TEMPLATES]</select>
			<input type="submit" class="btnBlue" onclick="assign()" value="{|Zuweisen|}">
		</fieldset>

	</div>


</div>

<script>
	function toggleall() {
      $('input[id^=\'aid_\']').prop('checked',$('#alle').prop('checked'));
  }

  function assign(){
      let aids = [];
      let checkboxen = $(":input[id^='aid_']:checked");
      for (let i = 0; i < checkboxen.length; i++) {
          let apidtmp = checkboxen[i].id.split('_');
          aids.push(apidtmp[1]);
      }


      $.ajax({
          url: 'index.php?module=retailpricetemplate&action=assigntemplates',
          type: 'POST',
          dataType: 'json',
          data: {
              aids: aids,
              templateid: $('#templates').val(),
          },
          success: function(data) {
              if(data.success){
              }else{
                  alert(data.error);
              }
          },
          beforeSend: function() {
          }
      });
      updateLiveTable('retail_price_template_assignment');
  }

  function updateLiveTable(name) {
      let oTableL = $('#'+name).dataTable();
      oTableL.fnFilter('%');
      oTableL.fnFilter('');
  }
</script>	