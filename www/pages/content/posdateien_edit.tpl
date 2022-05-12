<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1"></a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]
<form action="" method="post" enctype="multipart/form-data">
Kamera Bild: <input type="file" accept="image/*" name="upload" id="upload" capture="camera"> 
<input type="submit" value="Speichern">
</form>
<div>
[TAB1]
[TAB1NEXT]
</div>

</div>

<!-- tab view schließen -->
</div>

<div id="posdialog" style="display:none;text-align:center;"></div>

<script>
function imgpopup(el)
{
  var bwidth = parseInt($( window ).width());
  bwidth = bwidth - 200;
  if(bwidth < 600)bwidth = 600;
  //if(bwidth > 1000)bwidth = 1000;
  var bheight = parseInt($( window ).height());
  bheight = bheight - 200;
  if(bheight < 400)bheight = 400;
  if(bheight > 700)bheight = 700;
  if(bheight > bwidth)bheight = bwidth;
  
  $('#posdialog').html('<img style="max-width:100%;max-height:100%" src="index.php?module=posdateien&action=bild&id='+el+'" />');
  $('#posdialog').dialog({
                title: $('#imgtitel_'+el).html(),
                autoOpen: true,
                width: bwidth,
                height: bheight, 
                modal: true,
                resizable: true
            });
}
</script>
