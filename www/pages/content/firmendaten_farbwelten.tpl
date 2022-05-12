<!-- gehort zu tabview -->
<div id="tabs">
  <ul>
    <li><a href="#tabs-1"><!--[TABTEXT]--></a></li>
  </ul>
  <!-- ende gehort zu tabview -->
  <!-- erstes tab -->
  <div id="tabs-1">
    [MESSAGE]
    <style>

      #drop-files {
        width: 100%;
        min-widh:200px;
      //max-width:90vw;
        min-height: 100px;
        background: rgba(0,0,0,0.1);
        border: 4px dashed rgba(0,0,0,0.2);
        padding: 45px 0 0 0;
        text-align: center;
        font-size: 2em;
        float: left;
        font-weight: bold;
        margin: 0 20px 20px 0;
      }
    </style>
  <div class="row">
  <div class="row-height">
  <div class="col-xs-12 col-md-6 col-md-height">
  <div class="inside inside-full-height">

    <fieldset>
      <legend>{|Logo|}</legend>
      <form action="" method="post">
        <table width="100%">
          <tr>
            <td colspan="3"><div id="drop-files" ondragover="return false">[DATEI]</div><i>(Logo mit Drag & Drop auf Fläche ziehen)</i>
            <input type="hidden" id="bild" name="bild" value="" /></td></tr>
          </tr>
          <tr>
            <td>{|Hintergrundfarbe|}:</td><td><input type="text" name="firmenfarbehell" id="firmenfarbehell" value="[FIRMENFARBEHELL]" onchange="changefarbe2(this);" /></td><td align="right"><input type="submit" name="speichern" value="Farbe und Logo als Standard übernehmen" /></td>
          </tr>
          <!--<tr>
            <td>{|Hoher Formular-Kontrast:|}</td><td><input type="checkbox" onchange="changefarbe2($('#firmenfarbehell'));" value="1" id="firmenhoherformularkontrast" name="firmenhoherformularkontrast" [FIRMENHOHERFORMULARKONSTRAST] /> Vorschau: <input type="text" /></td>
          </tr>-->
          <!--<tr>
            <td>{|Schriftfarbe|}:</td><td><input type="text" name="schriftfarbe" id="schriftfarbe" value="[SCHRIFTFARBE]" /></td>
          </tr>-->
        </table>
      </form>
    </fieldset>
  
 
  </div>
  </div>

<div class="col-xs-12 col-md-6 col-md-height">
  <div class="inside inside-full-height">

  <fieldset>
    <legend>&nbsp;</legend>
  </fieldset>

  </div>
  </div>

  </div>
  </div>

 <!--<div class="row">
  <div class="row-height">-->
    [THEMESVORSCHAU]
  <!--
  <div class="col-xs-12 col-md-3 col-md-height">
  <div class="inside inside-full-height">

  <fieldset>
    <legend>WaWision New</legend> 
    <img src="./images/farbwelten/farbwelt_standard.jpg">
  </fieldset> 

  </div>
  </div>
  <div class="col-xs-12 col-md-3 col-md-height">
  <div class="inside inside-full-height">


  <fieldset>
    <legend>WaWision Legacy</legend> 
    <img src="./images/farbwelten/farbwelt_standard.jpg">
  </fieldset> 


  </div>
  </div>
  <div class="col-xs-12 col-md-3 col-md-height">
  <div class="inside inside-full-height">


  <fieldset>
    <legend>Hoher Kontrast</legend> 
    <img src="./images/farbwelten/farbwelt_standard.jpg">
  </fieldset> 

  </div>
  </div>
  <div class="col-xs-12 col-md-3 col-md-height">
  <div class="inside inside-full-height">


  <fieldset>
    <legend>Elegant</legend> 
    <img src="./images/farbwelten/farbwelt_standard.jpg">
  </fieldset> 

  </div>
  </div>
    -->

  <!--</div>
  </div>-->





    [TAB1NEXT]
  </div>
<!-- tab view schließen -->
</div>
<script>

  function changefarbe2(el)
  {
    $.ajax({
      url: 'index.php?module=firmendaten&action=farbwelten&cmd=checkfarbe',
      type: 'POST',
      dataType: 'json',
      data: { farbe:$(el).val()},
      success: function(data) {
        if(typeof data.color != 'undefined')
        {
          changefarbe(data.color,data.color2);
        }
      }
    });

  }

  function changefarbe(color, color2)
  {
    if(color != '') {
      $("html").attr("style", "--color1:" + color+"; "
      +  "--color2:" + color2+";"+"--textfield-border: "+($('#firmenhoherformularkontrast').prop('checked')?'#666':'#999')+";"
      );
    }
  }

  $('#drop-files').bind('dragenter', function() {
    $(this).css({'box-shadow' : 'inset 0px 0px 20px rgba(0, 0, 0, 0.1)', 'border' : '4px dashed #bb2b2b'});
    return false;
  });

  $('#drop-files').bind('drop', function() {
    $(this).css({'box-shadow' : 'none', 'border' : '4px dashed rgba(0,0,0,0.2)'});
    return false;
  });

  jQuery.event.props.push('dataTransfer');
  var z = -40;
  var maxFiles = 1;
  var errMessage = 0;

  var dataArray = [];

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
            $('#dateiname').val(file.name);
            $('#bild').val(image);
            $('#profilbild').css('background-image','url('+image+')');
            $.ajax({
              url: 'index.php?module=firmendaten&action=farbwelten&cmd=checkimage',
              type: 'POST',
              dataType: 'json',
              data: { bild:image, name:file.name},
              success: function(data) {
                if(typeof data.color != 'undefined')
                {
                  $('#firmenfarbehell').val(data.color);
                  $('#firmenfarbehell').trigger('change');
                  changefarbe(data.color,data.color2);
                }
              }
            });
            //vorschau = '<span class="image" style="float:right;padding:0;margin:0;height:40px;width:40px;display:inline-block;position:relative;max-width:40px;max-height:40px; background: url('+image+'); background-size: cover;"></span>';
          }else alert('kein Bild');
          //           $('.stddownload').hide();
          //$('#trdatei').before('<tr><td>Datei '+vorschau+'</td><td class="tddateiname"><input type="hidden" name="dateiv[]" value="'+image+'" /><input type="hidden" name="dateiname[]" value="'+file.name+'" />'+file.name+'</td><td>Titel: <input type="text" name="dateititel[]" /></td><td><select name="dateistichwort[]">'+$('#stichwort').html()+'</select></td></tr>')
        };

      })(files[index]);
      fileReader.readAsDataURL(file);

    });
  });
</script>
