<!-- gehort zu tabview -->
<div id="tabs">
	<ul>
		<li><a href="#tabs-1"></a></li>
	</ul>
	<div id="tabs-1">
		<div id="templateedit" style="display:none" title="Bearbeiten">
			<div class="row">
				<div class="row-height">
					<div class="col-xs-12 col-md-12 col-md-height">
						<div class="inside inside-full-height">
							<input type="hidden" id="templateid" value="">
							<fieldset>
								<legend>{|Vorlage|}</legend>
								<table>
									<tr>
										<td width="120">
											<label for="description">{|Bezeichnung|}:</label>
										</td>
										<td>
											<input type="text" name="description" id="description" size="40" />
										</td>
									</tr>
									<tr>
										<td>
											<label for="article">{|Artikel|}:</label>
										</td>
										<td>
											<input type="text" name="article" id="article" size="40" />
										</td>
									</tr>
									<tr>
										<td>
											<label for="active">{|Aktiv|}:</label>
										</td>
										<td>
											<input type="checkbox" name="active" id="active" />
										</td>
									</tr>
								</table>
							</fieldset>
						</div>
					</div>
				</div>
			</div>
		</div>

		[MESSAGE]

		[TAB1]
		[TAB1NEXT]
	</div>


	<!-- tab view schließen -->
</div>


<script>
    $(document).ready(function() {
        $("#templateedit").dialog({
            modal: true,
            bgiframe: true,
            closeOnEscape:false,
            minWidth:600,
            autoOpen: false,
            buttons: {
                ABBRECHEN: function() {
                    $(this).dialog('close');
                },
                SPEICHERN: function() {
                    savetemplate();
                }
            }
        });

        $("#priceedit").dialog({
            modal: true,
            bgiframe: true,
            closeOnEscape:false,
            minWidth:600,
            autoOpen: false,
            buttons: {
                ABBRECHEN: function() {
                    $(this).dialog('close');
                },
                SPEICHERN: function() {
                    saveprice();
                }
            }
        });
    });

    $("#type").change(function(){
        let type = $("#type").val();
        if(type === 'standard'){
					document.getElementById("customerdiv").style.display="none";
					document.getElementById("groupdiv").style.display="none";
        }
        if(type === 'customer'){
            document.getElementById("customerdiv").style.display="";
            document.getElementById("groupdiv").style.display="none";
        }
        if(type === 'group'){
            document.getElementById("customerdiv").style.display="none";
            document.getElementById("groupdiv").style.display="";
        }
    });

    function newedittemplate(id)
    {
        if(id){
            $.ajax({
                url: 'index.php?module=retailpricetemplate&action=gettemplate',
                type: 'POST',
                dataType: 'json',
                data: {
                    templateid: id
                },
                success: function(response) {
                    if(response.success){
                        $('#templateid').val(response.data.id);
                        $('#description').val(response.data.description);
                        $('#article').val(response.data.articleNoAndName);
                        $('#active').prop("checked",!!response.data.active);
                    }else{
                        alert(response.alert);
                    }
                },
                beforeSend: function() {
                }
            });
        }else{
						$('#templateid').val('');
						$('#description').val('');
						$('#article').val('');
						$('#active').prop("checked",true);
    		}

        $("#templateedit").dialog('open');
    }

    function savetemplate()
		{
        $.ajax({
            url: 'index.php?module=retailpricetemplate&action=savetemplate',
            type: 'POST',
            dataType: 'json',
            data: {
                templateid: $('#templateid').val(),
                description: $('#description').val(),
                article: $('#article').val(),
                active: $('#active').prop("checked")?1:0,
            },
            success: function(data) {
							if(data.success){
                  updateLiveTable('retail_price_template');
                  $("#templateedit").dialog('close');
              }else{
							    alert(data.error);
              }
            },
            beforeSend: function() {
            }
        });
    }

    function deletetemplate(id)
    {
        if(!confirm("Soll die Vorlage wirklich aus der Auflistung entfernt werden?")) return false;
        $.ajax({
            url: 'index.php?module=retailpricetemplate&action=deletetemplate',
            data: {
								templateid: id
            },
            method: 'post',
            dataType: 'json',
            beforeSend: function() {
            },
            success: function(data) {
                if(data.success){
                    updateLiveTable('retail_price_template');
                }else{
                    alert(data.error);
                }
            }
        });
    }

    function updateLiveTable(name) {
        let oTableL = $('#'+name).dataTable();
        oTableL.fnFilter('%');
        oTableL.fnFilter('');
    }


</script>
