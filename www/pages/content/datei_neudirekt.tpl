<table border="0" width="100%">
	<tr>
		<td>
			<table width="100%">
				<tr>
					<td>
						<form action="" method="post" name="eprooform" enctype="multipart/form-data">

							<table  class="tableborder" border="0" cellpadding="3" cellspacing="3" width="100%">
  							<tr>
  								<td colspan="4">[ERROR]</td>
  							</tr>
  							<tr id="trdropfiles">
    							<td colspan="4">	
    								<div id="drop-files" ondragover="return false">
											{|Dateien hier einf&uuml;gen|}
    								</div>
    							</td>
  							</tr>
								<tr id="trdatei" class="stddownload" data-maxsize="[MAXSIZE]">
									<td width="200">{|Datei|}:</td>
									<td colspan="3"><input type="file" name="upload" style="min-width: 230px;"></td>
								</tr>
								[STARTDISABLE]
								<tr class="stddownload">
									<td>{|Titel|}:</td>
									<td colspan="3"><input type="text" style="width: 230px;" name="titel" value="[TITEL]"></td>
								</tr>
								<tr class="stddownload">
									<td>{|Beschreibung|}:</td>
									<td colspan="3"><textarea style="width: 230px;height: 100px;" name="beschreibung">[BESCHREIBUNG]</textarea></td>
								</tr>

								<tr valign="middle" class="stddownload">
									<td>{|Stichwort|}:</td>
									<td colspan="3">
								    <table>
								      <tr> 
								      	<td><select id="stichwort" name="stichwort">
									    		[EXTRASTICHWOERTER]
								      		</select></td>
								      	<td></td>
								      	<td></td>
								      </tr>
								    </table>
								  </td>
								</tr>
								<tr><td><br><br></td><td></td><td></td><td></td></tr>
								[ENDEDISABLE]
<!--
	    <tr><td>ISO9001:</td><td colspan="3"> <select name="gruppe">      
                  <option>keine</option>
                  <option>100 Organigramme</option>
                  <option>105 Pl&auml;ne</option>
                  <option>110 Festlegungen</option>
                  <option>115 Protokolle</option>
                  <option>125 Stellenbeschreibung</option>
                  <option>610 Schaltpl&auml;ne</option>
                  <option>611 BOM</option>
              </select></td></tr>
-->
<!--
	    <tr><td>Dokumentenummer:</td><td colspan="3">
	      <table>
	      <tr><td>Gruppe</td><td><b>L</b>aufindex</td><td><b>V</b>ersion</td></tr>
	      <tr valign="top"><td>
              <select name="gruppe">	  
		  <option>keine</option>
		  <option>100 Organigramme</option>
		  <option>105 Pl&auml;ne</option>
		  <option>110 Festlegungen</option>
		  <option>115 Protokolle</option>
		  <option>125 Stellenbeschreibung</option>
		  <option>610 Schaltpl&auml;ne</option>
		  <option>611 BOM</option>
	      </select></td><td>
	      <input type="text" size="10" name="nummer" value="[NUMMER]"><br>
	      <input type="checkbox" name="automatisch">L + V automatisch
	      </td><td>
              <select name="version"><option value=""></option><option value="A">A</option><option value="B">B</option></select></td></tr></table>
	      </td></tr>
-->
								<tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
							  	<td width="" valign="" height="" bgcolor="" align="right" colspan="4" bordercolor="" classname="orange2" class="orange2">
							  	<input type="submit" name="speichern"
							  	value="{|Speichern|}" /> <input type="button" onclick="$( '#tabs' ).tabs({ active: 0 });" value="{|Abbrechen|}" /></td>
								</tr>
							</table>
						</form>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<div id="dropped-files">
	<div id="upload-button">
		<a href="#" class="upload">Upload!</a>
		<a href="#" class="delete">delete</a>
		<span>0 Files</span>
	</div>
	</div>
	<div id="extra-files">
		<div class="number">
			0
		</div>
		<div id="file-list">
			<ul></ul>
		</div>
	</div>
	<div id="loading">
		<div id="loading-bar">
			<div class="loading-color"> </div>
  	</div>
		<div id="loading-content">Uploading file.jpg</div>
	</div>
	
	<div id="file-name-holder">
		<ul id="uploaded-files">
			<h1>{|Hochgeladene Dateien|}</h1>
		</ul>
	</div>
  <style>
  
#drop-files {
	width: 100%;
  //max-width:90vw;
	height: 125px;
	background: rgba(0,0,0,0.1);
	border: 4px dashed rgba(0,0,0,0.2);
	padding: 75px 0 0 0;
	text-align: center;
	font-size: 2em;
	float: left;
	font-weight: bold;
	margin: 0 20px 20px 0;
}

#dropped-files {
	float: left;
	position: relative;
	width: 560px;
	height: 125px;
}

#upload-button {
	position: absolute;
	top: 87px;
	z-index: 9999;
	width: 210px;
	display: none;
}

#dropped-files .image {
	height: 0px;
	width:0px;
	border: 4px solid #fff;
	position: absolute;
	overflow: hidden;
}


#upload-button a:hover {
	box-shadow: 0 0 1000px 62px rgba(255, 255, 255, 1), inset 0 -5px 40px 0px #0A9FCA;	
}

#extra-files {
	display: none;
	float: left;
	position: relative;
}


#extra-files #file-list:after, #extra-files #file-list:before {
	position: absolute;
	content: " ";
	top: -40px;
	left: 40px;
	display: block;
	border: 20px solid;
	border-color: transparent transparent #ffffff transparent;
}



#loading {
	display: none;
	float: left;
	width: 100%;
	position: relative;
}






  </style>
<script>

$(document).ready(function() {
	
  $('#editdatei').dialog(
  {
    modal: true,
    autoOpen: false,
    minWidth: 940,
    title:'Dateitexte bearbeiten',
    buttons: {
    	'{|ABBRECHEN|}': function() {
        $(this).dialog('close');
      },
      '{|SPEICHERN|}': function()
      {
        	var file_data = $('#editfile').prop('files')[0];
        	var form_data = new FormData();
        	form_data.append('datei', file_data);
        	form_data.append('id',$('#editid').val());
        	form_data.append('subjekt',$('#editsubjekt').val());
        	form_data.append('typ',$('#edittyp').val());
        	form_data.append('titel',$('#edittitel').val());
        	form_data.append('beschreibung',$('#editbeschreibung').val());
        	form_data.append('parameter','[ID]');
          $.ajax({
              url: 'index.php?module=ajax&action=editdateititel',
              type: 'POST',
              dataType: 'json',
              data: form_data,
							cache: false,
							contentType: false,
							processData: false,
              success: function(data) {
                var nul = 0;
                var urla = window.location.href.split('#');
                
                window.location.href=urla[nul];
              },
              beforeSend: function() {

              }
          });

      }

    },
    close: function(event, ui){
      
    }
  });

	// Makes sure the dataTransfer information is sent when we
	// Drop the item in the drop box.
	jQuery.event.props.push('dataTransfer');
	
	var z = -40;
	// The number of images to display
	var maxFiles = 5;
	var errMessage = 0;
	
	// Get all of the data URIs and put them in an array
	var dataArray = [];
	
	// Bind the drop event to the dropzone.
	$('#drop-files').bind('drop', function(e) {
			
		// Stop the default action, which is to redirect the page
		// To the dropped file
		
		var files = e.dataTransfer.files;
		
    
    $.each(files, function(index, file) {
      var isimg = false;
      if (files[index].type.match('image.*')) {
        isimg = true;
      }
			var fileReader = new FileReader();
      
				
				// When the filereader loads initiate a function
				fileReader.onload = (function(file) {
					//alert('x');
					return function(e) { 
						
						// Push the data URI into an array
						dataArray.push({name : file.name, value : this.result});
						// Move each image 40 more pixels across
						z = z+40;
						
						
						
						// Just some grammatical adjustments
						if(dataArray.length == 1) {
							$('#upload-button span').html("1 file to be uploaded");
						} else {
							$('#upload-button span').html(dataArray.length+" files to be uploaded");
						}
						// Place extra files in a list
            var vorschau = '';
            var image = this.result;
						if(isimg) { 
							// Place the image inside the dropzone
							//$('#dropped-files').append('<div class="image" style="left: '+z+'px; background: url('+image+'); background-size: cover;"> </div>');
              
              vorschau = '<span class="image" style="float:right;padding:0;margin:0;height:40px;width:40px;display:inline-block;position:relative;max-width:40px;max-height:40px; background: url('+image+'); background-size: cover;"></span>';
						}
						else {
							vorschau = '';
							//$('#extra-files .number').html('+'+($('#file-list li').length + 1));
							// Show the extra files dialogue
							//$('#extra-files').show();
							
							// Start adding the file name to the file list
							//$('#extra-files #file-list ul').append('<li>'+file.name+'</li>');
							
						}
            $('.stddownload').hide();
            var filenameEncoded = encodeHtmlAttrValue(file.name);
            if($('#trdateierror').length) {
              $('#trdateierror').remove();
            }
            var maxsize = parseInt($('#trdatei').data('maxsize'));
            if(isNaN(maxsize)) {
                maxsize = 0;
            }
            if(maxsize > 0 && dataArray.length > 1) {
                var actLength = 0;
                $(dataArray).each(function(k, v) {
                    actLength += parseFloat(v.value.length);
                });
                if(actLength > maxsize) {
                    $('#trdatei').after('<tr id="trdateierror"><td colspan="4"><div class="error">Die ausgew&auml;hlten Dateien sind zu gro&szlig;. Bitte laden Sie diese einzeln hoch.</div></td></tr>');
                }
            }

            $('#trdatei').before('<tr><td>Datei '+vorschau+'</td><td class="tddateiname"><input type="hidden" name="dateiv[]" value="'+image+'" /><input type="hidden" name="dateiname[]" value="'+filenameEncoded+'" />'+filenameEncoded+'</td><td>Titel: <input type="text" name="dateititel[]" /></td><td><select name="dateistichwort[]">'+$('#stichwort').html()+'</select></td></tr>');
					}; 
					
				})(files[index]);
      fileReader.readAsDataURL(file);
      
    });
    

		

	});

	function encodeHtmlAttrValue(value) {
		return value
			.replace(/&/g, '&amp;')
			.replace(/'/g, '&#039;')
			.replace(/"/g, '&quot;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;');
	}
	
	function restartFiles() {
	
		// This is to set the loading bar back to its default state
		$('#loading-bar .loading-color').css({'width' : '0%'});
		$('#loading').css({'display' : 'none'});
		$('#loading-content').html(' ');
		// --------------------------------------------------------
		
		// We need to remove all the images and li elements as
		// appropriate. We'll also make the upload button disappear
		
		$('#upload-button').hide();
		$('#dropped-files > .image').remove();
		$('#extra-files #file-list li').remove();
		$('#extra-files').hide();
		$('#uploaded-holder').hide();
	
		// And finally, empty the array/set z to -40
		dataArray.length = 0;
		z = -40;
		
		return false;
	}
	
	$('#upload-button .upload').click(function() {
		
		$("#loading").show();
		var totalPercent = 100 / dataArray.length;
		var x = 0;
		var y = 0;
		
		$('#loading-content').html('Uploading '+dataArray[nu].name);
		
		$.each(dataArray, function(index, file) {	
    var nu = 0;
    var ein = 1;
			alert(dataArray[index].name);
			$.post('upload.php', dataArray[index], function(data) {
			
				var fileName = dataArray[index].name;
				++x;
				
				// Change the bar to represent how much has loaded
				$('#loading-bar .loading-color').css({'width' : totalPercent*(x)+'%'});
				
				if(totalPercent*(x) == 100) {
					// Show the upload is complete
					$('#loading-content').html('Uploading Complete!');
					
					// Reset everything when the loading is completed
					setTimeout(restartFiles, 500);
					
				} else if(totalPercent*(x) < 100) {
				
					// Show that the files are uploading
					$('#loading-content').html('Uploading '+fileName);
				
				}
				
				// Show a message showing the file URL.
				var dataSplit = data.split(':');
				if(dataSplit[ein] == 'uploaded successfully') {
					var realData = '<li><a href="images/'+dataSplit[nu]+'">'+fileName+'</a> '+dataSplit[ein]+'</li>';
					
					$('#uploaded-files').append('<li><a href="images/'+dataSplit[nu]+'">'+fileName+'</a> '+dataSplit[ein]+'</li>');
				
					// Add things to local storage 
					if(window.localStorage.length == 0) {
						y = 0;
					} else {
						y = window.localStorage.length;
					}
					
					window.localStorage.setItem(y, realData);
				
				} else {
					$('#uploaded-files').append('<li><a href="images/'+data+'. File Name: '+dataArray[index].name+'</li>');
				}
				
			});
		});
		
		return false;
	});
	
	// Just some styling for the drop file container.
	$('#drop-files').bind('dragenter', function() {
		$(this).css({'box-shadow' : 'inset 0px 0px 20px rgba(0, 0, 0, 0.1)', 'border' : '4px dashed #bb2b2b'});
		return false;
	});
	
	$('#drop-files').bind('drop', function() {
		$(this).css({'box-shadow' : 'none', 'border' : '4px dashed rgba(0,0,0,0.2)'});
		return false;
	});
	
	// For the file list
	$('#extra-files .number').toggle(function() {
		$('#file-list').show();
	}, function() {
		$('#file-list').hide();
	});
	
	$('#dropped-files #upload-button .delete').click(restartFiles);
	
	// Append the localstorage the the uploaded files section
	if(window.localStorage.length > 0) {
		$('#uploaded-files').hide();
		for (var t = 0; t < window.localStorage.length; t++) {
			var key = window.localStorage.key(t);
			var value = window.localStorage[key];
			// Append the list items
			if(value != undefined || value != '') {
				$('#uploaded-files').append(value);
			}
		}
	} else {
		$('#uploaded-files').hide();
	}
});

function editdatei(datei, cmd)
{
  $.ajax({
      url: 'index.php?module=ajax&action=getdateititel',
      type: 'POST',
      dataType: 'json',
      data: { id: datei, typ:cmd,parameter:"[ID]"},
      success: function(data) {
        if(typeof data.id != 'undefined')
        {
          $('#editid').val(data.id);
          $('#editsubjekt').html(data.subjekthtml);
          $('#editsubjekt').val(data.subjekt);
          $('#edittyp').val(cmd);
          $('#edittitel').val(data.titel);
          $('#editbeschreibung').val(data.beschreibung);
          $('#editdatei').dialog('open');
        }
      },
      beforeSend: function() {

      }
  });
}
  
</script>
<div id="editdatei" style="display:none;">
	<fieldset>
		<table>
			<tr>
				<td><input type="hidden" id="edittyp" value="" /><input type="hidden" id="editid" value="" />{|Titel|}:</td>
				<td><input type="text" id="edittitel" size="50"></td>
			</tr>
			<tr>
				<td>{|Beschreibung|}:</td>
				<td><textarea id="editbeschreibung" cols="50"></textarea></td>
			</tr>
			<tr>
				<td>{|Stichwort|}:</td>
				<td><select id="editsubjekt"></select></td>
			</tr>
			<tr>
				<td>{|Datei|}:</td>
				<td><input type="file" id="editfile" name="editfile" /></td>
			</tr>
		</table>
	</fieldset>
</div>
